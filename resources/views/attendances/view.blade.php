@extends('layouts.common')
@section('title', 'Attendance Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h4 class="mb-1">Attendance Management</h4>
                </div>
                <div>
                    <button class="btn btn-outline-success shadow-sm" id="exportExcelBtn">
                        <i class="menu-icon ri ri-file-excel-line me-1"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form id="syncForm" method="GET" action="{{ url('attendances') }}">
                        <div class="row gy-3 gx-3 align-items-end">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small fw-semibold">Attendance Date</label>
                                <input type="date" name="date" class="form-control" id="attendanceDate" value="{{ old('date', $date ?? date('Y-m-d')) }}">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small fw-semibold">Device</label>
                                {{-- <select class="select2 form-select" name="device" id="deviceSelect" data-placeholder="Choose device">
                                    <option value="">Choose device</option>
                                    <option value="AEVL183660459" {{ (isset($device) && $device === 'AEVL183660459') ? 'selected' : '' }}>HO</option>
                                    <option value="BJ2C180660790" {{ (isset($device) && $device === 'BJ2C180660790') ? 'selected' : '' }}>HO 1</option>
                                    <option value="CEXJ210460057" {{ (isset($device) && $device === 'CEXJ210460057') ? 'selected' : '' }}>KALAVASAL</option>
                                    <option value="CEXJ211160630" {{ (isset($device) && $device === 'CEXJ211160630') ? 'selected' : '' }}>SAMAYANALLUR</option>
                                </select> --}}
                                <select class="select2 form-select" name="device" id="deviceSelect" data-placeholder="Choose device">
                                    <option value="">Choose device</option>
                                    @foreach ($devices as $device)
                                        <option value="{{ $device->serial_number }}" {{ (isset($device) && $device->serial_number === $device) ? 'selected' : '' }}>{{ $device->device_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="d-flex gap-2">
                                    <div id="syncLoader" class="text-primary small mt-2 d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Reading Attendance...
                                    </div>
                                    {{-- <button type="button" class="btn btn-primary flex-fill" id="syncButton" disabled>
                                        <span id="syncButtonLabel">Sync Attendance</span>
                                    </button> --}}
                                    <button type="button" class="btn btn-secondary flex-fill" id="resetButton">Reset</button>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="alert alert-info py-2 mb-0">
                                    Select a device and attendance date for <strong>Reading Attendance</strong> 
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row mt-4 g-3" id="dailySummaryCards" style="display: none;">
                        <div class="col-sm-6 col-lg">
                            <div class="card bg-secondary text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1 opacity-75">Total Employees</h6>
                                        <h3 class="card-text text-white mb-0 fw-bold" id="summaryTotalCount">0</h3>
                                    </div>
                                    <div class="fs-1 opacity-50"><i class="ri-group-line"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1 opacity-75">Present</h6>
                                        <h3 class="card-text text-white mb-0 fw-bold" id="summaryPresentCount">0</h3>
                                    </div>
                                    <div class="fs-1 opacity-50"><i class="ri-user-follow-line"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1 opacity-75">Absent</h6>
                                        <h3 class="card-text text-white mb-0 fw-bold" id="summaryAbsentCount">0</h3>
                                    </div>
                                    <div class="fs-1 opacity-50"><i class="ri-user-unfollow-line"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg">
                            <div class="card bg-warning text-dark h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-dark mb-1 opacity-75">Late Comers</h6>
                                        <h3 class="card-text text-dark mb-0 fw-bold" id="summaryLateCount">0</h3>
                                    </div>
                                    <div class="fs-1 opacity-50"><i class="ri-time-line"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1 opacity-75">Overtime</h6>
                                        <h3 class="card-text text-white mb-0 fw-bold" id="summaryOvertimeCount">0</h3>
                                    </div>
                                    <div class="fs-1 opacity-50"><i class="ri-history-line"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 g-3 align-items-center">
                        <div class="col-md-10">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="viewAllButton" type="button" role="tab" aria-selected="true">All Attendance</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="viewAbsentButton" type="button" role="tab" aria-selected="false">Absent Report</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="viewLateButton" type="button" role="tab" aria-selected="false">Later Comer Report</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="viewMissingButton" type="button" role="tab" aria-selected="false">Missing Time Card</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="viewHolidaysButton" type="button" role="tab" aria-selected="false">Declared Holidays</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="viewStaffWiseButton" type="button" role="tab" aria-selected="false">Staff Wise Report</button>
                                </li>
                            </ul>
                            <span class="ms-3 text-muted small d-inline-block" id="viewModeLabel">Showing all attendance records</span>
                            <div id="holidaySummary" class="text-muted small mt-3"></div>
                        </div>
                        <div class="col-md-2">
                            <div class="small text-muted">Last Read Time</div>
                            <div id="lastSyncedText" class="fw-semibold">{{ $lastSynced ?? 'Not read yet' }}</div>
                        </div>
                        {{-- <div class="col-md-4 offset-md-8 text-md-end">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="ri ri-search-line"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search employee or code...">
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0 pb-3">
                        <h5 class="modal-title fw-bold">Edit Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 pt-4">
                        <input type="hidden" id="attendance_id">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">In Time</label>
                                <input type="time" id="edit_in_time" class="form-control form-control-lg">
                                <small class="text-danger error-text" id="in_time_error"></small>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-muted">Out Time</label>
                                <input type="time" id="edit_out_time" class="form-control form-control-lg">
                                <small class="text-danger error-text" id="out_time_error"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">Status</label>
                            <select id="edit_status" class="form-select form-select-lg">
                                <option value="">Select Status</option>
                                <option value="Present">Present</option>
                                <option value="Late">Late</option>
                                <option value="Absent">Absent</option>
                                <option value="Missing Time Card">Missing Time Card</option>
                                <option value="Overtime">Overtime</option>
                                <option value="Holiday">Holiday</option>
                                <option value="Week Off">Week Off</option>
                            </select>
                            <small class="text-danger error-text" id="status_error"></small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary px-4" id="saveAttendanceBtn">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div id="statusMessage" class="alert d-none mb-4" role="alert"></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0" id="attendanceTable">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Employee Name</th>
                                    <th scope="col">Employee Code</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">In Time</th>
                                    <th scope="col">Out Time</th>
                                    <th scope="col">Working Hours</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceBody"></tbody>
                        </table>
                    </div>
                    <div id="noDataMessage" class="text-center py-5 text-muted">
                        <div class="mb-2"><strong>No Data</strong></div>
                        <div>Select Attendance Date and Device for <strong>Reading Attendance</strong> </div>
                    </div>
                    <div id="holidayPanel" class="d-none">
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <div>
                                    <h6 class="mb-1">Declared Holidays</h6>
                                    <div class="small text-muted">Month: <span id="holidayMonthLabel"></span></div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" id="refreshHolidaysButton">Refresh List</button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="holiday-calendar-toolbar mb-3">
                                    <div class="holiday-calendar-nav">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="holidayPrevMonthButton">
                                            <i class="ri ri-arrow-left-s-line"></i>
                                        </button>
                                        <div class="holiday-month-picker">
                                            <button type="button" class="btn btn-sm" id="holidayMonthButton" aria-haspopup="dialog" aria-expanded="false"></button>
                                            <div class="holiday-month-popup d-none" id="holidayMonthPopup">
                                                <div class="holiday-month-grid" id="holidayMonthGrid"></div>
                                            </div>
                                        </div>
                                        <div class="holiday-year-picker">
                                            <button type="button" class="btn btn-sm" id="holidayYearButton" aria-haspopup="dialog" aria-expanded="false"></button>
                                            <div class="holiday-year-popup d-none" id="holidayYearPopup">
                                                <div class="holiday-year-popup-header">
                                                    <span id="holidayYearRangeLabel"></span>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-link p-0" id="holidayYearRangePrev" aria-label="Previous years"><i class="ri ri-arrow-up-s-line"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-link p-0" id="holidayYearRangeNext" aria-label="Next years"><i class="ri ri-arrow-down-s-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="holiday-year-grid" id="holidayYearGrid"></div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="holidayNextMonthButton">
                                            <i class="ri ri-arrow-right-s-line"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light" id="holidayTodayButton">Current Month</button>
                                </div>
                                <div class="small fw-semibold mb-2">Select holiday day from calendar</div>
                                <div id="holidayCalendarInline" class="holiday-calendar"></div>
                            </div>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered mb-0" id="holidayTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Holiday Date</th>
                                        <th scope="col">Holiday Name</th>
                                    </tr>
                                </thead>
                                <tbody id="holidayBody"></tbody>
                            </table>
                        </div>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-12 col-md-8">
                                <div class="small fw-semibold mb-1">Select multiple dates from the calendar to declare holidays.</div>
                                <div id="selectedHolidayCount" class="text-muted small">0 dates selected</div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <button type="button" class="btn btn-success w-100" id="saveSelectedHolidaysButton">Save Selected Holidays</button>
                            </div>
                        </div>

                        <div id="holidayEditModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1050;">
                            <div class="modal-dialog" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:white; border-radius:8px; padding:24px; box-shadow:0 2px 16px rgba(0,0,0,0.15); max-width:400px; width:90%; pointer-events:auto; z-index:1051;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Edit Holiday Details</h6>
                                    <button type="button" class="btn-close" id="closeHolidayEditModal"></button>
                                </div>
                                <form id="holidayEditForm" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Holiday Date</label>
                                        <input type="date" id="editHolidayDate" class="form-control" readonly />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Holiday Name</label>
                                        <input type="text" id="editHolidayName" class="form-control" placeholder="Enter holiday name" autofocus />
                                    </div>
                                    <div class="col-12 d-flex gap-2">
                                        <button type="button" class="btn btn-primary flex-fill" id="saveHolidayEditButton">Save</button>
                                        <button type="button" class="btn btn-secondary flex-fill" id="cancelHolidayEditButton">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="staffReportPanel" class="d-none">
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row g-3 align-items-end">
                                <div class="col-sm-6 col-lg-3">
                                    <label class="form-label small fw-semibold">Select Month</label>
                                    <input type="month" class="form-control" id="staffReportMonth">
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <label class="form-label small fw-semibold">Select Employee</label>
                                    <select class="select2 form-select" id="staffReportEmployee" data-placeholder="Choose employee">
                                        <option value="">Choose employee</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <label class="form-label small fw-semibold">From Date</label>
                                    <input type="date" class="form-control" id="staffReportFromDate">
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <label class="form-label small fw-semibold">To Date</label>
                                    <input type="date" class="form-control" id="staffReportToDate">
                                </div>
                                <div class="col-sm-12 col-lg-2">
                                    <button type="button" class="btn btn-primary w-100" id="staffReportGenerateButton">Show Report</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                            <div>
                                <div class="small text-muted">Filtered Period</div>
                                <div class="fw-semibold" id="staffReportRangeLabel">Select a month and employee to view report.</div>
                            </div>
                            <div class="small text-muted" id="staffReportSummary"></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0" id="staffReportTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Employee Code</th>
                                        {{-- <th scope="col">Device</th> --}}
                                        <th scope="col">In Time</th>
                                        <th scope="col">Out Time</th>
                                        <th scope="col">Working Hours</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="staffReportBody"></tbody>
                            </table>
                        </div>
                        <div id="staffReportNoData" class="text-center py-4 text-muted">
                            Select a month and employee to load staff-wise attendance records.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const canViewAttendance =
        {{ auth()->id() == 1 || auth()->user()->can('view_details attendance') ? 'true' : 'false' }};

    const canEditAttendance =
        {{ auth()->id() == 1 || auth()->user()->can('edit attendance') ? 'true' : 'false' }};
    document.addEventListener('DOMContentLoaded', function() {
        const attendanceBody = document.getElementById('attendanceBody');
        const noDataMessage = document.getElementById('noDataMessage');
        const statusMessage = document.getElementById('statusMessage');
        const lastSyncedText = document.getElementById('lastSyncedText');
        // const searchInput = document.getElementById('searchInput');
        // const syncButton = document.getElementById('syncButton');
        // const syncButtonLabel = document.getElementById('syncButtonLabel');
        const attendanceDate = document.getElementById('attendanceDate');
        const deviceSelect = document.getElementById('deviceSelect');
        const resetButton = document.getElementById('resetButton');
        const viewAllButton = document.getElementById('viewAllButton');
        const viewAbsentButton = document.getElementById('viewAbsentButton');
        const viewLateButton = document.getElementById('viewLateButton');
        const viewMissingButton = document.getElementById('viewMissingButton');
        const viewHolidaysButton = document.getElementById('viewHolidaysButton');
        const viewStaffWiseButton = document.getElementById('viewStaffWiseButton');
        const holidaySummary = document.getElementById('holidaySummary');
        const holidayPanel = document.getElementById('holidayPanel');
        const staffReportPanel = document.getElementById('staffReportPanel');
        const holidayMonthLabel = document.getElementById('holidayMonthLabel');
        const holidayMonthButton = document.getElementById('holidayMonthButton');
        const holidayMonthPopup = document.getElementById('holidayMonthPopup');
        const holidayMonthGrid = document.getElementById('holidayMonthGrid');
        const holidayYearButton = document.getElementById('holidayYearButton');
        const holidayYearPopup = document.getElementById('holidayYearPopup');
        const holidayYearRangeLabel = document.getElementById('holidayYearRangeLabel');
        const holidayYearRangePrev = document.getElementById('holidayYearRangePrev');
        const holidayYearRangeNext = document.getElementById('holidayYearRangeNext');
        const holidayYearGrid = document.getElementById('holidayYearGrid');
        const holidayPrevMonthButton = document.getElementById('holidayPrevMonthButton');
        const holidayNextMonthButton = document.getElementById('holidayNextMonthButton');
        const holidayTodayButton = document.getElementById('holidayTodayButton');
        const holidayCalendarInline = document.getElementById('holidayCalendarInline');
        const holidayBody = document.getElementById('holidayBody');
        const attendanceTableContainer = document.getElementById('attendanceTable').parentElement;
        const selectedHolidayCount = document.getElementById('selectedHolidayCount');
        const saveSelectedHolidaysButton = document.getElementById('saveSelectedHolidaysButton');
        const refreshHolidaysButton = document.getElementById('refreshHolidaysButton');
        const staffReportMonth = document.getElementById('staffReportMonth');
        const staffReportEmployee = document.getElementById('staffReportEmployee');
        const staffReportFromDate = document.getElementById('staffReportFromDate');
        const staffReportToDate = document.getElementById('staffReportToDate');
        const staffReportGenerateButton = document.getElementById('staffReportGenerateButton');
        const staffReportRangeLabel = document.getElementById('staffReportRangeLabel');
        const staffReportSummary = document.getElementById('staffReportSummary');
        const staffReportBody = document.getElementById('staffReportBody');
        const staffReportNoData = document.getElementById('staffReportNoData');
        const holidayEditModal = document.getElementById('holidayEditModal');
        const editHolidayDate = document.getElementById('editHolidayDate');
        const editHolidayName = document.getElementById('editHolidayName');
        const closeHolidayEditModal = document.getElementById('closeHolidayEditModal');
        const saveHolidayEditButton = document.getElementById('saveHolidayEditButton');
        const cancelHolidayEditButton = document.getElementById('cancelHolidayEditButton');
        let attendanceDataTable = null;
        let attendanceRecords = [];
        let declaredHolidaysByMonth = {};
        fetchHolidays();
        function fetchHolidays() {
            const monthKey = attendanceDate.value.slice(0, 7);
            fetch(`${APP_URL}/holidays/${monthKey}`)
                .then(res => res.json())
                .then(data => {
                    // 🔹 Store in main config (for summary etc.)
                    declaredHolidaysByMonth[monthKey] = {
                        label: formatMonthYear(monthKey),
                        description: 'Saved Holidays',
                        holidays: data.map(item => ({
                            date: item.date,
                            name: item.name
                        }))
                    };
                    // ✅ IMPORTANT: Sync selection state
                    selectedHolidayDates = data.map(item => item.date);
                    selectedHolidayNames = {};
                    data.forEach(item => {
                        selectedHolidayNames[item.date] = item.name;
                    });
                    activeHolidayMonthKey = monthKey;
                    renderHolidayPanel(); // re-render UI
                });
        }
        let currentView = 'all';
        let activeHolidayMonthKey = attendanceDate.value.slice(0, 7);
        let holidayYearRangeStart = null;
        const holidayMonthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        let staffReportRecords = [];

        function getBadgeClass(status) {
            if (status === 'Present') return 'badge bg-success';
            if (status === 'Late') return 'badge bg-danger';
            if (status === 'Overtime') return 'badge bg-warning text-dark';
            if (status === 'Absent') return 'badge bg-danger';
            if (status === 'Punch Out Missing') return 'badge bg-danger';
            if (status === 'Holiday') return 'badge bg-primary';
            if (status === 'Week Off') return 'badge bg-secondary';
            return 'badge bg-secondary';
        }

        function showStatus(type, message) {
            statusMessage.className = 'alert alert-' + type + ' mb-4';
            statusMessage.textContent = message;
            statusMessage.classList.remove('d-none');
            setTimeout(() => {
                statusMessage.classList.add('d-none');
            }, 3000);
        }

        function formatDate(value) {
            const date = new Date(value);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        } 

        function formatMonthInputValue(dateValue) {
            const date = new Date(dateValue);
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        }

        function formatMonthYear(value) {
            const date = new Date(value + '-01');
            return date.toLocaleDateString('en-GB', {
                month: 'long',
                year: 'numeric'
            });
        }

        function formatDateKey(value) {
            const date = value instanceof Date ? value : new Date(value);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function renderSelectedHolidayTable() {
            const displayRows = selectedHolidayDates
                .slice()
                .sort()
                .map(date => ({
                    date,
                    name: selectedHolidayNames[date] || 'Declared Holiday'
                }));

            holidayBody.innerHTML = displayRows.length ?
                renderHolidayRows(displayRows) :
                '<tr><td colspan="3" class="text-center text-muted py-3">No holidays declared for this month.</td></tr>';
        }

        function updateAttendanceDateForMonth(year, monthIndex) {
            const currentDay = Number(attendanceDate.value.split('-')[2] || '1');
            const month = monthIndex + 1;
            const daysInMonth = new Date(year, month, 0).getDate();
            const nextDay = String(Math.min(currentDay, daysInMonth)).padStart(2, '0');
            attendanceDate.value = `${year}-${String(month).padStart(2, '0')}-${nextDay}`;
        }

        function getHolidayYearRangeStart(year) {
            return Math.floor(Number(year) / 10) * 10;
        }

        function updateHolidayMonthButton(monthIndex, year) {
            holidayMonthButton.textContent = `${holidayMonthLabels[monthIndex]} ${year}`;
        }

        function closeHolidayMonthPopup() {
            holidayMonthPopup.classList.add('d-none');
            holidayMonthButton.setAttribute('aria-expanded', 'false');
        }

        function renderHolidayMonthPopup(selectedMonthIndex) {
            const months = holidayMonthLabels.map((label, index) => {
                const classes = ['holiday-month-option'];
                if (index === selectedMonthIndex) {
                    classes.push('active');
                }
                return `<button type="button" class="${classes.join(' ')}" data-month-index="${index}">${label}</button>`;
            });

            holidayMonthGrid.innerHTML = months.join('');
            holidayMonthGrid.querySelectorAll('[data-month-index]').forEach(button => {
                button.addEventListener('click', function() {
                    const pickedMonthIndex = Number(this.getAttribute('data-month-index'));
                    const {
                        year
                    } = getActiveHolidayMonthParts();
                    updateAttendanceDateForMonth(year, pickedMonthIndex);
                    updateHolidayBanner();
                    renderHolidayPanel();
                    closeHolidayMonthPopup();
                });
            });
        }

        function openHolidayMonthPopup(monthIndex) {
            renderHolidayMonthPopup(monthIndex);
            holidayMonthPopup.classList.remove('d-none');
            holidayMonthButton.setAttribute('aria-expanded', 'true');
        }

        function updateHolidayYearButton(year) {
            holidayYearButton.textContent = year;
        }

        function closeHolidayYearPopup() {
            holidayYearPopup.classList.add('d-none');
            holidayYearButton.setAttribute('aria-expanded', 'false');
        }

        function openHolidayYearPopup(year) {
            holidayYearRangeStart = getHolidayYearRangeStart(year);
            renderHolidayYearPopup(Number(year));
            holidayYearPopup.classList.remove('d-none');
            holidayYearButton.setAttribute('aria-expanded', 'true');
        }

        function renderHolidayYearPopup(selectedYear) {
            const startYear = holidayYearRangeStart ?? getHolidayYearRangeStart(selectedYear);
            const years = [];
            holidayYearRangeLabel.textContent = `${startYear} - ${startYear + 9}`;

            for (let year = startYear - 2; year <= startYear + 11; year++) {
                const classes = ['holiday-year-option'];
                if (year === selectedYear) {
                    classes.push('active');
                }
                if (year < startYear || year > startYear + 9) {
                    classes.push('outside');
                }

                years.push(`<button type="button" class="${classes.join(' ')}" data-year="${year}">${year}</button>`);
            }

            holidayYearGrid.innerHTML = years.join('');
            holidayYearGrid.querySelectorAll('[data-year]').forEach(button => {
                button.addEventListener('click', function() {
                    const pickedYear = Number(this.getAttribute('data-year'));
                    const {
                        monthIndex
                    } = getActiveHolidayMonthParts();
                    updateAttendanceDateForMonth(pickedYear, monthIndex);
                    updateHolidayBanner();
                    renderHolidayPanel();
                    closeHolidayYearPopup();
                });
            });
        }

        function getActiveHolidayMonthParts() {
            const [year, month] = attendanceDate.value.slice(0, 7).split('-').map(Number);
            return {
                year,
                monthIndex: month - 1
            };
        }

        function getHolidayConfigForMonth(monthKey) {
            return declaredHolidaysByMonth[monthKey] || null;
        }

        function isDeclaredHoliday(dateValue) {
            const monthKey = dateValue.slice(0, 7);
            const config = getHolidayConfigForMonth(monthKey);
            return config ? config.holidays.some(item => item.date === dateValue) : false;
        }

        function getHolidaySummary(dateValue) {
            const monthKey = dateValue.slice(0, 7);
            const config = getHolidayConfigForMonth(monthKey);
            if (!config) {
                return `No declared holidays set manually for ${formatMonthYear(monthKey)}.`;
            }
            const dates = config.holidays.map(item => formatDate(item.date)).join(', ');
            return `${config.label}: ${config.description}. Dates: ${dates}.`;
        }

        function updateHolidayBanner() {
            if (currentView !== 'holiday') {
                holidaySummary.textContent = '';
                holidaySummary.style.display = 'none';
                return;
            }

            holidaySummary.style.display = 'block';
            holidaySummary.textContent = getHolidaySummary(attendanceDate.value);
        }

        function getHolidayRowsForMonth(dateValue) {
            const monthKey = dateValue.slice(0, 7);
            const config = getHolidayConfigForMonth(monthKey);
            return config ? config.holidays : [];
        }

        function getEmployeeDirectory() {
            const seen = new Map();
            Object.entries(masterAttendance).forEach(([device, records]) => {
                records.forEach(record => {
                    if (!seen.has(record.code)) {
                        seen.set(record.code, {
                            code: record.code,
                            name: record.name,
                            preferredDevice: device
                        });
                    }
                });
            });
            return Array.from(seen.values()).sort((a, b) => a.name.localeCompare(b.name));
        }

        function populateStaffEmployeeOptions() {
            fetch(`${APP_URL}/get-employees`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) {
                    staffReportEmployee.innerHTML = '<option>No employees found</option>';
                    return;
                }
                const options = [
                    '<option value="">Choose employee</option>',
                    '<option value="all">All employees</option>'
                ];
                data.forEach(employee => {
                    options.push(
                        `<option value="${employee.code}">
                            ${employee.name} (${employee.code})
                        </option>`
                    );
                });
                staffReportEmployee.innerHTML = options.join('');
            })
            .catch(err => {
                console.error(err);
                showStatus('danger', 'Failed to load employees');
            });
        }

        function updateStaffReportRangeFields() {
            const monthValue = staffReportMonth.value;
            if (monthValue) {
                const [year, month] = monthValue.split('-').map(Number);
                const startDate = new Date(year, month - 1, 1);
                const endDate = new Date(year, month, 0);
                staffReportFromDate.value = formatDateKey(startDate);
                staffReportToDate.value = formatDateKey(endDate);
            }

            const fromVal = staffReportFromDate.value;
            const toVal = staffReportToDate.value;
            if (fromVal && toVal) {
                staffReportRangeLabel.textContent = `${formatDate(fromVal)} to ${formatDate(toVal)}`;
            } else {
                staffReportRangeLabel.textContent = 'Select a month/date range and employee to view report.';
            }
        }

        function generateStaffReportRecords(employeeCode, monthValue) {
            if (!employeeCode || !monthValue) {
                return [];
            }

            const [year, month] = monthValue.split('-').map(Number);
            const employee = getEmployeeDirectory().find(item => item.code === employeeCode);
            if (!employee) {
                return [];
            }

            const daysInMonth = new Date(year, month, 0).getDate();
            const preferredDevice = employee.preferredDevice;
            const records = [];
            const selectedMonthKey = `${year}-${String(month).padStart(2, '0')}`;
            const holidayDates = new Set(getHolidayRowsForMonth(`${selectedMonthKey}-01`).map(item => item.date));
            const codeSeed = employee.code.split('').reduce((total, char) => total + char.charCodeAt(0), 0);

            for (let day = 1; day <= daysInMonth; day++) {
                const dateValue = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const currentDate = new Date(year, month - 1, day);
                const weekDay = currentDate.getDay();
                const daySeed = (codeSeed + day) % 11;
                let status = 'Present';
                let inTime = '09:05 AM';
                let outTime = '06:05 PM';
                let hours = '9.0';

                if (holidayDates.has(dateValue)) {
                    status = 'Holiday';
                    inTime = '-';
                    outTime = '-';
                    hours = '-';
                } else if (weekDay === 0) {
                    status = 'Week Off';
                    inTime = '-';
                    outTime = '-';
                    hours = '-';
                } else if (daySeed === 0) {
                    status = 'Absent';
                    inTime = '-';
                    outTime = '-';
                    hours = '-';
                } else if (daySeed === 1) {
                    status = 'Punch Out Missing';
                    inTime = '09:18 AM';
                    outTime = '-';
                    hours = '-';
                } else if (daySeed === 2 || daySeed === 3) {
                    status = 'Late';
                    inTime = `09:${String(20 + (day % 20)).padStart(2, '0')} AM`;
                    outTime = '06:00 PM';
                    hours = '8.4';
                } else if (daySeed === 4) {
                    status = 'Overtime';
                    inTime = '08:45 AM';
                    outTime = '07:20 PM';
                    hours = '10.6';
                } else {
                    const inMinute = String((5 + daySeed) % 60).padStart(2, '0');
                    const outMinute = String((10 + daySeed) % 60).padStart(2, '0');
                    inTime = `09:${inMinute} AM`;
                    outTime = `06:${outMinute} PM`;
                    hours = (8.8 + ((daySeed % 3) * 0.2)).toFixed(1);
                }

                records.push({
                    date: dateValue,
                    employee: employee.name,
                    code: employee.code,
                    device: preferredDevice,
                    inTime,
                    outTime,
                    hours,
                    status
                });
            }

            return records;
        }

        function renderStaffReportRows(records) {
            return records.map((record, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${formatDate(record.date)}</td>
                    <td>${record.employee}</td>
                    <td>${record.code}</td>
                    <td>${record.device}</td>
                    <td>${record.inTime}</td>
                    <td>${record.outTime}</td>
                    <td>${record.hours}</td>
                    <td><span class="${getBadgeClass(record.status)}">${record.status}</span></td>
                </tr>
            `).join('');
        }

        function updateStaffReportSummary(records, employeeCode, monthValue) {
            if (!monthValue) {
                staffReportSummary.textContent = 'Month not selected.';
                return;
            }

            const employee = getEmployeeDirectory().find(item => item.code === employeeCode);
            const presentCount = records.filter(item => item.status === 'Present').length;
            const lateCount = records.filter(item => item.status === 'Late').length;
            const absentCount = records.filter(item => item.status === 'Absent').length;
        }

        function loadStaffReport() {
            const employeeCode = staffReportEmployee.value;
            const monthValue = staffReportMonth.value;
            const fromDateValue = staffReportFromDate.value;
            const toDateValue = staffReportToDate.value;
            updateStaffReportRangeFields();
            if (!monthValue && (!fromDateValue || !toDateValue)) {
                if ($.fn.DataTable.isDataTable('#staffReportTable')) {
                    $('#staffReportTable').DataTable().clear().destroy();
                }
                staffReportBody.innerHTML = '';
                staffReportNoData.style.display = 'block';
                staffReportSummary.textContent = 'Month or Date Range not selected.';
                return;
            }
            staffReportGenerateButton.disabled = true;
            staffReportGenerateButton.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Loading...';

            if ($.fn.DataTable.isDataTable('#staffReportTable')) {
                $('#staffReportTable').DataTable().clear().destroy();
            }
            staffReportNoData.style.display = 'none';
            staffReportBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 text-muted fw-semibold fs-5">Fetching Staff Report...</div>
                        <div class="text-muted small">Please wait while we generate the report</div>
                    </td>
                </tr>
            `;
            staffReportSummary.innerHTML = '<span class="text-primary fw-semibold">Loading data...</span>';
            fetch(`${APP_URL}/staff-report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    emp_code: employeeCode,
                    month: monthValue,
                    from_date: fromDateValue,
                    to_date: toDateValue
                })
            })
            .then(res => res.json())
            .then(data => {
                staffReportRecords = data || [];
                if ($.fn.DataTable.isDataTable('#staffReportTable')) {
                    $('#staffReportTable').DataTable().clear().destroy();
                }
                if (!data.length) {
                    staffReportBody.innerHTML = '';
                    staffReportNoData.style.display = 'block';
                    staffReportSummary.textContent = 'No records found.';
                    return;
                }
                staffReportNoData.style.display = 'none';
                staffReportBody.innerHTML = data.map((record, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${formatDate(record.date)}</td>
                        <td>${record.employee}</td>
                        <td>${record.code}</td>
                        <td>
                            ${
                                record.in_time
                                ? new Date(record.in_time).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                                : '-'
                            }
                        </td>
                        <td>
                            ${
                                record.out_time
                                ? new Date(record.out_time).toLocaleTimeString([], {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                                : '-'
                            }
                        </td>
                        <td>${record.hours ?? '-'}</td>
                        <td>
                            <span class="${getBadgeClass(record.status)}">
                                ${record.status}
                            </span>
                        </td>
                    </tr>
                `).join('');
                initStaffReportDataTable();
                const present =
                    data.filter(x => x.status === 'Present' || x.status === 'Overtime').length;
                const late =
                    data.filter(x => x.status === 'Late').length;
                const absent =
                    data.filter(x => x.status === 'Absent' || x.status === 'Missing Time Card').length;
                const overtime =
                    data.filter(x => x.status === 'Overtime').length;
                staffReportSummary.innerHTML = `<span class="badge bg-success me-1">${present} Present</span> <span class="badge bg-warning text-dark me-1">${late} Late</span> <span class="badge bg-danger me-1">${absent} Absent</span> <span class="badge bg-info me-1">${overtime} Overtime</span>`;
            })
            .catch(err => {
                console.error(err);
                showStatus(
                    'danger',
                    'Failed to load staff report'
                );
            })
            .finally(() => {
                staffReportGenerateButton.disabled = false;
                staffReportGenerateButton.textContent = 'Show Report';
            });
        }
        function renderStaffReportPanel() {
            staffReportMonth.value = staffReportMonth.value || attendanceDate.value.slice(0, 7);
            updateStaffReportRangeFields();
            if (staffReportFromDate.value) {
                staffReportToDate.min = staffReportFromDate.value;
            }
            loadStaffReport();
        }
        let selectedHolidayDates = [];
        let selectedHolidayNames = {};
        let currentEditingDate = null;

        function resetHolidaySelectionState(rows) {
            selectedHolidayDates = rows.map(item => item.date);
            selectedHolidayNames = {};
            rows.forEach(item => {
                selectedHolidayNames[item.date] = item.name;
            });
        }
        function updateSelectedHolidayCount() {
            selectedHolidayCount.textContent = `${selectedHolidayDates.length} date(s) selected`;
        }

        function openHolidayEditModal(dateValue) {
            currentEditingDate = dateValue;
            editHolidayDate.value = dateValue;
            editHolidayName.value = selectedHolidayNames[dateValue] || 'Declared Holiday';
            holidayEditModal.style.display = 'block';
            setTimeout(() => editHolidayName.focus(), 100);
        }

        function closeHolidayModal() {
            holidayEditModal.style.display = 'none';
            currentEditingDate = null;
        }

        function toggleHolidayDate(dateValue) {
            if (selectedHolidayDates.includes(dateValue)) {
                const holidayName = selectedHolidayNames[dateValue] || 'Declared Holiday';
                Swal.fire({
                    title: "Confirm Remove",
                    text: `Are you sure you want to remove the holiday "${holidayName}" on ${formatDate(dateValue)}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#8c57ff",
                    cancelButtonColor: "#ff4c51",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectedHolidayDates = selectedHolidayDates.filter(date => date !== dateValue);
                        delete selectedHolidayNames[dateValue];
                        updateSelectedHolidayCount();
                        renderSelectedHolidayTable();
                        renderHolidayCalendar();
                    }
                });
            } else {
                selectedHolidayDates.push(dateValue);
                selectedHolidayDates.sort();
                openHolidayEditModal(dateValue);
                updateSelectedHolidayCount();
                renderSelectedHolidayTable();
                renderHolidayCalendar();
            }
        }

        function renderHolidayCalendar() {
            const {
                year,
                monthIndex
            } = getActiveHolidayMonthParts();
            const monthKey = `${year}-${String(monthIndex + 1).padStart(2, '0')}`;
            const firstDay = new Date(year, monthIndex, 1).getDay();
            const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
            const config = getHolidayConfigForMonth(monthKey);
            const declaredDates = config ? config.holidays.map(item => item.date) : [];
            const dayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
            const cells = [];

            updateHolidayMonthButton(monthIndex, year);
            updateHolidayYearButton(year);
            holidayYearRangeStart = getHolidayYearRangeStart(year);

            dayLabels.forEach(label => {
                cells.push(`<div class="calendar-cell header"><strong>${label}</strong></div>`);
            });

            for (let index = 0; index < firstDay; index++) {
                cells.push('<div class="calendar-cell empty"></div>');
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateValue = `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const currentDate = new Date(year, monthIndex, day);
                const isSunday = currentDate.getDay() === 0;
                const classes = ['calendar-cell'];
                if (selectedHolidayDates.includes(dateValue)) {
                    classes.push('selected');
                }
                if (declaredDates.includes(dateValue)) {
                    classes.push('declared');
                }
                if (selectedHolidayNames[dateValue]) {
                    classes.push('has-name');
                }
                if (isSunday) {
                    classes.push('sunday');
                }
                cells.push(`
                    <button
                        type="button"
                        class="${classes.join(' ')}"
                        data-date="${dateValue}"
                        title="${selectedHolidayNames[dateValue] || ''}"
                        ${isSunday ? 'disabled' : ''}
                    >
                        ${day}
                    </button>
                `);
            }

            holidayCalendarInline.innerHTML = cells.join('');
            holidayCalendarInline.querySelectorAll('[data-date]').forEach(cell => {
                cell.addEventListener('click', function() {
                    toggleHolidayDate(this.getAttribute('data-date'));
                });
            });
        }

        function renderHolidayRows(records) {
            return records.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${formatDate(item.date)}</td>
                    <td>${item.name}</td>
                </tr>
            `).join('');
        }

        function renderHolidayPanel() {
            const monthKey = attendanceDate.value.slice(0, 7);
            const config = getHolidayConfigForMonth(monthKey);
            holidayMonthLabel.textContent = config ? config.label : formatMonthYear(monthKey);
            const rows = getHolidayRowsForMonth(attendanceDate.value);

            if (activeHolidayMonthKey !== monthKey) {
                resetHolidaySelectionState(rows);
                activeHolidayMonthKey = monthKey;
            }

            if (selectedHolidayDates.length === 0 && rows.length > 0) {
                resetHolidaySelectionState(rows);
            }

            renderSelectedHolidayTable();
            updateSelectedHolidayCount();
            renderHolidayCalendar();
        }

        function reloadHolidayPanelFromSavedData() {
            const rows = getHolidayRowsForMonth(attendanceDate.value);
            resetHolidaySelectionState(rows);
            activeHolidayMonthKey = attendanceDate.value.slice(0, 7);
            renderHolidayPanel();
        }

        function saveSelectedHolidaysForMonth() {
            const monthKey = attendanceDate.value.slice(0, 7);
            if (!declaredHolidaysByMonth[monthKey]) {
                declaredHolidaysByMonth[monthKey] = {
                    label: formatMonthYear(monthKey),
                    description: 'Manually declared holidays',
                    holidays: []
                };
            }
            declaredHolidaysByMonth[monthKey].holidays = selectedHolidayDates
                .sort()
                .map(date => ({
                    date,
                    name: selectedHolidayNames[date] || 'Declared Holiday'
                }));
        }

        function formatAttendanceTime(time) {
            if (!time || time === '-') {
                return '-';
            }

            const date = new Date(time);

            if (isNaN(date.getTime())) {
                return '-';
            }

            return date.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        function renderRows(records) {
            return records.map((item, index) => {
                const rowDate = formatDate(item.date) || formatDate(attendanceDate.value);
                const inTime = item.in_time || '-';
                const outTime = item.out_time || '-';
                const hours = item.hours || '-';
                return `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.name}</td>
                            <td>${item.code}</td>
                            <td>${item.department}</td>
                            <td>${rowDate}</td>
                            <td>${formatAttendanceTime(inTime)}</td>
                            <td>${formatAttendanceTime(outTime)}</td>
                            <td>${hours}</td>
                            <td><span class="${getBadgeClass(item.status)}">${item.status}</span></td>
                            <td>
                                ${canViewAttendance ? `
                                    <a href="${APP_URL}/view_attendance/${item.id}"
                                    class="btn btn-sm btn-light text-primary"
                                    title="View details">
                                        <i class="ri ri-eye-line"></i>
                                    </a>
                                ` : ''}
                                ${canEditAttendance ? `
                                    <button class="btn btn-edit editAttendanceBtn"
                                            data-id="${item.id}"
                                            data-status="${item.status}"
                                            data-in="${item.in_time ? item.in_time.split(' ')[1].substring(0,5) : '-'}"
                                            data-out="${item.out_time ? item.out_time.split(' ')[1].substring(0,5) : '-'}">
                                        <i class="icon-base ri ri-edit-box-line"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
            }).join('');
        }
    
        function formatTime(time) {
            if (!time || time === '-') {
                return '';
            }
            let date = new Date(`1970-01-01 ${time}`);
            if (isNaN(date.getTime())) {
                return '';
            }
            let hours = String(date.getHours()).padStart(2, '0');
            let minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        }
        $(document).on('click', '.editAttendanceBtn', function () {
            $('.error-text').text('');
            $('#edit_in_time').removeClass('is-invalid');
            $('#edit_out_time').removeClass('is-invalid');
            $('#edit_status').removeClass('is-invalid');
            $('#attendance_id').val($(this).data('id'));
            $('#edit_status').val($(this).data('status'));
            $('#edit_in_time').val(formatTime($(this).data('in')));
            $('#edit_out_time').val(formatTime($(this).data('out')));
            $('#editAttendanceModal').modal('show');
        });
        $('#saveAttendanceBtn').on('click', function () {
            // let syncButton = document.getElementById('syncButton');
            $('.error-text').text('');
            $('#edit_in_time').removeClass('is-invalid');
            $('#edit_out_time').removeClass('is-invalid');
            $('#edit_status').removeClass('is-invalid');
            let inTime = $('#edit_in_time').val().trim();
            let outTime = $('#edit_out_time').val().trim();
            let status = ($('#edit_status').val() || '').trim();
            let hasError = false;
            if (!inTime) {
                $('#in_time_error').text('In time is required');
                $('#edit_in_time').addClass('is-invalid');
                hasError = true;
            }

            if (!status) {
                $('#status_error').text('Status is required');
                $('#edit_status').addClass('is-invalid');
                hasError = true;
            }
            if (outTime && inTime && outTime < inTime) {
                $('#out_time_error').text('Out time must be greater than in time');
                $('#edit_out_time').addClass('is-invalid');
                hasError = true;
            }
            if (hasError) {
                return;
            }
            fetch(`${APP_URL}/attendance/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: $('#attendance_id').val(),
                    in_time: inTime,
                    out_time: outTime || null,
                    status: status
                })
            })
            .then(res => res.json())
            .then(res => {

                if (res.success) {
                    $('#editAttendanceModal').modal('hide');
                    showStatus('success', res.message);
                    // syncButton.click();
                    loadAttendanceData();
                } else {
                    showStatus('danger', res.message);
                }

            })
            .catch(err => {
                console.error(err);
                showStatus('danger', 'Update failed');
            });

        });
        function destroyDataTable() {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#attendanceTable')) {
                $('#attendanceTable').DataTable().destroy();
            }
        }
        let staffReportDataTable = null;
        function initStaffReportDataTable() {
            if ($.fn.DataTable.isDataTable('#staffReportTable')) {
                $('#staffReportTable').DataTable().clear().destroy();
            }
            $('#staffReportTable tbody').off();
            staffReportDataTable = $('#staffReportTable').DataTable({
                destroy: true,
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                dom: 'rtip'
            });
        }
        function initDataTable() {
            if (!$.fn.DataTable) return;
            destroyDataTable();
            attendanceDataTable = $('#attendanceTable').DataTable({
                paging: true,
                searching: true,
                info: false,
                lengthChange: false,
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                columnDefs: [{
                    orderable: false,
                    targets: 8
                }]
            });
        }

        function getCurrentRecords() {
            /* const selectedHoliday = isDeclaredHoliday(attendanceDate.value);
            if (selectedHoliday && (currentView === 'absent' || currentView === 'missing')) {
                return [];
            } */
            if (currentView === 'absent') {
                return attendanceRecords.filter(item => item.status === 'Absent');
            }
            if (currentView === 'late') {
                return attendanceRecords.filter(item => item.status === 'Late');
            }
            if (currentView === 'missing') {
                return attendanceRecords.filter(item => item.status === 'Missing Time Card');
            }
            return attendanceRecords;
        }

        function renderTable() {
            if (currentView === 'holiday') {
                destroyDataTable();
                noDataMessage.style.display = 'none';
                attendanceTableContainer.classList.add('d-none');
                staffReportPanel.classList.add('d-none');
                holidayPanel.classList.remove('d-none');
                renderHolidayPanel();
                return;
            }

            if (currentView === 'staff') {
                destroyDataTable();
                noDataMessage.style.display = 'none';
                attendanceTableContainer.classList.add('d-none');
                holidayPanel.classList.add('d-none');
                staffReportPanel.classList.remove('d-none');
                renderStaffReportPanel();
                return;
            }

            holidayPanel.classList.add('d-none');
            staffReportPanel.classList.add('d-none');
            attendanceTableContainer.classList.remove('d-none');
            destroyDataTable();
            const records = getCurrentRecords();

            if (records.length === 0) {
                attendanceBody.innerHTML = '';
                destroyDataTable();
                noDataMessage.style.display = 'block';
                return;
            }

            noDataMessage.style.display = 'none';
            attendanceBody.innerHTML = renderRows(records);
            initDataTable();

           /*  if (attendanceDataTable && searchInput.value.trim()) {
                attendanceDataTable.search(searchInput.value.trim()).draw();
            } */
        }

        function setActiveView(view) {
            currentView = view;
            viewAllButton.classList.toggle('active', view === 'all');
            viewAbsentButton.classList.toggle('active', view === 'absent');
            viewLateButton.classList.toggle('active', view === 'late');
            viewMissingButton.classList.toggle('active', view === 'missing');
            viewHolidaysButton.classList.toggle('active', view === 'holiday');
            viewStaffWiseButton.classList.toggle('active', view === 'staff');
            viewAllButton.setAttribute('aria-selected', view === 'all' ? 'true' : 'false');
            viewAbsentButton.setAttribute('aria-selected', view === 'absent' ? 'true' : 'false');
            viewLateButton.setAttribute('aria-selected', view === 'late' ? 'true' : 'false');
            viewMissingButton.setAttribute('aria-selected', view === 'missing' ? 'true' : 'false');
            viewHolidaysButton.setAttribute('aria-selected', view === 'holiday' ? 'true' : 'false');
            viewStaffWiseButton.setAttribute('aria-selected', view === 'staff' ? 'true' : 'false');
            viewModeLabel.textContent = view === 'all' ?
                'Showing all attendance records' :
                view === 'absent' ?
                'Showing absent report records' :
                view === 'late' ?
                'Showing later comer report records' :
                view === 'missing' ?
                'Showing missing time card records' :
                view === 'staff' ?
                'Showing staff wise attendance report' :
                'Showing declared holiday settings';
                
            const exportExcelBtn = document.getElementById('exportExcelBtn');
            if (exportExcelBtn) {
                if (view === 'all' || view === 'staff') {
                    exportExcelBtn.classList.remove('d-none');
                } else {
                    exportExcelBtn.classList.add('d-none');
                }
            }
            
            renderTable();
        }

        function resetAttendancePage() {
            attendanceRecords = [];
            // searchInput.value = '';
            deviceSelect.value = '';
            attendanceDate.value = new Date().toISOString().slice(0, 10);
            lastSyncedText.textContent = 'Not synced yet';
            // syncButton.disabled = false;
            // syncButtonLabel.textContent = 'Sync Attendance';
            setActiveView('all');
            updateHolidayBanner();
            showStatus('info', 'Attendance view reset.');
        }

        viewAllButton.addEventListener('click', function() {
            setActiveView('all');
        });

        viewAbsentButton.addEventListener('click', function() {
            setActiveView('absent');
        });

        viewLateButton.addEventListener('click', function() {
            setActiveView('late');
        });

        viewMissingButton.addEventListener('click', function() {
            setActiveView('missing');
        });

        viewHolidaysButton.addEventListener('click', function() {
            setActiveView('holiday');
            fetchHolidays();
        });

        viewStaffWiseButton.addEventListener('click', function() {
            setActiveView('staff');
        });

        const exportExcelBtn = document.getElementById('exportExcelBtn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function() {
                if (currentView === 'staff') {
                    if (!staffReportRecords || staffReportRecords.length === 0) {
                        showStatus('warning', 'No data available to export.');
                        return;
                    }
                    
                    const empCode = staffReportEmployee.value;
                    const month = staffReportMonth.value;
                    const fromDate = staffReportFromDate.value;
                    const toDate = staffReportToDate.value;
                    
                    let url = `${APP_URL}/export-staff-report?emp_code=${empCode}`;
                    if (month) url += `&month=${month}`;
                    if (fromDate) url += `&from_date=${fromDate}`;
                    if (toDate) url += `&to_date=${toDate}`;
                    
                    window.location.href = url;
                    return;
                }

                if (!attendanceRecords || attendanceRecords.length === 0) {
                    showStatus('warning', 'No data available to export.');
                    return;
                }
                
                const recordsToExport = getCurrentRecords();
                if(recordsToExport.length === 0) {
                    showStatus('warning', 'No data available for the current view.');
                    return;
                }

                let csvContent = "S.No,Employee Name,Employee Code,Department,Date,In Time,Out Time,Hours,Status\n";

                recordsToExport.forEach((item, index) => {
                    const rowDate = formatDate(item.date) || formatDate(attendanceDate.value);
                    const inTime = item.in_time || '-';
                    const outTime = item.out_time || '-';
                    const hours = item.hours || '-';
                    const row = [
                        index + 1,
                        `"${item.name}"`,
                        `"${item.code}"`,
                        `"${item.department || ''}"`,
                        `"${rowDate}"`,
                        `"${formatAttendanceTime(inTime)}"`,
                        `"${formatAttendanceTime(outTime)}"`,
                        `"${hours}"`,
                        `"${item.status}"`
                    ].join(",");
                    csvContent += row + "\n";
                });

                const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.setAttribute("href", url);
                link.setAttribute("download", `Attendance_Export_${attendanceDate.value}_${currentView}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }


        resetButton.addEventListener('click', resetAttendancePage);

        attendanceDate.addEventListener('change', function () {
            fetchHolidays();

            updateHolidayBanner();

            if (!staffReportMonth.value) {
                staffReportMonth.value = attendanceDate.value.slice(0, 7);
            }

            if (currentView === 'staff') {
                renderStaffReportPanel();
            }
        });

        saveHolidayEditButton.addEventListener('click', function() {
            if (!editHolidayName.value.trim()) {
                showStatus('warning', 'Please enter a holiday name.');
                return;
            }
            if (currentEditingDate) {
                selectedHolidayNames[currentEditingDate] = editHolidayName.value.trim();
            }
            closeHolidayModal();
            const displayRows = selectedHolidayDates
                .sort()
                .map(date => ({
                    date,
                    name: selectedHolidayNames[date] || 'Declared Holiday'
                }));
            holidayBody.innerHTML = displayRows.length ?
                renderHolidayRows(displayRows) :
                '<tr><td colspan="3" class="text-center text-muted py-3">No holidays declared for this month.</td></tr>';
            renderHolidayCalendar();
        });

        closeHolidayEditModal.addEventListener('click', closeHolidayModal);
        cancelHolidayEditButton.addEventListener('click', closeHolidayModal);

        holidayEditModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeHolidayModal();
            }
        });

        /* saveSelectedHolidaysButton.addEventListener('click', function() {
            saveSelectedHolidaysForMonth();
            showStatus('success', 'Selected holidays saved for the month.');
            renderHolidayPanel();
            updateHolidayBanner();
        }); */
        saveSelectedHolidaysButton.addEventListener('click', function () {
            const holidays = selectedHolidayDates.map(date => ({
                date: date,
                name: selectedHolidayNames[date] || 'Declared Holiday'
            }));
            saveSelectedHolidaysButton.disabled = true;
            const originalText = saveSelectedHolidaysButton.innerHTML;
            saveSelectedHolidaysButton.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Saving...
            `;
            fetch(`${APP_URL}/holidays/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ holidays })
            })
            .then(res => res.json())
            .then(() => {
                showStatus('success', 'Holidays saved to database');
                fetchHolidays(); // reload
            }).catch(err => {
                console.error(err);
                showStatus('danger', 'Failed to save holidays');
            })
            .finally(() => {
                saveSelectedHolidaysButton.disabled = false;
                saveSelectedHolidaysButton.innerHTML = originalText;
            });
        });

        refreshHolidaysButton.addEventListener('click', function() {
            reloadHolidayPanelFromSavedData();
            showStatus('info', 'Holiday list refreshed from saved data.');
        });

        holidayPrevMonthButton.addEventListener('click', function() {
            const {
                year,
                monthIndex
            } = getActiveHolidayMonthParts();
            const nextDate = new Date(year, monthIndex - 1, 1);
            updateAttendanceDateForMonth(nextDate.getFullYear(), nextDate.getMonth());
            updateHolidayBanner();
            renderHolidayPanel();
            fetchHolidays();
        });

        holidayNextMonthButton.addEventListener('click', function() {
            const {
                year,
                monthIndex
            } = getActiveHolidayMonthParts();
            const nextDate = new Date(year, monthIndex + 1, 1);
            updateAttendanceDateForMonth(nextDate.getFullYear(), nextDate.getMonth());
            updateHolidayBanner();
            renderHolidayPanel();
            fetchHolidays();
        });

        holidayTodayButton.addEventListener('click', function() {
            const today = new Date();
            updateAttendanceDateForMonth(today.getFullYear(), today.getMonth());
            updateHolidayBanner();
            renderHolidayPanel();
        });

        holidayYearButton.addEventListener('click', function(e) {
            e.stopPropagation();
            closeHolidayMonthPopup();
            const {
                year
            } = getActiveHolidayMonthParts();
            if (holidayYearPopup.classList.contains('d-none')) {
                openHolidayYearPopup(year);
            } else {
                closeHolidayYearPopup();
            }
        });

        holidayMonthButton.addEventListener('click', function(e) {
            e.stopPropagation();
            closeHolidayYearPopup();
            const {
                monthIndex
            } = getActiveHolidayMonthParts();
            if (holidayMonthPopup.classList.contains('d-none')) {
                openHolidayMonthPopup(monthIndex);
            } else {
                closeHolidayMonthPopup();
            }
        });

        holidayYearRangePrev.addEventListener('click', function(e) {
            e.stopPropagation();
            holidayYearRangeStart -= 10;
            renderHolidayYearPopup(getActiveHolidayMonthParts().year);
        });

        holidayYearRangeNext.addEventListener('click', function(e) {
            e.stopPropagation();
            holidayYearRangeStart += 10;
            renderHolidayYearPopup(getActiveHolidayMonthParts().year);
        });

        document.addEventListener('click', function(e) {
            if (!holidayMonthPopup.classList.contains('d-none') && !e.target.closest('.holiday-month-picker')) {
                closeHolidayMonthPopup();
            }
            if (!holidayYearPopup.classList.contains('d-none') && !e.target.closest('.holiday-year-picker')) {
                closeHolidayYearPopup();
            }
        });

        staffReportMonth.addEventListener('change', function() {
            updateStaffReportRangeFields();
            if (currentView === 'staff' && staffReportEmployee.value) {
                loadStaffReport();
            }
        });

        staffReportFromDate.addEventListener('change', function() {
            staffReportMonth.value = '';
            if (staffReportFromDate.value) {
                staffReportToDate.min = staffReportFromDate.value;
                if (staffReportToDate.value && staffReportToDate.value < staffReportFromDate.value) {
                    staffReportToDate.value = staffReportFromDate.value;
                }
            } else {
                staffReportToDate.removeAttribute('min');
            }
            updateStaffReportRangeFields();
            if (currentView === 'staff' && staffReportEmployee.value) {
                loadStaffReport();
            }
        });

        staffReportToDate.addEventListener('change', function() {
            staffReportMonth.value = '';
            updateStaffReportRangeFields();
            if (currentView === 'staff' && staffReportEmployee.value) {
                loadStaffReport();
            }
        });

        staffReportEmployee.addEventListener('change', function() {
            if (currentView === 'staff') {
                loadStaffReport();
            }
        });

        staffReportGenerateButton.addEventListener('click', function() {
            console.log("show report clicked.");
            console.log(currentView);
            
            if (currentView === 'staff') {
                loadStaffReport();
            }
        });
        const syncLoader = document.getElementById('syncLoader');
        function loadAttendanceData() {
            const device = deviceSelect.value;
            const date = attendanceDate.value;
            if (!device || !date) {
                return;
            }
            syncLoader.classList.remove('d-none');
            fetch(`${APP_URL}/attendance-records?date=${date}&device=${device}`)
                .then(res => res.json())
                .then(res => {
                    attendanceRecords = res.data || [];
                    
                    // Update Summary Cards
                    if (attendanceRecords.length > 0) {
                        document.getElementById('dailySummaryCards').style.display = 'flex';
                        document.getElementById('summaryTotalCount').textContent = attendanceRecords.length;
                        document.getElementById('summaryPresentCount').textContent = attendanceRecords.filter(r => r.status === 'Present' || r.status === 'Late' || r.status === 'Overtime').length;
                        document.getElementById('summaryAbsentCount').textContent = attendanceRecords.filter(r => r.status === 'Absent' || r.status === 'Missing Time Card').length;
                        document.getElementById('summaryLateCount').textContent = attendanceRecords.filter(r => r.status === 'Late').length;
                        document.getElementById('summaryOvertimeCount').textContent = attendanceRecords.filter(r => r.status === 'Overtime').length;
                    } else {
                        document.getElementById('dailySummaryCards').style.display = 'none';
                    }

                    renderTable();
                    syncLoader.classList.add('d-none');
                    showStatus(
                        'success',
                        'Attendance loaded successfully.'
                    );
                })
                .catch(err => {
                    console.error(err);
                    syncLoader.classList.add('d-none');
                    showStatus(
                        'danger',
                        'Failed to load attendance records.'
                    );
                });
        }

        attendanceDate.addEventListener('input', loadAttendanceData);

        $('#deviceSelect').on('change', loadAttendanceData);

        populateStaffEmployeeOptions();
        staffReportMonth.value = attendanceDate.value.slice(0, 7);
        updateStaffReportRangeFields();
        setActiveView('all');
        updateHolidayBanner();
    });
</script>
@endsection
