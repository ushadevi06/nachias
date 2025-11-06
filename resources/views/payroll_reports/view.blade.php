@extends('layouts.common')
@section('title', 'Payroll Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Payroll Reports</h4>
                <a class="btn btn-primary" href="{{ url('add_payroll_report') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Report Name</th>
                                    <th>Report Type</th>
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Gross Salary</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Monthly Payroll Sep</td>
                                    <td>Monthly</td>
                                    <td>Sep 2025</td>
                                    <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                                    <td>Production</td>
                                    <td>₹50,000</td>
                                    <td>₹45,000</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                    <td>
                                        <div class="button-btn">
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Annual Payroll 2024</td>
                                    <td>Annual</td>
                                    <td>Sep 2025</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>HR</td>
                                    <td>₹60,000</td>
                                    <td>₹53,500</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                    <td>
                                        <div class="button-btn">
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Dept-wise Payroll</td>
                                    <td>Dept-wise</td>
                                    <td>Sep 2025</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>Production</td>
                                    <td>₹52,000</td>
                                    <td>₹47,500</td>
                                    <td><span class="badge bg-success">Processed</span></td>
                                    <td>
                                        <div class="button-btn">
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
@endsection