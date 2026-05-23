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

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $rowErrors = [];

            if (
                !isset($row['finished_item_code']) &&
                !isset($row['selling_price']) &&
                !isset($row['effective_from'])
            ) {
                continue;
            }

            $itemCode = trim((string)($row['finished_item_code'] ?? ''));
            if ($itemCode === '') {
                $rowErrors[] = "Row {$rowNumber}: Finished Item Code is required.";
            }

            $artNo = isset($row['art_no']) ? trim((string)$row['art_no']) : null;
            $artNo = $artNo === '' ? null : $artNo;

            $sellingPrice = $row['selling_price'] ?? null;
            if ($sellingPrice === null || $sellingPrice === '' || !is_numeric($sellingPrice) || $sellingPrice < 0) {
                $rowErrors[] = "Row {$rowNumber}: Selling Price must be a number greater than or equal to 0.";
            }

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

            $unitPrice = $row['unit_price'] ?? null;
            if ($unitPrice !== null && $unitPrice !== '' && (!is_numeric($unitPrice) || $unitPrice < 0)) {
                $rowErrors[] = "Row {$rowNumber}: Unit Price must be a number greater than or equal to 0.";
            }

            if (!empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
                continue;
            }

            $sellingPriceFloat = (float)$sellingPrice;
            $unitPriceFloat = ($unitPrice === null || $unitPrice === '')
                ? ($sellingPriceFloat / 1.5)
                : (float)$unitPrice;

            $validRows[] = [
                'finished_item_code' => $itemCode,
                'art_no' => $artNo,
                'selling_price' => $sellingPriceFloat,
                'unit_price' => $unitPriceFloat,
                'effective_from' => $effectiveFrom->format('Y-m-d'),
                'status' => $status,
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['import' => $errors]);
        }

        DB::beginTransaction();
        try {
            foreach ($validRows as $data) {
                $query = ItemPrice::where('finished_item_code', $data['finished_item_code'])
                    ->whereDate('effective_from', $data['effective_from']);

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
            return Carbon::createFromFormat('d-m-Y', $value);
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}

