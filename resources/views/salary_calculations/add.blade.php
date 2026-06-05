@extends('layouts.common')
@section('title', 'Monthly Payroll - ' . env('WEBSITE_NAME'))
<style>
#paginationContainer {
    flex-wrap: wrap;
    gap: 5px;
}
#paginationContainer .btn {
    min-width: 38px;
}
</style>
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form" autocomplete="off">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Monthly Payroll</h4>
                        </div>
                        <div class="card">
                            <div class="d-flex justify-content-end mb-2">
                                <input type="text"
                                    id="employeeSearch"
                                    class="form-control"
                                    placeholder="Search Emp Name / Emp Code"
                                    style="width:250px;">
                            </div>
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Monthly Salary Generation</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold text-nowrap text-success">
                                        Total Days: <span id="total_days">30</span>
                                    </div>
                                    <input type="month"
                                        id="salary_month"
                                        class="form-control"
                                        max="{{ now()->subMonth()->format('Y-m') }}"
                                        value="{{ isset($salary) ? $salary->salary_year.'-'.date('m', strtotime($salary->salary_month)) : '' }}">
                                    @if(!isset($salary))
                                        <button type="button"
                                                id="generateSalary"
                                                class="btn btn-primary">
                                            Generate
                                        </button>
                                    @endif
                                    <a href="{{ url()->previous() }}"
                                    class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="80">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            class="form-check-input"
                                                            id="checkAllEmployees">

                                                        <label class="form-check-label"
                                                            for="checkAllEmployees">
                                                            Check All
                                                        </label>
                                                    </div>
                                                </th>
                                                <th>Employee</th>
                                                <th>Working Days</th>
                                                <th>Lop Days</th>
                                                <th>Holidays</th>
                                                <th>Fixed Gross</th>
                                                <th>Basic Pay</th>
                                                <th>HRA</th>
                                                <th>DA</th>
                                                <th>OA</th>
                                                <th>OT Hrs</th>
                                                <th>OT Amount</th>
                                                <th>Inctv</th>
                                                <th>Misc</th>
                                                <th>Bus Fare</th>
                                                <th>PF</th>
                                                <th>ESI</th>
                                                <th>Other Deduction</th>
                                                <th>Salary Advance</th>
                                                <th>Late Hrs</th>
                                                <th>Late Fine</th>
                                                <th>Gross Pay</th>
                                                <th>Total Deduction</th>
                                                <th>Net Pay</th>
                                            </tr>
                                        </thead>
                                        <tbody id="salaryTableBody">
                                            @if(isset($salary))
                                                <tr class="salary-row">
                                                    <input type="hidden" class="salary_id" value="{{ $salary->id }}">
                                                    <input type="hidden" class="employee_id" value="{{ $salary->employee_id }}">
                                                    <input type="hidden" class="total_days" value="{{ $salary->total_days }}">
                                                    {{-- Checkbox --}}
                                                    <td>
                                                        <input type="checkbox" class="form-check-input employee-check">
                                                    </td>
                                                    {{-- Employee --}}
                                                    <td>
                                                        {{ $salary->name }}
                                                        <br>
                                                        <span class="badge bg-primary mt-1">{{ $salary->emp_id }}</span>
                                                    </td>
                                                    {{-- Working Days --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control present_days"
                                                            value="{{ $salary->present_days }}">
                                                    </td>
                                                    {{-- Lop Days --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control absent_days"
                                                            value="{{ $salary->absent_days }}">
                                                    </td>
                                                    {{-- Holidays --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control holidays"
                                                            value="{{ $salary->holidays }}">
                                                    </td>
                                                    {{-- Fixed Gross --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control fixed_gross"
                                                            value="{{ $salary->fixed_gross }}">
                                                    </td>
                                                    {{-- Basic Pay --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control basic_salary"
                                                            value="{{ $salary->basic_salary }}">
                                                    </td>
                                                    {{-- HRA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control hra"
                                                            value="{{ $salary->hra }}">
                                                    </td>
                                                    {{-- DA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control da"
                                                            value="{{ $salary->da }}">
                                                    </td>
                                                    {{-- OA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control oa"
                                                            value="{{ $salary->oa }}">
                                                    </td>
                                                    {{-- OT Hours --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control ot_hours"
                                                            value="{{ $salary->ot_hours }}">
                                                    </td>
                                                    {{-- OT Amount --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control overtime_amount"
                                                            value="{{ $salary->overtime_amount }}">
                                                    </td>
                                                    {{-- Incentive --}}
                                                    <td>
                                                        <input type="number" class="form-control incentive"
                                                            value="{{ $salary->incentive ?? 0 }}">
                                                    </td>
                                                    {{-- Misc --}}
                                                    <td>
                                                        <input type="number" class="form-control misc"
                                                            value="{{ $salary->misc_amount ?? 0 }}">
                                                    </td>
                                                    {{-- Bus Fare --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control bus_fare"
                                                            value="{{ $salary->bus_fare ?? 0 }}">
                                                    </td>
                                                    {{-- PF --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control pf"
                                                            value="{{ $salary->pf ?? 0 }}">
                                                    </td>
                                                    {{-- ESI --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control esi"
                                                            value="{{ $salary->esi ?? 0 }}">
                                                    </td>
                                                    {{-- Other Deduction --}}
                                                    <td>
                                                        <input type="number" class="form-control other_deduction"
                                                            value="{{ $salary->other_deduction ?? 0 }}">
                                                    </td>
                                                    {{-- Salary Advance --}}
                                                    <td>
                                                        <input type="number" class="form-control salary_advance"
                                                            value="{{ $salary->salary_advance ?? 0 }}">
                                                    </td>
                                                    {{-- Late Hours --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control late_hours"
                                                            value="{{ $salary->late_hours ?? 0 }}">
                                                    </td>
                                                    {{-- Late Fine --}}
                                                    <td>
                                                        <input type="number" class="form-control late_fine"
                                                            value="{{ $salary->late_fine ?? 0 }}" readonly>
                                                    </td>
                                                    {{-- Gross Pay --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control gross_salary"
                                                            value="{{ $salary->gross_salary }}">
                                                    </td>
                                                    {{-- Total Deduction --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control total_deduction"
                                                            value="{{ $salary->total_deduction ?? 0 }}">
                                                    </td>
                                                    {{-- Net Pay --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control net_salary"
                                                            value="{{ $salary->net_salary }}">
                                                    </td>
                                                </tr>
                                                @endif
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center mt-3" id="paginationContainer"></div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="button"
                                            class="btn btn-success"
                                            id="savePayroll">
                                        {{ isset($salary) ? 'Update Payroll' : 'Save Payroll' }}
                                    </button>
                                </div>
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
    $(document).ready(function () {
        let allPayrollData = [];
        let currentPage = 1;
        const perPage = 10;
        $('#generateSalary').click(function () {
            let month = $('#salary_month').val();
            if(month == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select month',
                    confirmButtonText: 'OK'
                });
                return;
            }
            // Button loading state
            let btn = $(this);
            btn.prop('disabled', true);
            btn.html(`
                <span class="spinner-border spinner-border-sm me-1"></span>
                Generating...
            `);
            $.ajax({
                url: "{{ route('generate.payroll') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    month: month
                },
                success: function (response) {
                    allPayrollData = response.payroll;
                    currentPage = 1;
                    renderPayrollPage();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Something went wrong',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function () {
                    // Restore button
                    btn.prop('disabled', false);
                    btn.html('Generate');
                }
            });
        });
        function renderPayrollPage()
        {
            let start = (currentPage - 1) * perPage;
            let end = start + perPage;
            let pageData = allPayrollData.slice(start, end);
            let rows = '';
            $.each(pageData, function(index, item) {
                rows += `
                <tr class="salary-row" data-index="${start + index}">
                    <input type="hidden"
                        class="employee_id"
                        value="${item.employee_id}">
                    <input type="hidden"
                        class="total_days"
                        value="${item.total_days}">
                    <!-- Checkbox -->
                    <td class="text-center">
                        <input type="checkbox"
                            class="employee_checkbox"
                            ${item.is_selected ?? true ? 'checked' : ''}>
                    </td>

                    <!-- Employee -->
                    <td>
                        ${item.employee_name}
                        <br>
                        <span class="badge bg-primary mt-1">${item.emp_code}</span>
                    </td>

                    <!-- Working Days -->
                    <td>
                        <input type="text"
                            class="form-control present_days"
                            value="${item.present_days}" readonly>
                    </td>

                    <!-- Lop Days -->
                    <td>
                        <input type="text"
                            class="form-control absent_days"
                            value="${item.absent_days}" readonly>
                    </td>

                    <!-- Holidays -->
                    <td>
                        <input type="text"
                            class="form-control holidays"
                            value="${item.holidays}" readonly>
                    </td>

                    <!-- Fixed Gross -->
                    <td>
                        <input type="number"
                            class="form-control fixed_gross"
                            value="${item.fixed_gross}" readonly>
                    </td>

                    <!-- Basic Pay -->
                    <td>
                        <input type="number"
                            class="form-control basic_salary"
                            value="${item.basic_salary}" readonly>
                    </td>

                    <!-- HRA -->
                    <td>
                        <input type="number"
                            class="form-control hra"
                            value="${item.hra}" readonly>
                    </td>

                    <!-- DA -->
                    <td>
                        <input type="number"
                            class="form-control da"
                            value="${item.da}" readonly>
                    </td>

                    <!-- OA -->
                    <td>
                        <input type="number"
                            class="form-control oa"
                            value="${item.oa}" readonly>
                    </td>

                    <!-- OT Hours -->
                    <td>
                        <input type="number"
                            class="form-control ot_hours"
                            value="${item.ot_hours}">
                    </td>

                    <!-- OT Amount -->
                    <td>
                        <input type="text"
                            class="form-control overtime_amount"
                            value="${item.overtime_amount}" readonly>
                    </td>

                    <!-- Incentive -->
                    <td>
                        <input type="number"
                            class="form-control incentive"
                            value="${item.incentive}">
                    </td>

                    <!-- Misc -->
                    <td>
                        <input type="number"
                            class="form-control misc"
                            value="${item.misc}">
                    </td>

                    <!-- Bus Fare -->
                    <td>
                        <input type="number"
                            class="form-control bus_fare"
                            value="${item.bus_fare}" readonly>
                    </td>

                    <!-- PF -->
                    <td>
                        <input type="text"
                            class="form-control pf"
                            value="${item.pf}" readonly>
                    </td>

                    <!-- ESI -->
                    <td>
                        <input type="text"
                            class="form-control esi"
                            value="${item.esi}" readonly>
                    </td>

                    <!-- Other Deduction -->
                    <td>
                        <input type="number"
                            class="form-control other_deduction"
                            value="${item.other_deduction ?? 0}">
                    </td>

                    <!-- Salary Advance -->
                    <td>
                        <input type="number"
                            class="form-control salary_advance"
                            value="${item.salary_advance}">
                    </td>
                    <!-- Late Hours -->
                    <td>
                        <input type="number"
                            class="form-control late_hours"
                            value="${item.late_hours ?? 0}">
                    </td>

                    <!-- Late Fine -->
                    <td>
                        <input type="number"
                            class="form-control late_fine"
                            value="${item.late_fine ?? 0}" readonly>
                    </td>

                    <!-- Gross Pay -->
                    <td>
                        <input type="text"
                            class="form-control gross_salary"
                            value="${item.gross_salary}" readonly>
                    </td>

                    <!-- Total Deduction -->
                    <td>
                        <input type="text"
                            class="form-control total_deduction"
                            value="${item.total_deduction}" readonly>
                    </td>

                    <!-- Net Pay -->
                    <td>
                        <input type="text"
                            class="form-control net_salary"
                            value="${item.net_salary}" readonly>
                    </td>

                </tr>
                `;
            });
            $('#salaryTableBody').html(rows);
            renderPagination();
        }
        function renderPagination() {
            let totalPages = Math.ceil(allPayrollData.length / perPage);
            let html = '';
            html += `
                <button type="button"
                    class="btn btn-sm btn-light mx-1 page-btn"
                    data-page="${currentPage - 1}"
                    ${currentPage == 1 ? 'disabled' : ''}>
                    Previous
                </button>
            `;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);
            if(startPage > 1) {
                html += `
                    <button type="button"
                        class="btn btn-sm btn-light mx-1 page-btn"
                        data-page="1">
                        1
                    </button>
                `;
                if(startPage > 2) {
                    html += `<span class="mx-1 align-self-center">...</span>`;
                }
            }
            for(let i = startPage; i <= endPage; i++) {
                html += `
                    <button type="button"
                        class="btn btn-sm ${i == currentPage ? 'btn-primary' : 'btn-light'} mx-1 page-btn"
                        data-page="${i}">
                        ${i}
                    </button>
                `;
            }
            if(endPage < totalPages) {
                if(endPage < totalPages - 1) {
                    html += `<span class="mx-1 align-self-center">...</span>`;
                }
                html += `
                    <button type="button"
                        class="btn btn-sm btn-light mx-1 page-btn"
                        data-page="${totalPages}">
                        ${totalPages}
                    </button>
                `;
            }
            html += `
                <button type="button"
                    class="btn btn-sm btn-light mx-1 page-btn"
                    data-page="${currentPage + 1}"
                    ${currentPage == totalPages ? 'disabled' : ''}>
                    Next
                </button>
            `;
            $('#paginationContainer').html(html);
        }
        $(document).on('click', '.page-btn', function () {
            saveCurrentPageData();
            currentPage = $(this).data('page');
            renderPayrollPage();
        });
        function saveCurrentPageData() {
            $('.salary-row').each(function () {
                let row = $(this);
                let index = row.data('index');
                allPayrollData[index].is_selected = row.find('.employee_checkbox').is(':checked');
                allPayrollData[index].basic_salary =
                    parseFloat(row.find('.basic_salary').val()) || 0;
                allPayrollData[index].hra =
                    parseFloat(row.find('.hra').val()) || 0;
                allPayrollData[index].da =
                    parseFloat(row.find('.da').val()) || 0;
                allPayrollData[index].oa =
                    parseFloat(row.find('.oa').val()) || 0;
                allPayrollData[index].misc =
                    parseFloat(row.find('.misc').val()) || 0;
                allPayrollData[index].incentive =
                    parseFloat(row.find('.incentive').val()) || 0;
                allPayrollData[index].salary_advance =
                    parseFloat(row.find('.salary_advance').val()) || 0;
                allPayrollData[index].net_salary =
                    parseFloat(row.find('.net_salary').val()) || 0;
            });
        }
        $(document).on('change', '#checkAllEmployees', function () {
            let checked = $(this).is(':checked');
            $('.employee_checkbox').prop('checked', checked);
            $('.salary-row').each(function () {
                let row = $(this);
                let index = row.data('index');
                allPayrollData[index].is_selected = checked;
            });
        });
        $(document).on('change', '.employee_checkbox', function () {
            let row = $(this).closest('.salary-row');
            let index = row.data('index');
            allPayrollData[index].is_selected =
                $(this).is(':checked');
        });
        $(document).on('click', '#savePayroll', function () {
            saveCurrentPageData();
            let month = $('#salary_month').val();
            if(month == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select salary month',
                    confirmButtonText: 'OK'
                });
                return;
            }
            let hasError = false;
            $.each(allPayrollData, function(index, item) {
                if(!item.is_selected) {
                    return true;
                }
                if (
                    parseFloat(item.basic_salary) <= 0 ||
                    parseFloat(item.hra) <= 0 ||
                    parseFloat(item.da) <= 0 ||
                    parseFloat(item.gross_salary) <= 0 ||
                    parseFloat(item.net_salary) <= 0
                ) {
                    hasError = true;
                    return false;
                }
            });
            if(hasError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Basic Pay, HRA, DA, Gross Salary and Net Salary cannot be empty or zero.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            let selectedPayroll = allPayrollData.filter(x => x.is_selected);
            if(selectedPayroll.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select at least one employee.',
                    confirmButtonText: 'OK'
                });

                return;
            }
            $.ajax({
                url: "{{ route('save.payroll') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    month: month,
                    payroll: selectedPayroll
                },
                success: function (response) {
                    if(response.success) {
                        window.location.href = "{{ url('monthly_payroll') }}";
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: xhr.responseJSON.message ?? 'Something went wrong',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        function calculateSalary(row) {
            let fixed_gross = parseFloat(row.find('.fixed_gross').val()) || 0;
            let basic = parseFloat(row.find('.basic_salary').val()) || 0;
            let incentive = parseFloat(row.find('.incentive').val()) || 0;
            let misc = parseFloat(row.find('.misc').val()) || 0;
            let busFare = parseFloat(row.find('.bus_fare').val()) || 0;
            let otHours = parseFloat(row.find('.ot_hours').val()) || 0;
            let salaryAdvance = parseFloat(row.find('.salary_advance').val()) || 0;
            let totalDays = parseFloat(row.find('.total_days').val()) || 1;
            let lateFine = parseFloat(row.find('.late_fine').val()) || 0;
            let otherDeduction = parseFloat(row.find('.other_deduction').val()) || 0;
            let hra = parseFloat(row.find('.hra').val()) || 0;;
            let da  = parseFloat(row.find('.da').val()) || 0;
            let oa  = parseFloat(row.find('.oa').val()) || 0;
            row.find('.hra').val(hra.toFixed(2));
            row.find('.da').val(da.toFixed(2));
            row.find('.oa').val(oa.toFixed(2));
            let perDaySalary = totalDays > 0 ? fixed_gross / totalDays : 0;
            let perHourSalary = perDaySalary / 8;
            let otAmount = perHourSalary * otHours;
            row.find('.overtime_amount').val(otAmount.toFixed(2));
            let absentDays = parseFloat(row.find('.absent_days').val()) || 0;
            let lopAmount = perDaySalary * absentDays;
            let grossSalary = fixed_gross - lopAmount;
            let totalEarnings =
                grossSalary +
                incentive +
                misc +
                busFare +
                otAmount;
            row.find('.gross_salary').val(totalEarnings.toFixed(2));
            let pfWage = basic + da;
            let pf = (pfWage * 12) / 100;
            row.find('.pf').val(pf.toFixed(2));
            let esi = 0;
            if (pfWage <= 21000) {
                esi = (pfWage * 0.75) / 100;
            } else {
                esi = (21000 * 0.75) / 100;
            }
            row.find('.esi').val(esi.toFixed(2));
            let totalDeduction =
                pf +
                esi +
                salaryAdvance +
                lateFine +
                otherDeduction;

            row.find('.total_deduction').val(totalDeduction.toFixed(2));
            let net = totalEarnings - totalDeduction;
            row.find('.net_salary').val(net.toFixed(2));
        }
        $(document).on('keyup change','.basic_salary, .misc, .incentive, .bus_fare, .ot_hours',function () {
            let row = $(this).closest('tr');
            calculateSalary(row);
        });
        $(document).on('keyup change','.salary_advance',function () {
            let row = $(this).closest('tr');
            let gross =
                parseFloat(row.find('.gross_salary').val()) || 0;
            let pf =
                parseFloat(row.find('.pf').val()) || 0;
            let esi =
                parseFloat(row.find('.esi').val()) || 0;
            let ot =
                parseFloat(row.find('.overtime_amount').val()) || 0;
            let advance =
                parseFloat($(this).val()) || 0;
            let lateFine = parseFloat(row.find('.late_fine').val()) || 0;
            let otherDeduction = parseFloat(row.find('.other_deduction').val()) || 0;
            let net =
                gross -
                pf -
                esi -
                advance -
                lateFine -
                otherDeduction;
            row.find('.net_salary').val(net.toFixed(2));
        });
        $(document).on('keyup change', '.late_hours', function () {
            let row = $(this).closest('tr');
            let lateHours = parseFloat($(this).val()) || 0;
            let fixed_gross = parseFloat(row.find('.fixed_gross').val()) || 0;
            let totalDays = parseFloat(row.find('.total_days').val()) || 1;
            let perDaySalary = fixed_gross / totalDays;
            let perHourSalary = perDaySalary / 8;
            let lateFine = lateHours * perHourSalary;
            let pf = parseFloat(row.find('.pf').val()) || 0;
            let esi = parseFloat(row.find('.esi').val()) || 0;
            let ot = parseFloat(row.find('.overtime_amount').val()) || 0;
            let advance = parseFloat(row.find('.salary_advance').val()) || 0;
            let otherDeduction = parseFloat(row.find('.other_deduction').val()) || 0;
            let net = fixed_gross - pf - esi - advance - lateFine - otherDeduction;
            row.find('.late_fine').val(lateFine.toFixed(2));
            row.find('.net_salary').val(net.toFixed(2));
        });
        function updateTotalDays() {
            let monthValue = document.getElementById('salary_month').value;

            if (!monthValue) {
                document.getElementById('total_days').innerText = '0';
                return;
            }

            let [year, month] = monthValue.split('-');
            let totalDays = new Date(year, month, 0).getDate();

            document.getElementById('total_days').innerText = totalDays;
        }
        let searchTimer;
        $('#employeeSearch').on('keyup', function () {
            clearTimeout(searchTimer);
            let search = $(this).val();
            let month = $('#salary_month').val();
            searchTimer = setTimeout(function () {
                $.ajax({
                    url: "{{ route('salary-generation.search') }}",
                    type: "GET",
                    data: {
                        search: search,
                        month: month
                    },
                    success: function (response) {
                        allPayrollData = response.payroll;
                        currentPage = 1;
                        renderPayrollPage();
                    }
                });
            }, 300);
        });
        document.addEventListener('DOMContentLoaded', function () {
            updateTotalDays();
            document.getElementById('salary_month')
                .addEventListener('change', updateTotalDays);
        });
    });
</script>
@endsection
