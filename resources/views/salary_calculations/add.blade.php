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
.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}
.table thead tr th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #fff;
    border-bottom: 2px solid #dee2e6;
}
.table td input.form-control {
    min-width: 80px;
    padding: 6px 8px;
    text-align: center;
}
.sticky-col-employee {
    position: sticky !important;
    left: 0 !important;
    background-color: #fff !important;
    z-index: 5;
    min-width: 200px;
}
th.sticky-col-employee {
    z-index: 15 !important;
}
</style>
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form" autocomplete="off" onsubmit="return false;">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Monthly Payroll</h4>
                        </div>
                        <div class="card">
                            @if(!isset($salary))
                            <div class="d-flex justify-content-end mb-2">
                                <input type="text" id="employeeSearch" class="form-control" placeholder="Search Emp Name / Emp Code" style="width:250px;">
                            </div>
                            @endif
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Monthly Salary Generation</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold text-nowrap text-success">
                                        Total Days: <span id="total_days">{{ isset($salary) ? $salary->total_days : 0 }}</span>
                                    </div>
                                    @if(!isset($salary))
                                        <select id="payroll_type" class="form-select" style="width: auto;">
                                            <option value="monthly">Monthly</option>
                                            <option value="range">Date Range</option>
                                        </select>
                                        <div id="month_picker_wrapper">
                                            <input type="month" id="salary_month" class="form-control" max="{{ now()->format('Y-m') }}" value="{{ isset($salary) ? $salary->salary_year . '-' . str_pad($salary->salary_month, 2, '0', STR_PAD_LEFT) : now()->format('Y-m') }}">
                                        </div>
                                        <div id="date_range_wrapper" style="display: none;" class="gap-2">
                                            <input type="date" id="salary_from_date" class="form-control" max="{{ now()->format('Y-m-d') }}">
                                            <input type="date" id="salary_to_date" class="form-control" max="{{ now()->format('Y-m-d') }}">
                                        </div>
                                        <button type="button" id="generateSalary" class="btn btn-primary">
                                            Generate
                                        </button>
                                    @else
                                        @if($salary->from_date && $salary->to_date)
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control text-center" readonly style="width: 120px;" value="{{ date('d-m-Y', strtotime($salary->from_date)) }}">
                                                <input type="text" class="form-control text-center" readonly style="width: 120px;" value="{{ date('d-m-Y', strtotime($salary->to_date)) }}">
                                            </div>
                                        @else
                                            <div id="month_picker_wrapper">
                                                <input type="month" id="salary_month" class="form-control" readonly value="{{ $salary->salary_year.'-'.str_pad($salary->salary_month, 2, '0', STR_PAD_LEFT) }}">
                                            </div>
                                        @endif
                                    @endif
                                    <a href="{{ url('monthly_payroll') }}" class="btn btn-secondary">
                                        <i class="ri ri-arrow-left-line"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="sticky-col-checkbox" width="80">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="checkAllEmployees">
                                                        <label class="form-check-label" for="checkAllEmployees">Check All</label>
                                                    </div>  
                                                </th>
                                                <th class="sticky-col-employee">Employee</th>
                                                <th>Working Days</th>
                                                <th>Lop Days</th>
                                                <th>Holidays</th>
                                                <th>Fixed Gross</th>
                                                <th>Amount Payable</th>
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
                                                <th>LOP</th>
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
                                                <tr class="salary-row" data-index="0">
                                                    {{-- Checkbox --}}
                                                    <td class="sticky-col-checkbox">
                                                        <input type="hidden" class="salary_id" value="{{ $salary->id }}">
                                                        <input type="hidden" class="employee_id" value="{{ $salary->employee_id }}">
                                                        <input type="hidden" class="total_days" value="{{ $salary->total_days }}">
                                                        <input type="checkbox" class="form-check-input employee_checkbox" checked>
                                                    </td>
                                                    {{-- Employee --}}
                                                    <td class="sticky-col-employee">
                                                        {{ $salary->name }}
                                                        <br>
                                                        <span class="badge bg-primary mt-1">{{ $salary->emp_id }}</span>
                                                    </td>
                                                    {{-- Working Days --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control present_days" value="{{ $salary->present_days }}">
                                                    </td>
                                                    {{-- Lop Days --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control absent_days" value="{{ $salary->absent_days }}">
                                                    </td>
                                                    {{-- Holidays --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control holidays" value="{{ $salary->holidays }}">
                                                    </td>
                                                    {{-- Fixed Gross --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control fixed_gross" value="{{ $salary->fixed_gross }}">
                                                    </td>
                                                    {{-- Amount Payable --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control amount_payable" value="{{ $salary->amount_payable ?? 0 }}">
                                                    </td>
                                                    {{-- Basic Pay --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control basic_salary" value="{{ $salary->basic_salary }}">
                                                    </td>
                                                    {{-- HRA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control hra" value="{{ $salary->hra }}">
                                                    </td>
                                                    {{-- DA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control da" value="{{ $salary->da }}">
                                                    </td>
                                                    {{-- OA --}}
                                                    <td>
                                                        <input type="number" readonly class="form-control oa" value="{{ $salary->oa }}">
                                                    </td>
                                                    {{-- OT Hours --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control ot_hours" value="{{ $salary->ot_hours }}">
                                                    </td>
                                                    {{-- OT Amount --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control overtime_amount" value="{{ $salary->overtime_amount }}">
                                                    </td>
                                                    {{-- Incentive --}}
                                                    <td>
                                                        <input type="number" class="form-control incentive" value="{{ $salary->incentive ?? 0 }}">
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
                                                    {{-- LOP Amount --}}
                                                    <td>
                                                        <input type="text" readonly class="form-control lop_amount"
                                                            value="{{ $salary->lop_amount ?? 0 }}">
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
        updateTotalDays();
        let allPayrollData = {!! isset($salary) ? '[' . json_encode($salary) . ']' : '[]' !!};
        let currentPage = 1;
        const perPage = 10;

        $(document).on('change', '#payroll_type', function () {
            let val = $(this).val();
            if (val === 'monthly') {
                $('#month_picker_wrapper').show();
                $('#date_range_wrapper').hide();
            } else {
                $('#month_picker_wrapper').hide();
                $('#date_range_wrapper').show().css('display', 'flex');
            }
            updateTotalDays();
        });

        $(document).on('change', '#salary_from_date, #salary_to_date', function () {
            updateTotalDays();
        });

        $('#generateSalary').click(function () {
            let type = $('#payroll_type').val() || 'monthly';
            let month = $('#salary_month').val();
            let fromDate = $('#salary_from_date').val();
            let toDate = $('#salary_to_date').val();

            if (type === 'monthly' && month == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select month',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (type === 'range' && (fromDate == '' || toDate == '')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select both From and To dates',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (type === 'range' && fromDate > toDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'From Date cannot be greater than To Date',
                    confirmButtonText: 'OK'
                });
                return;
            }
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
                    type: type,
                    month: month,
                    from_date: fromDate,
                    to_date: toDate
                },
                success: function (response) {
                    allPayrollData = response.payroll;
                    currentPage = 1;
                    renderPayrollPage();
                },
                error: function (xhr) {
                    let errorMessage = 'Something went wrong';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: errorMessage,
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
                    <!-- Checkbox -->
                    <td class="text-center sticky-col-checkbox">
                        <input type="hidden" class="employee_id" value="${item.employee_id}">
                        <input type="hidden" class="total_days" value="${item.total_days}">
                        <input type="checkbox" class="employee_checkbox" ${item.is_selected ?? true ? 'checked' : ''}>
                    </td>

                    <!-- Employee -->
                    <td class="sticky-col-employee">${item.employee_name} <br><span class="badge bg-primary mt-1">${item.emp_code}</span></td>

                    <!-- Working Days -->
                    <td>
                        <input type="text" class="form-control present_days" value="${item.present_days}" readonly>
                    </td>

                    <!-- Lop Days -->
                    <td>
                        <input type="text" class="form-control absent_days" value="${item.absent_days}" readonly>
                    </td>

                    <!-- Holidays -->
                    <td>
                        <input type="text" class="form-control holidays" value="${item.holidays}" readonly>
                    </td>

                    <!-- Fixed Gross -->
                    <td>
                        <input type="number" class="form-control fixed_gross" value="${item.fixed_gross}" readonly>
                    </td>

                    <!-- Amount Payable -->
                    <td>
                        <input type="number" class="form-control amount_payable" value="${item.amount_payable ?? 0}" readonly>
                    </td>

                    <!-- Basic Pay -->
                    <td>
                        <input type="number" class="form-control basic_salary" value="${item.basic_salary}" readonly>
                    </td>

                    <!-- HRA -->
                    <td>
                        <input type="number" class="form-control hra" value="${item.hra}" readonly>
                    </td>

                    <!-- DA -->
                    <td>
                        <input type="number" class="form-control da" value="${item.da}" readonly>
                    </td>

                    <!-- OA -->
                    <td>
                        <input type="number" class="form-control oa" value="${item.oa}" readonly>
                    </td>

                    <!-- OT Hours -->
                    <td>
                        <input type="number" class="form-control ot_hours" value="${item.ot_hours}">
                    </td>

                    <!-- OT Amount -->
                    <td>
                        <input type="text" class="form-control overtime_amount" value="${item.overtime_amount}" readonly>
                    </td>

                    <!-- Incentive -->
                    <td>
                        <input type="number" class="form-control incentive" value="${item.incentive}">
                    </td>

                    <!-- Misc -->
                    <td>
                        <input type="number" class="form-control misc" value="${item.misc}">
                    </td>

                    <!-- Bus Fare -->
                    <td>
                        <input type="number" class="form-control bus_fare" value="${item.bus_fare}" readonly>
                    </td>

                    <!-- PF -->
                    <td>
                        <input type="text" class="form-control pf" value="${item.pf}" readonly>
                    </td>

                    <!-- ESI -->
                    <td>
                        <input type="text" class="form-control esi" value="${item.esi}" readonly>
                    </td>

                    <!-- ESI -->
                    <td>
                        <input type="text" class="form-control lop_amount" value="${item.lop_amount}" readonly>
                    </td>

                    <!-- Other Deduction -->
                    <td>
                        <input type="number" class="form-control other_deduction" value="${item.other_deduction ?? 0}">
                    </td>

                    <!-- Salary Advance -->
                    <td>
                        <input type="number" class="form-control salary_advance" value="${item.salary_advance}">
                    </td>
                    <!-- Late Hours -->
                    <td>
                        <input type="number" class="form-control late_hours" value="${item.late_hours ?? 0}">
                    </td>

                    <!-- Late Fine -->
                    <td>
                        <input type="number" class="form-control late_fine" value="${item.late_fine ?? 0}" readonly>
                    </td>

                    <!-- Gross Pay -->
                    <td>
                        <input type="text" class="form-control gross_salary" value="${item.gross_salary}" readonly>
                    </td>

                    <!-- Total Deduction -->
                    <td>
                        <input type="text" class="form-control total_deduction" value="${item.total_deduction}" readonly>
                    </td>

                    <!-- Net Pay -->
                    <td>
                        <input type="text" class="form-control net_salary" value="${item.net_salary}" readonly>
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
            html += `<button type="button" class="btn btn-sm btn-light mx-1 page-btn" data-page="${currentPage - 1}" ${currentPage == 1 ? 'disabled' : ''}> Previous </button>`;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);
            if(startPage > 1) {
                html += `<button type="button" class="btn btn-sm btn-light mx-1 page-btn" data-page="1">1</button>`;
                if(startPage > 2) {
                    html += `<span class="mx-1 align-self-center">...</span>`;
                }
            }
            for(let i = startPage; i <= endPage; i++) {
                html += `
                    <button type="button" class="btn btn-sm ${i == currentPage ? 'btn-primary' : 'btn-light'} mx-1 page-btn" data-page="${i}">${i}</button>
                `;
            }
            if(endPage < totalPages) {
                if(endPage < totalPages - 1) {
                    html += `<span class="mx-1 align-self-center">...</span>`;
                }
                html += `<button type="button" class="btn btn-sm btn-light mx-1 page-btn" data-page="${totalPages}">${totalPages}</button>`;
            }
            html += `<button type="button" class="btn btn-sm btn-light mx-1 page-btn" data-page="${currentPage + 1}" ${currentPage == totalPages ? 'disabled' : ''}> Next </button>`;
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
                if (typeof index === 'undefined') return;
                
                allPayrollData[index].salary_id = row.find('.salary_id').val();
                allPayrollData[index].is_selected = row.find('.employee_checkbox').is(':checked');
                allPayrollData[index].basic_salary = parseFloat(row.find('.basic_salary').val()) || 0;
                allPayrollData[index].hra = parseFloat(row.find('.hra').val()) || 0;
                allPayrollData[index].da = parseFloat(row.find('.da').val()) || 0;
                allPayrollData[index].oa = parseFloat(row.find('.oa').val()) || 0;
                allPayrollData[index].misc = parseFloat(row.find('.misc').val()) || 0;
                allPayrollData[index].incentive = parseFloat(row.find('.incentive').val()) || 0;
                allPayrollData[index].salary_advance = parseFloat(row.find('.salary_advance').val()) || 0;
                allPayrollData[index].net_salary = parseFloat(row.find('.net_salary').val()) || 0;
                allPayrollData[index].gross_salary = parseFloat(row.find('.gross_salary').val()) || 0;
                allPayrollData[index].total_deduction = parseFloat(row.find('.total_deduction').val()) || 0;
                allPayrollData[index].lop_amount = parseFloat(row.find('.lop_amount').val()) || 0;
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
        $('#savePayroll').click(function () {
            saveCurrentPageData();
            let month = $('#salary_month').val();
            let type = $('#payroll_type').val() || 'monthly';
            let fromDate = $('#salary_from_date').val();
            let toDate = $('#salary_to_date').val();

            if (type === 'monthly' && month == '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select month',
                    confirmButtonText: 'OK'
                });
                return;
            }
            if (type === 'range' && (fromDate == '' || toDate == '')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Please select dates',
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
                    type: type,
                    month: month,
                    from_date: fromDate,
                    to_date: toDate,
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
            let totalDays = parseFloat(row.find('.total_days').val()) || 1;
            let absentDays = parseFloat(row.find('.absent_days').val()) || 0;
            let perDaySalary = totalDays > 0 ? fixed_gross / totalDays : 0;
            let lopAmount = perDaySalary * absentDays;
            let payable = fixed_gross - lopAmount;

            let basic = (payable * 50) / 100;
            let hra = (payable * 20) / 100;
            let da = (payable * 20) / 100;
            let oa = (payable * 10) / 100;

            row.find('.basic_salary').val(basic.toFixed(2));
            row.find('.hra').val(hra.toFixed(2));
            row.find('.da').val(da.toFixed(2));
            row.find('.oa').val(oa.toFixed(2));

            let incentive = parseFloat(row.find('.incentive').val()) || 0;
            let misc = parseFloat(row.find('.misc').val()) || 0;
            let busFare = parseFloat(row.find('.bus_fare').val()) || 0;
            let otHours = parseFloat(row.find('.ot_hours').val()) || 0;
            let salaryAdvance = parseFloat(row.find('.salary_advance').val()) || 0;
            let lateFine = parseFloat(row.find('.late_fine').val()) || 0;
            let otherDeduction = parseFloat(row.find('.other_deduction').val()) || 0;

            let perHourSalary = perDaySalary / 8;
            let otAmount = perHourSalary * otHours;
            row.find('.overtime_amount').val(otAmount.toFixed(2));
            
            let totalEarnings = payable + incentive + misc + busFare + otAmount;
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
            let totalDeduction = pf + esi + salaryAdvance + lateFine + otherDeduction;

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
            let gross = parseFloat(row.find('.gross_salary').val()) || 0;
            let pf = parseFloat(row.find('.pf').val()) || 0;
            let esi = parseFloat(row.find('.esi').val()) || 0;
            let ot = parseFloat(row.find('.overtime_amount').val()) || 0;
            let advance = parseFloat($(this).val()) || 0;
            let lateFine = parseFloat(row.find('.late_fine').val()) || 0;
            let otherDeduction = parseFloat(row.find('.other_deduction').val()) || 0;
            let net = gross - pf - esi - advance - lateFine - otherDeduction;
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
        $(document).on('change', '#salary_month', function () {
            updateTotalDays();
        });
        function updateTotalDays() {
            if ($('.salary_id').length > 0) {
                return;
            }
            let type = $('#payroll_type').val() || 'monthly';
            if (type === 'monthly') {
                let monthValue = $('#salary_month').val();
                if (!monthValue) {
                    $('#total_days').text('0');
                    return;
                }
                let parts = monthValue.split('-');
                let year = parseInt(parts[0]);
                let month = parseInt(parts[1]);
                let totalDays = new Date(year, month, 0).getDate();
                $('#total_days').text(totalDays);
            } else {
                let fromDate = $('#salary_from_date').val();
                let toDate = $('#salary_to_date').val();
                if (!fromDate || !toDate) {
                    $('#total_days').text('0');
                    return;
                }
                let start = new Date(fromDate);
                let end = new Date(toDate);
                let diffTime = Math.abs(end - start);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                $('#total_days').text(diffDays);
            }
        }
        let searchTimer;
        $('#employeeSearch').on('keyup', function () {
            clearTimeout(searchTimer);
            let search = $(this).val();
            let type = $('#payroll_type').val() || 'monthly';
            let month = $('#salary_month').val();
            let fromDate = $('#salary_from_date').val();
            let toDate = $('#salary_to_date').val();
            searchTimer = setTimeout(function () {
                $.ajax({
                    url: "{{ route('salary-generation.search') }}",
                    type: "GET",
                    data: {
                        search: search,
                        type: type,
                        month: month,
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function (response) {
                        allPayrollData = response.payroll;
                        currentPage = 1;
                        renderPayrollPage();
                    }
                });
            }, 300);
        });
    });
</script>
@endsection
