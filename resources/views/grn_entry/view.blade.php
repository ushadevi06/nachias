@extends('layouts.common')
@section('title', 'GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>GRN Entry</h4>
                <a class="btn btn-primary" href="{{ url('add_grn_entry') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="supplier" id="supplier" class="form-select select2" data-placeholder="Select Supplier">
                                    <option value="">Select Supplier</option>
                                    <option value="Krishna Fabrics(SUP001)">Krishna Fabrics(SUP001)</option>
                                    <option value="Vasanth Garments(SUP002)">Vasanth Garments(SUP002)</option>
                                    <option value="Jaya Fabrics(SUP003)">Jaya Fabrics(SUP003)</option>
                                    <option value="Sri Meena Traders(SUP004)">Sri Meena Traders(SUP004)</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select QC Status">
                                    <option value="">Select QC Status</option>
                                    <option value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                    <option value="Hold">Hold</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GRN No.</th>
                                    <th>GRN Date</th>
                                    <th>PO Invoice No.</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Invoice No.</th>
                                    <th>Total Items</th>
                                    <th>Amount (₹)</th>
                                    <th>QC Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>GRN001</td>
                                    <td>22-09-2025</td>
                                    <td>PINV-1001</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>SUPINV101</td>
                                    <td>2</td>
                                    <td>₹96,025</td>
                                    <td><span class="badge bg-success">Pass</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_grn_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_grn_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>GRN002</td>
                                    <td>22-09-2025</td>
                                    <td>PINV-1001</td>
                                    <td>Vasanth Garments <span class="mini-title">(SUP002)</span></td>
                                    <td>SUPINV102</td>
                                    <td>2</td>
                                    <td>₹7,960</td>
                                    <td><span class="badge bg-success">Pass</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_grn_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_grn_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>GRN003</td>
                                    <td>22-09-2025</td>
                                    <td>PINV-1001</td>
                                    <td>Jaya Fabrics <span class="mini-title">(SUP003)</span></td>
                                    <td>SUPINV103</td>
                                    <td>1</td>
                                    <td>₹10,725</td>
                                    <td><span class="badge bg-warning">Hold</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_grn_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_grn_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>GRN004</td>
                                    <td>18-09-2025</td>
                                    <td>PINV-1001</td>
                                    <td>Sri Meena Traders <span class="mini-title">(SUP004)</span></td>
                                    <td>SUPINV104</td>
                                    <td>1</td>
                                    <td>₹10,725</td>
                                    <td><span class="badge bg-danger">Fail</span></td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_grn_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_grn_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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