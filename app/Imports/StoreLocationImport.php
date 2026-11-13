<?php

namespace App\Imports;

use App\Models\StoreLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreLocationImport implements ToCollection, WithHeadingRow
{
    protected int $importedCount = 0;

    public function collection(Collection $rows)
    {
        $allErrors = [];
        $validRows = [];
        $seenInFile = [];

        $existingDbLocations = StoreLocation::whereNull('deleted_at')
            ->pluck('store_location')
            ->map(function ($val) {
                return mb_strtoupper(trim(preg_replace('/\s+/', ' ', (string) $val)));
            })
            ->filter()
            ->flip()
            ->toArray();

        $hasDataRows = false;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $isEmpty = collect($row)->filter(function ($val) {
                return $val !== null && trim((string) $val) !== '';
            })->isEmpty();

            if ($isEmpty) {
                continue;
            }

            $hasDataRows = true;
            $rowErrors = [];

            $rawLoc = $row['store_location']
                ?? $row['store_location_']
                ?? $row['location']
                ?? $row['store_location_name']
                ?? null;

            $location = trim(preg_replace('/[\x{00A0}\x{200B}\x{FEFF}]/u', ' ', (string) $rawLoc));
            $location = trim(preg_replace('/\s+/', ' ', $location));

            if ($location === '') {
                $rowErrors[] = "Row {$rowNumber}: Store Location is required.";
            } else {
                $len = mb_strlen($location);
                if ($len < 2) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$location}' must be at least 2 characters.";
                } elseif ($len > 50) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$location}' should not be more than 50 characters.";
                }

                if (preg_match('/^0+$/', $location)) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$location}' is an invalid format (cannot be all zeros).";
                }

                $lookupKey = mb_strtoupper($location);

                if (isset($seenInFile[$lookupKey])) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$location}' is duplicated in this file (already entered in Row {$seenInFile[$lookupKey]}).";
                } else {
                    $seenInFile[$lookupKey] = $rowNumber;
                }

                if (isset($existingDbLocations[$lookupKey])) {
                    $rowErrors[] = "Row {$rowNumber}: Store Location '{$location}' already exists in the system.";
                }
            }

            $rawStatus = $row['status'] ?? 'Active';
            $status = trim((string) $rawStatus);
            if ($status === '') {
                $status = 'Active';
            }

            if (strcasecmp($status, 'active') === 0) {
                $status = 'Active';
            } elseif (strcasecmp($status, 'inactive') === 0) {
                $status = 'Inactive';
            } else {
                $rowErrors[] = "Row {$rowNumber}: Status '{$status}' is invalid. Status must be 'Active' or 'Inactive'.";
            }

            if (!empty($rowErrors)) {
                foreach ($rowErrors as $err) {
                    $allErrors[] = $err;
                }
            } else {
                $validRows[] = [
                    'store_location' => $location,
                    'status' => $status,
                ];
            }
        }

        if (!$hasDataRows) {
            throw ValidationException::withMessages([
                'import_file' => ['The uploaded Excel file does not contain any data rows. Please use the sample template.']
            ]);
        }

        if (!empty($allErrors)) {
            throw ValidationException::withMessages([
                'import_file' => $allErrors
            ]);
        }

        DB::transaction(function () use ($validRows) {
            $userId = auth()->id() ?? 1;
            foreach ($validRows as $item) {
                $newLoc = StoreLocation::create([
                    'store_location' => $item['store_location'],
                    'status' => $item['status'],
                    'created_by' => $userId,
                ]);

                if (function_exists('addLog')) {
                    addLog('create', 'Store Location', 'store_locations', $newLoc->id, null, $newLoc->toArray());
                }

                $this->importedCount++;
            }
        });
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
