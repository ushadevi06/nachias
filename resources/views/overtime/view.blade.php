@extends('layouts.common')
@section('title', 'Overtime / Bonus - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Overtime / Bonus</h4>
                <a class="btn btn-primary" href="{{ url('add_overtime') }}">
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
                                    <th>Overtime Hours</th>
                                    <th>Overtime Rate</th>
                                    <th>Overtime Amount</th>
                                    <th>Bonus Type</th>
                                    <th>Bonus Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Sep 2025</td>
                                    <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                                    <td>10</td>
                                    <td>₹300</td>
                                    <td>₹3,000</td>
                                    <td>Festival</td>
                                    <td>₹2,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Sep 2025</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>5</td>
                                    <td>₹250</td>
                                    <td>₹1,250</td>
                                    <td>Performance</td>
                                    <td>₹1,500</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Sep 2025</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>5</td>
                                    <td>₹200</td>
                                    <td>₹1,600</td>
                                    <td>Production</td>
                                    <td>₹1,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_overtime') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                            <a href="{{ url('add_overtime') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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