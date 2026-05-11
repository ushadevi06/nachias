@extends('layouts.common')
@section('title', 'Add Overtime - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form" autocomplete="off">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Department Wise OT & Late Entry</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" id="ot_date" name="ot_date" value="{{ date('Y-m-d') }}">
                                    <label for="ot_date">OT Date *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="department" class="select2 form-select" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                        <option value="Checking">Checking</option>
                                        <option value="Collar Fuse">Collar Fuse</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Dispatch">Dispatch</option>
                                        <option value="House Keeping">House Keeping</option>
                                    </select>
                                    <label for="department">Department *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ot_day" value="{{ \Carbon\Carbon::now()->format('l') }}" readonly>
                                    <label for="ot_day">Day</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="d-flex gap-2 h-100 align-items-end">
                                    <button type="button" class="btn btn-primary w-100" id="loadEmployeesButton">Load Employees</button>
                                    <button type="button" class="btn btn-secondary w-100" id="resetOtButton">Reset</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info py-2 mb-0">
                                    Select OT date and department to load employees. Enter OT hours and late hours for each employee in one screen, similar to OT note entry.
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="selected_period" value="{{ \Carbon\Carbon::now()->format('F Y') }}" readonly>
                                    <label for="selected_period">Period</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="department_summary" value="No department selected" readonly>
                                    <label for="department_summary">Department Summary</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="total_ot_hours" value="0.00" readonly>
                                    <label for="total_ot_hours">Total OT Hours</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="otEntryTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 70px;">#</th>
                                                <th>Emp No</th>
                                                <th>Employee Name</th>
                                                <th>Department</th>
                                                <th style="width: 140px;">OT Hours</th>
                                                <th style="width: 140px;">Late Hours</th>
                                                <th style="width: 170px;">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="otEntryBody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Choose a department and click <strong>Load Employees</strong> to begin OT entry.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="overtime_rate" placeholder="Enter Overtime Rate" name="overtime_rate" value="300">
                                    <label for="overtime_rate">Overtime Rate *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="bonus_type" class="select2 form-select" data-placeholder="Select Bonus Type">
                                        <option value="">Select Bonus Type</option>
                                        <option value="Performance">Performance</option>
                                        <option value="Festival">Festival</option>
                                        <option value="Production">Production</option>
                                        <option value="Nil" selected>Nil</option>
                                    </select>
                                    <label for="bonus_type">Bonus Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bonus_amt" placeholder="Enter Bonus Amount" name="bonus_amt" value="0">
                                    <label for="bonus_amt">Bonus Amount</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="overtime_tamount" placeholder="Enter Overtime Amount" name="overtime_amount" readonly>
                                    <label for="overtime_total_amount">OT Amount</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Save OT Entry</button>
                                <a href="{{ url('overtime') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const department = document.getElementById('department');
        const otDate = document.getElementById('ot_date');
        const otDay = document.getElementById('ot_day');
        const selectedPeriod = document.getElementById('selected_period');
        const departmentSummary = document.getElementById('department_summary');
        const totalOtHours = document.getElementById('total_ot_hours');
        const overtimeRate = document.getElementById('overtime_rate');
        const overtimeTotalAmount = document.getElementById('overtime_tamount');
        const loadEmployeesButton = document.getElementById('loadEmployeesButton');
        const resetOtButton = document.getElementById('resetOtButton');
        const otEntryBody = document.getElementById('otEntryBody');
        const employeesByDepartment = {
            'Checking': [{ empNo: '10', name: 'RENUKADEVI.R' }, { empNo: '70', name: 'ANITHA.M' }, { empNo: '71', name: 'SRIDHAR.K' }, { empNo: '73', name: 'ALAGAMMAL.M' }, { empNo: '205', name: 'CHITRA.M' }],
            'Collar Fuse': [{ empNo: '301', name: 'KALAIVANI.G' }, { empNo: '302', name: 'MAGESH.K' }],
            'Cutting': [{ empNo: '401', name: 'SARAVANAN.P' }, { empNo: '402', name: 'PRAKASH.R' }],
            'Dispatch': [{ empNo: '501', name: 'ASHA.V' }, { empNo: '502', name: 'RAHUL.B' }],
            'House Keeping': [{ empNo: '601', name: 'SHOBANA.R' }, { empNo: '602', name: 'ANNAPOORNA.B' }]
        };

        function updateDateMeta() {
            const chosenDate = new Date(otDate.value);
            if (Number.isNaN(chosenDate.getTime())) return;
            otDay.value = chosenDate.toLocaleDateString('en-US', { weekday: 'long' });
            selectedPeriod.value = chosenDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        }

        function updateTotals() {
            const rate = parseFloat(overtimeRate.value || '0');
            let totalHours = 0;
            document.querySelectorAll('.ot-hours-input').forEach(input => {
                totalHours += parseFloat(input.value || '0');
            });
            totalOtHours.value = totalHours.toFixed(2);
            overtimeTotalAmount.value = (totalHours * rate).toFixed(2);
        }

        function buildRows(records) {
            if (!records.length) {
                otEntryBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No employees found for the selected department.</td></tr>';
                departmentSummary.value = 'No employees available';
                updateTotals();
                return;
            }

            otEntryBody.innerHTML = records.map((employee, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${employee.empNo}</td>
                    <td>${employee.name}</td>
                    <td>${department.value}</td>
                    <td><input type="number" step="0.25" min="0" class="form-control ot-hours-input" value="0"></td>
                    <td><input type="number" step="0.25" min="0" class="form-control late-hours-input" value="0"></td>
                    <td><input type="text" class="form-control" value="" placeholder="Remarks"></td>
                </tr>
            `).join('');

            departmentSummary.value = `${department.value} - ${records.length} employee(s) loaded`;
            document.querySelectorAll('.ot-hours-input').forEach(input => {
                input.addEventListener('input', updateTotals);
            });
            updateTotals();
        }

        loadEmployeesButton.addEventListener('click', function() {
            const selectedDepartment = department.value;
            if (!selectedDepartment) {
                departmentSummary.value = 'Please select a department first';
                return;
            }
            buildRows(employeesByDepartment[selectedDepartment] || []);
        });

        resetOtButton.addEventListener('click', function() {
            department.value = '';
            otDate.value = new Date().toISOString().slice(0, 10);
            updateDateMeta();
            departmentSummary.value = 'No department selected';
            otEntryBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Choose a department and click <strong>Load Employees</strong> to begin OT entry.</td></tr>';
            totalOtHours.value = '0.00';
            overtimeTotalAmount.value = '0.00';
        });

        otDate.addEventListener('change', updateDateMeta);
        overtimeRate.addEventListener('input', updateTotals);
        updateDateMeta();
        updateTotals();
    });
</script>
@endsection
