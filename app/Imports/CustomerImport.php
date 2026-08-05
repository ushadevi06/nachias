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
use Illuminate\Support\Facades\DB;

class CustomerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $validData = [];
        $seenCodes = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Check if the entire row is empty (ignore trailing empty rows in Excel)
            $isEmpty = collect($row)->filter(function ($value) {
                return !is_null($value) && trim($value) !== '';
            })->isEmpty();

            if ($isEmpty) {
                continue;
            }

            $stateId = null;
            if (!empty($row['state'])) {
                $state = State::where('state_name', trim($row['state']))->first();
                if (!$state) {
                    $errors[] = "Row {$rowNumber}: {$row['state']} does not exist in state table";
                    continue;
                }
                $stateId = $state->id;
            }

            $cityId = null;
            if (!empty($row['city']) && $stateId) {
                $city = City::where('city_name', trim($row['city']))->where('state_id', $stateId)->first();
                if (!$city) {
                    $errors[] = "Row {$rowNumber}: {$row['city']} does not exist in city table";
                    continue;
                }
                $cityId = $city->id;
            }

            $placeId = null;
            if (!empty($row['place']) && $cityId && $stateId) {
                $place = Place::where('place_name', trim($row['place']))->where('city_id', $cityId)->where('state_id', $stateId)->first();
                if (!$place) {
                    $errors[] = "Row {$rowNumber}: {$row['place']} does not exist in place table";
                    continue;
                }
                $placeId = $place->id;
            }


            $zoneId = null;
            if (!empty($row['zone'])) {
                $zone = Zone::where('zone_name', trim($row['zone']))->first();
                if ($zone) {
                    $zoneId = $zone->id;
                } else {
                    $errors[] = "Row {$rowNumber}: {$row['zone']} does not exist in zone table";
                    continue;
                }
            }



            $storeId = null;
            if (!empty($row['store'])) {
                $store = StoreType::where('store_type_name', trim($row['store']))->first();
                $storeId = $store ? $store->id : null;
            }

            $taxId = null;
            if (!empty($row['tax_type'])) {
                $tax = Tax::where('item_name', trim($row['tax_type']))->first();
                $taxId = $tax ? $tax->id : null;
            }

            $name = $row['name'] ?? null;
            $code = isset($row['code']) ? (string) $row['code'] : null;

            if (isset($row['name_with_code'])) {
                if (preg_match('/^(.*)\s*\((.*)\)$/', $row['name_with_code'], $matches)) {
                    $name = trim($matches[1]);
                    $code = trim($matches[2]);
                } else {
                    $name = trim($row['name_with_code']);
                }
            }

            $mobileNo = null;
            if (isset($row['mobile_number']) && trim((string)$row['mobile_number']) !== '') {
                $mobileNo = preg_replace('/[^0-9]/', '', (string) $row['mobile_number']);
            } elseif (isset($row['phone']) && trim((string)$row['phone']) !== '') {
                $mobileNo = preg_replace('/[^0-9]/', '', (string) $row['phone']);
            }
			
            $data = [
                'category'            => $row['category'] ?? 'Retailer',
                'name'                => $name,
                'code'                => $code,
                'mobile_no'           => $mobileNo,
                'email'               => $row['email'] ?? null,
                'website_url'         => $row['website_url'] ?? null,
                'transport_name'      => $row['transport_place'] ?? ($row['transport_name'] ?? null),
                'booking_office'      => $row['booking_office'] ?? null,
                'zone_id'             => $zoneId,
                'store_id'            => $storeId,
                'status'              => $row['status'] ?? 'Active',
                'state_id'            => $stateId,
                'city_id'             => $cityId,
                'place_id'            => $placeId,
                'address_line_1'      => $row['address'] ?? ($row['address_line_1'] ?? null),
                'address_line_2'      => $row['address_line_2'] ?? null,
                'address_line_3'      => $row['address_line_3'] ?? null,
                'zip_code'            => isset($row['zipcode']) ? preg_replace('/[^0-9]/', '', (string)$row['zipcode']) : (isset($row['zip_code']) ? preg_replace('/[^0-9]/', '', (string)$row['zip_code']) : null),  
                'contact_person_name' => $row['contact_person_name'] ?? null,
                'designation'         => $row['designation'] ?? null,
                'contact_mobile_no'   => isset($row['contact_person_mobile']) ? preg_replace('/[^0-9]/', '', (string) $row['contact_person_mobile']) : null,
                'contact_email'       => $row['contact_person_email'] ?? ($row['contact_email'] ?? null),
                'tax_type_id'         => $taxId,
                'gst_no' => isset($row['gst_no']) ? trim(preg_replace('/^(GSTIN\s*NO\s*:|GST\s*IN\s*:|GST\s*:)\s*/i', '', trim($row['gst_no']))) : null,
                'pan_no'              => $row['pan_no'] ?? null,
                'payment_terms'       => $row['payment_term'] ?? ($row['payment_terms'] ?? null),
                'credit_limit'        => $row['credit_limit'] ?? 0,
                'sales_discount'      => $row['sales_discount'] ?? 0,
                'box_discount_amount' => $row['box_discount_amountper_pcs'] ?? ($row['box_discount_amount_per_pcs'] ?? ($row['box_discount_amount'] ?? ($row['box_discount'] ?? 0))),
                'bank_name'           => $row['bank_name'] ?? null,
                'branch'              => $row['branch'] ?? null,
                'account_number'      => $row['account_number'] ?? null,
                'ifsc_code'           => $row['ifsc_code'] ?? null,
            ];

            if (!empty($data['code'])) {
                if (in_array($data['code'], $seenCodes)) {
                    $errors[] = "Row {$rowNumber}: Duplicate Code ({$data['code']}) found in the Excel file.";
                    continue;
                }
                $seenCodes[] = $data['code'];
            }

            $customerId = null;
            if (!empty($data['code'])) {
                $existing = Customer::where('code', $data['code'])->first();
                $customerId = $existing ? $existing->id : null;
            }

            if ($customerId) {
                $errors[] = "Row {$rowNumber}: Customer Code '{$data['code']}' already exists.";
                continue;
            }

            $messages = [
                '*.required' => 'The :attribute field is required.',
                '*.unique'   => 'The :attribute field already exists.',
                'code.not_regex' => 'Code cannot be 0.',
                '*.not_regex' => 'The :attribute field is an invalid format.',
                '*.numeric'  => 'The :attribute field must be a valid number.',
                'min'      => 'The :attribute field must be at least :min characters.',
                'max'      => 'The :attribute field should not be more than :max characters.',
                '*.email'    => 'Please enter a valid email address.',
                'gst_no.regex' => 'The GST number format is invalid.',
            ];

            $attributes = [
                'state_id'  => 'state',
                'city_id'   => 'city',
                'place_id'  => 'place',
                'zone_id'   => 'zone',
                'mobile_no' => 'mobile number',
                'email'     => 'email',
                'website_url' => 'website url',
                'address_line_1' => 'address',
                'zip_code'  => 'zipcode',
                'gst_no'    => 'GST number',
            ];

            $validator = Validator::make($data, [
                'category'       => ['bail', 'required', 'in:Retailer,Wholesaler'],
                'name'           => ['bail', 'required', 'string', 'min:2', 'max:100', 'not_regex:/^0+$/'],
                'code'           => ['bail', 'required', 'string', 'min:2', 'max:50', 'not_regex:/^0+$/', 'unique:customers,code'],
                'mobile_no'      => ['bail', 'required', 'numeric', 'digits_between:10,15', 'not_regex:/^0+$/'],
                'email'          => ['bail', 'nullable', 'email', 'max:128', 'unique:customers,email', 'not_regex:/^0+$/'],
                'website_url'    => ['bail', 'nullable', 'max:255', 'not_regex:/^0+$/'],
                'zone_id'        => ['bail', 'required'],
                'status'         => ['bail', 'required', 'in:Active,Inactive'],
                'state_id'       => ['bail', 'required'],
                'city_id'        => ['bail', 'required'],
                'place_id'       => ['bail', 'required'],
                'address_line_1' => ['bail', 'required', 'string', 'min:3', 'max:150', 'not_regex:/^0+$/'],
                'zip_code'       => ['bail', 'required', 'digits:6', 'not_regex:/^0+$/'],
                'gst_no'         => ['bail', 'required', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', 'not_regex:/^0+$/'],
            ], $messages, $attributes);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
            } else {
                $validData[] = $data;
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }

        DB::beginTransaction();
		try {
			foreach ($validData as $d) {
				$d['created_by'] = auth()->id() ?? 1;

				if (is_null($d['zone_id'])) {
					unset($d['zone_id']);
				}

				$existing = Customer::where('code', $d['code'])->first();   
				if (!$existing) {
					$customer = Customer::create($d);
					addLog('create', 'Customer via Import', 'customers', $customer->id, null, $customer->toArray());
				}
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
    }
}