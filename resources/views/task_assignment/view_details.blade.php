@extends('layouts.common')
@section('title', 'View Task Assignment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Task Assignment</h4>
                <a href="{{ url('task_assignment') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Task ID: </label>
                            <div class="text-muted">TASK-2025-001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Assigned By:</label>
                            <div class="text-muted">Admin</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Assigned To:</label>
                            <div class="text-muted">Ramesh(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Assigned Type:</label>
                            <div class="text-muted">Direct</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Permission(Allow Delegation):</label>
                            <div class="text-muted">Yes</div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive" id="task_table">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Sales Order No</th>
                                        <th>Task Title</th>
                                        <th>Task Type</th>
                                        <th>Assigned Team</th>
                                        <th>Priority</th>
                                        <th>Deadline Date</th>
                                        <th>Related Module</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr>
                                        <td>SO-1001</td>
                                        <td>Cutting for Order #SO-1001</td>
                                        <td>Scheduled</td>
                                        <td>Cutting Department</td>
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