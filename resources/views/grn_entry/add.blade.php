@extends('layouts.common')
@section('title', 'Add GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
<style>
    .art-no-input {
        width: 180px !important;
        max-width: none !important;
        display: inline-block !important;
    }
</style>
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add GRN Entry</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control" placeholder="Enter GRN No" value="GRN001" readonly />
                                    <label for="code">GRN No * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control grn_date" placeholder="Enter GRN Date" />
                                    <label for="code">GRN Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="po_no" class="select2 form-select" data-placeholder="Select PO Invoice Number">
                                        <option value="">Select PO Invoice Number</option>
                                        <option value="PINV-1001">PINV-1001</option>
                                        <option value="PINV-1002">PINV-1002</option>
                                        <option value="PINV-1003">PINV-1003</option>
                                    </select>
                                    <label for="select2Basic">PO Invoice Number * <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div id="show_item_det" class="d-none col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <select name="supplier" id="supplier" class="form-select select2" data-placeholder="Select Supplier">
                                                <option value="">Select Supplier</option>
                                                <option value="Krishna Fabrics(SUP001)" selected>Krishna Fabrics(SUP001)</option>
                                                <option value="Vasanth Garments(SUP002)">Vasanth Garments(SUP002)</option>
                                                <option value="Jaya Fabrics(SUP003)">Jaya Fabrics(SUP003)</option>
                                                <option value="Sri Meena Traders(SUP004)">Sri Meena Traders(SUP004)</option>
                                            </select>
                                            <label for="select2Basic">Supplier * <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control form-control sup_inv_date" />
                                            <label for="code">Supplier Invoice Date * </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-5">
                                        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
                                            <table class="table table-bordered align-middle text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th>Supplier Design Name(Code)</th>
                                                        <th>Art No.</th>
                                                        <th>UOM</th>
                                                        <th>Fabric Type</th>
                                                        <th>Quantity Ordered</th>
                                                        <th>Quantity Received</th>
                                                        <th>Quantity Accepted</th>
                                                        <th>Quantity Rejected</th>
                                                        <th>Quantity Balanced</th>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                        <th>Quality Check Status</th>
                                                        <th>Store Location</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>
                                                            Men’s Casual Denim Shirt(ITEM001)
                                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#variantModal">Add Variants</button>
                                                        </td>
                                                        <td><input type="text" name="art_no" value="ART1001" class="form-control art-no-input" placeholder="Enter Art No."></td>
                                                        <td>MTR</td>
                                                        <td> 
                                                            <select class="form-control select2" name="fabric_type_1" data-placeholder="Select Fabric Type">
                                                                <option value="">Select Fabric Type</option>
                                                                <option value="Polyester">Polyester</option>
                                                                <option value="Polycotton">Polycotton</option>
                                                            </select>
                                                        </td>
                                                        <td>500</td>
                                                        <td><input type="text" name="qty_receive" value="500" class="form-control"></td>
                                                        <td><input type="text" name="qty_accept" value="480" class="form-control"></td>
                                                        <td><input type="text" name="qty_reject" value="20" class="form-control"></td>
                                                        <td>0</td>
                                                        <td>₹200</td>
                                                        <td>₹96,000</td>
                                                        <td>
                                                            <select class="form-control select2" data-placeholder="Select Status">
                                                                <option value="">Select Status</option>
                                                                <option value="Pass">Pass</option>
                                                                <option value="Fail">Fail</option>
                                                                <option value="Hold">Hold</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control select2" data-placeholder="Select Store Location">
                                                                <option value="">Select Store Location</option>
                                                                <option value="Warehouse 1">Warehouse 1</option>
                                                                <option value="Warehouse 2">Warehouse 2</option>
                                                                <option value="Warehouse 3">Warehouse 3</option>
                                                            </select>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>2</td>
                                                        <td>
                                                            Men’s Formal Cotton Shirt(ITEM002)
                                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#variantModal">Add Variants</button>
                                                        </td>
                                                        <td><input type="text" name="art_no" value="ART1002" class="form-control art-no-input" placeholder="Enter Art No."></td>
                                                        <td>MTR</td>
                                                        <td>
                                                            <select class="form-control select2" name="fabric_type_2" data-placeholder="Select Fabric Type">
                                                                <option value="">Select Fabric Type</option>
                                                                <option value="Polyester">Polyester</option>
                                                                <option value="Polycotton">Polycotton</option>
                                                            </select>
                                                        </td>
                                                        <td>25</td>
                                                        <td><input type="text" name="qty_receive" value="25" class="form-control"></td>
                                                        <td><input type="text" name="qty_accept" value="25" class="form-control"></td>
                                                        <td><input type="text" name="qty_reject" value="0" class="form-control"></td>
                                                        <td>0</td>
                                                        <td>₹10</td>
                                                        <td>₹250</td>
                                                        <td>
                                                            <select class="form-control select2" data-placeholder="Select Status">
                                                                <option value="">Select Status</option>
                                                                <option value="Pass">Pass</option>
                                                                <option value="Fail">Fail</option>
                                                                <option value="Hold">Hold</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control select2" data-placeholder="Select Store Location">
                                                                <option value="">Select Store Location</option>
                                                                <option value="Warehouse 1">Warehouse 1</option>
                                                                <option value="Warehouse 2">Warehouse 2</option>
                                                                <option value="Warehouse 3">Warehouse 3</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('grn_entries') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0" id="variantModalLabel">
                    Add Variants (Specify Quantity per Color)
                </h5>
                <h5 class="mb-0 text-muted">Total Ordered Quantity: 500</h5>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Colors</label>
                    <select id="variantColors" class="form-control select2" multiple="multiple" data-placeholder="Select Colors">
                    <option value="White">White</option>
                    <option value="Black">Black</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Green">Green</option>
                    <option value="Blue">Blue</option>
                    <option value="Red">Red</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="variantQtyTable">
                    <thead class="table-light">
                        <tr>
                        <th>Color</th>
                        <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Variants</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.grn_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.sup_inv_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#po_no').on('change', function() {
            let po_no = $(this).val();
            if (po_no && po_no.length > 0) {
                $('#show_item_det').removeClass('d-none');
            } else {
                $('#show_item_det').addClass('d-none');
            }
        });
        $('#variantColors').on('change', function () {
            let selectedColors = $(this).val() || [];
            let tbody = $('#variantQtyTable tbody');
            let existingData = {};

            tbody.find('tr').each(function () {
                let color = $(this).find('td:first').text();
                let qty = $(this).find('input').val();
                existingData[color] = qty;
            });

            tbody.empty();

            selectedColors.forEach(function (color) {
                let existingQty = existingData[color] || '';
                tbody.append(`
                    <tr>
                        <td>${color}</td>
                        <td><input type="number" name="variant_qty[${color}]" value="${existingQty}" class="form-control text-center" placeholder="Enter Qty" min="0"></td>
                    </tr>
                `);
            });
        });

    });
</script>
@endsection