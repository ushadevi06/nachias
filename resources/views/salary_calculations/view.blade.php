@extends('layouts.common')
@section('title', 'Monthly Payroll - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Monthly Payroll</h4>
                <div class="d-flex gap-2 align-items-center">
                    <input type="month" id="export_month" class="form-control" style="width: 180px;" title="Select month to export" max="{{ now()->format('Y-m') }}" value="{{ now()->format('Y-m') }}">
                    <button type="button" id="exportPayroll" class="btn btn-outline-success"><i class="menu-icon icon-base ri ri-file-excel-2-line"></i> Export</button>
                    <button type="button" id="exportPdfPayroll" class="btn btn-outline-danger"><i class="menu-icon icon-base ri ri-file-pdf-2-line"></i> Export PDF</button>
                    <a class="btn btn-primary" href="{{ url('add_monthly_payroll') }}"><i class="menu-icon icon-base ri ri-add-circle-line"></i> Add</a>
                </div>
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-3 col-lg-2 unit">
                                <select name="unit" id="filter_unit" class="form-select select2" data-placeholder="Select Unit">
                                    <option value="">Select Unit</option>
                                    @if(isset($units))
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2 roll_type">
                                <select name="roll_type" id="filter_roll_type" class="form-select select2" data-placeholder="Select Roll Type">
                                    <option value="">Select Roll Type</option>
                                    <option value="On Roll">On Roll</option>
                                    <option value="Off Roll">Off Roll</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <input type="month" id="filter_month" class="form-control" placeholder="Select Month" max="{{ now()->format('Y-m') }}" value="{{ now()->format('Y-m') }}">
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">FILTER</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">RESET</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table nowrap w-100" id="payrollTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>#</th>
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Emp Code</th>
                                    <th>Unit</th>
                                    <th>Gross</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="10" class="text-end">
                                        <button class="btn btn-primary" id="generatePdfBtn">Generate PDF</button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let table = $('#payrollTable').DataTable({
        responsive: true,
        paging: true,
        autoWidth: false,
        searching: true,
        ordering: false,
        info: true,
        lengthChange: true,
        processing: true,
        ajax: {
            url: "{{ url('monthly_payroll') }}",
            data: function (d) {
                d.unit = $('#filter_unit').val();
                d.roll_type = $('#filter_roll_type').val();
                d.status = $('#status').val();
                d.month = $('#filter_month').val();
            }
        },
        columns: [
            {
                data: 'checkbox',
                searchable: false,
                orderable: false
            },
            {
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'month_year'
            },
            {
                data: 'employee_name'
            },
            {
                data: 'emp_code'
            },
            {
                data: 'unit'
            },
            {
                data: 'gross'
            },
            {
                data: 'net_salary'
            },
            {
                data: 'status',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function (settings) {
            let api = this.api();
            let data = api.rows().data();
            if (data.length > 0) {
                $('#generatePdfBtn').show();
                $('#selectAll').show();
            } else {
                $('#generatePdfBtn').hide();
                $('#selectAll').hide();
            }
        }
    });
    $('#filterBtn').click(function () {
        table.ajax.reload();
    });

    $('#resetBtn').click(function () {
        $('#filter_unit').val('').trigger('change');
        $('#filter_roll_type').val('').trigger('change');
        $('#status').val('').trigger('change');
        $('#filter_month').val('');
        table.ajax.reload();
    });
    $(document).on('change', '.salaryStatus', function () {
        let status = $(this).val();
        let salaryId = $(this).data('id');
        let currentDropdown = $(this);
        $.ajax({
            url: "{{ route('update.payroll.status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: salaryId,
                status: status
            },
            success: function (response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        if (table && typeof table.ajax !== 'undefined') {
                            table.ajax.reload(null, false); 
                        } else {
                            location.reload();  
                        }
                    });
                    if(status == 'Paid') {
                        currentDropdown.prop('disabled', true);
                    }
                }
            },
            error: function(xhr) {
                if(xhr.responseJSON.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message,
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });
    $('#selectAll').change(function () {
        $('.salary-checkbox').prop(
            'checked',
            $(this).prop('checked')
        );
    });
    $('#exportPayroll').click(function () {
        let month = $('#export_month').val();
        if (!month) {
            alert('Please select Month/Year to export');
            return;
        }
        window.location.href = "{{ route('monthly-payroll.export') }}?month=" + month;
    });
    $('#exportPdfPayroll').click(function () {
        let month = $('#export_month').val();
        if (!month) {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Please select Month to export PDF',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Open in new tab via dynamic form POST
        let form = $('<form>', {
            action: "{{ route('generate.payslip.pdf') }}",
            method: 'POST',
            target: '_blank'
        }).append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: "{{ csrf_token() }}"
        })).append($('<input>', {
            type: 'hidden',
            name: 'month',
            value: month
        }));

        let unit = $('#filter_unit').val();
        if (unit) {
            form.append($('<input>', { type: 'hidden', name: 'unit', value: unit }));
        }
        let rollType = $('#filter_roll_type').val();
        if (rollType) {
            form.append($('<input>', { type: 'hidden', name: 'roll_type', value: rollType }));
        }

        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(function () {
            table.ajax.reload(null, false);
        }, 1500);
    });
    $('#generatePdfBtn').click(function () {
        let ids = [];
        $('.salary-checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        if(ids.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Please select employees to generate PDF',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Open in new tab via dynamic form POST
        let form = $('<form>', {
            action: "{{ route('generate.payslip.pdf') }}",
            method: 'POST',
            target: '_blank'
        }).append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: "{{ csrf_token() }}"
        }));

        ids.forEach(function(id) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'ids[]',
                value: id
            }));
        });

        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(function () {
            table.ajax.reload(null, false);
            $('#selectAll').prop('checked', false);
        }, 1500);
    });
</script>
@endsection
