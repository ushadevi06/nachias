<?php

namespace App\Imports;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\StoreLocation;
use App\Models\Uom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RawMaterialStockImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if (!isset($row['stock_date']) && !isset($row['raw_material']) && !isset($row['qty_in']) && !isset($row['store_location'])) {
                continue;
            }

            $stockDateStr = $row['stock_date'] ?? null;
            $stockDate = null;
            if (empty($stockDateStr)) {
                $errors[] = "Row {$rowNumber}: Stock Date is required.";
            } else {
                $stockDate = $this->parseExcelDate($stockDateStr);
                if (!$stockDate) {
                    $errors[] = "Row {$rowNumber}: Stock Date '{$stockDateStr}' is invalid. Please use DD-MM-YYYY format.";
                }
            }

            $categoryName = $row['store_category'] ?? null;
            $storeCategory = null;
            if (empty($categoryName)) {
                $errors[] = "Row {$rowNumber}: Store Category is required.";
            } else {
                $storeCategory = StoreCategory::where('category_name', $categoryName)
                    ->orWhere('code', $categoryName)
                    ->first();
                if (!$storeCategory) {
                    $errors[] = "Row {$rowNumber}: Store Category '{$categoryName}' does not exist in master table.";
                }
            }

            $materialName = $row['raw_material'] ?? null;
            $rawMaterial = null;
            if (empty($materialName)) {
                $errors[] = "Row {$rowNumber}: Raw Material is required.";
            } else {
                $rawMaterial = RawMaterial::where('name', $materialName)
                    ->orWhere('code', $materialName)
                    ->first();
                if (!$rawMaterial) {
                    $errors[] = "Row {$rowNumber}: Raw Material '{$materialName}' does not exist in master table.";
                }
            }

            if ($storeCategory && $rawMaterial) {
                if ($rawMaterial->store_category_id != $storeCategory->id) {
                    $errors[] = "Row {$rowNumber}: Raw Material '{$materialName}' does not belong to Store Category '{$categoryName}'.";
                }
            }

            $uomName = $row['uom'] ?? null;
            $uom = null;
            if (!empty($uomName)) {
                $uom = Uom::where('uom_code', $uomName)
                    ->orWhere('uom_name', $uomName)
                    ->first();
                if (!$uom) {
                    $errors[] = "Row {$rowNumber}: UOM '{$uomName}' does not exist in master table.";
                }
            } elseif ($rawMaterial) {
                $uom = Uom::find($rawMaterial->uom_id);
            }

            $locationName = $row['store_location'] ?? null;
            $storeLocation = null;
            if (empty($locationName)) {
                $errors[] = "Row {$rowNumber}: Store Location is required.";
            } else {
                $storeLocation = StoreLocation::where('store_location', $locationName)->first();
                if (!$storeLocation) {
                    $errors[] = "Row {$rowNumber}: Store Location '{$locationName}' does not exist in master table.";
                }
            }

            $qtyIn = $row['qty_in'] ?? null;
            if ($qtyIn === null || !is_numeric($qtyIn) || $qtyIn <= 0) {
                $errors[] = "Row {$rowNumber}: Qty In must be a number greater than 0.";
            }

            $price = $row['price'] ?? 0;
            if (!is_numeric($price) || $price < 0) {
                $errors[] = "Row {$rowNumber}: Price must be a positive number.";
            }

            $grnNo = $row['grn_no'] ?? null;
            $grnEntry = null;
            $grnEntryItem = null;
            if (!empty($grnNo)) {
                $grnEntry = GrnEntry::where('grn_number', $grnNo)->first();
                if (!$grnEntry) {
                    $errors[] = "Row {$rowNumber}: GRN Number '{$grnNo}' does not exist.";
                } elseif ($rawMaterial) {
                    $grnEntryItem = GrnEntryItem::where('grn_entry_id', $grnEntry->id)
                        ->whereHas('purchaseInvoiceItem', function ($query) use ($rawMaterial) {
                            $query->where('raw_material_id', $rawMaterial->id);
                        })
                        ->when(isset($row['art_no']) && !empty($row['art_no']), function ($query) use ($row) {
                            $query->where('art_no', $row['art_no']);
                        })
                        ->first();

                    if (!$grnEntryItem) {
                        $errors[] = "Row {$rowNumber}: Raw Material '{$materialName}'" . (empty($row['art_no']) ? "" : " with Art No '{$row['art_no']}'") . " was not found under GRN '{$grnNo}'.";
                    }
                }
            }

            if (empty($errors)) {
                $validData[] = [
                    'stock_date' => $stockDate ? $stockDate->format('Y-m-d') : null,
                    'grn_entry_id' => $grnEntry ? $grnEntry->id : null,
                    'grn_entry_item_id' => $grnEntryItem ? $grnEntryItem->id : null,
                    'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                    'store_category_id' => $storeCategory ? $storeCategory->id : null,
                    'store_location_id' => $storeLocation ? $storeLocation->id : null,
                    'uom_id' => $uom ? $uom->id : null,
                    'qty_in' => floatval($qtyIn),
                    'price' => floatval($price),
                    'art_no' => $row['art_no'] ?? ($grnEntryItem ? $grnEntryItem->art_no : null),
                    'remarks' => $row['remarks'] ?? null,
                ];
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }

        $grouped = [];
        foreach ($validData as $data) {
            $key = $data['stock_date'] . '_' . ($data['grn_entry_id'] ?? 'null') . '_' . ($data['remarks'] ?? '');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'stock_date' => $data['stock_date'],
                    'grn_entry_id' => $data['grn_entry_id'],
                    'remarks' => $data['remarks'],
                    'items' => []
                ];
            }
            $grouped[$key]['items'][] = $data;
        }

        DB::beginTransaction();
        try {
            foreach ($grouped as $group) {
                $lastEntry = StockEntry::latest('id')->first();
                $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
                $stockEntryNo = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

                $stockEntry = StockEntry::create([
                    'stock_entry_no' => $stockEntryNo,
                    'stock_date' => $group['stock_date'],
                    'grn_entry_id' => $group['grn_entry_id'],
                    'entry_type' => 'Raw Material',
                    'remarks' => $group['remarks'],
                    'status' => 'Posted', 
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($group['items'] as $itemData) {
                    StockEntryItem::create([
                        'stock_entry_id' => $stockEntry->id,
                        'stock_type' => 'raw_material',
                        'grn_entry_item_id' => $itemData['grn_entry_item_id'],
                        'art_no' => $itemData['art_no'],
                        'raw_material_id' => $itemData['raw_material_id'],
                        'store_category_id' => $itemData['store_category_id'],
                        'store_location_id' => $itemData['store_location_id'],
                        'uom_id' => $itemData['uom_id'],
                        'qty_in' => $itemData['qty_in'],
                        'qty_out' => 0,
                        'price' => $itemData['price'],
                    ]);
                }
                addLog('create', 'Stock Entry via Import', 'stock_entries', $stockEntry->id, null, $stockEntry->toArray());
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
        try {
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d-m-Y', $dateValue);
            } catch (\Exception $ex) {
                try {
                    return Carbon::createFromFormat('Y-m-d', $dateValue);
                } catch (\Exception $ex2) {
                    return null;
                }
            }
        }
    }
}
