@extends('layouts.common')
@section('title', 'Add Overtime / Bouns - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Overtime / Bouns</h4>
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
                                    <input type="text" class="form-control" id="overtime_hrs" placeholder="Enter Overtime Hours" name="overtime_hrs">
                                    <label for="overtime_hrs">Overtime Hours * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="overtime_rate" placeholder="Enter Overtime Rate" name="overtime_rate">
                                    <label for="overtime_rate">Overtime Rate * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="overtime_tamount" placeholder="Enter Overtime Amount" name="overtime_amount">
                                    <label for="overtime_total_amount">Overtime Amount * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Bonus Type">
                                        <option value="">Select Bonus Type</option>
                                        <option value="Performance">Performance</option>
                                        <option value="Festival">Festival</option>
                                        <option value="Production">Production</option>
                                    </select>
                                    <label for="bonus_type">Bonus Type * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bonus_amt" placeholder="Enter Bonus Amount" name="bonus_amt">
                                    <label for="bonus_amt">Bonus Amount * </label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('overtime') }}" class="btn btn-secondary">Cancel</a>
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
        $('.start_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.end_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
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