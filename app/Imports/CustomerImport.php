<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Zone;
use App\Models\State;
use App\Models\City;
use App\Models\Place;
use App\Models\Tax;
use App\Models\StoreType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];
        $seenCodes = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if (!isset($row['name']) && !isset($row['code']) && !isset($row['mobile_number'])) {
                continue;
            }
            $stateId = null;
            if (!empty($row['state'])) {
                $state = State::where('state_name', $row['state'])->first();
                if ($state) {
                    $stateId = $state->id;
                } else {
                    $errors[] = "Row {$rowNumber}: State '{$row['state']}' does not exist in master table.";
                    $stateId = -1;
                }
            }

            $cityId = null;
            if (!empty($row['city'])) {
                if ($stateId && $stateId > 0) {
                    $city = City::where('city_name', $row['city'])->where('state_id', $stateId)->first();
                    if ($city) {
                        $cityId = $city->id;
                    } else {
                        $errors[] = "Row {$rowNumber}: City '{$row['city']}' does not exist in master table for the given State.";
                        $cityId = -1;
                    }
                } else {
                    $cityId = -1;
                }
            }

            $placeId = null;
            if (!empty($row['place'])) {
                if ($cityId && $cityId > 0 && $stateId && $stateId > 0) {
                    $place = Place::where('place_name', $row['place'])->where('city_id', $cityId)->where('state_id', $stateId)->first();
                    if ($place) {
                        $placeId = $place->id;
                    } else {
                        $errors[] = "Row {$rowNumber}: Place '{$row['place']}' does not exist in master table for the given City and State.";
                        $placeId = -1;
                    }
                } else {
                    $placeId = -1;
                }
            }

            $zoneId = null;
            if (!empty($row['zone'])) {
                $zone = Zone::where('zone_name', $row['zone'])->first();
                if ($zone) {
                    $zoneId = $zone->id;
                } else {
                    $errors[] = "Row {$rowNumber}: Zone '{$row['zone']}' does not exist in master table.";
                    $zoneId = -1;
                }
            }

            $storeId = null;
            if (!empty($row['store'])) {
                $store = StoreType::where('store_type_name', $row['store'])->first();
                if ($store) {
                    $storeId = $store->id;
                } else {
                    $errors[] = "Row {$rowNumber}: Store '{$row['store']}' does not exist in master table.";
                    $storeId = -1;
                }
            }

            $taxId = null;
            if (!empty($row['tax_type'])) {
                $tax = Tax::where('item_name', $row['tax_type'])->first();
                $taxId = $tax ? $tax->id : null;
            }

            $category = $row['category_retailerwholesaler'] ?? ($row['category'] ?? 'Retailer');
            $status = $row['status_activeinactive'] ?? ($row['status'] ?? 'Active');

            $data = [
                'category' => $category,
                'name' => $row['name'] ?? null,
                'code' => isset($row['code']) ? (string) $row['code'] : null,
                'mobile_no' => isset($row['mobile_number']) ? preg_replace('/[^0-9]/', '', (string) $row['mobile_number']) : null,
                'email' => $row['email'] ?? null,
                'website_url' => $row['website_url'] ?? null,
                'transport_name' => $row['transport_name'] ?? null,
                'booking_office' => $row['booking_office'] ?? null,
                'zone_id' => $zoneId,
                'store_id' => $storeId,
                'status' => $status,
                'state_id' => $stateId,
                'city_id' => $cityId,
                'place_id' => $placeId,
                'address_line_1' => $row['address_line_1'] ?? null,
                'address_line_2' => $row['address_line_2'] ?? null,
                'address_line_3' => $row['address_line_3'] ?? null,
                'zip_code' => $row['zip_code'] ?? null,
                'contact_person_name' => $row['contact_person_name'] ?? null,
                'designation' => $row['designation'] ?? null,
                'contact_mobile_no' => isset($row['contact_mobile_no']) ? preg_replace('/[^0-9]/', '', (string) $row['contact_mobile_no']) : null,
                'contact_email' => $row['contact_email'] ?? null,
                'tax_type_id' => $taxId,
                'gst_no' => $row['gst_no'] ?? null,
                'pan_no' => $row['pan_no'] ?? null,
                'payment_terms' => $row['payment_terms'] ?? null,
                'credit_limit' => $row['credit_limit'] ?? 0,
                'sales_discount' => $row['sales_discount'] ?? 0,
                'box_discount' => $row['box_discount'] ?? 0,
                'bank_name' => $row['bank_name'] ?? null,
                'branch' => $row['branch'] ?? null,
                'account_number' => $row['account_number'] ?? null,
                'ifsc_code' => $row['ifsc_code'] ?? null,
            ];

            $customerId = null;
            if (!empty($data['code'])) {
                $existing = Customer::where('code', $data['code'])->first();
                $customerId = $existing ? $existing->id : null;
            }

            $validator = Validator::make($data, [
                'category' => 'required|in:Retailer,Wholesaler',
                'name' => 'required|string|min:3|max:50',
                'code' => 'required|string|min:3|max:20|unique:customers,code' . ($customerId ? ',' . $customerId : ''),
                'mobile_no' => 'required|numeric|digits_between:10,15|unique:customers,mobile_no' . ($customerId ? ',' . $customerId : ''),
                'email' => 'nullable|email|max:128|unique:customers,email' . ($customerId ? ',' . $customerId : ''),
                'zone_id' => 'required',
                'status' => 'required|in:Active,Inactive',
                'state_id' => 'required',
                'city_id' => 'required',
                'place_id' => 'required',
                'address_line_1' => 'required|string|min:3|max:150',
            ]);

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
            Customer::updateOrCreate(
                ['code' => $d['code']],
                $d
            );
        }
    }
}
