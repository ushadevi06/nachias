@extends('layouts.common')
@section('title', 'Daily Production Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Daily Production Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Daily Production Report</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card No</th>
                                    <th>Production Stage</th>
                                    <th>Service Provider</th>
                                    <th>Planned Quantity</th>
                                    <th>Completed Quantity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Cutting</td>
                                    <td>In-House Cutting <span class="mini-title">(SP003)</span></td>
                                    <td>5</td>
                                    <td>5</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Stitching</td>
                                    <td>Vendor A Stitching <span class="mini-title">(SP004)</span></td>
                                    <td>5</td>
                                    <td>0</td>
                                    <td><span class="badge bg-danger">Not Started</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Cutting</td>
                                    <td>In-House Cutting <span class="mini-title">(SP003)</span></td>
                                    <td>10</td>
                                    <td>8</td>
                                    <td><span class="badge bg-warning">Inprogress</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Stitching</td>
                                    <td>Vendor A Stitching <span class="mini-title">(SP004)</span></td>
                                    <td>10</td>
                                    <td>5</td>
                                    <td><span class="badge bg-warning">Inprogress</span></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>JC20250924-003-K</td>
                                    <td>Cutting</td>
                                    <td>In-House Cutting <span class="mini-title">(SP003)</span></td>
                                    <td>9</td>
                                    <td>9</td>
                                    <td><span class="badge bg-success">Completed</span></td>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="generateReportLabel">Generate Daily Production Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="report_type" class="form-select select2" data-placeholder="Select Report Type">
                                <option value="">Select Report Type</option>
                                <option value="Daily" selected>Daily</option>
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
                            <label for="date_range">Date </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="job_card_no" class="form-select select2" data-placeholder="Select Job Card No">
                                <option value="">Select Job Card No</option>
                                <option value="JC20250924-001-K">JC20250924-001-K</option>
                                <option value="JC20250924-002-K">JC20250924-002-K</option>
                                <option value="JC20250924-003-K">JC20250924-003-K</option>
                            </select>
                            <label for="job_card_no">Job Card No * </label>
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