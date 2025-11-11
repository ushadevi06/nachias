@extends('layouts.common')
@section('title', 'Attendances - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Attendances</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                                    <td>27-09-2025</td>
                                    <td>09:06 AM</td>
                                    <td>06:30 PM</td>
                                    <td>9</td>
                                    <td><span class="badge bg-success">Present</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_attendance') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>27-09-2025</td>
                                    <td>09:30 AM</td>
                                    <td>06:00 PM</td>
                                    <td>8.5</td>
                                    <td><span class="badge bg-danger">Late</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_attendance') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>27-09-2025</td>
                                    <td>08:45 AM</td>
                                    <td>07:00 PM</td>
                                    <td>10.25</td>
                                    <td><span class="badge bg-warning">Overtime</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_attendance') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                                    <td>26-09-2025</td>
                                    <td>09:00 AM</td>
                                    <td>05:00 PM</td>
                                    <td>8</td>
                                    <td><span class="badge bg-success">Present</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_attendance') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#correctAttendanceModal"><i class="icon-base ri ri-edit-box-line"></i></a>
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
<div class="modal fade" id="correctAttendanceModal" tabindex="-1" aria-labelledby="correctAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="correctAttendanceModalLabel">Correct Attendance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="employee" name="employee" value="Ramesh Kumar(EMP001)" readonly>
                            <label for="employee">Employee * </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="attendanceDate" class="form-label">Date</label>
                        <input type="text" class="form-control" id="attendanceDate" value="27-09-2025" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="correctHours" class="form-label">Total Hours</label>
                        <input type="number" class="form-control" id="correctHours" value="9">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Total Status</label>
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" data-placeholder="Select Status">
                                <option value="">Select Status</option>
                                <option value="Present" selected>Present</option>
                                <option value="Late">Late</option>
                                <option value="Absent">Absent</option>
                                <option value="Overtime">Overtime</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection