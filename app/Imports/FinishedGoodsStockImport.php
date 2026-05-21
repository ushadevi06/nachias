<?php

namespace App\Imports;

use App\Models\Color;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StoreLocation;
use App\Models\Uom;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FinishedGoodsStockImport implements ToCollection, WithHeadingRow
{
    protected array $entryMap = [];

    public function collection(Collection $rows)
    {
        $errors = [];

        DB::transaction(function () use ($rows, &$errors) {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if ($this->isBlankRow($row)) {
                    continue;
                }

                try {
                    $this->importRow($row);
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                throw new \Exception(implode('<br>', $errors));
            }
        });
    }

    protected function importRow($row): void
    {
        $stockDate = $this->parseDate($this->getRowValue($row, ['stock_date', 'stockdate']), 'Stock Date is required.');
        $productCode = $this->nullableTrim($this->getRowValue($row, ['product_code', 'productcode', 'product_cod', 'product_code_finished_item_code', 'product_codefinished_item_code']));
        $artNo = $this->nullableTrim($this->getRowValue($row, ['art_no', 'artno']));
        $finishedItemCode = $productCode;
        $uom = $this->resolveUom($this->getRowValue($row, ['uom_id', 'uomid', 'uom']));
        $storeLocation = $this->resolveStoreLocation($this->getRowValue($row, ['store_location_id', 'storelocationid', 'store_location', 'store_loca', 'storeloca', 'store']));
        $qtyIn = $this->parseNumber($this->getRowValue($row, ['qty_in', 'qtyin']), 'Qty In is required.');
        $qtyOut = $this->parseOptionalNumber($this->getRowValue($row, ['qty_out', 'qtyout'])) ?? 0;
        $price = $this->parseNumber($this->getRowValue($row, ['price']), 'Price is required.');
        $remarks = trim((string) ($this->getRowValue($row, ['remarks']) ?? ''));
        $color = $this->resolveColor($this->getRowValue($row, ['color_id', 'colorid', 'color']));
        $sku = $this->nullableTrim($this->getRowValue($row, ['sku', 'sku_barcode', 'skubarcode', 'sku_barcode_', 'sku_barco']));

        if (!$finishedItemCode) {
            throw new \Exception('Product Code is required.');
        }

        $entryKey = implode('|', [
            $stockDate->format('Y-m-d'),
            $storeLocation->id,
            $remarks,
        ]);

        if (!isset($this->entryMap[$entryKey])) {
            $stockEntry = StockEntry::create([
                'stock_entry_no' => $this->generateStockEntryNo(),
                'stock_date' => $stockDate->format('Y-m-d'),
                'entry_type' => 'Finished Goods',
                'to_store_location_id' => $storeLocation->id,
                'remarks' => $remarks ?: null,
                'status' => 'Posted',
                'created_by' => auth()->id() ?? 1,
                'price' => 0,
            ]);

            $this->entryMap[$entryKey] = $stockEntry;
        }

        $stockEntry = $this->entryMap[$entryKey];

        StockEntryItem::create([
            'stock_entry_id' => $stockEntry->id,
            'stock_type' => 'finished_goods',
            'item_id' => null,
            'art_no' => $artNo,
            'finished_item_code' => $finishedItemCode,
            'size' => $this->nullableTrim($this->getRowValue($row, ['size'])),
            'color_id' => $color?->id,
            'sleeve_type' => $this->nullableTrim($this->getRowValue($row, ['sleeve_type', 'sleevetype', 'sleeve_typ'])),
            'store_location_id' => $storeLocation->id,
            'uom_id' => $uom->id,
            'qty_in' => $qtyIn,
            'qty_out' => $qtyOut,
            'price' => $price,
            'sku' => $sku,
        ]);

        $stockEntry->price = (float) $stockEntry->price + $price;
        if (empty($stockEntry->remarks) && $remarks !== '') {
            $stockEntry->remarks = $remarks;
        }
        if (empty($stockEntry->to_store_location_id)) {
            $stockEntry->to_store_location_id = $storeLocation->id;
        }
        $stockEntry->save();
    }

    protected function isBlankRow($row): bool
    {
        return empty(array_filter($row->toArray(), fn ($value) => $value !== null && trim((string) $value) !== ''));
    }

    protected function parseDate($value, string $message): Carbon
    {
        if ($value === null || $value === '') {
            throw new \Exception($message);
        }

        if (is_numeric($value)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }

        foreach (['d-m-Y', 'Y-m-d', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim((string) $value));
            } catch (\Exception $e) {
            }
        }

        return Carbon::parse($value);
    }

    protected function parseNumber($value, string $message): float
    {
        if ($value === null || $value === '') {
            throw new \Exception($message);
        }

        return (float) $value;
    }

    protected function getRowValue($row, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($row[$key])) {
                return $row[$key];
            }
        }

        return null;
    }

    protected function parseOptionalNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function resolveUom($value): Uom
    {
        $lookup = $this->nullableTrim($value);
        if (!$lookup) {
            throw new \Exception('UOM ID is required.');
        }

        $record = Uom::query()
            ->when(is_numeric($lookup), function ($query) use ($lookup) {
                $query->where('id', (int) $lookup);
            }, function ($query) use ($lookup) {
                $query->where('uom_code', $lookup)
                    ->orWhere('uom_name', $lookup);
            })
            ->first();

        if (!$record) {
            throw new \Exception("UOM '{$lookup}' was not found.");
        }

        return $record;
    }

    protected function resolveStoreLocation($value): StoreLocation
    {
        $lookup = $this->nullableTrim($value);
        if (!$lookup) {
            throw new \Exception('Store Location is required.');
        }
     
        $record = StoreLocation::query()
            ->where('store_location', $lookup)
            ->when(is_numeric($lookup), function ($query) use ($lookup) {
                $query->orWhere('id', (int) $lookup);
            })
            ->first();

        if (!$record) {
            throw new \Exception("Store Location '{$lookup}' was not found.");
        }

        return $record;
    }

    protected function resolveColor($value): ?Color
    {
        $lookup = $this->nullableTrim($value);
        if (!$lookup) {
            return null;
        }

        $record = Color::query()
            ->where('color_name', $lookup)
            ->when(is_numeric($lookup), function ($query) use ($lookup) {
                $query->orWhere('id', (int) $lookup);
            })
            ->first();

        if (!$record) {
            throw new \Exception("Color '{$lookup}' was not found.");
        }

        return $record;
    }

    protected function nullableTrim($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function generateStockEntryNo(): string
    {
        $maxSequence = StockEntry::withTrashed()
            ->where('stock_entry_no', 'like', 'SE%')
            ->selectRaw('MAX(CAST(SUBSTRING(stock_entry_no, 3) AS UNSIGNED)) as max_sequence')
            ->value('max_sequence');

        $nextSequence = ((int) $maxSequence) + 1;
        $stockEntryNo = 'SE' . str_pad((string) $nextSequence, 5, '0', STR_PAD_LEFT);

        while (StockEntry::withTrashed()->where('stock_entry_no', $stockEntryNo)->exists()) {
            $nextSequence++;
            $stockEntryNo = 'SE' . str_pad((string) $nextSequence, 5, '0', STR_PAD_LEFT);
        }

        return $stockEntryNo;
    }
}
