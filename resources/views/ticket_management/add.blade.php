@extends('layouts.common')
@section('title', ($ticket ? 'Edit Ticket' : 'Raise Ticket') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="card-header-box mb-4">
                        <h4>{{ $ticket ? 'Edit' : 'Add' }} Ticket [{{ $ticketNo }}]</h4>
                    </div>
                    <form action="{{ url('ticket_management/add' . ($ticket ? '/' . $ticket->id : '')) }}" method="POST" class="common-form" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Section 1: Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Basic Information</h6></div>
                            <div class="col-md-6 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="subject" placeholder="Ticket Title" name="subject" value="{{ old('subject', $ticket->subject ?? '') }}">
                                    <label for="subject">Ticket Title <span class="text-danger">*</span></label>
                                    @error('subject') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="ticket_cat_id" id="ticket_cat_id" class="select2 form-select" data-placeholder="Select Category">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" {{ (old('ticket_cat_id', $ticket->ticket_cat_id ?? '') == $c->id) ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="ticket_cat_id">Category <span class="text-danger">*</span></label>
                                    @error('ticket_cat_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="priority" id="priority" class="select2 form-select" data-placeholder="Select Priority">
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $p)
                                            <option value="{{ $p }}" {{ (old('priority', $ticket->priority ?? '') == $p) ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    @error('priority') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Requester & Location -->
                        <div class="row mb-4">
                            <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Requester & Location</h6></div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="requester_id" id="requester_id" class="select2 form-select" data-placeholder="Select Requester">
                                        <option value="">Select Requester</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('requester_id', $ticket->requester_id ?? (auth()->id())) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="requester_id">Created By <span class="text-danger">*</span></label>
                                    @error('requester_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="department_id" id="department_id" class="select2 form-select" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ (old('department_id', $ticket->department_id ?? '') == $dept->id) ? 'selected' : '' }}>{{ $dept->department }}</option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    @error('department_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="operation_stage_id" id="operation_stage_id" class="select2 form-select" data-placeholder="Select Operation Stage">
                                        <option value="">Select Operation Stage</option>
                                        @foreach($operationStages as $stg)
                                            <option value="{{ $stg->id }}" {{ (old('operation_stage_id', $ticket->operation_stage_id ?? '') == $stg->id) ? 'selected' : '' }}>{{ $stg->operation_stage_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="operation_stage_id">Operation Stage</label>
                                    @error('operation_stage_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Assignment & Progress -->
                        <div class="row mb-4">
                            <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Assignment & Progress</h6></div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="assigned_to_id" id="assigned_to_id" class="select2 form-select" data-placeholder="Assign To">
                                        <option value="">Select Assignee</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('assigned_to_id', $ticket->assigned_to_id ?? '') == $user->id) ? 'selected' : '' }}>{{ $user->name }} {{ $user->emp_id ? '[' . $user->emp_id . ']' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <label for="assigned_to_id">Assigned To</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="due_date" name="due_date" value="{{ old('due_date', $ticket->due_date ?? '') }}" placeholder="Select Due Date">
                                    <label for="due_date">Due Date</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select">
                                        <option value="Active" {{ (old('status', $ticket->status ?? '') == 'Active') ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ (old('status', $ticket->status ?? '') == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Detailed Description & Attachments</h6></div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="description" name="description" placeholder="Description" maxlength="255">{{ old('description', $ticket->description ?? '') }}</textarea>
                                    <label for="description">Issue Description <span class="text-danger">*</span></label>
                                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="file" class="form-control" id="attachment" name="attachment">
                                    <label for="attachment">Attachment Upload</label>
                                    <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                    @if($ticket && $ticket->attachment)
                                        <div class="mt-2"><a href="{{ url($ticket->attachment) }}" target="_blank"><i class="ri ri-image-line"></i> View</a></div>
                                    @endif
                                    @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks" maxlength="255">{{ old('remarks', $ticket->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                    @error('remarks') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Resolution Info (Visible on Edit) -->
                        <div class="row mb-5">
                            <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Resolution Details</h6></div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="resolution_details" name="resolution_details" placeholder="How was it resolved?" maxlength="255">{{ old('resolution_details', $ticket->resolution_details ?? '') }}</textarea>
                                    <label for="resolution_details">Resolution Details</label>
                                    @error('resolution_details') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="resolved_date" name="resolved_date" value="{{ old('resolved_date', $ticket->resolved_date ?? '') }}" placeholder="Select Resolved Date">
                                    <label for="resolved_date">Resolved Date</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 text-end">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">Submit</button>
                            <a href="{{ url('ticket_management') }}" class="btn btn-secondary px-5 shadow-sm ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
