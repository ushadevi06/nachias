<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\User;
use App\Models\Device;
use App\Models\ServiceProvider;
use App\Models\OperationStage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $errors = [];
        $insertData = [];
        $updateData = [];
        $seenEmpIds = [];
        $existingUsers = User::select('id', 'emp_id')->get()->keyBy('emp_id');
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $empId = trim((string)($row['emp_code'] ?? ''));
            $name = trim((string)($row['name'] ?? ''));
            $isEmpty = collect($row)->filter(function ($value) {
                return !is_null($value) && trim($value) !== '';
            })->isEmpty();

            if ($isEmpty) {
                continue;
            }
            if (strtolower($name) === 'admin' || $empId === '-') {
                continue;
            }
            $dateOfJoining = null;
            if (!empty($row['date_of_joining'])) {
                $rawDate = trim($row['date_of_joining']);
                if (is_numeric($rawDate)) {
                    try {
                        $dateOfJoining = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dateOfJoining = $rawDate;
                    }
                } else {
                    $parsed = date_parse($rawDate);
                    if ($parsed['error_count'] > 0 || empty($parsed['year']) || empty($parsed['month']) || empty($parsed['day'])) {
                        $dateOfJoining = $rawDate;
                    } else {
                        try {
                            $dateOfJoining = Carbon::parse($rawDate)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $dateOfJoining = $rawDate;
                        }
                    }
                }
            }
            $rowErrors = [];
            
            $departmentId = null;
            if (!empty($row['department'])) {
                $deptString = trim($row['department']);
                $departmentId = Department::where('department', $deptString)->value('id');
                if (!$departmentId) {
                    $rowErrors[] = "Department '{$deptString}' does not exist.";
                }
            }

            $deviceSerialNumber = null;
            if (!empty($row['device'])) {
                $deviceString = trim($row['device']);
                $deviceSerialNumber = Device::where('device_name', $deviceString)
                                            ->orWhere('serial_number', $deviceString)
                                            ->value('serial_number');
                if (!$deviceSerialNumber) {
                    $rowErrors[] = "Device '{$deviceString}' does not exist.";
                }
            }

            $serviceProviderId = null;
            if (!empty($row['service_provider'])) {
                $spString = trim($row['service_provider']);
                $serviceProviderId = ServiceProvider::where('name', $spString)->value('id');
                if (!$serviceProviderId) {
                    $rowErrors[] = "Service Provider '{$spString}' does not exist.";
                }
            }

            $operationStageIds = [];
            if (!empty($row['operation_stage'])) {
                $stages = array_map('trim', explode(',', $row['operation_stage']));
                $foundStagesCollection = OperationStage::whereIn('operation_stage_name', $stages)->pluck('operation_stage_name', 'id');
                $operationStageIds = $foundStagesCollection->keys()->toArray();
                
                $foundStageNamesLower = array_map('strtolower', $foundStagesCollection->values()->toArray());
                $missingStages = [];
                foreach ($stages as $stage) {
                    if (!in_array(strtolower($stage), $foundStageNamesLower)) {
                        $missingStages[] = $stage;
                    }
                }
                
                if (!empty($missingStages)) {
                    $missingList = implode(', ', $missingStages);
                    $rowErrors[] = count($missingStages) > 1 ? "Operation Stages '{$missingList}' do not exist." : "Operation Stage '{$missingList}' does not exist.";
                }
            }

            $phone = !empty($row['phone']) ? preg_replace('/[^0-9]/', '', (string)$row['phone']) : '9' . str_pad($empId, 9, '0', STR_PAD_LEFT);
            $email = !empty($row['email']) ? trim((string)$row['email']) : 'emp'.$empId.'@company.local';
            $esiNo = trim((string)($row['esi_no'] ?? ''));
            $pfNo = trim((string)($row['pf_no'] ?? ''));
            $fixedGross = !empty($row['fixed_gross']) ? preg_replace('/[^0-9.]/', '', $row['fixed_gross']): 0;
            $busFare = !empty($row['bus_fare']) ? preg_replace('/[^0-9.]/', '', $row['bus_fare']): 0;
            $designation = trim((string)($row['role'] ?? ($row['designation'] ?? '')));
            $roleId = null;
            if(!empty($designation)) {
                $roleId = DB::table('roles')->whereRaw('LOWER(name) = ?', [strtolower($designation)])->value('id');
                if (!$roleId) {
                    $rowErrors[] = "Role '{$designation}' does not exist.";
                }
            }
            
            if (!empty($rowErrors)) {
                $errors[] = "Row {$rowNumber}: " . implode(' ', $rowErrors);
                continue;
            }

            $data = [
                'emp_id' => $empId,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'date_of_joining' => $dateOfJoining,
                'department_id' => $departmentId,
                'service_provider_id' => $serviceProviderId,
                'esi_no' => $esiNo,
                'pf_no' => $pfNo,
                'fixed_gross' => $fixedGross,
                'bus_fare' => $busFare,
                'role_id' => $roleId,
                'device' => $deviceSerialNumber,
                'operation_stage_id' => !empty($operationStageIds) ? json_encode($operationStageIds) : null,
            ];
            if (in_array($empId, $seenEmpIds)) {
                $errors[] = "Row {$rowNumber}: Duplicate Employee ID ({$empId}) in file.";
                continue;
            }
            $seenEmpIds[] = $empId;
            $validator = Validator::make($data, [
                'emp_id' => 'required|max:20|not_in:0',
                'department_id' => 'required|exists:departments,id',
                'name' => 'required|max:100',
                'phone' => 'required|digits_between:10,15',
                'email' => 'nullable|email:filter|max:128',
                'role_id' => 'required|exists:roles,id',
                'esi_no' => 'nullable|max:30',
                'pf_no' => 'nullable|max:30',
                'fixed_gross' => 'nullable|numeric',
                'bus_fare' => 'nullable|numeric',
                'device' => 'required|exists:devices,serial_number',
                'service_provider_id' => 'nullable|exists:service_providers,id',
            ], [
                'department_id.required' => 'Department is required.',
                'department_id.exists' => 'Department does not exist.',
                'role_id.required' => 'Designation is required.',
                'role_id.exists' => 'Designation does not exist.',
                'device.required' => 'Device is required.',
                'device.exists' => 'Device does not exist.',
                'service_provider_id.exists' => 'Service Provider does not exist.',
                'emp_id.required' => 'Employee ID (Emp Code) is required.',
                'emp_id.max' => 'Employee ID (Emp Code) cannot be more than 30 characters.',
                'emp_id.not_in' => 'Employee ID cannot be 0.',
                'name.required' => 'Name is required.',
                'name.max' => 'Name cannot be more than 100 characters.',
                'phone.required' => 'Phone Number is required.',
                'phone.digits_between' => 'Phone Number must be between 10 and 15 digits.',
                'email.email' => 'Invalid email format.',
                'email.max' => 'Email cannot be more than 128 characters.',
                'esi_no.max' => 'ESI No cannot be more than 30 characters.',
                'pf_no.max' => 'PF No cannot be more than 30 characters.',
                'fixed_gross.numeric' => 'Fixed Gross must be a number.',
                'bus_fare.numeric' => 'Bus Fare must be a number.',
            ]);
            if ($validator->fails()) {
                $errors[] ="Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }
            if(isset($existingUsers[$empId])) {
                $errors[] ="Row {$rowNumber}: Employee Code '{$empId}' already exists.";
                continue;
            } else {
                $insertData[] = [
                    'emp_id' => $empId,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'date_of_joining' => $dateOfJoining,
                    'department_id' => $departmentId,
                    'service_provider_id' => $serviceProviderId,
                    'device' => $deviceSerialNumber,
                    'esi_no' => $esiNo,
                    'pf_no' => $pfNo,
                    'fixed_gross' => $fixedGross,
                    'bus_fare' => $busFare,
                    'role_id' => $roleId,
                    'operation_stage_id' => !empty($operationStageIds) ? json_encode($operationStageIds) : null,
                    'created_by' => auth()->id() ?? 1,
                    'password' => bcrypt('123456'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if(count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }
        if(count($insertData) > 0) {
            DB::table('users')->insert($insertData);
        }
    }
}
