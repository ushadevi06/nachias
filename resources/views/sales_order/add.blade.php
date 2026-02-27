@extends('layouts.common')
@section('title', 'Add Sale Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="mb-0">Add Sale Order</h4>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Main Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="so_no" placeholder="Enter SO Number" name="so_no" value="SO-1001">
                                    <label for="so_no">SO Number *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control so_date" id="so_date" name="so_date" value="{{ date('d-m-Y') }}">
                                    <label for="so_date">SO Date *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="order_type" name="order_type" class="select2 form-select">
                                        <option value="Regular">Regular Order</option>
                                        <option value="Sample">Sample Order</option>
                                        <option value="Bulk">Bulk/Export</option>
                                    </select>
                                    <label for="order_type">Order Type *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control request_date" id="request_date" name="request_date" value="{{ date('d-m-Y') }}">
                                    <label for="request_date">Request Date *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season" name="season" class="select2 form-select" data-placeholder="Select Season">
                                        <option value="" selected>Select Season</option>
                                        <option value="Spring/Summer 2026">Spring/Summer 2026</option>
                                        <option value="Autumn/Winter 2025">Autumn/Winter 2025</option>
                                        <option value="Year Round">Year Round (Core)</option>
                                    </select>
                                    <label for="season">Season *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="customer_id" class="select2 form-select" data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear (CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store (CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain (CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters (CUS004)</option>
                                    </select>
                                    <label for="customer_id">Customer *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="customer_po_ref" placeholder="Enter Ref No" name="customer_po_ref">
                                    <label for="customer_po_ref">Customer PO Ref No</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store" name="store" class="select2 form-select" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        <option value="Finished Goods Store">Finished Goods Store</option>
                                        <option value="Retail Outlet">Retail Outlet</option>
                                        <option value="Online Store">Online Store</option>
                                    </select>
                                    <label for="store">Store *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="agent_id" class="select2 form-select" data-placeholder="Select Broker/Sales Agent">
                                        <option value="">Select Broker/Sales Agent</option>
                                        <option value="Amit Kumar(SA101)">Amit Kumar(SA101)</option>
                                        <option value="Neha Sharma(SA102)">Neha Sharma(SA102)</option>
                                    </select>
                                    <label for="agent_id">Broker/Sales Agent *</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Logistics & Destination</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control delivery_date" id="delivery_date" name="delivery_date" value="{{ date('d-m-Y', strtotime('+7 days')) }}">
                                    <label for="delivery_date">Expected Delivery Date *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control est_dispatch_date" id="est_dispatch_date" name="est_dispatch_date">
                                    <label for="est_dispatch_date">Est. Dispatch Date</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="shipping_method" name="shipping_method" class="select2 form-select" data-placeholder="Select Shipping Method">
                                        <option value="">Select Shipping Method</option>
                                        <option value="DTDC">DTDC</option>
                                        <option value="BlueDart">BlueDart</option>
                                        <option value="Self Pickup">Self Pickup</option>
                                        <option value="Local Courier">Local Courier</option>
                                    </select>
                                    <label for="shipping_method">Shipping Method</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="dispatch_from" name="dispatch_from" class="select2 form-select" data-placeholder="Select Warehouse">
                                        <option value="">Select Warehouse</option>
                                        <option value="Main Warehouse">Main Warehouse</option>
                                        <option value="Factory Outlet">Factory Outlet</option>
                                        <option value="North Storage">North Storage</option>
                                    </select>
                                    <label for="dispatch_from">Dispatch From</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="transport_mode" name="transport_mode" class="select2 form-select" data-placeholder="Select Transport Mode">
                                        <option value="">Select Transport Mode</option>
                                        <option value="Truck">Truck</option>
                                        <option value="Tempo">Tempo</option>
                                        <option value="Courier">Courier</option>
                                        <option value="By Hand">By Hand</option>
                                        <option value="Rail">Rail</option>
                                        <option value="Air">Air</option>
                                    </select>
                                    <label for="transport_mode">Transport Mode</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="consignee_location" placeholder="Enter Consignee Location" name="consignee_location">
                                    <label for="consignee_location">Consignee Location</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="final_destination" placeholder="Enter Final Destination" name="final_destination">
                                    <label for="final_destination">Final Destination</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="zone" class="select2 form-select" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        <option value="South Zone">South Zone</option>
                                        <option value="North Zone">North Zone</option>
                                        <option value="West Zone">West Zone</option>
                                        <option value="Central Zone">Central Zone</option>
                                    </select>
                                    <label for="zone">Zone *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="city_id" name="city_id" class="select2 form-select" data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        <option value="Chennai">Chennai</option>
                                        <option value="Coimbatore">Coimbatore</option>
                                        <option value="Madurai">Madurai</option>
                                    </select>
                                    <label for="city_id">City *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="place_id" name="place_id" class="select2 form-select" data-placeholder="Select Place">
                                        <option value="">Select Place</option>
                                        <option value="T.Nagar">T.Nagar</option>
                                        <option value="Adyar">Adyar</option>
                                    </select>
                                    <label for="place_id">Place *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="zip_code" placeholder="Enter Zip Code" name="zip_code">
                                    <label for="zip_code">Zip Code *</label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" id="address_line_1" placeholder="Enter Address Line 1" name="address_line_1">
                                    <label for="address_line_1">Address Line 1 *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" id="address_line_2" placeholder="Enter Address Line 2" name="address_line_2">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                            <h4>Item Details</h4>
                            <button type="button" class="btn btn-sm btn-outline-primary add_item"><i class="ri ri-add-line me-1"></i>Add Item</button>
                        </div>
                        <div id="item-rows">
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                                    <h4>Additional Information</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <select id="payment_mode" name="payment_mode" class="select2 form-select form-select-sm" data-placeholder="Select Payment Mode">
                                                <option value="" selected>Select Payment Mode</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                            </select>
                                            <label class="payment_mode">Payment Mode</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <select id="payment_terms" name="payment_terms" class="select2 form-select form-select-sm" data-placeholder="Select Payment Terms">
                                                <option value="">Select Payment Terms</option>
                                                <option value="Advanced">Advanced (100%)</option>
                                                <option value="50% Advance">50% Advance / 50% Delivery</option>
                                                <option value="Net 30">Net 30 Days</option>
                                                <option value="Net 60">Net 60 Days</option>
                                            </select>
                                            <label>Payment Terms</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <select id="status_footer" name="status" class="select2 form-select form-select-sm">
                                                <option value="Draft" selected>Draft</option>
                                                <option value="Approved">Approved</option>
                                            </select>
                                            <label class="order_status">Order Status</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control form-control-erp" id="internal_remarks" name="internal_remarks" rows="3" placeholder="Enter team remarks"></textarea>
                                            <label class="form-label-erp">Internal Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control file-input" id="attachment" name="attachment">
                                            <label for="attachment">Attachment</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                                    <h4>Tax Summary</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="total_qty" class="fw-medium">Total Qty:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="total_qty" name="total_qty" value="0.00" readonly="">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="sub_total_qty" class="fw-medium">Sub Total:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="sub_total_qty" name="sub_total_qty" value="0.00" readonly="">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Discount:</label>
                                            <div class="input-group input-group-sm" style="width:120px;">
                                                <input type="number" class="form-control form-control-sm text-end " id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="0">
                                                <span class="input-group-text px-1">%</span>
                                            </div>
                                        </div>
                                        <div class="text-end mt-1">
                                            <input type="text" class="form-control-plaintext form-control-sm text-end py-0" id="discount_amount" name="discount_amount" value="0.00" readonly="">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                            <label for="taxable_amount" class="fw-medium">Net Amount (Before Tax):</label>
                                            <input type="text" id="taxable_amount" name="taxable_amount" class="form-control-plaintext text-end w-50 fw-bold" value="0.00" readonly="">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Other State:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input " type="radio" name="other_state" id="other_state_yes" value="yes">
                                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input " type="radio" name="other_state" id="other_state_no" value="no" checked="">
                                                    <label class="form-check-label" for="other_state_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="igst-field d-none">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="igst_percent" class="fw-medium">IGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end " id="igst_percent" name="igst_percent" step="0.01" min="0" max="100" value="9">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cgst-field ">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="cgst_percent" class="fw-medium">CGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end " id="cgst_percent" name="cgst_percent" step="0.01" min="0" max="100" value="18">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sgst-field  mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="sgst_percent" class="fw-medium">SGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end " id="sgst_percent" name="sgst_percent" step="0.01" min="0" max="100" value="9">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="tax_amount" class="fw-medium">Tax Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50" id="tax_amount" name="tax_amount" value="0.00" readonly="">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Round Off:</label>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" checked="">
                                                    <label class="form-check-label" for="round_off_add">Add</label>
                                                </div>
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less">
                                                    <label class="form-check-label" for="round_off_less">Less</label>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-end" style="width: 100px;" id="round_off" name="round_off" step="0.01" min="0" value="0.00" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                            <label for="total_amount" class="fw-bold fs-5">Total Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold fs-5 text-primary" id="total_amount" name="total_amount" value="0.00" readonly="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit </button>
                    <a href="{{ url('sales_order') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .item-block {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s ease;
    }
    .item-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .item-number {
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .remove_item_btn {
        background: #fff0f0;
        border: 1px solid #ffe0e0;
        border-radius: 8px;
        padding: 5px 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .remove_item_btn:hover {
        background: #ff4d4d;
        color: #fff !important;
    }
    .sleeve-container {
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        background: #fff;
    }
    .sleeve-label {
        margin-bottom: 5px;
        display: block;
    }
</style>
<script>
$(document).ready(function () {
    const rateMapping = {
        'ITEM001': { '38,40,42,44 (1,2,3,7)': 1200, '38,40 (5,2)': 1100 },
        'ITEM002': { '42,44 (5,7)': 1500 },
        'ITEM003': { '38,40,42 (1,3,2)': 950 },
        'ITEM004': { '38,40,42 (1,3,1)': 800 },
        'ITEM005': { '38,40,42,44 (1,2,3,7)': 1800 }
    };

    let rowCount = 0;

    function createRow() {
        rowCount++;
        const rowId = rowCount;
        const rowHtml = `
            <div class="item-block" id="row-${rowId}">
                <div class="item-block-header">
                    <span class="item-number">Item #${rowCount}</span>
                    <button type="button" class="remove_item_btn" onclick="removeRow(${rowId})"><i class="ri ri-delete-bin-line"></i></button>
                </div>
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select name="brand[]" class="select2 form-select" data-placeholder="Brand Category">
                                <option value="">Select Brand Category</option>
                                <option value="BlueBay">BlueBay</option>
                                <option value="Ethnic Edge">Ethnic Edge</option>
                                <option value="Royal Attire">Royal Attire</option>
                                <option value="WorkPro">WorkPro</option>
                            </select>
                            <label>Brand Category *</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <select name="item[]" class="select2 form-select item-select" data-placeholder="Item">
                                <option value="">Select Item</option>
                                <option value="ITEM001">Men’s Casual Denim Shirt (ITEM001)</option>
                                <option value="ITEM002">Men’s Formal Cotton Shirt (ITEM002)</option>
                                <option value="ITEM003">School Uniform Shirt (ITEM003)</option>
                                <option value="ITEM004">Kids Polo Shirt (ITEM004)</option>
                                <option value="ITEM005">Premium Linen Shirt (ITEM005)</option>
                            </select>
                            <label>Item *</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select name="color[]" class="select2 form-select" data-placeholder="Color">
                                <option value="">Select Color</option>
                                <option value="Red">Red</option>
                                <option value="Blue">Blue</option>
                                <option value="Black">Black</option>
                                <option value="White">White</option>
                                <option value="Green">Green</option>
                            </select>
                            <label>Color</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="art_no[]" class="form-control" placeholder="Art No">
                            <label>Art No</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-floating form-floating-outline">
                            <select name="uom[]" class="select2 form-select" data-placeholder="UOM">
                                <option value="PCS" selected>PCS</option>
                                <option value="MTR">MTR</option>
                                <option value="ROLL">ROLL</option>
                            </select>
                            <label>UOM *</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select name="size[]" class="select2 form-select size-select" data-placeholder="Size">
                                <option value="">Select Size</option>
                                <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                                <option value="38,40 (5,2)">38,40 (5,2)</option>
                                <option value="42,44 (5,7)">42,44 (5,7)</option>
                                <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                                <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                            </select>
                            <label>Size *</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" name="qty[]" class="form-control qty-input" value="1" min="1">
                            <label>Quantity *</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" name="rate[]" class="form-control rate-input" placeholder="0.00">
                            <label>Rate per Unit *</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" name="amount[]" class="form-control amount-input" value="0.00" readonly>
                            <label>Amount</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sleeve-container">
                            <span class="sleeve-label">Sleeve Type</span>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="sleeve_${rowId}[]" id="sleeve_full_${rowId}" value="Full">
                                        <label class="form-check-label" for="sleeve_full_${rowId}">Checked Full</label>
                                    </div>
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="sleeve_${rowId}[]" id="sleeve_half_${rowId}" value="Half">
                                        <label class="form-check-label" for="sleeve_half_${rowId}">Checked Half</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="sleeve_${rowId}[]" id="sleeve_others_full_${rowId}" value="Others Full">
                                        <label class="form-check-label" for="sleeve_others_full_${rowId}">Others Full</label>
                                    </div>
                                    <div class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="sleeve_${rowId}[]" id="sleeve_others_half_${rowId}" value="Others Half">
                                        <label class="form-check-label" for="sleeve_others_half_${rowId}">Others Half</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        const $row = $(rowHtml);
        $('#item-rows').append($row);
        $row.find('.select2').select2({ dropdownParent: $row });
    }

    window.removeRow = function(id) {
        if ($('#item-rows .item-block').length > 1) {
            $(`#row-${id}`).remove();
            calculateTotals();
        }
    }

    $(document).on('change', '.item-select, .size-select', function() {
        const $row = $(this).closest('.item-block');
        const item = $row.find('.item-select').val();
        const size = $row.find('.size-select').val();
        if (item && size && rateMapping[item] && rateMapping[item][size]) {
            $row.find('.rate-input').val(rateMapping[item][size]);
            calculateRow($row);
        }
    });

    $(document).on('input', '.qty-input, .rate-input', function() {
        calculateRow($(this).closest('.item-block'));
    });

    function calculateRow($row) {
        const qty = parseFloat($row.find('.qty-input').val()) || 0;
        const rate = parseFloat($row.find('.rate-input').val()) || 0;
        const amount = qty * rate;
        $row.find('.amount-input').val(amount.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        let totalQty = 0, subTotal = 0;
        $('#item-rows .item-block').each(function() {
            totalQty += parseFloat($(this).find('.qty-input').val()) || 0;
            subTotal += parseFloat($(this).find('.amount-input').val()) || 0;
        });

        $('#total_qty').val(totalQty.toFixed(2));
        $('#sub_total_qty').val(subTotal.toFixed(2));

        const discPercent = parseFloat($('#discount_percent').val()) || 0;
        const discountAmount = (subTotal * discPercent) / 100;
        $('#discount_amount').val(discountAmount.toFixed(2));

        const taxableAmount = subTotal - discountAmount;
        $('#taxable_amount').val(taxableAmount.toFixed(2));

        let taxPercent = 0;
        const isOtherState = $('input[name="other_state"]:checked').val() === 'yes';
        
        if (isOtherState) {
            $('.igst-field').removeClass('d-none');
            $('.cgst-field, .sgst-field').addClass('d-none');
            taxPercent = parseFloat($('#igst_percent').val()) || 0;
        } else {
            $('.igst-field').addClass('d-none');
            $('.cgst-field, .sgst-field').removeClass('d-none');
            taxPercent = (parseFloat($('#cgst_percent').val()) || 0) + (parseFloat($('#sgst_percent').val()) || 0);
        }

        const taxAmount = (taxableAmount * taxPercent) / 100;
        $('#tax_amount').val(taxAmount.toFixed(2));

        const roundOffVal = parseFloat($('#round_off').val()) || 0;
        const roundOffType = $('input[name="round_off_type"]:checked').val();
        
        let finalTotal = taxableAmount + taxAmount;
        if (roundOffType === 'Add') {
            finalTotal += roundOffVal;
        } else {
            finalTotal -= roundOffVal;
        }

        $('#total_amount').val(finalTotal.toFixed(2));
    }

    const customerData = {
        'Hero Mens Wear(CUS001)': {
            address: '123 Main St, T.Nagar',
            city: 'Chennai',
            place: 'T.Nagar',
            zip: '600017',
            zone: 'South Zone',
            other_state: 'no'
        },
        'Unlimited Fashion Store(CUS002)': {
            address: '456 High Road, Madurai',
            city: 'Madurai',
            place: 'Adyar',
            zip: '625001',
            zone: 'South Zone',
            other_state: 'no'
        },
        'Nikhil Jain(CUS003)': {
            address: '789 Business Park, Bengaluru',
            city: 'Chennai',
            place: 'T.Nagar',
            zip: '560001',
            zone: 'Central Zone',
            other_state: 'yes'
        },
        'Elite Garments Exporters(CUS004)': {
            address: '101 Export Plaza, Mumbai',
            city: 'Coimbatore',
            place: 'Adyar',
            zip: '400001',
            zone: 'West Zone',
            other_state: 'yes'
        }
    };

    $('#customer_id').on('change', function() {
        const cid = $(this).val();
        if (customerData[cid]) {
            const data = customerData[cid];
            $('#address_line_1').val(data.address);
            $('#city_id').val(data.city).trigger('change');
            $('#place_id').val(data.place).trigger('change');
            $('#zip_code').val(data.zip);
            $('#zone').val(data.zone).trigger('change');
            
            if (data.other_state === 'yes') {
                $('#other_state_yes').prop('checked', true).trigger('change');
            } else {
                $('#other_state_no').prop('checked', true).trigger('change');
            }
        }
    });

    $(document).on('input', '#discount_percent, #igst_percent, #cgst_percent, #sgst_percent, #round_off', calculateTotals);
    $(document).on('change', 'input[name="other_state"], input[name="round_off_type"]', calculateTotals);
    $('.add_item').click(createRow);
    
    createRow(); 
    $(".select2").select2(); 
});
</script>
@endsection