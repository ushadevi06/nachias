@extends('layouts.common')
@section('title', 'Add Payroll Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Payroll Report</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="report_name" placeholder="Enter Report Name" name="report_name">
                                    <label for="report_name">Report Name * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="report_type" class="select2 form-select" data-placeholder="Select Report Type">
                                        <option value="">Select Report Type</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Annual">Annual</option>
                                        <option value="Dept-wise">Dept-wise</option>
                                        <option value="Employee-wise">Employee-wise</option>
                                    </select>
                                    <label for="report_type">Report Type * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="export" class="select2 form-select" data-placeholder="Select Export Option">
                                        <option value="">Select Export Option</option>
                                        <option value="Excel">Excel</option>
                                        <option value="PDF">PDF</option>
                                        <option value="CSV">CSV</option>
                                    </select>
                                    <label for="export_option">Export Option * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="month_year" placeholder="Enter Month / Year" name="month_year">
                                    <label for="month_year">Month / Year * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="department" class="select2 form-select" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                        <option value="Production">Production</option>
                                        <option value="HR">HR</option>
                                    </select>
                                    <label for="department">Department * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="employee" class="select2 form-select" data-placeholder="Select Employee">
                                        <option value="">Select Employee</option>
                                        <option value="Ramesh Kumar(EMP001)">Ramesh Kumar(EMP001)</option>
                                        <option value="Karthick(EMP002)">Karthick(EMP002)</option>
                                        <option value="Akash Mehta (EMP003)	">Akash Mehta (EMP003) </option>
                                    </select>
                                    <label for="leave_type">Employee * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Processed">Processed</option>
                                    </select>
                                    <label for="export_option">Status * </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="leave_type">Include Fields/Columns * </label><br>
                                <div class="form-check form-check-inline mb-2 mt-5">
                                    <input class="form-check-input" type="checkbox" value="1" name="employee_id" id="employee_id" />
                                    <label class="form-check-label" for="employee_id"> Employee ID </label>
                                </div>
                                <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="employee_name" id="employee_name" />
                                    <label class="form-check-label" for="employee_name"> Employee Name </label>
                                </div>
                                <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="department" id="department" />
                                    <label class="form-check-label" for="department"> Department </label>
                                </div>
                                <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="gross_check" id="gross_check" />
                                    <label class="form-check-label" for="gross_check"> Gross </label>
                                </div>
                                <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" name="net_check" id="net_check" />
                                    <label class="form-check-label" for="net_check"> Gross </label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('payroll_reports') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script>
    $(document).ready(function() {
        flatpickr("#month_year", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light"
                })
            ]
        });
    });
</script>
@endsection