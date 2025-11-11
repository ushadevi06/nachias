@extends('layouts.common')
@section('title', 'Order Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Order Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Order Report</button>
            </div>
            <div class="card">
                <di class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Date</th>
                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th>Ordered Quantity</th>
                                    <th>Delivered Quantity</th>
                                    <th>Pending Quantity</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>25-09-25</td>
                                    <td>SO-1001</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>100</td>
                                    <td>50</td>
                                    <td>50</td>
                                    <td>30-09-2025</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>26-09-2025</td>
                                    <td>SO-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>300</td>
                                    <td>300</td>
                                    <td>0</td>
                                    <td>02-10-25</td>
                                    <td><span class="badge bg-success">Dispatched</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>28-09-2025</td>
                                    <td>SO-1003</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>60</td>
                                    <td>30</td>
                                    <td>30</td>
                                    <td>03-10-2025</td>
                                    <td><span class="badge bg-info">In Production</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>28-09-2025</td>
                                    <td>SO-1004</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>80</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>-</td>
                                    <td><span class="badge bg-danger">Cancelled</span></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>28-09-2025</td>
                                    <td>SO-1005</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>60</td>
                                    <td>20</td>
                                    <td>40</td>
                                    <td>05-01-2025</td>
                                    <td><span class="badge bg-info">In Production</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </di>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="generateReport" tabindex="-1" aria-labelledby="generateReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="generateReportLabel">Generate Order Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <div class="form-floating form-floating-outline">
                            <select id="department_module" class="form-select select2" data-placeholder="Select Department / Module">
                                <option value="">Select Department / Module</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Retail">Retail</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Online">Online</option>
                                <option value="Distribution">Distribution</option>
                            </select>
                            <label for="department_module">Department / Module * </label>
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