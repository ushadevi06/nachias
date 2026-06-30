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
                                <input type="month"
                                    class="form-control"
                                    id="filter_month"
                                    value="{{ date('Y-m') }}">

                                <label for="filter_month">Month & Year</label>
                            </div>
                        </div>
                        {{-- <div class="col-md-6 col-xl-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="filter_to_date" >
                                <label for="filter_to_date">Date To</label>
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-6 col-xl-2">
                            <div class="form-floating form-floating-outline">
                                <select id="filter_department" class="select2 form-select">
                                    <option value="">All Departments</option>
                                    <option value="Checking">Checking</option>
                                    <option value="Collar Fuse">Collar Fuse</option>
                                    <option value="Cutting">Cutting</option>
                                    <option value="Dispatch">Dispatch</option>
                                    <option value="House Keeping">House Keeping</option>
                                </select>
                                <label for="filter_department">Department</label>
                            </div>
                        </div> --}}
                        <div class="col-md-12 col-xl-4">
                            <button type="button" class="btn btn-primary w-100" id="applyOtFilters">Apply Filters</button>
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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Overtime</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="ot_attendance_id">
                        <div class="mb-3">
                            <label>In Time</label>
                            <input
                                type="time"
                                class="form-control"
                                id="ot_in_time">
                            <span class="text-danger small error-text" id="ot_in_time_error"></span>
                        </div>
                        <div class="mb-3">
                            <label>Out Time</label>
                            <input
                                type="time"
                                class="form-control"
                                id="ot_out_time">
                            <span class="text-danger small error-text" id="ot_out_time_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary" id="saveOtBtn">
                            Save
                        </button>
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
                // d.from_date = $('#filter_from_date').val();
                // d.to_date = $('#filter_to_date').val();
                // d.department = $('#filter_department').val();
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
            { data: 'action', orderable: false, searchable: false }
        ]
    });
    $('#applyOtFilters').click(function () {
        table.ajax.reload();
    });
    const fromDatePicker = $('#filter_from_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });
    const toDatePicker = $('#filter_to_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });
    $(document).on('click', '.editOtBtn', function () {
        $('#ot_attendance_id').val($(this).data('id'));
        $('#ot_in_time').val($(this).data('in'));
        $('#ot_out_time').val($(this).data('out'));
        $('#editOtModal').modal('show');
    });
    $('#saveOtBtn').on('click', function () {
        $('.error-text').text('');
        $('#ot_in_time').removeClass('is-invalid');
        $('#ot_out_time').removeClass('is-invalid');
        let inTime = $('#ot_in_time').val().trim();
        let outTime = $('#ot_out_time').val().trim();
        let hasError = false;
        if (!inTime) {
            $('#ot_in_time_error')
                .text('In time is required');
            $('#ot_in_time')
                .addClass('is-invalid');
            hasError = true;
        }

        if (inTime && outTime && outTime < inTime) {
            $('#ot_out_time_error')
                .text(
                    'Out time must be greater than in time'
                );
            $('#ot_out_time')
                .addClass('is-invalid');
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
                status: status
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
                            $('#' + key + '_error')
                                .text(value[0]);
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
