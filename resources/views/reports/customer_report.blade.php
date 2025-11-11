@extends('layouts.common')
@section('title', 'Customer Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Customer Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Customer Report</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>SO-1001</td>
                                    <td>01-09-2025</td>
                                    <td>₹78,400</td>
                                    <td>Cash</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>SO-1002</td>
                                    <td>06-09-2025</td>
                                    <td>₹23,400</td>
                                    <td>UPI</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>SO-1003</td>
                                    <td>16-09-2025</td>
                                    <td>₹13,000</td>
                                    <td>UPI</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>SO-1004</td>
                                    <td>17-09-2025</td>
                                    <td>₹18,400</td>
                                    <td>UPI</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>SO-1005</td>
                                    <td>20-09-2025</td>
                                    <td>₹98,900</td>
                                    <td>UPI</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
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
                <h5 class="modal-title text-white" id="generateReportLabel">Generate Customer Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="report_name" placeholder="Enter Report Name" name="report_name">
                            <label for="report_name">Report Name * </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="selected_fields" class="form-select select2" multiple="multiple" data-placeholder="Select Fields">
                                <option value="SalesOrderDate">Sales Order Date</option>
                                <option value="CustomerName">Customer Name</option>
                                <option value="Amount">Amount</option>
                                <option value="Status">Status</option>
                                <option value="Department">Department</option>
                            </select>
                            <label for="selected_fields">Selected Fields * </label>
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
                            <select id="status" class="form-select select2" data-placeholder="Select Status">
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <label for="status">Select Status * </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4 mb-3">
                        <div class="form-floating form-floating-outline">
                            <select id="customer" class="select2 form-select" data-placeholder="Select Customer">
                                <option value="">Select Customer</option>
                                <option value="All">All</option>
                                <option value="Hero Mens Wear(CUS001)">Hero Mens Wear(CUS001)</option>
                                <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store(CUS002)</option>
                            </select>
                            <label for="customer">Customer </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Select Minimum Amount">
                            <label for="min_amount">Minimum Amount </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="max_amount" id="max_amount" class="form-control" placeholder="Select Maximum Amount">
                            <label for="max_amount">Maximum Amount </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="grouping" class="select2 form-select" data-placeholder="Select Grouping">
                                <option value="">Select Grouping</option>
                                <option value="SalesOrderDate">By Customer</option>
                                <option value="By Product">By Product</option>
                                <option value="By Department">By Department</option>
                            </select>
                            <label for="grouping">Grouping </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4 align-self-center">
                        <label class="form-label">Sorting Options *</label><br>
                        <div class="form-check-inline">
                            <input class="form-check-input" type="radio" name="sorting_option" id="ascending_option" value="ascending" checked>
                            <label class="form-check-label" for="ascending_option">
                                Ascending
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <input class="form-check-input" type="radio" name="sorting_option" id="descending_option" value="descending">
                            <label class="form-check-label" for="descending_option">
                                Descending
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div>
                            <label class="form-label me-2">Save Template</label>
                            <label class="switch switch-success switch-xl">
                                <input type="checkbox" class="switch-input" checked>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on">Yes</span>
                                    <span class="switch-off">No</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="leave_type">Export Format * </label>
                        <div class="form-check form-check-inline mb-2 mt-5">
                            <input class="form-check-input" type="checkbox" value="1" name="excel" id="excel" />
                            <label class="form-check-label" for="excel"> Excel </label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
                            <input class="form-check-input" type="checkbox" value="1" name="pdf" id="pdf" />
                            <label class="form-check-label" for="pdf"> PDF </label>
                        </div>
                        <div class="form-check form-check-inline mb-2">
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