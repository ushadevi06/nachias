@extends('layouts.common')
@section('title', 'Add Payslip - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Payslip</h4>
                        </div>
                        <div class="row g-4">
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
                                    <input type="text" class="form-control" id="month_year" placeholder="Enter Month / Year" name="month_year">
                                    <label for="month_year">Month / Year * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="basic_salary" placeholder="Enter Basic Salary" name="basic_salary">
                                    <label for="basic_salary">Basic Salary * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="hra" placeholder="Enter HRA" name="hra">
                                    <label for="hra">HRA *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="allowances" placeholder="Enter Allowances" name="allowances">
                                    <label for="allowances">Allowances *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="overtime_amount" placeholder="Enter Overtime Amount" name="overtime_amount">
                                    <label for="overtime_total_amount">Overtime Amount * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bonus_amt" placeholder="Enter Bonus Amount" name="bonus_amt">
                                    <label for="bonus_amt">Bonus Amount *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gross_salary" placeholder="Enter Gross Salary" name="gross_salary" readonly>
                                    <label for="gross_salary">Gross Salary *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="deductions" placeholder="Enter Deductions" name="deductions">
                                    <label for="deductions">Deductions *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="net_salary" placeholder="Enter Net Salary" name="net_salary">
                                    <label for="net_salary">Net Salary *</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Payslip Option *</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payslip_option" id="pdf_option" value="pdf" checked>
                                        <label class="form-check-label" for="pdf_option">
                                            Download PDF
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payslip_option" id="email_option" value="email">
                                        <label class="form-check-label" for="email_option">
                                            Send via Email
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('payslip') }}" class="btn btn-secondary">Cancel</a>
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
        $('#employee').change(function() {
            var employee = $(this).val();
            if (employee == "Ramesh Kumar(EMP001)") {
                $('#basic_salary').val('₹45,000');
                $('#hra').val('₹10,000');
                $('#allowances').val('₹5,000');
                $('#overtime_amount').val('₹3,000');
                $('#bonus_amt').val('₹2,000');
                $('#gross_salary').val('₹65,000');
                $('#deductions').val('₹5,000');
                $('#net_salary').val('₹60,000');
            } else if (employee == "Karthick(EMP002)") {
                $('#basic_salary').val('₹45,000');
                $('#hra').val('₹10,000');
                $('#allowances').val('₹5,000');
                $('#overtime_amount').val('₹1,250');
                $('#bonus_amt').val('₹1,500');
                $('#gross_salary').val('₹52,750');
                $('#deductions').val('₹4,000');
                $('#net_salary').val('₹48,750');
            } else if (employee == "Akash Mehta (EMP003)") {
                $('#basic_salary').val('₹48,000');
                $('#hra').val('₹12,000');
                $('#allowances').val('₹6,000');
                $('#overtime_amount').val('₹1,600');
                $('#bonus_amt').val('₹1,000');
                $('#gross_salary').val('₹68,600');
                $('#deductions').val('₹5,600');
                $('#net_salary').val('₹63,000');
            } else {
                $('#basic_salary').val('');
                $('#hra').val('');
                $('#allowances').val('');
                $('#overtime_amount').val('');
                $('#bonus_amt').val('');
                $('#gross_salary').val('');
                $('#deductions').val('');
                $('#net_salary').val('');
            }
        });
    });
</script>
@endsection