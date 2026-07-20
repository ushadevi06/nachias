@extends('layouts.common')
@section('title', 'Employee Attendance - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Employee Attendance</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Employee Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                @php
                    $profileImg = $user->profile_image && file_exists(public_path('uploads/employee/' . $user->id . '/' . $user->profile_image)) 
                        ? url('uploads/employee/' . $user->id . '/' . $user->profile_image) 
                        : asset('assets/images/user.png');
                @endphp
                <img src="{{ $profileImg }}" alt="Profile" class="rounded-circle me-4" style="width: 80px; height: 80px; object-fit: cover;">
                <div>
                    <h5 class="fw-bold mb-1">Hello, {{ $user->name }}!</h5>
                    <p class="text-muted mb-0"><span class="badge bg-light text-dark">{{ $user->emp_id }}</span> &nbsp; {{ date('l, d M Y') }} <span id="clock" class="fw-bold ms-2"></span></p>
                    <div class="mt-2 text-primary">
                        <i class="ri ri-time-line"></i> In Time: <strong>{{ $todayRecord && $todayRecord->in_time ? date('h:i A', strtotime($todayRecord->in_time)) : '--:--' }}</strong>
                        @if($todayRecord && $todayRecord->out_time)
                            <span class="ms-3"><i class="ri ri-time-line"></i> Out Time: <strong>{{ date('h:i A', strtotime($todayRecord->out_time)) }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <!-- Punch In/Out button removed as requested -->
                <button class="btn btn-primary" disabled>
                    @if($todayRecord && $todayRecord->in_time && !$todayRecord->out_time)
                        Punched In
                    @elseif($todayRecord && $todayRecord->out_time)
                        Punched Out
                    @else
                        No Punch Log Today
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-light-primary border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Total Hours Today</p>
                        <h3 class="fw-bold text-primary mb-0" id="kpi-today-hours">{{ number_format($todayHours, 2) }} <span class="fs-6 fw-normal">hrs</span></h3>
                    </div>
                    <div class="kpi-icon bg-white text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri ri-time-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-light-success border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">No of Late This Month</p>
                        <h3 class="fw-bold text-success mb-0" id="kpi-late-count">{{ $monthlyLateCount }} <span class="fs-6 fw-normal">days</span></h3>
                    </div>
                    <div class="kpi-icon bg-white text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri ri-alarm-warning-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-light-danger border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">No of Absent This Month</p>
                        <h3 class="fw-bold text-danger mb-0" id="kpi-absent-count">{{ $monthlyAbsentCount }} <span class="fs-6 fw-normal">days</span></h3>
                    </div>
                    <div class="kpi-icon bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri ri-user-unfollow-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card bg-light-warning border-0 shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Overtime Days This Month</p>
                        <h3 class="fw-bold text-warning mb-0" id="kpi-overtime-count">{{ $monthlyOvertimeDays }} <span class="fs-6 fw-normal">days</span></h3>
                    </div>
                    <div class="kpi-icon bg-white text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri ri-history-line fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Filter</h5>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Month</label>
                    <input type="text" id="attendance_month" class="form-control" placeholder="Select Month" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select id="status_filter" class="select2 form-select" data-placeholder="Select Status">
                        <option value="">Select Status</option>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Overtime">Overtime</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button id="filter_btn" class="btn btn-primary">Filter</button>
                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-bold mb-0">Attendance History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover w-100" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Late Hrs</th>
                            <th>Overtime Hrs</th>
                            <th>Working Hrs</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Clock
        setInterval(() => {
            const now = new Date();
            $('#clock').text(now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true }));
        }, 1000);

        // Live Total Hours Today
        @if($todayRecord && $todayRecord->in_time && !$todayRecord->out_time)
        (function() {
            var punchIn = new Date('{{ date('Y-m-d', strtotime($todayRecord->date)) }}T{{ date('H:i:s', strtotime($todayRecord->in_time)) }}');
            function updateTodayHours() {
                var now = new Date();
                var diff = (now - punchIn) / 1000 / 3600;
                if (diff < 0) diff = 0;
                var hrs = Math.floor(diff);
                var mins = Math.floor((diff - hrs) * 60);
                $('#kpi-today-hours').html(
                    String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') +
                    ' <span class="fs-6 fw-normal">hrs</span>'
                );
            }
            updateTodayHours();
            setInterval(updateTodayHours, 1000);
        })();
        @endif

        // Datepickers
        var fp = flatpickr("#attendance_month", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: "F Y",
                    altFormat: "F Y"
                })
            ]
        });
        fp.setDate(new Date());

        // DataTable
        var table = $('#attendanceTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('employee_attendance/data') }}",
                data: function(d) {
                    d.month_year = $('#attendance_month').val();
                    d.status = $('#status_filter').val();
                }
            },
            columns: [
                { data: 'date' },
                { data: 'in_time' },
                { data: 'out_time' },
                {
                    data: 'status',
                    render: function(data) {
                        let badge = 'bg-secondary';

                        if (data === 'Present') badge = 'bg-success';
                        else if (data === 'Late') badge = 'bg-warning text-dark';
                        else if (data === 'Overtime') badge = 'bg-info';
                        else if (data === 'Absent') badge = 'bg-danger';
                        else if (data === 'Punch Out Missing') badge = 'bg-danger';
                        else if (data === 'Holiday') badge = 'bg-primary';
                        else if (data === 'Week Off') badge = 'bg-secondary';

                        return `<span class="badge ${badge}">${data || '-'}</span>`;
                    }
                },
                { data: 'late' },
                { data: 'overtime' },
                { data: 'working_hours' }
            ],
            order: [[0, 'desc']],
            pageLength: 10
        });

        function refreshKpiCounts() {
            $.get("{{ url('employee_attendance/kpi-counts') }}", { month_year: $('#attendance_month').val() }, function(res) {
                $('#kpi-late-count').html(res.lateCount + ' <span class="fs-6 fw-normal">days</span>');
                $('#kpi-absent-count').html(res.absentCount + ' <span class="fs-6 fw-normal">days</span>');
                $('#kpi-overtime-count').html(res.overtimeDays + ' <span class="fs-6 fw-normal">days</span>');
            });
        }

        $('#filter_btn').click(function() {
            table.ajax.reload();
            refreshKpiCounts();
        });
        $('#resetBtn').click(function () {
            $('#status_filter').val('').trigger('change');
            $('#attendance_month').val('');
            fp.setDate(new Date());
            table.ajax.reload();
            refreshKpiCounts();
        });
    });
</script>
@endsection
