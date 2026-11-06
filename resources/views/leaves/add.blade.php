@php
$user = auth()->user();
$isSuper = $user && $user->id == 1;
$isAdmin = $isAdmin ?? ($user && ($user->id == 1 || (method_exists($user, 'hasRole') && $user->hasRole(['Admin', 'Super Admin', 'ADMIN', 'SUPER ADMIN', 'HR & ADMIN']))));
@endphp
@extends('layouts.common')
@section('title', 'Add Leave - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <div class="col-lg-12">
            <form action="{{ $leaveEntry ? url('leaves/add/' . $leaveEntry->id) : url('leaves/add') }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $leaveEntry ? 'Edit Leave' : 'Add Leave' }}</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $empValue = old('emp_code', $leaveEntry->emp_code ?? '');
                                    @endphp
                                    @if($isSuper)
                                    <select class="select2 form-select" id="emp_code_display" {{ $leaveEntry ? 'disabled' : '' }} data-placeholder="Select Employee">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->emp_id }}"
                                                 {{ $empValue == $employee->emp_id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->emp_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="emp_code" id="emp_code" value="{{ $empValue }}">
                                    <label for="leave_type">Employee * </label>
                                    @else
                                    <input type="text" class="form-control @error('emp_code') is-invalid @enderror"
                                        value="{{ auth()->user()->name }} ({{ auth()->user()->emp_id }})" readonly>
                                    <input type="hidden" name="emp_code" value="{{ auth()->user()->emp_id }}">
                                    @endif
                                </div>
                                @error('emp_code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $leaveType = old('leave_type', $leaveEntry->leave_type ?? '');
                                    @endphp
                                    <select class="select2 form-select" id="leave_type_display" {{ $leaveEntry ? 'disabled' : '' }} data-placeholder="Select Leave Type">
                                        <option value="">Select Leave Type</option>
                                        @if(isset($leaveTypes) && $leaveTypes->count() > 0)
                                            @foreach($leaveTypes as $lt)
                                                <option value="{{ $lt->leave_type }}" {{ ($leaveType == $lt->leave_type || $leaveType == str_replace(' Leave', '', $lt->leave_type)) ? 'selected' : '' }}>{{ $lt->leave_type }}</option>
                                            @endforeach
                                        @else
                                            <option value="Casual Leave" {{ ($leaveType == 'Casual Leave' || $leaveType == 'Casual') ? 'selected' : '' }}>Casual Leave</option>
                                            <option value="Sick Leave" {{ ($leaveType == 'Sick Leave' || $leaveType == 'Sick') ? 'selected' : '' }}>Sick Leave</option>
                                            <option value="Paid Leave" {{ ($leaveType == 'Paid Leave' || $leaveType == 'Paid') ? 'selected' : '' }}>Paid Leave</option>
                                            <option value="Compensatory Off" {{ $leaveType == 'Compensatory Off' ? 'selected' : '' }}>Compensatory Off</option>
                                            <option value="Maternity Leave" {{ ($leaveType == 'Maternity Leave' || $leaveType == 'Maternity') ? 'selected' : '' }}>Maternity Leave</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="leave_type" id="leave_type" value="{{ $leaveType }}">
                                    <label for="leave_type">Leave Type * </label>
                                </div>
                                @error('leave_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $initialLeaveDays = '';
                                        $initialNoOfDays = '';
                                        if ($leaveEntry) {
                                            $sDate = $leaveEntry->start_date ?? $leaveEntry->leave_date;
                                            $eDate = $leaveEntry->end_date ?? $leaveEntry->leave_date;
                                            if ($sDate && $eDate && $sDate != $eDate) {
                                                $initialLeaveDays = date('d-m-Y', strtotime($sDate)) . ' to ' . date('d-m-Y', strtotime($eDate));
                                            } else if ($sDate) {
                                                $initialLeaveDays = date('d-m-Y', strtotime($sDate));
                                            }
                                            $daysVal = floatval($leaveEntry->no_of_days ?? 1);
                                            $initialNoOfDays = ($daysVal == 1) ? '1 Day' : (rtrim(rtrim(number_format($daysVal, 1), '0'), '.') . ' Days');
                                        }
                                    @endphp
                                    <input type="text" id="leave_days_display" class="form-control @error('leave_days') is-invalid @enderror" placeholder="Enter Leave Days" value="{{ old('leave_days', $initialLeaveDays) }}" {{ $leaveEntry ? 'disabled' : '' }}>
                                    <input type="hidden" name="leave_days" id="leave_days" value="{{ old('leave_days', $initialLeaveDays) }}">
                                    <label for="leave_days">Leave Day(s) * </label>
                                </div>
                                @error('leave_days')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="no_of_days" name="no_of_days" class="form-control bg-light fw-bold text-primary" placeholder="No of Days" value="{{ old('no_of_days', $initialNoOfDays) }}" readonly>
                                    <label for="no_of_days">No of Days</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $statusValue = old('status', $leaveEntry->status ?? 'Pending');
                                        $isFinal = isset($leaveEntry) && in_array($leaveEntry->status, ['Approved', 'Rejected']);
                                    @endphp
                                    <input type="hidden" name="status" id="status" value="{{ $statusValue }}">
                                    <select id="status_display" class="select2 form-select @error('status') is-invalid @enderror" {{ ($isFinal || (!$isAdmin && !isset($leaveEntry))) ? 'disabled' : '' }}>
                                        @if(isset($leaveEntry))
                                            @if($isFinal)
                                                <option value="{{ $statusValue }}" selected>{{ $statusValue }}</option>
                                            @else
                                                <option value="Pending" {{ $statusValue == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                @if($isAdmin)
                                                    <option value="Approved" {{ $statusValue == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                @endif
                                                <option value="Rejected" {{ $statusValue == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                            @endif
                                        @else
                                            <option value="Pending" selected>Pending</option>
                                            @if($isAdmin)
                                                <option value="Approved" {{ $statusValue == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            @endif
                                        @endif
                                    </select>
                                    <label for="approval_status">Approval Status * </label>
                                </div>
                                @if(!$isAdmin)
                                    <small class="text-muted d-block mt-1"><i class="ri ri-information-line me-1"></i>Only Admin can approve leave requests.</small>
                                @endif
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" id="attachment" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <label for="attachment">Attachment File</label>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">PDF, JPG, PNG, DOC (Max: 5MB)</small>
                                    @if(isset($leaveEntry) && !empty($leaveEntry->attachment))
                                        <a href="{{ asset('uploads/leaves/' . $leaveEntry->attachment) }}" target="_blank" class="badge bg-primary text-white text-decoration-none">
                                            <i class="ri ri-attachment-line me-1"></i>View Current File
                                        </a>
                                    @endif
                                </div>
                                @error('attachment')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('reason') is-invalid @enderror" name="reason" id="reason" placeholder="Enter Reason" {{ $leaveEntry ? 'readonly' : '' }}>{{ old('reason', $leaveEntry ? $leaveEntry->reason : '') }}</textarea>
                                    <label for="reason">Reason * </label>
                                </div>
                                @error('reason')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('leaves') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function updateNoOfDays(selectedDates, dateStr) {
        if (!dateStr) {
            $('#no_of_days').val('');
            return;
        }

        if (selectedDates && selectedDates.length === 2) {
            const d1 = new Date(selectedDates[0].getFullYear(), selectedDates[0].getMonth(), selectedDates[0].getDate());
            const d2 = new Date(selectedDates[1].getFullYear(), selectedDates[1].getMonth(), selectedDates[1].getDate());
            const diffTime = Math.abs(d2.getTime() - d1.getTime());
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
            $('#no_of_days').val(diffDays === 1 ? '1 Day' : diffDays + ' Days');
            return;
        }

        if (selectedDates && selectedDates.length === 1 && !dateStr.includes(' to ')) {
            $('#no_of_days').val('1 Day');
            return;
        }

        // Fallback parse range string e.g. "05-11-2026 to 06-11-2026"
        const parts = dateStr.split(' to ');
        if (parts.length === 2) {
            const p1 = parts[0].trim().split('-');
            const p2 = parts[1].trim().split('-');
            if (p1.length === 3 && p2.length === 3) {
                const d1 = new Date(parseInt(p1[2], 10), parseInt(p1[1], 10) - 1, parseInt(p1[0], 10));
                const d2 = new Date(parseInt(p2[2], 10), parseInt(p2[1], 10) - 1, parseInt(p2[0], 10));
                const diffTime = Math.abs(d2.getTime() - d1.getTime());
                const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
                $('#no_of_days').val(diffDays === 1 ? '1 Day' : diffDays + ' Days');
                return;
            }
        } else if (parts.length === 1 && parts[0].trim()) {
            $('#no_of_days').val('1 Day');
            return;
        }

        $('#no_of_days').val('');
    }

    let currentDisabledRanges = @json($existingLeaveRanges ?? []);

    function isRangeOverlappingDisabled(d1, d2, disabledRanges) {
        if (!d1 || !disabledRanges || !disabledRanges.length) return false;
        let start = new Date(d1.getFullYear(), d1.getMonth(), d1.getDate()).getTime();
        let end = d2 ? new Date(d2.getFullYear(), d2.getMonth(), d2.getDate()).getTime() : start;

        for (let range of disabledRanges) {
            let f = new Date(range.from).getTime();
            let t = new Date(range.to).getTime();
            if (start <= t && end >= f) {
                return true;
            }
        }
        return false;
    }

    const leaveDaysPicker = $('#leave_days_display').flatpickr({
        mode: 'range',
        dateFormat: 'd-m-Y',
        allowInput: true,
        minDate: "today",
        disable: currentDisabledRanges,
        onChange: function(selectedDates, dateStr) {
            if (selectedDates && selectedDates.length > 0) {
                let d1 = selectedDates[0];
                let d2 = selectedDates.length === 2 ? selectedDates[1] : d1;
                if (isRangeOverlappingDisabled(d1, d2, currentDisabledRanges)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Leave Already Applied',
                            text: 'This employee has already applied leave for this date. Please select a different date.',
                            confirmButtonColor: '#7367f0'
                        });
                    } else {
                        alert('This employee has already applied leave for this date.');
                    }
                    leaveDaysPicker.clear();
                    $('#leave_days').val('');
                    $('#no_of_days').val('');
                    return;
                }
            }
            $('#leave_days').val(dateStr);
            updateNoOfDays(selectedDates, dateStr);
        },
        onClose: function(selectedDates, dateStr) {
            if (selectedDates && selectedDates.length > 0) {
                let d1 = selectedDates[0];
                let d2 = selectedDates.length === 2 ? selectedDates[1] : d1;
                if (isRangeOverlappingDisabled(d1, d2, currentDisabledRanges)) {
                    leaveDaysPicker.clear();
                    $('#leave_days').val('');
                    $('#no_of_days').val('');
                    return;
                }
            }
            $('#leave_days').val(dateStr);
            updateNoOfDays(selectedDates, dateStr);
        }
    });

    function fetchAndApplyDisabledDates(empCode) {
        if (!empCode) {
            currentDisabledRanges = [];
            leaveDaysPicker.set('disable', []);
            return;
        }

        let editId = "{{ $leaveEntry ? $leaveEntry->id : '' }}";
        $.ajax({
            url: "{{ url('leaves/disabled-dates') }}/" + encodeURIComponent(empCode),
            type: 'GET',
            data: { id: editId },
            success: function(res) {
                if (res.success && res.disabled_ranges) {
                    currentDisabledRanges = res.disabled_ranges;
                    leaveDaysPicker.set('disable', currentDisabledRanges);

                    if (leaveDaysPicker.selectedDates.length > 0) {
                        let d1 = leaveDaysPicker.selectedDates[0];
                        let d2 = leaveDaysPicker.selectedDates.length === 2 ? leaveDaysPicker.selectedDates[1] : d1;
                        if (isRangeOverlappingDisabled(d1, d2, currentDisabledRanges)) {
                            leaveDaysPicker.clear();
                            $('#leave_days').val('');
                            $('#no_of_days').val('');
                        }
                    }
                }
            }
        });
    }

    $(document).on('input change', '#leave_days_display', function () {
        let val = $(this).val();
        $('#leave_days').val(val);
        updateNoOfDays(null, val);
    });

    // Run on initial load
    if ($('#leave_days_display').val()) {
        updateNoOfDays(leaveDaysPicker.selectedDates, $('#leave_days_display').val());
    }

    $(document).on('change', '#status_display', function () {
        $('#status').val($(this).val());
    });
    $(document).on('change', '#emp_code_display', function () {
        let code = $(this).val();
        $('#emp_code').val(code);
        fetchAndApplyDisabledDates(code);
    });
    $(document).on('change', '#leave_type_display', function () {
        $('#leave_type').val($(this).val());
    });
</script>
@endsection
