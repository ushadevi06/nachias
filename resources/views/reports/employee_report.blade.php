@extends('layouts.common')
@section('title', 'Employee Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Employee Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Employee Report</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Date of Joining</th>
                                    <th>Attendance(Days)</th>
                                    <th>Total Production</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                    <td>Cutting</td>
                                    <td>Manager</td>
                                    <td>02-01-2024</td>
                                    <td>23</td>
                                    <td>500</td>
                                    <td>₹65,000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>Stitching</td>
                                    <td>Supervisior</td>
                                    <td>26-09-2021</td>
                                    <td>21</td>
                                    <td>300</td>
                                    <td>₹35,000</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>Ironing</td>
                                    <td>Worker</td>
                                    <td>05-09-2019</td>
                                    <td>20</td>
                                    <td>182</td>
                                    <td>₹15,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="generateReport" tabindex="-1" aria-labelledby="generateReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateReportLabel">Generate Employee Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="report_type" class="form-select select2" data-placeholder="Select Report Type">
                                <option value="">Select Report Type</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annual">Annual</option>
                            </select>
                            <label for="report_type">Report Type * </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select Date Range">
                            <label for="date_range">Date Range </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="leave_type">Export Format * </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="excel" id="excel" />
                            <label class="form-check-label" for="excel"> Excel </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="pdf" id="pdf" />
                            <label class="form-check-label" for="pdf"> PDF </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="csv" id="csv" />
                            <label class="form-check-label" for="csv"> CSV </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save/Generate</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection