@extends('layouts.common')
@section('title', 'Overtime / Bonus - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Overtime / Bonus</h4>
                <a class="btn btn-primary" href="{{ url('add_overtime') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-md-6 col-xl-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="filter_from_date" value="2024-03-01">
                                <label for="filter_from_date">Date From</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="filter_to_date" value="2024-03-31">
                                <label for="filter_to_date">Date To</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
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
                        </div>
                        <div class="col-md-12 col-xl-4">
                            <button type="button" class="btn btn-primary w-100" id="applyOtFilters">Apply Filters</button>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                        <div>
                            <div class="small text-muted">Filtered Range</div>
                            <div class="fw-semibold" id="otFilterSummary">01-03-2024 to 31-03-2024</div>
                        </div>
                        <div class="small text-muted" id="otFilterCount">2 record(s) found</div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table" id="otReportTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Department</th>
                                    <th>Employees</th>
                                    <th>OT Hours</th>
                                    <th>Late Hours</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="otReportBody">
                                <tr>
                                    <td>1</td>
                                    <td>17-03-2024</td>
                                    <td>Checking</td>
                                    <td>3</td>
                                    <td>23.50</td>
                                    <td>0.00</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>18-03-2024</td>
                                    <td>House Keeping</td>
                                    <td>1</td>
                                    <td>4.00</td>
                                    <td>0.00</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterFromDate = document.getElementById('filter_from_date');
        const filterToDate = document.getElementById('filter_to_date');
        const filterDepartment = document.getElementById('filter_department');
        const applyOtFilters = document.getElementById('applyOtFilters');
        const otFilterSummary = document.getElementById('otFilterSummary');
        const otFilterCount = document.getElementById('otFilterCount');
        const otReportBody = document.getElementById('otReportBody');
        const employeeRows = [
            { date: '17-03-2024', department: 'Checking', empNo: '10', employee: 'RENUKADEVI.R', otHours: 8.00, lateHours: 0.00, otRate: 300, otAmount: 2400 },
            { date: '17-03-2024', department: 'Checking', empNo: '70', employee: 'ANITHA.M', otHours: 7.50, lateHours: 0.00, otRate: 300, otAmount: 2250 },
            { date: '17-03-2024', department: 'Checking', empNo: '71', employee: 'SRIDHAR.K', otHours: 8.00, lateHours: 0.00, otRate: 300, otAmount: 2400 },
            { date: '18-03-2024', department: 'House Keeping', empNo: '601', employee: 'SHOBANA.R', otHours: 4.00, lateHours: 0.00, otRate: 280, otAmount: 1120 }
        ];

        function toComparable(dateValue) {
            const [day, month, year] = dateValue.split('-');
            return `${year}-${month}-${day}`;
        }

        function buildSummaryRows(rows) {
            const grouped = rows.reduce((accumulator, row) => {
                const key = `${row.date}__${row.department}`;
                if (!accumulator[key]) {
                    accumulator[key] = {
                        date: row.date,
                        department: row.department,
                        employees: 0,
                        otHours: 0,
                        lateHours: 0
                    };
                }

                accumulator[key].employees += 1;
                accumulator[key].otHours += row.otHours;
                accumulator[key].lateHours += row.lateHours;

                return accumulator;
            }, {});

            return Object.values(grouped).sort((left, right) => toComparable(left.date).localeCompare(toComparable(right.date)));
        }

        function renderRows(rows) {
            if (!rows.length) {
                otReportBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No OT summary records found for the selected filters.</td></tr>';
                otFilterCount.textContent = '0 record(s) found';
                return;
            }

            otReportBody.innerHTML = rows.map((row, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${row.date}</td>
                    <td>${row.department}</td>
                    <td>${row.employees}</td>
                    <td>${row.otHours.toFixed(2)}</td>
                    <td>${row.lateHours.toFixed(2)}</td>
                    <td>
                        <div class="button-box">
                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                        </div>
                    </td>
                </tr>
            `).join('');

            otFilterCount.textContent = `${rows.length} record(s) found`;
        }

        const reportRows = buildSummaryRows(employeeRows);

        function applyFilters() {
            const fromDate = filterFromDate.value;
            const toDate = filterToDate.value;
            const department = filterDepartment.value;

            const filtered = reportRows.filter(row => {
                const rowDate = toComparable(row.date);
                if (fromDate && rowDate < fromDate) return false;
                if (toDate && rowDate > toDate) return false;
                if (department && row.department !== department) return false;
                return true;
            });

            otFilterSummary.textContent = `${fromDate.split('-').reverse().join('-')} to ${toDate.split('-').reverse().join('-')}`;
            renderRows(filtered);
        }

        applyOtFilters.addEventListener('click', applyFilters);
        applyFilters();
    });
</script>
@endsection
