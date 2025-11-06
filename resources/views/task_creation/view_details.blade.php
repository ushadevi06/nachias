@extends('layouts.common')
@section('title', 'View Task Creation - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="table-header-box">
                <h4>View Task Creation</h4>
                <a href="{{ url('task_creation') }}" class="btn btn-primary">
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
                            <label class="detail-title">Task Title:</label>
                            <div class="text-muted">Cutting for Order #SO-1001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Task Description:</label>
                            <div class="text-muted">
                                Cut 500 shirts (Style SH-Blue, Sizes M–XL) as per Sales Order #1025.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Task Type:</label>
                            <div class="text-muted">Instant</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Assigned Team:</label>
                            <div class="text-muted">Cutting Department</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Priority:</label>
                            <div class="text-muted">High</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Deadline Date:</label>
                            <div class="text-muted">27-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Related Department:</label>
                            <div class="text-muted">Production</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted"><span class="badge bg-warning">In Progress</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ Job Card Details Table -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Job Card Details</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Item</th>
                                    <th>Ordered Qty</th>
                                    <th>Balanced Qty</th>
                                    <th>Size</th>
                                    <th>Sleeve Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Men’s Denim Shirt (ITEM001)</td>
                                    <td class="text-center">100</td>
                                    <td class="text-center">40</td>
                                    <td>38, 40, 42, 44 — 1, 2, 3, 1</td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            <li>Checked Full Sleeve</li>
                                            <li>Checked Half Sleeve</li>
                                            <li>Others Full Sleeve</li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Men’s Cotton Shirt (ITEM002)</td>
                                    <td class="text-center">50</td>
                                    <td class="text-center">20</td>
                                    <td>38, 40, 42, 44 — 5, 6, 7, 8</td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            <li>Checked Full & Half Sleeve</li>
                                            <li>Others Half Sleeve</li>
                                        </ul>
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
