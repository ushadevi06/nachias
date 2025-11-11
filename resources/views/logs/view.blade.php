@extends('layouts.common')
@section('title', 'Logs & Audit Log - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Logs & Audit Log</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action Type</th>
                                    <th>Employee</th>
                                    <th>Module</th>
                                    <th>Record</th>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Create</td>
                                    <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                    <td>Sales Order</td>
                                    <td>SO-1001 (3 Items)</td>
                                    <td>27-09-2025 09:30 AM</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0)" class="btn btn-view" data-bs-toggle="modal" data-bs-target="#logDetailModal"><i class="icon-base ri ri-eye-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Edit</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>Production Order</td>
                                    <td>PO-2025-001(2 Items)</td>
                                    <td>26-09-2025 09:30 AM</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0)" class="btn btn-view" data-bs-toggle="modal" data-bs-target="#logDetailModal"><i class="icon-base ri ri-eye-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Delete</td>
                                    <td>Admin</td>
                                    <td>Production Order</td>
                                    <td>PO-2025-0910 (Cancelled due to fabric shortage)</td>
                                    <td>26-09-2025 04:30 PM</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0)" class="btn btn-view" data-bs-toggle="modal" data-bs-target="#logDetailModal"><i class="icon-base ri ri-eye-line"></i></a>
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
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="logDetailModalLabel">Log Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Action Type</th>
                        <td>Create</td>
                    </tr>
                    <tr>
                        <th>Employee</th>
                        <td>Ramesh <span class="mini-title">(CUS001)</span></td>
                    </tr>
                    <tr>
                        <th>Module</th>
                        <td>Sales Order</td>
                    </tr>
                    <tr>
                        <th>Record</th>
                        <td>SO-1001 (3 Items)</td>
                    </tr>
                    <tr>
                        <th>Date & Time</th>
                        <td>27-09-2025 09:30 AM</td>
                    </tr>
                    <tr>
                        <th>Details</th>
                        <td>
                            <ul class="mb-0">
                                <li>Item 1: Men’s Formal Cotton Shirt(ITEM001), Qty 500</li>
                                <li>Item 2: Men’s Casual Denim Shirt(ITEM002), Qty 300</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection