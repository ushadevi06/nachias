@extends('layouts.common')
@section('title', 'Monthly Payroll - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Monthly Payroll</h4>
                <div class="d-flex gap-2">
                    <input type="month"
                        id="filter_month"
                        class="form-control">

                    <button type="button"
                            id="exportPayroll"
                            class="btn btn-outline-success">
                        <i class="ri ri-file-excel-2-line"></i> Export
                    </button>
                    <a class="btn btn-primary"
                    href="{{ url('add_monthly_payroll') }}">
                        <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                    </a>
                </div>
            </div>
            @if(request()->success)
                <div class="alert alert-success alert-dismissible fade show"
                    role="alert">
                    {{ request()->success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table nowrap w-100" id="payrollTable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>#</th>
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Gross</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-end">
                                        <button class="btn btn-primary"
                                                id="generatePdfBtn">
                                            Generate PDF
                                        </button>
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
                d.status = $('#status').val();
            }
        },
        columns: [
            {
                data: 'checkbox',
                searchable: false
            },
            {
                data: 'DT_RowIndex'
            },
            {
                data: 'month_year'
            },
            {
                data: 'employee'
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
        ]
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
        let month = $('#filter_month').val();
        if (!month) {
            alert('Please select Month/Year');
            return;
        }
        window.location.href = "{{ route('monthly-payroll.export') }}?month=" + month;
    });
    $('#generatePdfBtn').click(function () {
        let button = $(this);
        let ids = [];
        $('.salary-checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        if(ids.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Please select employees',
                confirmButtonText: 'OK'
            });
            return;
        }
        button.prop('disabled', true);
        let originalText = button.html();
        button.html('Processing...');
        $.ajax({
            url: "{{ route('generate.payslip.pdf') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: ids
            },
            success: function(response) {
                if(response.success) {
                    button.html('Generated');
                    setTimeout(function () {
                        window.location.reload();
                    }, 800);
                }
            },
            error: function(xhr) {
                button.prop('disabled', false);
                button.html(originalText);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Something went wrong',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
@endsection
