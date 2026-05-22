<?php

namespace App\Imports;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\StoreLocation;
use App\Models\Uom;
use App\Models\Style;
use App\Models\FabricSize;
use App\Models\Color;
use App\Models\Brand;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RawMaterialStockImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            if (!isset($row['stock_date']) && !isset($row['raw_material']) && !isset($row['qty_in']) && !isset($row['store_location'])) {
                continue;
            }

            $stockDateStr = $row['stock_date'] ?? null;
            $stockDate = null;
            if (empty($stockDateStr)) {
                $rowErrors[] = "Row {$rowNumber}: Stock Date is required.";
            } else {
                $stockDate = $this->parseExcelDate($stockDateStr);
                if (!$stockDate) {
                    $rowErrors[] = "Row {$rowNumber}: Stock Date '{$stockDateStr}' is invalid. Please use DD-MM-YYYY format.";
                }
            }

            $categoryName = $row['store_category'] ?? null;
            $storeCategory = null;
            if (empty($categoryName)) {
                $rowErrors[] = "Row {$rowNumber}: Store Category is required.";
            } else {
                $storeCategory = StoreCategory::where('category_name', $categoryName)->orWhere('code', $categoryName)->first();
                if (!$storeCategory) {
                    $rowErrors[] = "Row {$rowNumber}: Store Category '{$categoryName}' does not exist in master table.";
                }
            }

            $materialName = $row['raw_material'] ?? null;
            $rawMaterial = null;
            if (empty($materialName)) {
                $rowErrors[] = "Row {$rowNumber}: Raw Material is required.";
            } else {
                $rawMaterial = RawMaterial::where('name', $materialName)->orWhere('code', $materialName)->first();
                if (!$rawMaterial) {
                    $rowErrors[] = "Row {$rowNumber}: Raw Material '{$materialName}' does not exist in master table.";
                }
            }

            if ($storeCategory && $rawMaterial) {
                if ($rawMaterial->store_category_id != $storeCategory->id) {
                    $rowErrors[] = "Row {$rowNumber}: Raw Material '{$materialName}' does not belong to Store Category '{$categoryName}'.";
                }
            }

            $styleName = $row['style'] ?? null;
            $style = null;

            if (!empty($styleName)) {
                $style = Style::where('style_name', $styleName)->orWhere('code', $styleName)->first();
                if (!$style) {
                    $rowErrors[] = "Row {$rowNumber}: Style '{$styleName}' not found.";
                }
            }

            $fabricWidthName = $row['fabric_width'] ?? null;
            $fabricWidth = null;

            if (!empty($fabricWidthName)) {
                $fabricWidth = FabricSize::where('width', $fabricWidthName)->first();
                if (!$fabricWidth) {
                    $rowErrors[] = "Row {$rowNumber}: Fabric Width '{$fabricWidthName}' not found.";
                }
            }

            $colorName = $row['color'] ?? null;
            $color = null;
    
            if (!empty($colorName)) {
                $color = Color::where('color_name', $colorName)->first();
                if (!$color) {
                    $rowErrors[] = "Row {$rowNumber}: Color '{$colorName}' not found.";
                }
            }

            $brandName = $row['brand'] ?? null;
            $brand = null;

            if (!empty($brandName)) {
                $brand = Brand::where('brand_name', $brandName)->orWhere('code', $brandName)->first();
                if (!$brand) {
                    $rowErrors[] = "Row {$rowNumber}: Brand '{$brandName}' not found.";
                }
            }

            $uomName = $row['uom'] ?? null;
            $uom = null;
            if (!empty($uomName)) {
                $uom = Uom::where('uom_code', $uomName)->orWhere('uom_name', $uomName)->first();
                if (!$uom) {
                    $rowErrors[] = "Row {$rowNumber}: UOM '{$uomName}' does not exist in master table.";
                }
            } elseif ($rawMaterial) {
                $uom = Uom::find($rawMaterial->uom_id);
            }

            $locationName = $row['store_location'] ?? null;
            $storeLocation = null;
            if (empty($locationName)) {
                $rowErrors[] = "Row {$rowNumber}: Store Location is required.";
            } else {
                $storeLocation = StoreLocation::where('store_location', $locationName)->first();
                if (!$storeLocation) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$locationName}' does not exist in master table.";
                }
            }

            $qtyIn = $row['qty_in'] ?? null;
            if ($qtyIn === null || !is_numeric($qtyIn) || $qtyIn <= 0) {
                $rowErrors[] = "Row {$rowNumber}: Qty In must be a number greater than 0.";
            }

            $price = $row['price'] ?? 0;
            if (!is_numeric($price) || $price < 0) {
                $rowErrors[] = "Row {$rowNumber}: Price must be a positive number.";
            }

            if (empty($rowErrors)) {
                $validData[] = [
                    'stock_date' => $stockDate ? $stockDate->format('Y-m-d') : null,
                    'raw_material_id' => $rawMaterial ? $rawMaterial->id : null,
                    'store_category_id' => $storeCategory ? $storeCategory->id : null,
                    'store_location_id' => $storeLocation ? $storeLocation->id : null,
                    'uom_id' => $uom ? $uom->id : null,
                    'style_id' => $style ? $style->id : null,
                    'fabric_width_id' => $fabricWidth ? $fabricWidth->id : null,
                    'color_id' => $color ? $color->id : null,
                    'brand_id' => $brand ? $brand->id : null,
                    'qty_in' => floatval($qtyIn),
                    'price' => floatval($price),
                    'art_no' => $row['art_no'] ?? null,
                    'remarks' => $row['remarks'] ?? null,
                ];
            } else {
                $errors = array_merge($errors, $rowErrors);
            }
        }

        if (count($errors) > 0) {
            throw ValidationException::withMessages(['import' => $errors]);
        }

        $grouped = [];
        foreach ($validData as $data) {
            $key =
                $data['stock_date'] . '_' .
                ($data['remarks'] ?? '');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'stock_date' => $data['stock_date'],
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
                    'entry_type' => 'Raw Material',
                    'remarks' => $group['remarks'],
                    'status' => 'Posted', 
                    'created_by' => auth()->id() ?? 1,
                ]);

                foreach ($group['items'] as $itemData) {
                    StockEntryItem::create([
                        'stock_entry_id' => $stockEntry->id,
                        'stock_type' => 'raw_material',
                        'art_no' => $itemData['art_no'],
                        'raw_material_id' => $itemData['raw_material_id'],
                        'store_category_id' => $itemData['store_category_id'],
                        'store_location_id' => $itemData['store_location_id'],
                        'style_id' => $itemData['style_id'],
                        'fabric_width_id' => $itemData['fabric_width_id'],
                        'color_id' => $itemData['color_id'],
                        'brand_id' => $itemData['brand_id'],
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
