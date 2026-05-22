@extends('layouts.common')
@section('title', 'Payroll Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Payroll Reports</h4>
                {{-- <a class="btn btn-primary" href="{{ url('add_payroll_report') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a> --}}
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table nowrap w-100" id="payrollTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Report Name</th>
                                    <th>Report Type</th>
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Gross Salary</th>
                                    <th>Net Salary</th>
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
    </div>
</div>
@endsection
@section('scripts')
<script>
let table = $('#payrollTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ url('payroll_reports') }}",
        data: function (d) {
            d.month = $('#month').val();
            d.year = $('#year').val();
        }
    },
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'report_name' },
        { data: 'report_type' },
        { data: 'month_year' },
        { data: 'employee' },
        { data: 'department' },
        { data: 'gross_salary' },
        { data: 'net_salary' },
        { data: 'status', orderable: false, searchable: false },
        { data: 'action', orderable: false, searchable: false }
    ]
});
$('#filterBtn').click(function () {
    table.ajax.reload();
});
$('#resetBtn').click(function () {
    $('#month').val('');
    $('#year').val('');
    table.ajax.reload();
});
</script>
@endsection
