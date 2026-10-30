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
    public $updatedCount = 0;
    public $createdCount = 0;

    public function getSuccessMessage()
    {
        $parts = [];
        if ($this->updatedCount > 0) {
            $parts[] = "{$this->updatedCount} customer(s) updated";
        }
        if ($this->createdCount > 0) {
            $parts[] = "{$this->createdCount} new customer(s) created";
        }
        return count($parts) > 0 ? "Import completed successfully: " . implode(', ', $parts) . "." : "Import completed: No changes were made.";
    }

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

            // Resolve Customer Code
            $code = isset($row['code']) ? (string)$row['code'] : ($row['retailer_reference_id'] ?? null);
            if ($code !== null) {
                $code = trim($code);
            }

            if (!empty($code)) {
                if (in_array($code, $seenCodes)) {
                    $errors[] = "Row {$rowNumber}: Duplicate Code ({$code}) found in the Excel file.";
                    continue;
                }
                $seenCodes[] = $code;
            }

            // Check if this customer already exists
            $existing = !empty($code) ? Customer::where('code', $code)->first() : null;

            // Resolve State
            $stateId = null;
            if (!empty($row['state']) && trim($row['state']) !== '-') {
                $state = State::where('state_name', trim($row['state']))->first();
                if (!$state && !$existing) {
                    $errors[] = "Row {$rowNumber}: State '{$row['state']}' does not exist in state table";
                    continue;
                }
                $stateId = $state ? $state->id : null;
            } elseif ($existing) {
                $stateId = $existing->state_id;
            }

            // Resolve City
            $cityId = null;
            if (!empty($row['city']) && trim($row['city']) !== '-' && $stateId) {
                $city = City::where('city_name', trim($row['city']))->where('state_id', $stateId)->first();
                if (!$city && !$existing) {
                    $errors[] = "Row {$rowNumber}: City '{$row['city']}' does not exist for the selected state";
                    continue;
                }
                $cityId = $city ? $city->id : null;
            } elseif ($existing) {
                $cityId = $existing->city_id;
            }

            // Resolve Place
            $placeId = null;
            if (!empty($row['place']) && trim($row['place']) !== '-' && $cityId) {
                $place = Place::where('place_name', trim($row['place']))->where('city_id', $cityId)->first();
                if (!$place && !$existing) {
                    $errors[] = "Row {$rowNumber}: Place '{$row['place']}' does not exist for the selected city";
                    continue;
                }
                $placeId = $place ? $place->id : null;
            } elseif ($existing) {
                $placeId = $existing->place_id;
            }

            // Resolve Zone
            $zoneId = null;
            if (!empty($row['zone']) && trim($row['zone']) !== '-') {
                $zone = Zone::where('zone_name', trim($row['zone']))->first();
                if ($zone) {
                    $zoneId = $zone->id;
                } elseif (!$existing) {
                    $errors[] = "Row {$rowNumber}: Zone '{$row['zone']}' does not exist in zone table";
                    continue;
                }
            } elseif ($existing) {
                $zoneId = $existing->zone_id;
            }

            // Resolve Store
            $storeId = null;
            if (!empty($row['store'])) {
                $store = StoreType::where('store_type_name', trim($row['store']))->first();
                $storeId = $store ? $store->id : null;
            } elseif ($existing) {
                $storeId = $existing->store_id;
            }

            // Resolve Tax
            $taxId = null;
            if (!empty($row['tax_type'])) {
                $tax = Tax::where('item_name', trim($row['tax_type']))->first();
                $taxId = $tax ? $tax->id : null;
            } elseif ($existing) {
                $taxId = $existing->tax_type_id;
            }

            // Resolve Name
            $name = $row['name'] ?? ($row['retailer_name'] ?? null);
            if (isset($row['name_with_code'])) {
                if (preg_match('/^(.*)\s*\((.*)\)$/', $row['name_with_code'], $matches)) {
                    $name = trim($matches[1]);
                    $code = $code ?: trim($matches[2]);
                } else {
                    $name = trim($row['name_with_code']);
                }
            }
            if ($name !== null) {
                $name = trim($name);
            }

            // Resolve Tamil Fields
            $tamilName = $row['tamil_name'] ?? ($row['tamil_customer_name'] ?? ($row['customer_tamil_name'] ?? ($row['name_in_tamil'] ?? null)));
            if ($tamilName !== null) $tamilName = trim($tamilName);

            $tamilAddress = $row['tamil_address'] ?? ($row['address_in_tamil'] ?? ($row['customer_tamil_address'] ?? null));
            if ($tamilAddress !== null) $tamilAddress = trim($tamilAddress);

            $tamilPlace = $row['tamil_place'] ?? ($row['place_in_tamil'] ?? ($row['tamil_city'] ?? null));
            if ($tamilPlace !== null) $tamilPlace = trim($tamilPlace);

            // Resolve Mobile Number
            $mobileNo = null;
            if (isset($row['mobile_number']) && trim((string)$row['mobile_number']) !== '') {
                $mobileNo = preg_replace('/[^0-9]/', '', (string)$row['mobile_number']);
            } elseif (isset($row['phone_number']) && trim((string)$row['phone_number']) !== '') {
                $mobileNo = preg_replace('/[^0-9]/', '', (string)$row['phone_number']);
            } elseif (isset($row['phone']) && trim((string)$row['phone']) !== '') {
                $mobileNo = preg_replace('/[^0-9]/', '', (string)$row['phone']);
            }

            // Transport details
            $transportName = $row['transport_name'] ?? ($row['transporter_name'] ?? ($row['transport_place'] ?? null));
            if ($transportName !== null) $transportName = trim($transportName);

            $bookingOffice = $row['booking_office'] ?? null;
            if ($bookingOffice !== null) $bookingOffice = trim($bookingOffice);

            $altTransportName = $row['alternate_transport_name'] ?? ($row['alt_transport_name'] ?? ($row['alternate_transport'] ?? ($row['alt_transport'] ?? ($row['alternative_transport_office'] ?? ($row['alt_transport_office'] ?? ($row['alternate_transport_office'] ?? ($row['alternative_booking_office'] ?? ($row['alt_booking_office'] ?? null))))))));
            if ($altTransportName !== null) $altTransportName = trim($altTransportName);

            $data = [
                'category'            => $row['category'] ?? ($existing ? $existing->category : 'Retailer'),
                'name'                => $name ?: ($existing ? $existing->name : null),
                'tamil_name'          => $tamilName ?: ($existing ? $existing->tamil_name : null),
                'tamil_address'       => $tamilAddress ?: ($existing ? $existing->tamil_address : null),
                'tamil_place'         => $tamilPlace ?: ($existing ? $existing->tamil_place : null),
                'code'                => $code,
                'mobile_no'           => $mobileNo ?: ($existing ? $existing->mobile_no : null),
                'email'               => $row['email'] ?? ($existing ? $existing->email : null),
                'website_url'         => $row['website_url'] ?? ($existing ? $existing->website_url : null),
                'transport_name'      => $transportName,
                'alt_transport_name'  => $altTransportName,
                'booking_office'      => $bookingOffice,
                'zone_id'             => $zoneId,
                'store_id'            => $storeId,
                'status'              => $row['status'] ?? ($existing ? $existing->status : 'Active'),
                'state_id'            => $stateId,
                'city_id'             => $cityId,
                'place_id'            => $placeId,
                'address_line_1'      => $row['address'] ?? ($row['address_line_1'] ?? ($existing ? $existing->address_line_1 : null)),
                'address_line_2'      => $row['address_line_2'] ?? ($existing ? $existing->address_line_2 : null),
                'address_line_3'      => $row['address_line_3'] ?? ($existing ? $existing->address_line_3 : null),
                'zip_code'            => isset($row['zipcode']) ? preg_replace('/[^0-9]/', '', (string)$row['zipcode']) : (isset($row['zip_code']) ? preg_replace('/[^0-9]/', '', (string)$row['zip_code']) : ($existing ? $existing->zip_code : null)),  
                'contact_person_name' => $row['contact_person_name'] ?? ($existing ? $existing->contact_person_name : null),
                'designation'         => $row['designation'] ?? ($existing ? $existing->designation : null),
                'contact_mobile_no'   => isset($row['contact_person_mobile']) ? preg_replace('/[^0-9]/', '', (string)$row['contact_person_mobile']) : ($existing ? $existing->contact_mobile_no : null),
                'contact_email'       => $row['contact_person_email'] ?? ($row['contact_email'] ?? ($existing ? $existing->contact_email : null)),
                'tax_type_id'         => $taxId,
                'gst_no'              => isset($row['gst_no']) ? trim(preg_replace('/^(GSTIN\s*NO\s*:|GST\s*IN\s*:|GST\s*:)\s*/i', '', trim($row['gst_no']))) : (isset($row['gstin']) ? trim($row['gstin']) : ($existing ? $existing->gst_no : null)),
                'pan_no'              => $row['pan_no'] ?? ($row['pan'] ?? ($existing ? $existing->pan_no : null)),
                'payment_terms'       => $row['payment_term'] ?? ($row['payment_terms'] ?? ($existing ? $existing->payment_terms : null)),
                'credit_limit'        => $row['credit_limit'] ?? ($existing ? $existing->credit_limit : 0),
                'sales_discount'      => $row['sales_discount'] ?? ($existing ? $existing->sales_discount : 0),
                'box_discount_amount' => $row['box_discount_amountper_pcs'] ?? ($row['box_discount_amount_per_pcs'] ?? ($row['box_discount_amount'] ?? ($existing ? $existing->box_discount_amount : 0))),
                'bank_name'           => $row['bank_name'] ?? ($existing ? $existing->bank_name : null),
                'branch'              => $row['branch'] ?? ($existing ? $existing->branch : null),
                'account_number'      => $row['account_number'] ?? ($existing ? $existing->account_number : null),
                'ifsc_code'           => $row['ifsc_code'] ?? ($existing ? $existing->ifsc_code : null),
            ];

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
                'address_line_1'     => 'address',
                'zip_code'           => 'zipcode',
                'gst_no'             => 'GST number',
                'transport_name'     => 'Transport Name',
                'alt_transport_name' => 'Alternate Transport Name',
                'booking_office'     => 'Booking Office',
            ];

            if ($existing) {
                // UPDATE EXISTING: validate without blocking existing code/email
                $updateRules = [
                    'code'               => ['required', 'string'],
                    'transport_name'     => ['nullable', 'string', 'max:50'],
                    'alt_transport_name' => ['nullable', 'string', 'max:50'],
                    'booking_office'     => ['nullable', 'string', 'max:50'],
                ];
                $validator = Validator::make($data, $updateRules, $messages, $attributes);
                if ($validator->fails()) {
                    $errors[] = "Row {$rowNumber} (Code: {$data['code']}): " . implode(', ', $validator->errors()->all());
                } else {
                    $validData[] = [
                        'action' => 'update',
                        'customer' => $existing,
                        'data' => $data,
                    ];
                }
            } else {
                // CREATE NEW CUSTOMER: Run full creation validation
                $createRules = [
                    'category'           => ['bail', 'required', 'in:Retailer,Wholesaler'],
                    'name'               => ['bail', 'required', 'string', 'min:2', 'max:100', 'not_regex:/^0+$/'],
                    'code'               => ['bail', 'required', 'string', 'min:2', 'max:50', 'not_regex:/^0+$/', 'unique:customers,code,NULL,id,deleted_at,NULL'],
                    'mobile_no'          => ['bail', 'required', 'numeric', 'digits_between:10,15', 'not_regex:/^0+$/'],
                    'email'              => ['bail', 'nullable', 'email', 'max:128', 'unique:customers,email,NULL,id,deleted_at,NULL', 'not_regex:/^0+$/'],
                    'website_url'        => ['bail', 'nullable', 'max:255', 'not_regex:/^0+$/'],
                    'transport_name'     => ['bail', 'nullable', 'string', 'max:50'],
                    'alt_transport_name' => ['bail', 'nullable', 'string', 'max:50'],
                    'booking_office'     => ['bail', 'nullable', 'string', 'max:50'],
                    'zone_id'            => ['bail', 'required'],
                    'status'             => ['bail', 'required', 'in:Active,Inactive'],
                    'state_id'           => ['bail', 'required'],
                    'city_id'            => ['bail', 'required'],
                    'place_id'           => ['bail', 'required'],
                    'address_line_1'     => ['bail', 'required', 'string', 'min:3', 'max:150', 'not_regex:/^0+$/'],
                    'zip_code'           => ['bail', 'required', 'digits:6', 'not_regex:/^0+$/'],
                    'gst_no'             => ['bail', 'required', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', 'not_regex:/^0+$/'],
                ];
                $validator = Validator::make($data, $createRules, $messages, $attributes);
                if ($validator->fails()) {
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                } else {
                    $validData[] = [
                        'action' => 'create',
                        'customer' => null,
                        'data' => $data,
                    ];
                }
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }

        $updated = 0;
        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($validData as $item) {
                if ($item['action'] === 'update') {
                    /** @var Customer $cust */
                    $cust = $item['customer'];
                    $d = $item['data'];
                    $oldData = $cust->toArray();

                    $fieldsToUpdate = [];

                    // Transport & Booking Office fields
                    if (array_key_exists('transport_name', $d) && $d['transport_name'] !== null) {
                        $fieldsToUpdate['transport_name'] = $d['transport_name'] === '-' ? null : $d['transport_name'];
                    }
                    if (array_key_exists('booking_office', $d) && $d['booking_office'] !== null) {
                        $fieldsToUpdate['booking_office'] = $d['booking_office'] === '-' ? null : $d['booking_office'];
                    }
                    if (array_key_exists('alt_transport_name', $d) && $d['alt_transport_name'] !== null) {
                        $fieldsToUpdate['alt_transport_name'] = $d['alt_transport_name'] === '-' ? null : $d['alt_transport_name'];
                    }

                    // Optional details if updated in file
                    if (!empty($d['name']) && $d['name'] !== '-') $fieldsToUpdate['name'] = $d['name'];
                    if (!empty($d['category']) && $d['category'] !== '-') $fieldsToUpdate['category'] = $d['category'];
                    if (!empty($d['mobile_no'])) $fieldsToUpdate['mobile_no'] = $d['mobile_no'];
                    if (!empty($d['email']) && $d['email'] !== '-') $fieldsToUpdate['email'] = $d['email'];
                    if (!empty($d['website_url']) && $d['website_url'] !== '-') $fieldsToUpdate['website_url'] = $d['website_url'];
                    if (!empty($d['state_id'])) $fieldsToUpdate['state_id'] = $d['state_id'];
                    if (!empty($d['city_id'])) $fieldsToUpdate['city_id'] = $d['city_id'];
                    if (!empty($d['place_id'])) $fieldsToUpdate['place_id'] = $d['place_id'];
                    if (!empty($d['zone_id'])) $fieldsToUpdate['zone_id'] = $d['zone_id'];
                    if (!empty($d['address_line_1']) && $d['address_line_1'] !== '-') $fieldsToUpdate['address_line_1'] = $d['address_line_1'];
                    if (!empty($d['zip_code']) && $d['zip_code'] !== '-') $fieldsToUpdate['zip_code'] = $d['zip_code'];
                    if (!empty($d['gst_no']) && $d['gst_no'] !== '-') $fieldsToUpdate['gst_no'] = $d['gst_no'];
                    if (!empty($d['pan_no']) && $d['pan_no'] !== '-') $fieldsToUpdate['pan_no'] = $d['pan_no'];
                    if (!empty($d['tamil_name']) && $d['tamil_name'] !== '-') $fieldsToUpdate['tamil_name'] = $d['tamil_name'];
                    if (!empty($d['tamil_address']) && $d['tamil_address'] !== '-') $fieldsToUpdate['tamil_address'] = $d['tamil_address'];
                    if (!empty($d['tamil_place']) && $d['tamil_place'] !== '-') $fieldsToUpdate['tamil_place'] = $d['tamil_place'];

                    $fieldsToUpdate['updated_by'] = auth()->id() ?? 1;

                    $cust->update($fieldsToUpdate);
                    $newData = $cust->fresh()->toArray();
                    addLog('update', 'Customer via Import', 'customers', $cust->id, $oldData, $newData);
                    $updated++;
                } else {
                    $d = $item['data'];
                    $d['created_by'] = auth()->id() ?? 1;

                    if (is_null($d['zone_id'])) {
                        unset($d['zone_id']);
                    }

                    $customer = Customer::create($d);
                    addLog('create', 'Customer via Import', 'customers', $customer->id, null, $customer->toArray());
                    $created++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->updatedCount = $updated;
        $this->createdCount = $created;
    }
}