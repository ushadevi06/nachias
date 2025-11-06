@extends('layouts.common')
@section('title', 'View Task Tracking & Monitoring - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Task Tracking & Monitoring</h4>
                <a href="{{ url('task_tracking_monitoring') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-4">
                            <label class="detail-title">Task ID:</label>
                            <div class="text-muted">TASK-2025-001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Job Card No:</label>
                            <div class="text-muted">JC20250924-001-K</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Sales Order No:</label>
                            <div class="text-muted">SO-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Task Title:</label>
                            <div class="text-muted">Cutting for Order #SO-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Task Description:</label>
                            <div class="text-muted">Cut 500 shirts (Style SH-Blue, Sizes M–XL) as per Sales Order #1025.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Assigned By:</label>
                            <div class="text-muted">Admin</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Assigned To:</label>
                            <div class="text-muted">Ramesh (EMP001)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Task Status:</label>
                            <div class="text-muted"><span class="badge text-bg-info">In Progress</span></div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Deadline Date:</label>
                            <div class="text-muted">27-09-2025</div>
                        </div>

                        <!-- ✅ Added Fields from Add Page -->
                        <div class="col-md-4">
                            <label class="detail-title">Total Quantity:</label>
                            <div class="text-muted">25</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Quantity Completed:</label>
                            <div class="text-muted">20</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Quantity Pending:</label>
                            <div class="text-muted">5</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Progress (%):</label>
                            <div class="text-muted">80%</div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex justify-content-between flex-wrap py-2">
                                <div class="form-check mb-0">
                                    <label class="detail-title">Alerts & Reminders:</label><br>
                                    <input class="form-check-input me-2" type="checkbox" value="1" checked disabled>
                                    <small class="text-muted">Next Reminder: 26-09-2025 09:00 AM (Email)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Task Info Table -->
                        <div class="col-lg-12">
                            <div class="table-responsive" id="task_table">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Task Title</th>
                                        <th>Task Type</th>
                                        <th>Assigned Team</th>
                                        <th>Assigned By</th>
                                        <th>Assigned To</th>
                                        <th>Priority</th>
                                        <th>Related Module</th>
                                    </tr>
                                    <tr>
                                        <td>Cutting for Order #SO-1001</td>
                                        <td>Scheduled</td>
                                        <td>Cutting Department</td>
                                        <td>Admin</td>
                                        <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                        <td>High</td>
                                        <td>Production</td>
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
