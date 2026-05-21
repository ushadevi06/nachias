@extends('layouts.common')
@section('title', 'Stock Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Reports</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateReport"><i class="menu-icon icon-base ri ri-stack-line"></i>Generate Stock Report</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Stock No</th>
                                    <th>Material Category</th>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Location / Store</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockBalances as $index => $stock) 
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($stock->stock_type == 'raw_material' && $stock->rawMaterial)
                                            {{ $stock->rawMaterial->code }}
                                        @elseif($stock->stock_type == 'finished_goods' && $stock->item)
                                            {{ $stock->item->code ?? $stock->item->item_code ?? 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->storeCategory)
                                            {{ $stock->storeCategory->category_name }} <span class="mini-title">({{ $stock->storeCategory->code }})</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock->stock_type == 'raw_material' && $stock->rawMaterial)
                                            {{ $stock->rawMaterial->name }}
                                        @elseif($stock->stock_type == 'finished_goods' && $stock->item)
                                            {{ $stock->item->item_name }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ number_format($stock->current_stock, 2) }}</td>
                                    <td>{{ $stock->storeLocation ? $stock->storeLocation->store_location : 'N/A' }}</td>
                                    <td>₹{{ number_format($stock->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="generateReport" tabindex="-1" aria-labelledby="generateReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header  bg-primary">
                <h5 class="modal-title  text-white" id="generateReportLabel">Generate Stock Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="report_type" class="form-select select2" data-placeholder="Select Report Type">
                                <option value="">Select Report Type</option>
                                <option value="Daily">Daily</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Annual">Annual</option>
                            </select>
                            <label for="report_type">Report Type * </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select Date Range">
                            <label for="date_range">Date Range </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select id="department_module" class="form-select select2" data-placeholder="Select Department / Module">
                                <option value="">Select Department / Module</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Retail">Retail</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Online">Online</option>
                                <option value="Distribution">Distribution</option>
                            </select>
                            <label for="department_module">Department / Module * </label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label for="leave_type">Export Format * </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="excel" id="excel" />
                            <label class="form-check-label" for="excel"> Excel </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="pdf" id="pdf" />
                            <label class="form-check-label" for="pdf"> PDF </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" value="1" name="csv" id="csv" />
                            <label class="form-check-label" for="csv"> CSV </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save/Generate</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection