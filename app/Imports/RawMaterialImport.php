<?php

namespace App\Imports;

use App\Models\RawMaterial;
use App\Models\StoreCategory;
use App\Models\Uom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class RawMaterialImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];
        $seenCodes = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            if (!isset($row['name']) && !isset($row['code']) && !isset($row['store_category']) && !isset($row['uom'])) {
                continue;
            }

            $categoryName = $row['store_category'] ?? null;
            $storeCategoryId = null;
            if (empty($categoryName)) {
                $errors[] = "Row {$rowNumber}: Store Category is required.";
                $storeCategoryId = -1;
            } else {
                $category = StoreCategory::where('category_name', $categoryName)->orWhere('code', $categoryName)->first();
                if ($category) {
                    $storeCategoryId = $category->id;
                } else {
                    $errors[] = "Row {$rowNumber}: Store Category '{$categoryName}' does not exist in master table.";
                    $storeCategoryId = -1;
                }
            }

            $uomName = $row['uom'] ?? null;
            $uomId = null;
            if (empty($uomName)) {
                $errors[] = "Row {$rowNumber}: UOM is required.";
                $uomId = -1;
            } else {
                $uom = Uom::where('uom_code', $uomName)->orWhere('uom_name', $uomName)->first();
                if ($uom) {
                    $uomId = $uom->id;
                } else {
                    $errors[] = "Row {$rowNumber}: UOM '{$uomName}' does not exist in master table.";
                    $uomId = -1;
                }
            }

            $status = $row['status'] ?? 'Active';

            $data = [
                'store_category_id' => $storeCategoryId,
                'code' => isset($row['code']) ? (string) $row['code'] : null,
                'name' => $row['name'] ?? null,
                'uom_id' => $uomId,
                'material_type' => $row['material_type'] ?? null,
                'specification' => $row['specification'] ?? null,
                'min_stock' => $row['min_stock'] ?? 0,
                'status' => $status,
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                'code.regex' => 'Code can only contain letters, numbers, dashes, and underscores.',
                'code.not_in' => 'This field is an invalid format.',
                'min'      => 'This field must be at least :min characters.',
                'max'      => 'This field should not be more than :max characters.',
            ];

            $existingId = null;
            if (!empty($data['code'])) {
                $existing = RawMaterial::where('code', $data['code'])->first();
                $existingId = $existing ? $existing->id : null;
            }

            if ($existingId) {
                $errors[] = "Row {$rowNumber}: Raw Material Code '{$data['code']}' already exists.";
                continue;
            }

            $validator = Validator::make($data, [
                'store_category_id' => 'required|exists:store_categories,id',
                'code' => 'required|string|min:3|max:50|not_in:0|regex:/^[a-zA-Z0-9\-_]+$/',
                'name' => 'required|string|min:3|max:150',
                'uom_id' => 'required|exists:uoms,id',
                'status' => 'required|in:Active,Inactive',
                'min_stock' => 'nullable|numeric|min:0',
            ], $messages);

            if (!empty($data['code'])) {
                if (in_array($data['code'], $seenCodes)) {
                    $errors[] = "Row " . $rowNumber . ": Duplicate Code (" . $data['code'] . ") found in the Excel file.";
                    continue;
                }
                $seenCodes[] = $data['code'];
            }

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                $errors[] = "Row " . $rowNumber . ": " . implode(', ', $errorMessages);
            } else {
                $validData[] = $data;
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }

        foreach ($validData as $d) {
            $d['created_by'] = auth()->id() ?? 1;
            RawMaterial::create($d);
        }
    }
}
