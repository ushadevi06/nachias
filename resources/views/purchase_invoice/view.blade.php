@extends('layouts.common')
@section('title', 'Purchase Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding mt-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Purchase Invoices</h4>
                <a class="btn btn-primary" href="{{ url('add_purchase_invoice') }}">
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
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Dispatched">Dispatched</option>
                                    <option value="Received">Received</option>
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
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Supplier Name</th>
                                    <th>Consignee location </th>
                                    <th>Dispatch Location </th>
                                    <th>Service Provider</th>
                                    <th>Destination</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>PINV-1001</td>
                                    <td>17-09-2025</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>Delhi Warehouse</td>
                                    <td>Gurugram Factory</td>
                                    <td>Udaan Road Ways Pvt Ltd</td>
                                    <td>Chennai</td>
                                    <td>₹141,000.00</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Finalized" selected>Finalized</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:;" class="btn btn-item btn-invoice-items" title="Adjust Invoice Quantity" data-bs-toggle="modal" data-bs-target="#invoiceItemsModal">
                                                <i class="icon-base ri ri-file-list-3-line"></i>
                                            </a>
                                            <a href="{{ url('view_purchase_invoice') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_invoice') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>PINV-1002</td>
                                    <td>17-09-2025</td>
                                    <td>Vasanth Garments <span class="mini-title">(SUP002)</span></td>
                                    <td>Bangalore Depot</td>
                                    <td>Chennai Warehouse</td>
                                    <td>TVSL Transpot Ltd</td>
                                    <td>Gurugram</td>
                                    <td>₹95,000</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Draft" selected>Draft</option>
                                                <option value="Finalized">Finalized</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:;" class="btn btn-item btn-invoice-items" title="Adjust Invoice Quantity" data-bs-toggle="modal" data-bs-target="#invoiceItemsModal">
                                                <i class="icon-base ri ri-file-list-3-line"></i>
                                            </a>
                                            <a href="{{ url('view_purchase_invoice') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_invoice') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>PINV-1003</td>
                                    <td>17-09-2025</td>
                                    <td>Jaya Fabrics <span class="mini-title">(SUP003)</span></td>
                                    <td>Kolkata Warehouse</td>
                                    <td>Kolkata Factory</td>
                                    <td>FedEx</td>
                                    <td>Kolkata</td>
                                    <td>₹42,750</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Draft" selected>Draft</option>
                                                <option value="Finalized">Finalized</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="javascript:;" class="btn btn-item btn-invoice-items" title="Adjust Invoice Quantity" data-bs-toggle="modal" data-bs-target="#invoiceItemsModal">
                                                <i class="icon-base ri ri-file-list-3-line"></i>
                                            </a>
                                            <a href="{{ url('view_purchase_invoice') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_invoice') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
<!-- Invoice Items Modal -->
<div class="modal fade" id="invoiceItemsModal" tabindex="-1" aria-labelledby="invoiceItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="invoiceItemsModalLabel">Invoice Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                   <div class="">
                       <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Art no</th>
                                    <th>UOM</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Qty Ordered</th>
                                    <th>Qty Received</th>
                                    <th>Qty Invoiced</th>
                                    <th>Qty Invoice</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceItemsTableBody">
                                <tr>
                                    <td>1</td>
                                    <td>90/10/10 X 90/10/10 <span class="text-muted">[HSN:52102110]</span></td>
                                    <td>Meter</td>
                                    <td>120.00</td>
                                    <td class="amount text-end">60,000.00</td>
                                    <td>500</td> 
                                    <td>400</td>
                                    <td>300</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end qty-invoice" value="100" min="0" max="100">
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>NOVA COTTON 58" <span class="text-muted">[HSN:52081990]</span></td>
                                    <td>Meter</td>
                                    <td>150.00</td>
                                    <td class="amount text-end">45,000.00</td>
                                    <td>300</td> 
                                    <td>280</td> 
                                    <td>250</td> 
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end qty-invoice" value="30" min="0" max="30">
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>ORIENTAL 58" <span class="text-muted">[HSN:52083310]</span></td>
                                    <td>Meter</td>
                                    <td>180.00</td>
                                    <td class="amount text-end">36,000.00</td>
                                    <td>200</td> 
                                    <td>180</td>
                                    <td>100</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-end qty-invoice" value="80" min="0" max="80">
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr class="table-light fw-semibold">
                                    <td colspan="5" class="text-end">Total Amount:</td>
                                    <td id="totalAmount" class="text-end">141,000.00</td>
                                    <td colspan="5"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection