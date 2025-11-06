@extends('layouts.common')
@section('title', 'Salary Calculation - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Salary Calculation</h4>
                <a class="btn btn-primary" href="{{ url('add_salary_calculation') }}">
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
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Gross</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Sep 2025</td>
                                    <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                                    <td>₹65,000</td>
                                    <td>₹5,000</td>
                                    <td>₹60,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_salary_calculation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_salary_calculation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Sep 2025</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>₹60,500</td>
                                    <td>₹6,500</td>
                                    <td>₹54,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_salary_calculation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_salary_calculation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Sep 2025</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>₹52,000</td>
                                    <td>₹4,500</td>
                                    <td>₹47,500</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_salary_calculation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_salary_calculation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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