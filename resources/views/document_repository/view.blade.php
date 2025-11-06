@extends('layouts.common')
@section('title', 'Document Repository - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Document Repository</h4>
                <a class="btn btn-primary" href="{{ url('add_document_repository') }}">
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
                                    <th>Document Name</th>
                                    <th>Document Type</th>
                                    <th>Department</th>
                                    <th>Validity Date</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>ISO Certification 2024</td>
                                    <td>Certification</td>
                                    <td>Quality</td>
                                    <td>31-12-2025</td>
                                    <td>
                                        <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="pdf" class="table-img"></a>
                                    </td>
                                    <td>
                                        <div class="button-btn">
                                            <a href="{{ url('view_document_repository') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_document_repository') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Fire Safety Policy</td>
                                    <td>Compliance</td>
                                    <td>Safety</td>
                                    <td>31-03-2026</td>
                                    <td>
                                        <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="pdf" class="table-img"></a>
                                    </td>
                                    <td>
                                        <div class="button-btn">
                                            <a href="{{ url('view_document_repository') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_document_repository') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Supplier Agreement</td>
                                    <td>Contract</td>
                                    <td>Procurement</td>
                                    <td>15-09-2025</td>
                                    <td>
                                        <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="pdf" class="table-img"></a>
                                    </td>
                                    <td>
                                        <div class="button-btn">
                                            <a href="{{ url('view_document_repository') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_document_repository') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
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