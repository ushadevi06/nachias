<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\BloodGroup;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use App\Models\ServiceProvider;
use App\Models\JobCardEntry;
use App\Models\JobCardOperation;
use App\Models\Task;
use App\Models\TaskAdjustment;
use App\Models\OperationStage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view employees')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {

            $query = User::with(['department', 'role', 'serviceProvider', 'operationStage'])->orderBy('id', 'desc');

            if (!empty($request->department)) {
                $query->where('department_id', $request->department);
            }

            if (!empty($request->role)) {
                $query->where('role_id', $request->role);
            }

            $employees = $query->get();
            $data = [];
            $count = 1;

            foreach ($employees as $emp) {

                $actionBtn = '<div class="button-box">';

                if ($emp->id != 1 && (auth()->id() == 1 || auth()->user()->can('view_details employees'))) {
                    $actionBtn .= '<a href="' . url('employees/view/' . $emp->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }

                if ($emp->id != 1 && (auth()->id() == 1 || auth()->user()->can('edit employees'))) {
                    $actionBtn .= '<a href="' . url('employees/add/' . $emp->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                if ($emp->id != 1 && (auth()->id() == 1 || auth()->user()->can('delete employees'))) {
                    $actionBtn .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('employees/delete/' . $emp->id) . '\')" data-id="' . $emp->id . '"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }

                $actionBtn .= '</div>';

                $checked = $emp->status === 'Active' ? 'checked' : '';

                $statusSwitch = '<label class="switch switch-success switch-lg"><input type="checkbox" class="switch-input employee-status-toggle" data-id="' . $emp->id . '" ' . $checked . '><span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span></label><div class="status_msg_' . $emp->id . '"></div>';

                $image = $emp->profile_image ? url('uploads/employee/' . $emp->id . '/' . $emp->profile_image) : url('assets/images/user.jpg');

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'name'        => $emp->name,
                    'image'       => '<img src="' . $image . '" class="rounded-circle" width="50">',
                    'role'        => $emp->role->name ?? '-',
                    'department'  => $emp->department->department ?? '-',
                    'service_provider' => $emp->serviceProvider->name ?? '-',
                    'operation_stage' => $emp->operationStage->operation_stage_name ?? '-',
                    'contact_info' => ' 
                        <div class="contact-info">
                            <div>
                                <i class="ri ri-mail-line icon-email"></i>
                                ' . ($emp->email ?? '-') . '
                            </div>
                            <div>
                                <i class="ri ri-phone-line icon-phone"></i>
                                ' . ($emp->phone ?? '-') . '
                            </div>
                        </div>',
                    'status'      => $statusSwitch,
                    'action'      => $actionBtn
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('employees.view');
    }


    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit employees')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create employees')) {
                return unauthorizedRedirect();
            }
        }
        $employee = $id ? User::findOrFail($id) : null;
        $departments = Department::active()->orderBy('id', 'desc')->get();
        $roles = Role::where('status', 'Active')->orderBy('id', 'desc')->get();
        $bloodGroups = BloodGroup::orderBy('id', 'desc')->get();
        $serviceProviders = ServiceProvider::active()->orderBy('id', 'desc')->get();
        $operationStages = OperationStage::active()->orderBy('id', 'desc')->get();
        $states = State::active()->orderBy('id', 'desc')->get();
        $cities = [];

        $stateId = old('state_id') ?? ($employee->state_id ?? null);

        if ($stateId) {
            $cities = City::active()->where('state_id', $stateId)->orderBy('id', 'desc')->get();
        }

        if (request()->isMethod('post')) {
            $rules = [
                'name' => 'required|string|min:3|max:50',
                'email' => [
                    'required',
                    'email',
                    'max:128',
                    Rule::unique('users', 'email')->ignore($id ?? null)->whereNull('deleted_at'),
                ],
                'phone' => [
                    'required',
                    'numeric',
                    'digits_between:10,15',
                    Rule::unique('users', 'phone')->ignore($id ?? null)->whereNull('deleted_at'),
                ],
                'emp_id' => [
                    'required',
                    'max:20',
                    Rule::unique('users', 'emp_id')->ignore($id ?? null)->whereNull('deleted_at'),
                ],
                'department_id' => 'required|exists:departments,id',
                'service_provider_id' => 'nullable|exists:service_providers,id',
                'operation_stage_id' => 'nullable|exists:operation_stages,id',
                'role_id' => 'required|exists:roles,id',
                'blood_group_id' => 'nullable|exists:blood_groups,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
                'status' => 'required|in:Active,Inactive',
                'father_name' => 'nullable|string|min:3|max:50',
                'father_phone' => 'nullable|numeric|digits_between:10,15',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
                'esi' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
                'pf' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
                'aadhaar' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
                'pan' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
                'address_line1' => 'required|string|min:3|max:150',
                'contact_person_name' => 'nullable|string|min:3|max:100',
                'contact_person_phone' => 'nullable|numeric|digits_between:10,15',
                'contact_person_email' => 'nullable|email|max:128',
                'basic_salary'   => 'nullable|numeric|min:0|max:9999999',
                'hra'            => 'nullable|numeric|min:0|max:9999999',
                'allowances'     => 'nullable|numeric|min:0|max:9999999',
                'deductions'     => 'nullable|numeric|min:0|max:9999999',
                'gross_salary'   => 'nullable|numeric|min:0|max:9999999',
                'net_salary'     => 'nullable|numeric|min:0|max:9999999',
                'bank_name'      => 'nullable|string|min:3|max:50',
                'account_number' => [
                    'nullable',
                    'digits_between:9,20',
                    'unique:users,account_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
                'ifsc_code' => [
                    'nullable',
                    'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
                    'unique:users,ifsc_code,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'
                ],
            ];

            $messages =  [
                '*.required' => 'This field is required.',
                '*.unique'   => 'This field already exists.',
                '*.regex' => 'This field is an invalid format.',
                '*.numeric' => 'This field must be a number.',
                'esi.max' => 'ESI file should not be more than 2MB.',
                'pf.max' => 'PF file should not be more than 2MB.',
                'aadhaar.max' => 'Aadhaar file should not be more than 2MB.',
                'pan.max' => 'Pan file should not be more than 2MB.',
                '*.min'      => 'This field must be at least :min characters.',
                '*.max'      => 'This field should not be more than :max characters.',
            ];

            if (!$id) {
                $rules['password'] = 'required|string|min:6|max:15';
            }

            $validated = $request->validate($rules, $messages);

            $dateOfJoining = $request->date_of_joining ? date('Y-m-d', strtotime($request->date_of_joining)) : null;

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'emp_id' => $request->emp_id,
                'service_provider_id' => $request->service_provider_id,
                'department_id' => $request->department_id,
                'operation_stage_id' => $request->operation_stage_id,
                'role_id' => $request->role_id,
                'blood_group_id' => $request->blood_group_id,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'date_of_joining' => $dateOfJoining,
                'father_name' => $request->father_name,
                'father_phone' => $request->father_phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'zipcode' => $request->zipcode,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'contact_person_email' => $request->contact_person_email,
                'basic_salary' => $request->basic_salary,
                'hra' => $request->hra,
                'allowances' => $request->allowances,
                'deductions' => $request->deductions,
                'gross_salary' => $request->gross_salary,
                'net_salary' => $request->net_salary,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'ifsc_code' => $request->ifsc_code,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            if ($id) {
                $oldData = $employee->toArray();
                $data['updated_by'] = auth()->id();
                $employee->update($data);
                $employeeId = $employee->id;

                $role = Role::findById($request->role_id, 'web');
                $employee->syncRoles([$role]);
            } else {
                $data['created_by'] = auth()->id();
                $created = User::create($data);
                $employeeId = $created->id;

                $role = Role::findById($request->role_id, 'web');
                $created->assignRole($role);
            }

            $uploadPath = public_path('uploads/employee/' . $employeeId);
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            if ($request->hasFile('image')) {
                if ($employee && $employee->profile_image) {
                    $oldImagePath = $uploadPath . '/' . $employee->profile_image;
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                $file = $request->file('image');
                $filename = 'profile.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                User::where('id', $employeeId)->update(['profile_image' => $filename]);
            }

            if ($request->hasFile('esi')) {
                if ($employee && $employee->esi_document) {
                    $oldEsiPath = $uploadPath . '/' . $employee->esi_document;
                    if (file_exists($oldEsiPath)) {
                        @unlink($oldEsiPath);
                    }
                }
                $file = $request->file('esi');
                $filename = 'esi_document.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                User::where('id', $employeeId)->update(['esi_document' => $filename]);
            }

            if ($request->hasFile('pf')) {
                if ($employee && $employee->pf_document) {
                    $oldPfPath = $uploadPath . '/' . $employee->pf_document;
                    if (file_exists($oldPfPath)) {
                        @unlink($oldPfPath);
                    }
                }
                $file = $request->file('pf');
                $filename = 'pf_document.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                User::where('id', $employeeId)->update(['pf_document' => $filename]);
            }

            if ($request->hasFile('aadhaar')) {
                if ($employee && $employee->aadhaar_document) {
                    $oldAadhaarPath = $uploadPath . '/' . $employee->aadhaar_document;
                    if (file_exists($oldAadhaarPath)) {
                        @unlink($oldAadhaarPath);
                    }
                }
                $file = $request->file('aadhaar');
                $filename = 'aadhaar_document.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                User::where('id', $employeeId)->update(['aadhaar_document' => $filename]);
            }

            if ($request->hasFile('pan')) {
                if ($employee && $employee->pan_document) {
                    $oldPanPath = $uploadPath . '/' . $employee->pan_document;
                    if (file_exists($oldPanPath)) {
                        @unlink($oldPanPath);
                    }
                }
                $file = $request->file('pan');
                $filename = 'pan_document.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                User::where('id', $employeeId)->update(['pan_document' => $filename]);
            }

            if ($id) {
                $newData = User::find($employeeId)->toArray();
                addLog('update', 'User', 'users', $employeeId, $oldData, $newData);
                $message = 'User updated successfully';
            } else {
                $newEmployee = User::find($employeeId);
                addLog('create', 'User', 'users', $employeeId, null, $newEmployee->toArray());
                $message = 'User added successfully';
            }

            return redirect('employees')->with('success', $message);
        }

        return view('employees.add', compact('employee', 'departments', 'roles', 'bloodGroups', 'states', 'cities', 'serviceProviders', 'operationStages'));
    }


    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details employees')) {
            return unauthorizedRedirect();
        }

        $employee = User::with(['department', 'role', 'bloodGroup', 'state', 'city', 'serviceProvider', 'operationStage'])->findOrFail($id);

        return view('employees.view_details', compact('employee'));
    }
    
    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete employees')) {
            return unauthorizedRedirect();
        }

        if ($id == 1) {
            return redirect('employees')->with('danger', 'The super administrator cannot be deleted.');
        }

        if (JobCardEntry::where('cutting_master_id', $id)->exists()) {
            return redirect('employees')->with('danger', 'Cannot delete This employee is a Cutting Master in Job Cards.');
        }

        if (JobCardOperation::where('employee_id', $id)->orWhere('received_by', $id)->exists()) {
            return redirect('employees')->with('danger', 'Cannot delete This employee has operations recorded in Job Cards.');
        }
        if (JobCardOperation::where('employee_id', $id)->orWhere('received_by', $id)->exists()) {
            return redirect('employees')->with('danger', 'Cannot delete This employee has operations recorded in Job Cards.');
        }

        if (Task::where('issued_to', $id)->exists()) {
            return redirect('employees')->with('danger', 'Cannot delete This employee has tasks issued to them.');
        }

        if (TaskAdjustment::where('approved_by', $id)->exists()) {
            return redirect('employees')->with('danger', 'Cannot delete This employee has approved task adjustments.');
        }

        $employee = User::findOrFail($id);

        $uploadPath = public_path('uploads/employee/' . $id);
        if (File::exists($uploadPath)) {
            File::deleteDirectory($uploadPath);
        }

        addLog('delete', 'User', 'users', $id, $employee->toArray(), null);
        $employee->delete();

        return redirect('employees')->with('success', 'User deleted successfully');
    }

    public function updateStatus($id)
    {
        $employee = User::findOrFail($id);
        $oldData = $employee->toArray();

        $employee->status = request('status');
        $employee->save();

        addLog('update_status', 'User', 'users', $id, $oldData, $employee->fresh()->toArray());

        return response()->json([
            'success' => true,
            'status' => $employee->status,
            'message' => 'Status updated successfully'
        ]);
    }
}
