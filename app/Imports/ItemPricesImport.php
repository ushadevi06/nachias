<?php

namespace App\Imports;

use App\Models\ItemPrice;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemPricesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validRows = [];
        $supportedSizes = ['36', '38', '40', '42', '44', '46', '48', '50'];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            $isEmpty = collect($row)->filter(function ($value) {
                return !is_null($value) && trim((string)$value) !== '';
            })->isEmpty();

            if ($isEmpty) {
                continue;
            }

            $itemCode = trim((string)($row['finished_item_code'] ?? ''));
            if ($itemCode === '') {
                $rowErrors[] = "Row {$rowNumber}: Finished Item Code is required.";
            }

            $artNo = isset($row['art_no']) ? trim((string)$row['art_no']) : null;
            $artNo = $artNo === '' ? null : $artNo;

            $effectiveFromRaw = $row['effective_from'] ?? null;
            $effectiveFrom = $this->parseDate($effectiveFromRaw);
            if (!$effectiveFrom) {
                $rowErrors[] = "Row {$rowNumber}: Effective From '{$effectiveFromRaw}' is invalid. Use DD-MM-YYYY format.";
            }

            $status = trim((string)($row['status'] ?? 'Active'));
            if ($status === '') {
                $status = 'Active';
            }
            if (!in_array($status, ['Active', 'Inactive'], true)) {
                $rowErrors[] = "Row {$rowNumber}: Status must be Active or Inactive.";
            }

            $hasAnySize = false;
            foreach ($supportedSizes as $size) {
                $mrpKey = 'mrp_' . $size;
                $spKey = 'selling_price_' . $size;

                $mrp = $row[$mrpKey] ?? null;
                $sp = $row[$spKey] ?? null;

                if ($mrp !== null && $mrp !== '' || $sp !== null && $sp !== '') {
                    $hasAnySize = true;

                    if ($mrp !== null && $mrp !== '' && (!is_numeric($mrp) || $mrp < 0)) {
                        $rowErrors[] = "Row {$rowNumber}: MRP {$size} must be a number greater than or equal to 0.";
                    }

                    if ($sp !== null && $sp !== '' && (!is_numeric($sp) || $sp < 0)) {
                        $rowErrors[] = "Row {$rowNumber}: Selling Price {$size} must be a number greater than or equal to 0.";
                    }

                    if (empty($rowErrors)) {
                        if ($mrp === null || $mrp === '') {
                            $spFloat = round((float)$sp, 2);
                            $mrpFloat = round($spFloat * 1.5, 2);
                        } else if ($sp === null || $sp === '') {
                            $mrpFloat = round((float)$mrp, 2);
                            $spFloat = round($mrpFloat / 1.5, 2);
                        } else {
                            $mrpFloat = round((float)$mrp, 2);
                            $spFloat = round((float)$sp, 2);
                        }

                        $validRows[] = [
                            'finished_item_code' => $itemCode,
                            'art_no' => $artNo,
                            'size' => $size,
                            'selling_price' => $mrpFloat,
                            'unit_price' => $spFloat,
                            'effective_from' => $effectiveFrom->format('Y-m-d'),
                            'status' => $status,
                        ];
                    }
                }
            }

            if (!$hasAnySize) {
                $rowErrors[] = "Row {$rowNumber}: At least one size-wise price must be provided.";
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['import' => $errors]);
        }

        DB::beginTransaction();
        try {
            foreach ($validRows as $data) {
                $query = ItemPrice::where('finished_item_code', $data['finished_item_code'])
                    ->where('size', $data['size']);

                if ($data['art_no'] === null) {
                    $query->whereNull('art_no');
                } else {
                    $query->where('art_no', $data['art_no']);
                }

                $existing = $query->first();

                if ($existing) {
                    $oldData = $existing->toArray();
                    $existing->update([
                        'selling_price' => $data['selling_price'],
                        'unit_price' => $data['unit_price'],
                        'effective_from' => $data['effective_from'],
                        'status' => $data['status'],
                        'updated_by' => auth()->id() ?? 1,
                    ]);
                    $newData = $existing->fresh()->toArray();
                    addLog('update', 'Item Price via Import', 'item_prices', $existing->id, $oldData, $newData);
                } else {
                    $data['created_by'] = auth()->id() ?? 1;
                    $created = ItemPrice::create($data);
                    addLog('create', 'Item Price via Import', 'item_prices', $created->id, null, $created->toArray());
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            } catch (\Exception $e) {
                return null;
            }
        }

        $value = trim((string)$value);
        try {
            $date = Carbon::createFromFormat('d-m-Y', $value);
            if ($date->year < 100) { 
                $date->addYears(2000);
            }
            return $date;
        } catch (\Exception $e) {
            try {
                $date = Carbon::parse($value);
                if ($date->year < 100) {
                    $date->addYears(2000);
                }
                return $date;
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}

