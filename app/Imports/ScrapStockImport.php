<?php

namespace App\Imports;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StoreCategory;
use App\Models\StoreType;
use App\Models\RawMaterial;
use App\Models\Uom;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScrapStockImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];

        // Fetch Scrap Store Category and Scrap Store Type
        $scrapCategory = StoreCategory::where('category_name', 'LIKE', '%SCRAP%')->orWhere('code', 'SCR')->first();
        $scrapCategoryId = $scrapCategory ? $scrapCategory->id : 6;

        $scrapStoreType = StoreType::where('store_type_name', 'LIKE', '%SCRAP%')->first();
        $scrapStoreTypeId = $scrapStoreType ? $scrapStoreType->id : 15;

        // Fetch KGS UOM
        $kgsUom = Uom::where('uom_code', 'KGS')->orWhere('uom_name', 'LIKE', '%KILOGRAM%')->first();
        $kgsUomId = $kgsUom ? $kgsUom->id : 11;

        // Scrap store entries do not require a warehouse (stored as null)
        $defaultWarehouseId = null;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowArray = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);

            // Skip completely empty rows or Grand Total lines
            $rowText = implode(' ', array_map('strval', $rowArray));
            if (stripos($rowText, 'Grand Total') !== false) {
                continue;
            }

            $nonEmpty = array_filter($rowArray, function ($value) {
                return $value !== null && trim((string)$value) !== '';
            });
            if (empty($nonEmpty)) {
                continue;
            }

            // 1. Stock Date Validation
            $stockDateStr = $row['stock_date'] ?? $row['date'] ?? null;
            $stockDate = null;
            if (empty($stockDateStr)) {
                $errors[] = "Row {$rowNumber}: Stock Date is required.";
            } else {
                $stockDate = $this->parseExcelDate($stockDateStr);
                if (!$stockDate) {
                    $errors[] = "Row {$rowNumber}: Stock Date '{$stockDateStr}' is invalid. Please use DD-MM-YYYY format.";
                }
            }

            // 2. Store Category Validation (Strictly SCRAP STORE only)
            $categoryName = trim((string)($row['store_category'] ?? $row['category'] ?? ''));
            if (empty($categoryName)) {
                $errors[] = "Row {$rowNumber}: Store Category is required.";
            } else {
                $isScrapCat = (strcasecmp($categoryName, 'SCRAP STORE') === 0 || strcasecmp($categoryName, 'SCRAP') === 0 || strcasecmp($categoryName, 'SCR') === 0);
                if (!$isScrapCat) {
                    $errors[] = "Row {$rowNumber}: Store Category '{$categoryName}' is not allowed. Only 'SCRAP STORE' is allowed.";
                }
            }

            // 3. Scrap Material Name Validation
            $materialName = trim((string)($row['scrap_material_name'] ?? $row['scrap_material'] ?? $row['material_name'] ?? $row['raw_material'] ?? ''));
            $rawMaterial = null;
            if (empty($materialName)) {
                $errors[] = "Row {$rowNumber}: Scrap Material Name is required.";
            } else {
                $rawMaterial = RawMaterial::where('name', $materialName)->orWhere('code', $materialName)->first();
                if (!$rawMaterial) {
                    $errors[] = "Row {$rowNumber}: Scrap Material '{$materialName}' does not exist in master table.";
                } elseif ($rawMaterial->store_category_id != $scrapCategoryId) {
                    $errors[] = "Row {$rowNumber}: Material '{$materialName}' does not belong to 'SCRAP STORE' category.";
                }
            }

            // 4. UOM Validation (Strictly KGS only)
            $uomStr = trim((string)($row['uom'] ?? $row['uom_code'] ?? ''));
            if (empty($uomStr)) {
                $errors[] = "Row {$rowNumber}: UOM is required.";
            } else {
                $isKgs = (strcasecmp($uomStr, 'KGS') === 0 || strcasecmp($uomStr, 'KG') === 0 || strcasecmp($uomStr, 'KILOGRAMS') === 0 || strcasecmp($uomStr, 'KILOGRAM') === 0);
                if (!$isKgs) {
                    $errors[] = "Row {$rowNumber}: UOM '{$uomStr}' is invalid. Only 'KGS' is allowed for Scrap Store.";
                }
            }

            // 5. Qty In Validation
            $qtyIn = $row['qty_in'] ?? $row['quantity'] ?? $row['qty'] ?? null;
            if ($qtyIn === null || trim((string)$qtyIn) === '') {
                $errors[] = "Row {$rowNumber}: Qty In is required.";
            } elseif (!is_numeric($qtyIn) || (float)$qtyIn <= 0) {
                $errors[] = "Row {$rowNumber}: Qty In must be a positive number.";
            }

            // 6. Price (Optional, numeric >= 0)
            $price = $row['price'] ?? $row['rate'] ?? 0;
            if ($price !== null && trim((string)$price) !== '') {
                if (!is_numeric($price) || (float)$price < 0) {
                    $errors[] = "Row {$rowNumber}: Price must be 0 or a positive number.";
                }
            } else {
                $price = 0;
            }

            // 7. Remarks (Optional)
            $remarks = trim((string)($row['remarks'] ?? ''));

            if (empty($errors)) {
                $validData[] = [
                    'stock_date' => $stockDate ? $stockDate->format('Y-m-d') : null,
                    'store_category_id' => $scrapCategoryId,
                    'store_type_id' => $scrapStoreTypeId,
                    'raw_material_id' => $rawMaterial?->id,
                    'uom_id' => $kgsUomId,
                    'qty_in' => (float)$qtyIn,
                    'price' => (float)$price,
                    'remarks' => $remarks ?: 'Scrap Store Stock Import',
                    'warehouse_id' => $defaultWarehouseId,
                    'store_location_id' => null,
                ];
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['error' => $errors]);
        }

        if (empty($validData)) {
            throw ValidationException::withMessages(['error' => ['The uploaded file contains no valid scrap records.']]);
        }

        $grouped = [];
        foreach ($validData as $data) {
            $key = $data['stock_date'] . '_' . ($data['warehouse_id'] ?? 'default');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'stock_date' => $data['stock_date'],
                    'warehouse_id' => $data['warehouse_id'],
                    'store_type_id' => $data['store_type_id'],
                    'remarks' => $data['remarks'] ?: 'Scrap Store Stock Import',
                    'items' => []
                ];
            }
            $grouped[$key]['items'][] = $data;
        }

        DB::beginTransaction();
        try {
            foreach ($grouped as $group) {
                $lastEntry = StockEntry::where('stock_entry_no', 'REGEXP', '^SE[0-9]+$')->orderByRaw('CAST(SUBSTRING(stock_entry_no, 3) AS UNSIGNED) DESC')->first();
                $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
                $stockEntryNo = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

                $stockEntry = StockEntry::create([
                    'stock_entry_no' => $stockEntryNo,
                    'stock_date' => $group['stock_date'],
                    'warehouse_id' => $group['warehouse_id'],
                    'store_type_id' => $group['store_type_id'],
                    'entry_type' => 'Raw Material',
                    'remarks' => $group['remarks'],
                    'status' => 'Posted',
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($group['items'] as $itemData) {
                    StockEntryItem::create([
                        'stock_entry_id' => $stockEntry->id,
                        'stock_type' => 'raw_material',
                        'raw_material_id' => $itemData['raw_material_id'],
                        'store_category_id' => $itemData['store_category_id'],
                        'store_type_id' => $itemData['store_type_id'],
                        'store_location_id' => $itemData['store_location_id'] ?? null,
                        'warehouse_id' => $itemData['warehouse_id'],
                        'uom_id' => $itemData['uom_id'],
                        'qty_in' => $itemData['qty_in'],
                        'qty_out' => 0,
                        'price' => $itemData['price'],
                    ]);
                }

                if (function_exists('addLog')) {
                    addLog('create', 'Scrap Stock Entry via Import', 'stock_entries', $stockEntry->id, null, $stockEntry->toArray());
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseExcelDate($dateValue)
    {
        if (empty($dateValue)) return null;
        if (is_numeric($dateValue)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue));
        }

        $formats = ['d-m-Y', 'd/m/Y', 'd-m-y', 'd/m/y', 'Y-m-d', 'Y/m/d'];
        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, trim((string)$dateValue));
                if ($parsed->year < 100) {
                    $parsed->addYears(2000);
                }
                return $parsed;
            } catch (\Exception $e) {
            }
        }

        try {
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            return null;
        }
    }
}
