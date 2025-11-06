@extends('layouts.common')
@section('title', 'View Status Updates - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Status Updates</h4>
                <a href="{{ url('task_tracking_monitoring') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Task ID: </label>
                            <div class="text-muted">TASK-2025-001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Task Status:</label>
                            <div class="text-muted"><span class="badge text-bg-info">In Progress</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Comments / Notes: </label>
                            <div class="text-muted">Fabric cutting 50% complete, awaiting next batch.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">File:</label>
                            <div class="text-muted">
                                <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="" width="30"></a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Updated By:</label>
                            <div class="text-muted">Ramesh(EMP001) - Cutting Team</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Updated On:</label>
                            <div class="text-muted">25-09-2025 03:15 PM</div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Sales Order No</th>
                                        <th>Task Title</th>
                                        <th>Task Type</th>
                                        <th>Assigned Team</th>
                                        <th>Assigned By</th>
                                        <th>Assigned To</th>
                                        <th>Priority</th>
                                        <th>Deadline Date</th>
                                        <th>Related Department</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr>
                                        <td>SO-1001</td>
                                        <td>Cutting for Order #SO-1001</td>
                                        <td>Scheduled</td>
                                        <td>Cutting Department</td>
                                        <td>Admin</td>
                                        <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                        <td>High</td>
                                        <td>30-09-2025</td>
                                        <td>Production</td>
                                        <td><span class="badge bg-danger">Not Started</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection