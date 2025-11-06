@extends('layouts.common')
@section('title', 'Backup & Restore - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Backup & Restore</h4>
                <a class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="menu-icon icon-base ri ri-stack-line"></i> Generate
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Backup ID</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Storage Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>BK001</td>
                                    <td>Full</td>
                                    <td>27-09-2025</td>
                                    <td>Local Drive (D:/Backup)</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:void(0)" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                            <a href="javascript:void(0)" class="btn btn-view"><i class="icon-base ri ri-refresh-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>BK002</td>
                                    <td>Incremental</td>
                                    <td>26-09-2025</td>
                                    <td>Cloud (AWS S3)</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:void(0)" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                            <a href="javascript:void(0)" class="btn btn-view"><i class="icon-base ri ri-refresh-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>BK003</td>
                                    <td>Full</td>
                                    <td>25-09-2025</td>
                                    <td>Local Drive (D:/Backup)</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:void(0)" class="btn btn-cancel"><i class="icon-base ri ri-file-download-line"></i></a>
                                            <a href="javascript:void(0)" class="btn btn-view"><i class="icon-base ri ri-refresh-line"></i></a>
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
<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateModalLabel">Generate Backup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to generate database backup?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmBackupBtn">Yes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
@endsection