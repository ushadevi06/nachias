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

            if (empty($row['name']) && empty($row['code']) && empty($row['mobile_number'])) {
                continue;
            }

            // ── State: find or create ──────────────────────────────
            $stateId = null;
            if (!empty($row['state'])) {
                $state = State::firstOrCreate(
					['state_name' => trim($row['state'])],
					[
						'state_code'  => strtoupper(substr(trim($row['state']), 0, 3)),
						'status'      => 'Active',
						'created_by'  => auth()->id() ?? 1,
					]
				);
                $stateId = $state->id;
            }

            // ── City: find or create ───────────────────────────────
            $cityId = null;
            if (!empty($row['city']) && $stateId) {
                $city = City::firstOrCreate(
                    ['city_name' => trim($row['city']), 'state_id' => $stateId],
                    ['status' => 'Active', 'created_by' => auth()->id() ?? 1]
                );
                $cityId = $city->id;
            }

            // ── Place: find or create ──────────────────────────────
            $placeId = null;
            if (!empty($row['place']) && $cityId && $stateId) {
                $place = Place::firstOrCreate(
                    ['place_name' => trim($row['place']), 'city_id' => $cityId, 'state_id' => $stateId],
                    ['place_type' => 'Commercial', 'status' => 'Active', 'created_by' => auth()->id() ?? 1]
                );
                $placeId = $place->id;
            }

            // ── Auto-fill Place from City if place column empty ────
            if ($cityId && !$placeId) {
                $firstPlace = Place::where('city_id', $cityId)->first();
                if ($firstPlace) {
                    $placeId = $firstPlace->id;
                } else {
                    // Create a default place using city name
                    $newPlace = Place::firstOrCreate(
                        ['place_name' => trim($row['city']), 'city_id' => $cityId, 'state_id' => $stateId],
                        ['place_type' => 'Commercial', 'status' => 'Active', 'created_by' => auth()->id() ?? 1]
                    );
                    $placeId = $newPlace->id;
                }
            }

            // ── Zone: find or auto-assign from city ───────────────
            $zoneId = null;
            if (!empty($row['zone'])) {
                $zone = Zone::where('zone_name', trim($row['zone']))->first();
                if ($zone) {
                    $zoneId = $zone->id;
                }
                // Zone not found → silently skip, auto-assign below
            }

            if (!$zoneId && $cityId) {
                $firstZone = Zone::active()->whereRaw("FIND_IN_SET(?, city_ids)", [$cityId])->first();
                $zoneId = $firstZone ? $firstZone->id : null;
            }

            // ── Store ──────────────────────────────────────────────
            $storeId = null;
            if (!empty($row['store'])) {
                $store = StoreType::where('store_type_name', trim($row['store']))->first();
                $storeId = $store ? $store->id : null;
            }

            // ── Tax ────────────────────────────────────────────────
            $taxId = null;
            if (!empty($row['tax_type'])) {
                $tax = Tax::where('item_name', trim($row['tax_type']))->first();
                $taxId = $tax ? $tax->id : null;
            }

            // ── Name & Code ────────────────────────────────────────
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

            // ── Mobile ─────────────────────────────────────────────
            $mobileNo = null;
            if (!empty($row['mobile_number'])) {
                $mobileNo = preg_replace('/[^0-9]/', '', (string) $row['mobile_number']);
            } elseif (!empty($row['phone'])) {
                $mobileNo = preg_replace('/[^0-9]/', '', (string) $row['phone']);
            }
			
            // ── Data Array ─────────────────────────────────────────
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
                'box_discount'        => $row['box_discount'] ?? 0,
                'bank_name'           => $row['bank_name'] ?? null,
                'branch'              => $row['branch'] ?? null,
                'account_number'      => $row['account_number'] ?? null,
                'ifsc_code'           => $row['ifsc_code'] ?? null,
            ];

            // ── Duplicate code check within file ───────────────────
            if (!empty($data['code'])) {
                if (in_array($data['code'], $seenCodes)) {
                    $errors[] = "Row {$rowNumber}: Duplicate Code ({$data['code']}) found in the Excel file.";
                    continue;
                }
                $seenCodes[] = $data['code'];
            }

            // ── Existing customer for unique-ignore ────────────────
            $customerId = null;
            if (!empty($data['code'])) {
                $existing = Customer::where('code', $data['code'])->first();
                $customerId = $existing ? $existing->id : null;
            }

            // ── Validation ─────────────────────────────────────────
            $validator = Validator::make($data, [
                'category'       => 'required|in:Retailer,Wholesaler',
                'name'           => 'required|string|min:2|max:100',
                'code'           => 'required|string|min:2|max:50|unique:customers,code,' . ($customerId ?? 'NULL') . ',id,deleted_at,NULL',
                'mobile_no'      => 'required|numeric|digits_between:10,15|unique:customers,mobile_no,' . ($customerId ?? 'NULL') . ',id,deleted_at,NULL',
                'email'          => 'nullable|email|max:128|unique:customers,email,' . ($customerId ?? 'NULL') . ',id,deleted_at,NULL',
                'zone_id'        => 'nullable',
                'status'         => 'required|in:Active,Inactive',
                'state_id'       => 'required',
                'city_id'        => 'required',
                'place_id'       => 'required',
                'address_line_1' => 'nullable|string|min:3|max:150',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
            } else {
                $validData[] = $data;
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }

        // ── Persist ────────────────────────────────────────────────
        DB::beginTransaction();
		try {
			foreach ($validData as $d) {
				$d['created_by'] = auth()->id() ?? 1;

				// Remove null zone_id so DB default/existing value is used
				if (is_null($d['zone_id'])) {
					unset($d['zone_id']);
				}

				$existing = Customer::where('code', $d['code'])->first();
				$oldData  = $existing ? $existing->toArray() : null;
				$action   = $existing ? 'update' : 'create';

				$customer = Customer::updateOrCreate(
					['code' => $d['code']],
					$d
				);

				addLog($action, 'Customer via Import', 'customers', $customer->id, $oldData, $customer->toArray());
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}
    }
}