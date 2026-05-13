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
                if (count($dates) == 2) {
                    $start = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $end = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();

                    $query->whereBetween('start_date', [$start, $end]);
                }
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
                $action = '
                    <a href="' . url('view_leave/' . $leave->id) . '" class="btn btn-view">
                        <i class="icon-base ri ri-eye-line"></i>
                    </a>
                ';
                if ($leave->status === 'Pending') {
                    $action .= '
                        <a href="' . url('add_leave?id=' . $leave->id) . '" class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>
                    ';
                }
                $data[] = [
                    'DT_RowIndex' => $count++,
                    // 'leave_id' => 'LV-' . $leave->id,
                    'employee' => $leave->employee->name ? $leave->employee->name.' ('.$leave->employee->emp_id.')' : '-',
                    'leave_type' => $leave->leave_type,
                    'start_date' => Carbon::parse($leave->leave_date)->format('d-m-Y'),
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
                'leave_type' => 'required|string|in:Casual,Sick,Paid,Maternity',
                'leave_days' => 'required|string',
                'reason' => 'required|string|min:5|max:500',
                'status' => 'required|in:Pending,Approved,Rejected',
            ];
            $messages = [
                'emp_code.required' => 'This field is required.',
                'leave_type.required' => 'This field is required.',
                'leave_type.in' => 'Invalid leave type selected.',
                'leave_days.required' => 'This field is required.',
                'reason.required' => 'This field is required.',
                'reason.min' => 'Reason must be at least :min characters.',
                'reason.max' => 'Reason cannot exceed :max characters.',
                'status.required' => 'This field is required.',
                'status.in' => 'Invalid approval status selected.',
            ];
            $validated = $request->validate($rules, $messages);
            DB::beginTransaction();
            try {
                if ($id) {
                    $leave = Leave::find($id);
                    $oldData = $leave ? $leave->toArray() : [];
                    $newData = [
                        'status' => $request->status,
                        'updated_by' => auth()->id()
                    ];
                    Leave::where('id', $id)->update([
                        'status' => $request->status,
                        'updated_by' => auth()->id()
                    ]);
                    DB::commit();
                    addLog('update', 'Leave', 'leaves', $id, $oldData, $newData);
                    return redirect('leave')->with('success', 'Leave approved successfully');
                }
                $dates = explode(' to ', $request->leave_days);
                $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]));
                $endDate = isset($dates[1]) 
                    ? Carbon::createFromFormat('d-m-Y', trim($dates[1])) 
                    : $startDate;
                $period = CarbonPeriod::create($startDate, $endDate);
                $newData = [];
                foreach ($period as $date) {
                    $exists = Leave::where('emp_code', $request->emp_code)
                        ->where('leave_date', $date->format('Y-m-d'))
                        ->whereIn('status', ['Pending', 'Approved'])
                        ->exists();

                    if ($exists) {
                        return back()->withInput()->withErrors([
                            'leave_days' => 'Leave already exists for ' . $date->format('d-m-Y') . ' (Pending/Approved)'
                        ]);
                    }
                    $leave = Leave::create([
                        'emp_code'   => $request->emp_code,
                        'leave_type' => $request->leave_type,
                        'leave_date' => $date->format('Y-m-d'),
                        'reason'     => $request->reason,
                        'status'     => $request->status,
                        'created_by' => auth()->id()
                    ]);
                    $newData[] = $leave->toArray();
                }

                DB::commit();
                addLog('create', 'Leave', 'leaves', $request->id, null, $newData);
                return redirect('leave')->with('success', 'Leave applied successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'error' => 'Failed: ' . $e->getMessage()
                ]);
            }
        }
        $leaveEntry = $leaveEntry ?? null;
        // dd($leaveEntry);
        $employees = User::where('id','!=',1)->where('status','Active')->get();
        return view('leaves/add', compact('employees', 'leaveEntry'));
    } 
    public function view($id)
    {
        $leave = Leave::with('employee')->findOrFail($id);
        return view('leaves/view_details', compact('leave'));
    }
} 
