@extends('layouts.common')
@section('title', 'Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Entry</h4>
                <a class="btn btn-primary" href="{{ url('add_stock_entry') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">

                    <!-- ✅ Filter Section -->
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="material_category" id="material_category" class="form-select select2" data-placeholder="Select Material Category">
                                        <option value="">Select Material Category</option>
                                        <option value="Fabric(MC001)">Fabric(MC001)</option>
                                        <option value="Accessories(MC002)">Accessories(MC002)</option>
                                        <option value="Trims(MC003)">Trims(MC003)</option>
                                        <option value="Thread(MC004)">Thread(MC004)</option>
                                        <option value="Buttons(MC005)">Buttons(MC005)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="material" id="material" class="form-select select2" data-placeholder="Select Material">
                                    <option value="">Select Material</option>
                                    <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                                    <option value="Zipper(M002)">Zipper(M002)</option>
                                    <option value="Lace(M003)">Lace(M003)</option>
                                    <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                                    <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select id="entry_type" class="select2 form-select" data-placeholder="Select Entry Type">
                                    <option value="">Select Entry Type</option>
                                    <option value="Inward">Inward</option>
                                    <option value="Outward">Outward</option>
                                    <option value="Adjustment">Adjustment</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ Data Table -->
                    <div class="card-datatable mt-4">
                        <div class="table-responsive">
                            <table class="datatables-products table ">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Stock Entry No.</th>
                                        <th>Stock Date</th>
                                        <th>Entry Type</th>
                                        <th>Material Category</th>
                                        <th>Material</th>
                                        <th>UOM</th>
                                        <th>Invoice No.</th>
                                        <th>Invoice Status</th>
                                        <th>Total Qty</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>STOCK001</td>
                                        <td>22-09-2025</td>
                                        <td>Transfer</td>
                                        <td>Fabric <span class="mini-title">(MC001)</span></td>
                                        <td>Cotton Poplin 60 GSM <span class="mini-title">(M001)</span></td>
                                        <td>M</td>
                                        <td>PINV-001</td>
                                        <td><span class="badge bg-label-success">Completed</span></td>
                                        <td>500</td>
                                        <td>₹9,025</td>
                                        <td>
                                            <div class="button-box">
                                                <a href="{{ url('view_stock_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                                <a href="{{ url('add_stock_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>STOCK002</td>
                                        <td>22-09-2025</td>
                                        <td>Issue</td>
                                        <td>Accessories <span class="mini-title">(MC002)</span></td>
                                        <td>Zipper <span class="mini-title">(M002)</span></td>
                                        <td>PCS</td>
                                        <td>PINV-002</td>
                                        <td><span class="badge bg-label-warning">Pending</span></td>
                                        <td>200</td>
                                        <td>₹925</td>
                                        <td>
                                            <div class="button-box">
                                                <a href="{{ url('view_stock_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                                <a href="{{ url('add_stock_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>STOCK003</td>
                                        <td>22-09-2025</td>
                                        <td>Receipt</td>
                                        <td>Trims <span class="mini-title">(MC003)</span></td>
                                        <td>Lace <span class="mini-title">(M003)</span></td>
                                        <td>PCS</td>
                                        <td>PINV-033</td>
                                        <td><span class="badge bg-label-danger">Cancelled</span></td>
                                        <td>100</td>
                                        <td>₹755</td>
                                        <td>
                                            <div class="button-box">
                                                <a href="{{ url('view_stock_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                                <a href="{{ url('add_stock_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- /datatable -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
