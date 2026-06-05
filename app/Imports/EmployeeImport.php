<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\User;
use App\Models\Device;
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
        // Fetch existing users once
        $existingUsers = User::select('id', 'emp_id')
            ->get()
            ->keyBy('emp_id');
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $empId = trim((string)($row['emp_code'] ?? ''));
            $name = trim((string)($row['name'] ?? ''));
            if (empty($empId) && empty($name)) {
                continue;
            }
            $dateOfJoining = null;
            if (!empty($row['date_of_joining'])) {
                try {
                    $dateOfJoining = Carbon::parse($row['date_of_joining'])
                        ->format('Y-m-d');
                } catch (\Exception $e) {
                    $dateOfJoining = null;
                }
            }
            $departmentId = null;

            if (!empty($row['department'])) {
                $departmentId = Department::where(
                    'department',
                    trim($row['department'])
                )->value('id');
            }

            $deviceSerialNumber = null;

            if (!empty($row['device'])) {
                $deviceSerialNumber = Device::where(
                    'device_name',
                    trim($row['device'])
                )->value('serial_number');
            }

            $phone = !empty($row['phone'])
                ? preg_replace('/[^0-9]/', '', (string)$row['phone'])
                : '9' . str_pad($empId, 9, '0', STR_PAD_LEFT);
            $email = !empty($row['email'])
                ? trim((string)$row['email'])
                : 'emp'.$empId.'@company.local';
            $esiNo = trim((string)($row['esi_no'] ?? ''));
            $pfNo = trim((string)($row['pf_no'] ?? ''));
            $fixedGross =
                !empty($row['fixed_gross'])
                    ? preg_replace('/[^0-9.]/', '', $row['fixed_gross'])
                    : 0;
            $busFare =
                !empty($row['bus_fare'])
                    ? preg_replace('/[^0-9.]/', '', $row['bus_fare'])
                    : 0;
            $designation = trim((string)($row['designation'] ?? ''));
            $roleId = null;
            if(!empty($designation)) {
                $roleId = DB::table('roles')
                    ->whereRaw('LOWER(name) = ?', [strtolower($designation)])
                    ->value('id');
            }
            $data = [
                'emp_id' => $empId,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'date_of_joining' => $dateOfJoining,
                'department_id' => $departmentId,
                'esi_no' => $esiNo,
                'pf_no' => $pfNo,
                'fixed_gross' => $fixedGross,
                'bus_fare' => $busFare,
                'role_id' => $roleId,
                'device' => $deviceSerialNumber,
            ];
            // Duplicate inside Excel
            if (in_array($empId, $seenEmpIds)) {
                $errors[] =
                    "Row {$rowNumber}: Duplicate Employee ID ({$empId}) in file.";
                continue;
            }
            $seenEmpIds[] = $empId;
            // Validation
            $validator = Validator::make($data, [
                'emp_id' => 'required|max:30',
                'department_id' => 'required|exists:departments,id',
                'name' => 'required|max:100',
                'phone' => 'nullable|digits_between:10,15',
                'email' => 'nullable|email|max:128',
                'role_id' => 'required|exists:roles,id',
                'esi_no' => 'nullable|max:30',
                'pf_no' => 'nullable|max:30',
                'fixed_gross' => 'nullable|numeric',
                'bus_fare' => 'nullable|numeric',
                'device' => 'required|exists:devices,serial_number',
            ]);
            if ($validator->fails()) {
                $errors[] =
                    "Row {$rowNumber}: " .
                    implode(', ', $validator->errors()->all());
                continue;
            }
            // EXISTING EMPLOYEE -> UPDATE
            if(isset($existingUsers[$empId])) {
                $updateData[] = [
                    'id' => $existingUsers[$empId]->id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'date_of_joining' => $dateOfJoining,
                    'department_id' => $departmentId,
                    'esi_no' => $esiNo,
                    'pf_no' => $pfNo,
                    'fixed_gross' => $fixedGross,
                    'bus_fare' => $busFare,
                    'role_id' => $roleId,
                    'device' => $deviceSerialNumber,
                    'updated_at' => now(),
                ];
            } else {
                // NEW EMPLOYEE -> INSERT
                $insertData[] = [
                    'emp_id' => $empId,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'date_of_joining' => $dateOfJoining,
                    'department_id' => $departmentId,
                    'device' => $deviceSerialNumber,
                    'esi_no' => $esiNo,
                    'pf_no' => $pfNo,
                    'fixed_gross' => $fixedGross,
                    'bus_fare' => $busFare,
                    'role_id' => $roleId,
                    'created_by' => auth()->id() ?? 1,
                    'password' => bcrypt('123456'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        // Validation errors
        if(count($errors) > 0) {
            throw new \Exception(implode('<br>', $errors));
        }
        // Bulk insert
        if(count($insertData) > 0) {
            DB::table('users')->insert($insertData);
        }
        // Bulk update
        if(count($updateData) > 0) {
            foreach($updateData as $update) {
                DB::table('users')
                    ->where('id', $update['id'])
                    ->update($update);
            }
        }
    }
}
