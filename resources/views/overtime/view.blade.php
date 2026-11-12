@extends('layouts.common')
@section('title', 'Overtime - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Overtime</h4>
                {{-- <a class="btn btn-primary" href="{{ url('add_overtime') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a> --}}
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-6 col-xl-3">
                            <div class="form-floating form-floating-outline">
                                <input type="month" class="form-control" id="filter_month" value="{{ date('Y-m') }}">
                                <label for="filter_month">Month & Year</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="form-floating form-floating-outline">
                                <select id="filter_status" class="select2 form-select" data-placeholder="Select Status">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Not Approved">Not Approved</option>
                                </select>
                                <label for="filter_status">Status</label>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-4">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="applyOtFilters">Apply</button>
                                <button type="button" class="btn btn-secondary" id="resetOtFilters">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                        <div>
                            <div class="small text-muted">Filtered Range</div>
                            <div class="fw-semibold" id="otFilterSummary"></div>
                        </div>
                        <div class="small text-muted" id="otFilterCount"></div>
                    </div>
                    <div class="card-datatable">
                        <div id="statusMessage" class="alert d-none mb-4" role="alert"></div>
                        <table class="table nowrap w-100" id="otTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    {{-- <th>Department</th> --}}
                                    <th>Employee</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>OT Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editOtModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Overtime</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="ot_attendance_id">
                        <div class="mb-3">
                            <label class="form-label" for="ot_in_time">In Time</label>
                            <input type="time" class="form-control" id="ot_in_time">
                            <span class="text-danger small error-text" id="ot_in_time_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ot_out_time">Out Time</label>
                            <input type="time" class="form-control" id="ot_out_time">
                            <span class="text-danger small error-text" id="ot_out_time_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="ot_hours_select">OT Hours</label>
                            <select class="form-select select2" id="ot_hours_select" data-placeholder="Select OT Hours">
                                <option value="">Auto (Calculate from Punch Time)</option>
                                <option value="0.5">30 mins (0.5 hr)</option>
                                <option value="1">1 hr</option>
                                <option value="1.5">1 hr 30 mins (1.5 hr)</option>
                                <option value="2">2 hr</option>
                                <option value="2.5">2 hr 30 mins (2.5 hr)</option>
                                <option value="3">3 hr</option>
                                <option value="3.5">3 hr 30 mins (3.5 hr)</option>
                                <option value="4">4 hr</option>
                                <option value="4.5">4 hr 30 mins (4.5 hr)</option>
                                <option value="5">5 hr</option>
                                <option value="5.5">5 hr 30 mins (5.5 hr)</option>
                                <option value="6">6 hr</option>
                                <option value="6.5">6 hr 30 mins (6.5 hr)</option>
                                <option value="7">7 hr</option>
                                <option value="7.5">7 hr 30 mins (7.5 hr)</option>
                                <option value="8">8 hr</option>
                                <option value="8.5">8 hr 30 mins (8.5 hr)</option>
                                <option value="9">9 hr</option>
                                <option value="9.5">9 hr 30 mins (9.5 hr)</option>
                                <option value="10">10 hr</option>
                                <option value="11">11 hr</option>
                                <option value="12">12 hr</option>
                            </select>
                            <span class="text-danger small error-text" id="ot_hours_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveOtBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(function () {
    const statusMessage = document.getElementById('statusMessage');
    let table = $('#otTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('overtime') }}",
            data: function (d) {
                d.month = $('#filter_month').val();
                d.status = $('#filter_status').val();
            },
            dataSrc: function (json) {
                let month = $('#filter_month').val();
                if (month) {
                    let dateObj = new Date(month + '-01');
                    let formattedMonth = dateObj.toLocaleString('default', {
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#otFilterSummary').text(formattedMonth);
                } else {
                    $('#otFilterSummary').text('All Months');
                }
                $('#otFilterCount').text(
                    json.recordsFiltered + ' record(s) found'
                );
                return json.data;
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date' },
            { data: 'employee' },
            { data: 'in_time' },
            { data: 'out_time' },
            { data: 'ot_hours' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ],
        drawCallback: function () {
            $('.ot-status-select').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }
    });
    $('#applyOtFilters').click(function () {
        table.ajax.reload();
    });
    $('#resetOtFilters').click(function () {
        const defaultMonth = "{{ date('Y-m') }}";
        $('#filter_month').val(defaultMonth);
        $('#filter_status').val('').trigger('change.select2');
        $('input[type="search"]').val('');
        table.search('').ajax.reload();
    });
    const fromDatePicker = $('#filter_from_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });
    const toDatePicker = $('#filter_to_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });
    $('#ot_hours_select').select2({
        dropdownParent: $('#editOtModal'),
        width: '100%'
    });

    $(document).on('click', '.editOtBtn', function () {
        $('#ot_attendance_id').val($(this).data('id'));
        $('#ot_in_time').val($(this).data('in'));
        $('#ot_out_time').val($(this).data('out'));
        let otHoursVal = $(this).data('ot-hours');
        let selectedVal = (otHoursVal !== undefined && otHoursVal !== null && otHoursVal !== '') ? String(otHoursVal) : '';
        $('#ot_hours_select').val(selectedVal).trigger('change');
        $('#editOtModal').modal('show');
    });
    $('#saveOtBtn').on('click', function () {
        $('.error-text').text('');
        $('#ot_in_time').removeClass('is-invalid');
        $('#ot_out_time').removeClass('is-invalid');
        let inTime = $('#ot_in_time').val().trim();
        let outTime = $('#ot_out_time').val().trim();
        let otHours = $('#ot_hours_select').val();
        let hasError = false;
        if (!inTime) {
            $('#ot_in_time_error').text('In time is required');
            $('#ot_in_time').addClass('is-invalid');
            hasError = true;
        }

        if (inTime && outTime && outTime < inTime) {
            $('#ot_out_time_error').text('Out time must be greater than in time');
            $('#ot_out_time').addClass('is-invalid');
            hasError = true;
        }
        if (hasError) {
            return;
        }
        fetch(`${APP_URL}/update-overtime`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
            },
            body: JSON.stringify({
                id: $('#ot_attendance_id').val(),
                in_time: inTime,
                out_time: outTime,
                ot_hours: otHours
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                $('#editOtModal').modal('hide');
                showStatus('success', res.message);
                $('#otTable')
                    .DataTable()
                    .ajax.reload(null, false);
            } else {
                if (res.errors) {
                    $.each(
                        res.errors,
                        function (key, value) {
                            $('#' + key + '_error').text(value[0]);
                        }
                    );
                }
                showStatus(
                    'danger',
                    res.message
                );
            }
        })
        .catch(err => {
            console.error(err);
            showStatus(
                'danger',
                'Update failed'
            );
        });
    });
    function showStatus(type, message) {
        statusMessage.className = 'alert alert-' + type + ' mb-4';
        statusMessage.textContent = message;
        statusMessage.classList.remove('d-none');
        setTimeout(() => {
            statusMessage.classList.add('d-none');
        }, 3000);
    }

    $(document).on('change', '.ot-status-select', function () {
        let select = $(this);
        let id = select.data('id');
        let newStatus = select.val();
        let prevStatus = select.data('prev');

        if (!newStatus || newStatus === prevStatus) {
            return;
        }

        select.prop('disabled', true);

        $.ajax({
            url: "{{ url('update-overtime-status') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id,
                ot_status: newStatus
            },
            success: function (res) {
                select.prop('disabled', false);
                if (res.success) {
                    select.data('prev', newStatus);

                    // Option 1: Once Approved, permanently remove 'Pending' option from this dropdown
                    if (newStatus === 'Approved') {
                        select.find('option[value="Pending"]').remove();
                        select.trigger('change.select2');
                    }

                    select.closest('tr').find('.editOtBtn').data('ot-status', newStatus);
                    showStatus('success', res.message || 'Status updated successfully');
                } else {
                    select.val(prevStatus).trigger('change.select2');
                    showStatus('danger', res.message || 'Failed to update status');
                }
            },
            error: function (xhr) {
                select.prop('disabled', false);
                select.val(prevStatus).trigger('change.select2');
                let err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update status';
                showStatus('danger', err);
            }
        });
    });
    $('#filter_from_date').on('change', function () {
        let fromDate = fromDatePicker.parseDate($(this).val(), "d-m-Y");
        if (fromDate) {
            toDatePicker.set('minDate', fromDate);
            let currentToDate = toDatePicker.selectedDates[0];
            if (currentToDate && currentToDate < fromDate) {
                toDatePicker.setDate(fromDate);
            }
        }
    });
});
</script>
@endsection
