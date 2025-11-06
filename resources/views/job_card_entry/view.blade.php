@extends('layouts.common')
@section('title', 'Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Job Card Entry</h4>
                <a class="btn btn-primary" href="{{ url('add_job_card_entry') }}">
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
                                    <th>Job Card Number</th>
                                    <th>Sales Order </th>
                                    <th>Customer / Buyer Name</th>
                                    <th>Delivery Date </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>JC20250924-001-K</td>
                                    <td>S0-1001</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>24-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_jc_item') }}" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>JC20250924-002-K</td>
                                    <td>SO-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>21-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_jc_item') }}" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>JC20250924-003-K</td>
                                    <td>SO-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>21-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_jc_item') }}" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
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