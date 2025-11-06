@extends('layouts.common')
@section('title', 'Stock Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Stock Report</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Stock No</th>
                                    <th>Material Category</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Location / Store</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>STOCK001</td>
                                    <td>Fabric <span class="mini-title">(MC001)</span></td>
                                    <td>Cotton Poplin 60 GSM(M)</td>
                                    <td>1000</td>
                                    <td>Warehouse A</td>
                                    <td>₹7,000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>STOCK002</td>
                                    <td>Accessories <span class="mini-title">(MC002)</span></td>
                                    <td>Interlinings(CM)</td>
                                    <td>4</td>
                                    <td>Warehouse AB</td>
                                    <td>₹200</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>STOCK003</td>
                                    <td>Accessories <span class="mini-title">(MC002)</span></td>
                                    <td>Collar Stays(CM)</td>
                                    <td>10</td>
                                    <td>Warehouse B</td>
                                    <td>₹100</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>STOCK004</td>
                                    <td>Trims <span class="mini-title">(MC003)</span></td>
                                    <td>Zippers(PCS)</td>
                                    <td>20</td>
                                    <td>Warehouse B</td>
                                    <td>₹1400</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>STOCK005</td>
                                    <td>Trims <span class="mini-title">(MC003)</span></td>
                                    <td>Elastic Bands(M)</td>
                                    <td>9</td>
                                    <td>Warehouse N</td>
                                    <td>₹98,900</td>
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
                <h5 class="modal-title" id="generateReportLabel">Generate Stock Report</h5>
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