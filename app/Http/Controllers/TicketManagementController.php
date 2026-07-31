<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\OperationStage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TicketManagementController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view ticket-management')) {
            return unauthorizedRedirect(); 
        }

        if ($request->ajax()) {
            $query = Ticket::with(['category', 'requester', 'assignedTo'])->latest();

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('priority', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q2) use ($search) {
                            $q2->where('category_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('requester', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('assignedTo', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->has('filter_priority') && !empty($request->filter_priority)) {
                $query->where('priority', $request->filter_priority);
            }

            if ($request->has('filter_assigned_to') && !empty($request->filter_assigned_to)) {
                $query->where('assigned_to_id', $request->filter_assigned_to);
            }

            if ($request->has('filter_date_range') && !empty($request->filter_date_range)) {
                $dates = explode(' to ', $request->filter_date_range);
                if (count($dates) == 2) {
                    $startDate = trim($dates[0]) . ' 00:00:00';
                    $endDate = trim($dates[1]) . ' 23:59:59';
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = trim($dates[0]) . ' 00:00:00';
                    $endDate = trim($dates[0]) . ' 23:59:59';
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $tickets = $query->get();
            $data = [];
            $i = $start + 1;

            foreach ($tickets as $row) {
                $status = '
                <label class="switch switch-success switch-lg">
                    <input type="checkbox" class="switch-input ticket-status-toggle"
                        data-id="' . $row->id . '" ' . ($row->status == "Active" ? "checked" : "") . '>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                    </span>
                </label>
                <div class="status_msg_' . $row->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('edit tickets')) {
                    $action .= '<a href="' . url('ticket_management/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete tickets')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('ticket_management/delete/' . $row->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }
                $action .= '</div>';

                $priorityBadge = match($row->priority) {
                    'Low' => '<span class="badge bg-success">Low</span>',
                    'Medium' => '<span class="badge bg-warning">Medium</span>',
                    'High' => '<span class="badge bg-orange" style="background-color: #fd7e14 !important;">High</span>',
                    'Critical' => '<span class="badge bg-danger">Critical</span>',
                    default => '<span class="badge bg-secondary">' . $row->priority . '</span>'
                };

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'ticket_no'   => $row->ticket_no,
                    'subject'     => '<strong>' . $row->subject . '</strong><br><small class="text-muted">' . ($row->requester->name ?? 'N/A') . ' (' . ($row->category->category_name ?? 'N/A') . ')</small>',
                    'assigned_to' => ($row->assignedTo->name ?? 'Unassigned') . ($row->assignedTo?->emp_id ? ' (' . $row->assignedTo->emp_id . ')' : ''),
                    'priority'    => $priorityBadge,
                    'created_at'  => $row->created_at->format('d-M-Y'),
                    'status'      => $status,
                    'action'      => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        $users = User::active()->get();
        $assignees = User::active()->where('id', '!=', 1)->get();
        return view('ticket_management.index', compact('users', 'assignees'));
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit ticket-management')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create ticket-management')) {
                return unauthorizedRedirect();
            }
        }
        $ticket = $id ? Ticket::findOrFail($id) : null;

        if ($request->isMethod('post')) {
            $rules = [
                'subject'            => 'required|string|max:150',
                'description'        => 'required|string|regex:/^[^<>]*$/',
                'ticket_cat_id'      => 'required|exists:ticket_categories,id',
                'priority'           => 'required|in:Low,Medium,High,Critical',
                'department_id'      => 'required|exists:departments,id',
                'operation_stage_id' => 'nullable|exists:operation_stages,id',
                'assigned_to_id'     => 'nullable|exists:users,id',
                'due_date'           => 'nullable|date',
                'status'             => 'required',
                'attachment'         => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx,webp',
                'remarks'            => 'nullable|string|regex:/^[^<>]*$/',
                'resolution_details' => 'nullable|string|regex:/^[^<>]*$/',
                'resolved_date'      => 'nullable|date',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists'   => 'The selected option is invalid.',
                'attachment.max' => 'Uploaded file cannot exceed 2MB.',
                '*.max'      => 'This field should not exceed :max characters.',
                '*.mimes'    => 'Upload a valid file (e.g.,.jpg, .png, .jpeg, .pdf,.doc,.docx,.webp).',
                '*.file'     => 'The uploaded file is invalid.',
                'regex' => 'This field is an invalid format',
            ];

            $request->validate($rules, $messages);

            $data = $request->except(['_token', 'attachment']);
            
            if ($request->due_date) $data['due_date'] = Carbon::parse($request->due_date)->format('Y-m-d');
            if ($request->resolved_date) $data['resolved_date'] = Carbon::parse($request->resolved_date)->format('Y-m-d');

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $destinationPath = public_path('uploads/tickets');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $filename);
                $data['attachment'] = 'uploads/tickets/' . $filename;

                if ($ticket && $ticket->attachment && file_exists(public_path($ticket->attachment))) {
                    @unlink(public_path($ticket->attachment));
                }
            }

            if ($id) {
                $oldData = $ticket->toArray();
                $data['updated_by'] = auth()->id();
                $ticket->update($data);
                if (function_exists('addLog')) {
                    addLog('update', 'Ticket', 'tickets', $id, $oldData, $ticket->fresh()->toArray());
                }
                $msg = 'Ticket updated successfully';
            } else {
                $data['created_by'] = auth()->id();
                $data['requester_id'] = auth()->id();
                
                $year = date('Y');
                $lastTicket = Ticket::withTrashed()->where('ticket_no', 'like', "TLT-$year-%")->orderBy('id', 'desc')->first();
                $lastNum = $lastTicket ? (int) substr($lastTicket->ticket_no, -3) : 0;
                $data['ticket_no'] = 'TLT-' . $year . '-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);

                $newTicket = Ticket::create($data);
                if (function_exists('addLog')) {
                    addLog('create', 'Ticket', 'tickets', $newTicket->id, null, $newTicket->toArray());
                }
                $msg = 'Ticket added successfully';
            }

            return redirect('ticket_management')->with('success', $msg);
        }

        $users = User::active()->orderBy('id','desc')->get();
        $assignees = User::active()->where('id', '!=', 1)->orderBy('id','desc')->get();
        $departments = Department::active()->orderBy('id','desc')->get();
        $categories = TicketCategory::active()->orderBy('id','desc')->get();
        $operationStages = OperationStage::active()->orderBy('id','desc')->get();
        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        
        if ($id) {
            $ticketNo = $ticket->ticket_no;
        } else {
            $year = date('Y');
            $lastTicket = Ticket::withTrashed()->where('ticket_no', 'like', "TLT-$year-%")->orderBy('id', 'desc')->first();
            $lastNum = $lastTicket ? (int) substr($lastTicket->ticket_no, -3) : 0;
            $ticketNo = 'TLT-' . $year . '-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('ticket_management.add', compact('ticket', 'users', 'assignees', 'departments', 'categories', 'priorities', 'operationStages', 'ticketNo'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete ticket-management')) {
            return unauthorizedRedirect();
        }
        $ticket = Ticket::findOrFail($id);
        $oldData = $ticket->toArray();
        $ticket->delete();
        if (function_exists('addLog')) {
            addLog('delete', 'Ticket', 'tickets', $id, $oldData, null);
        }
        return redirect('ticket_management')->with('success', 'Ticket deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $oldData = $ticket->toArray();
        $ticket->status = $request->status;
        $ticket->save();
        $newData = $ticket->toArray();
        if (function_exists('addLog')) {
            addLog('update_status', 'Ticket Status', 'tickets', $ticket->id, $oldData, $newData);
        }
        return response()->json([
            'success' => true,
            'status'  => $ticket->status
        ]);
    }
}
