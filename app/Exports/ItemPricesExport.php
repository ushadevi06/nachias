<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ItemPricesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // 1. Preload BarcodeMasters for fast lookup
        $bmMap = [];
        $bms = DB::table('barcode_masters')
            ->select('art_no', 'size', 'sleeve_type', 'barcode_no')
            ->whereNotNull('barcode_no')
            ->where('barcode_no', '!=', '')
            ->get();

        foreach ($bms as $bm) {
            $art = strtolower(trim((string)$bm->art_no));
            $sz = strtolower(trim((string)$bm->size));
            $sl = strtolower(trim((string)$bm->sleeve_type));
            $bmMap[$art . '|' . $sz . '|' . $sl] = $bm->barcode_no;
        }

        // 2. Fetch prices ordered by effective_from desc, id desc (picks latest price if revisions exist)
        $query = DB::table('item_prices')
            ->whereNull('deleted_at')
            ->whereNotNull('art_no')
            ->where('art_no', '!=', '')
            ->where('art_no', '!=', '-');

        if (!empty($this->filters['date_range'])) {
            $dates = explode(' to ', $this->filters['date_range']);
            if (count($dates) === 2) {
                try {
                    $from = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                    $to = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                    $query->whereBetween('effective_from', [$from, $to]);
                } catch (\Exception $e) {}
            } elseif (count($dates) === 1 && !empty(trim($dates[0]))) {
                try {
                    $single = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                    $query->whereDate('effective_from', $single);
                } catch (\Exception $e) {}
            }
        }

        $prices = $query->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $supportedSizes = ['36', '38', '40', '42', '44', '46', '48', '50'];
        $seenKeys = [];
        $rows = [];

        foreach ($prices as $p) {
            $artNo = trim((string)($p->art_no ?? ''));
            $itemCode = trim((string)($p->finished_item_code ?? ''));
            $mrp = floatval($p->selling_price);
            $sp = floatval($p->unit_price);
            $status = $p->status ?? 'Active';
            $eff = $p->effective_from ? Carbon::parse($p->effective_from)->format('d-m-Y') : '-';

            // If specific size is present
            $targetSizes = [];
            if (!empty($p->size) && $p->size !== 'ALL') {
                $targetSizes[] = (string)$p->size;
            } else {
                $targetSizes = $supportedSizes;
            }

            foreach ($targetSizes as $sz) {
                $uniqueKey = $itemCode . '|' . strtolower($artNo) . '|' . $sz;
                if (isset($seenKeys[$uniqueKey])) {
                    continue;
                }
                $seenKeys[$uniqueKey] = true;

                // Determine sleeve from finished_item_code (e.g. FS -> Full, HS -> Half)
                $sleeve = 'Full';
                if (stripos($itemCode, '-HS') !== false || stripos($itemCode, 'HS') !== false) {
                    $sleeve = 'Half';
                }
                $sleeveTypeShort = ($sleeve === 'Full') ? 'f/s' : 'h/s';
                $sleeveCode = ($sleeve === 'Full') ? '01' : '02';

                // Barcode resolution: 1. BarcodeMaster, 2. Auto-generate
                $lookupKey = strtolower($artNo) . '|' . strtolower($sz) . '|' . $sleeveTypeShort;

                if (!empty($artNo) && isset($bmMap[$lookupKey])) {
                    $barcode = $bmMap[$lookupKey];
                } else {
                    if (empty($artNo)) {
                        $barcode = '-';
                    } else {
                        $hasAlpha = preg_match('/[a-zA-Z]/', $artNo);
                        $matchesExistingPattern = preg_match('/^([a-zA-Z]*)(\d+)(?:-(\d+))?$/', $artNo);

                        if ($hasAlpha && !$matchesExistingPattern) {
                            $cleanedArtNo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $artNo));
                            $cleanSize = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $sz));
                            $barcode = 'BC' . $cleanedArtNo . $cleanSize . $sleeveCode;
                        } else {
                            preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $artNo, $matches);
                            $numericBase = $matches[2] ?? '';
                            $suffix = $matches[3] ?? '1';
                            $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
                            $formattedSize = str_pad(trim((string)$sz), 2, '0', STR_PAD_LEFT);
                            $barcode = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;
                        }
                    }
                }

                $rows[] = [
                    'finished_item_code' => $itemCode,
                    'art_no' => $artNo ?: '-',
                    'size' => $sz,
                    'size_num' => is_numeric($sz) ? (int)$sz : 999,
                    'mrp' => $mrp > 0 ? (float)$mrp : 0,
                    'selling_price' => $sp > 0 ? (float)$sp : 0,
                    'effective_from' => $eff,
                    'status' => $status,
                    'barcode' => $barcode,
                ];
            }
        }

        // Sort rows: Art No ASC, Finished Item Code ASC, Size ASC
        usort($rows, function ($a, $b) {
            $cmpArt = strnatcasecmp($a['art_no'], $b['art_no']);
            if ($cmpArt !== 0) return $cmpArt;

            $cmpCode = strnatcasecmp($a['finished_item_code'], $b['finished_item_code']);
            if ($cmpCode !== 0) return $cmpCode;

            return $a['size_num'] <=> $b['size_num'];
        });

        // Assign S.No
        $sno = 1;
        foreach ($rows as &$r) {
            $r['sno'] = $sno++;
        }
        unset($r);

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Finished Item Code',
            'Art No',
            'SIZE',
            'MRP',
            'Selling Price',
            'Effective From',
            'Status',
            'Barcode',
        ];
    }

    public function map($row): array
    {
        return [
            $row['sno'],
            $row['finished_item_code'],
            $row['art_no'],
            $row['size'],
            $row['mrp'],
            $row['selling_price'],
            $row['effective_from'],
            $row['status'],
            $row['barcode'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
