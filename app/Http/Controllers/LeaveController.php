<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Leave::with('employee')->orderBy('id', 'desc');
            if (auth()->user()->id != 1) {
                $query->where('emp_code', auth()->user()->emp_id);
            }
            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }
            if (!empty($request->date_range)) {
                $dates = explode(' to ', $request->date_range);
                $start = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end = isset($dates[1]) ? Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d') : $start;

                $query->whereRaw('COALESCE(start_date, leave_date) <= ?', [$end])
                      ->whereRaw('COALESCE(end_date, leave_date) >= ?', [$start]);
            }
            $totalRecords = $query->count();
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('leave_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->select(DB::raw(1))
                            ->from('users')
                            ->whereRaw("
                                CONVERT(users.emp_id USING utf8mb4)
                                =
                                CONVERT(leaves.emp_code USING utf8mb4)
                            ")
                            ->where(function ($q2) use ($search) {
                                $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('emp_id', 'like', "%{$search}%");
                            });
                    });
                });
            }
            $filteredRecords = $query->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            if ($length != -1) {
                $query->skip($start)->take($length);
            }
            $leaves = $query->get();
            $data = [];
            $count = $start + 1;
            foreach ($leaves as $leave) {
                $statusBadge = '';
                switch ($leave->status) {
                    case 'Approved':
                        $statusBadge = '<span class="badge bg-success">Approved</span>';
                        break;

                    case 'Rejected':
                        $statusBadge = '<span class="badge bg-danger">Rejected</span>';
                        break;

                    default:
                        $statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                        break;
                }
                $action = '<div class="d-inline-block text-nowrap">';
                if(auth()->id()== 1 || auth()->user()->can('view_details manage-leaves')){
                $action .= '
                    <a href="' . url('view_details/' . $leave->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>
                ';
                }
                if(auth()->id()== 1 || auth()->user()->can('edit manage-leaves')){
                    if ($leave->status === 'Pending') {
                        $action .= '
                            <a href="' . url('leaves/add/' . $leave->id) . '" class="btn btn-edit">
                                <i class="icon-base ri ri-edit-box-line"></i>
                            </a>
                        ';
                    }
                }
                $action .= '</div>';
                $startDate = $leave->start_date ?? $leave->leave_date;
                $endDate = $leave->end_date ?? $leave->leave_date;

                $startFormatted = $startDate ? Carbon::parse($startDate)->format('d-m-Y') : '-';
                $endFormatted = $endDate ? Carbon::parse($endDate)->format('d-m-Y') : $startFormatted;

                $dateDisplay = ($startFormatted === $endFormatted) 
                    ? $startFormatted 
                    : ($startFormatted . ' to ' . $endFormatted);

                $daysCount = floatval($leave->no_of_days ?? 1);
                $daysBadge = '<span class="badge bg-label-info fw-semibold">' . ($daysCount == 1 ? '1 Day' : (rtrim(rtrim(number_format($daysCount, 1), '0'), '.') . ' Days')) . '</span>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    // 'leave_id' => 'LV-' . $leave->id,
                    'employee' => $leave->employee ? $leave->employee->name.' ('.$leave->employee->emp_id.')' : '-',
                    'leave_type' => $leave->leave_type,
                    'start_date' => $dateDisplay,
                    'days' => $daysBadge,
                    // 'end_date' => Carbon::parse($leave->end_date)->format('d-m-Y'),
                    'status' => $statusBadge,
                    'action' => $action,
                ];
            }
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        return view('leaves.view');
    }
    public function add($id = null)
    {
        $request = request();
        $id = $id ?? request()->get('id');
        $user = auth()->user();
        $isAdmin = $user && ($user->id == 1 || (method_exists($user, 'hasRole') && $user->hasRole(['Admin', 'Super Admin', 'ADMIN', 'SUPER ADMIN', 'HR & ADMIN'])));
        // dd($request->all());
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit manage-leaves')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create manage-leaves')) {
                return unauthorizedRedirect();
            }
        }
        $leaveEntry = null;
        if ($id) {
            $leaveEntry = Leave::with('employee')->findOrFail($id);
        }
        if ($request->isMethod('post')) {
            $rules = [
                'emp_code' => 'required',
                'leave_type' => 'required|string',
                'leave_days' => 'required|string',
                'reason' => 'required|string|min:5|max:500',
                'status' => 'required|in:Pending,Approved,Rejected',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ];
            $messages = [
                'emp_code.required' => 'This field is required.',
                'leave_type.required' => 'This field is required.',
                'leave_days.required' => 'This field is required.',
                'reason.required' => 'This field is required.',
                'reason.min' => 'Reason must be at least :min characters.',
                'reason.max' => 'Reason cannot exceed :max characters.',
                'status.required' => 'This field is required.',
                'status.in' => 'Invalid approval status selected.',
                'attachment.mimes' => 'Attachment must be a file of type: pdf, jpg, jpeg, png, doc, docx.',
                'attachment.max' => 'Attachment size must not exceed 5MB.',
            ];
            $validated = $request->validate($rules, $messages);
            if (!$isAdmin && $request->status === 'Approved') {
                return back()->withInput()->withErrors([
                    'status' => 'Only Admin has permission to set Approved status.'
                ]);
            }
            DB::beginTransaction();
            try {
                $attachmentName = null;
                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment');
                    $attachmentName = 'leave_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $uploadPath = public_path('uploads/leaves');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $attachmentName);
                }

                if ($id) {
                    $leave = Leave::find($id);
                    $oldData = $leave ? $leave->toArray() : [];
                    $updateData = [
                        'status' => $request->status,
                        'updated_by' => auth()->id()
                    ];
                    if ($attachmentName && \Illuminate\Support\Facades\Schema::hasColumn('leaves', 'attachment')) {
                        $updateData['attachment'] = $attachmentName;
                    }
                    Leave::where('id', $id)->update($updateData);
                    DB::commit();
                    addLog('update', 'Leave', 'leaves', $id, $oldData, $updateData);
                    return redirect('leaves')->with('success', 'Leave updated successfully');
                }
                $dates = explode(' to ', $request->leave_days);
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                $endDate = isset($dates[1]) 
                    ? Carbon::createFromFormat('d-m-Y', trim($dates[1]))->startOfDay() 
                    : $startDate->copy();

                if ($endDate->lt($startDate)) {
                    $temp = $startDate;
                    $startDate = $endDate;
                    $endDate = $temp;
                }

                $diffDays = $startDate->diffInDays($endDate) + 1;
                $startStr = $startDate->format('Y-m-d');
                $endStr = $endDate->format('Y-m-d');

                $exists = Leave::where('emp_code', $request->emp_code)
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->where(function ($q) use ($startStr, $endStr) {
                        $q->whereRaw('COALESCE(start_date, leave_date) <= ?', [$endStr])
                          ->whereRaw('COALESCE(end_date, leave_date) >= ?', [$startStr]);
                    })
                    ->first();

                if ($exists) {
                    $existingFrom = $exists->start_date ? Carbon::parse($exists->start_date)->format('d-m-Y') : Carbon::parse($exists->leave_date)->format('d-m-Y');
                    $existingTo = $exists->end_date ? Carbon::parse($exists->end_date)->format('d-m-Y') : $existingFrom;
                    $rangeStr = ($existingFrom === $existingTo) ? $existingFrom : "{$existingFrom} to {$existingTo}";
                    return back()->withInput()->withErrors([
                        'leave_days' => "Leave already exists for this employee for {$rangeStr} ({$exists->status})"
                    ]);
                }

                $leaveData = [
                    'emp_code'   => $request->emp_code,
                    'leave_type' => $request->leave_type,
                    'start_date' => $startStr,
                    'end_date'   => $endStr,
                    'no_of_days' => $diffDays,
                    'leave_date' => $startStr,
                    'reason'     => $request->reason,
                    'status'     => ($isAdmin && $request->status === 'Approved') ? 'Approved' : 'Pending',
                    'created_by' => auth()->id()
                ];
                if ($attachmentName && \Illuminate\Support\Facades\Schema::hasColumn('leaves', 'attachment')) {
                    $leaveData['attachment'] = $attachmentName;
                }
                $leave = Leave::create($leaveData);

                DB::commit();
                addLog('create', 'Leave', 'leaves', $leave->id, null, $leave->toArray());
                return redirect('leaves')->with('success', 'Leave applied successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'error' => 'Failed: ' . $e->getMessage()
                ]);
            }
        }
        $leaveEntry = $leaveEntry ?? null;
        // dd($leaveEntry);
        $leaveTypes = \App\Models\LeaveType::active()->get();
        $employees = User::where('id','!=',1)->where('status','Active')->get();

        $initialEmpCode = old('emp_code', $leaveEntry->emp_code ?? ($user->id == 1 ? '' : $user->emp_id));
        $existingLeaveRanges = [];
        if (!empty($initialEmpCode)) {
            $empLeaves = Leave::where('emp_code', $initialEmpCode)
                ->whereIn('status', ['Pending', 'Approved'])
                ->when($id, function($q) use ($id) {
                    $q->where('id', '!=', $id);
                })
                ->get();
            foreach ($empLeaves as $l) {
                $s = $l->start_date ?? $l->leave_date;
                $e = $l->end_date ?? $l->leave_date;
                if ($s) {
                    $existingLeaveRanges[] = [
                        'from' => Carbon::parse($s)->format('Y-m-d'),
                        'to'   => Carbon::parse($e ?? $s)->format('Y-m-d')
                    ];
                }
            }
        }

        return view('leaves/add', compact('employees', 'leaveEntry', 'leaveTypes', 'isAdmin', 'existingLeaveRanges'));
    } 

    public function getDisabledDates($emp_code)
    {
        $id = request()->get('id');
        $leaves = Leave::where('emp_code', $emp_code)
            ->whereIn('status', ['Pending', 'Approved'])
            ->when($id, function($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->get();

        $disabledRanges = [];
        foreach ($leaves as $leave) {
            $s = $leave->start_date ?? $leave->leave_date;
            $e = $leave->end_date ?? $leave->leave_date;
            if ($s) {
                $disabledRanges[] = [
                    'from' => Carbon::parse($s)->format('Y-m-d'),
                    'to'   => Carbon::parse($e ?? $s)->format('Y-m-d')
                ];
            }
        }

        return response()->json([
            'success' => true,
            'disabled_ranges' => $disabledRanges
        ]);
    } 
    public function view($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('leaves/view_details', compact('leave'));
    }
} 
