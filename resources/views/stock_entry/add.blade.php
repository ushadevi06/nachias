@extends('layouts.common')
@section('title', 'Add Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Stock Entry</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="stock_entry_no" class="form-control" placeholder="Enter Stock Entry No" value=""/>
                                    <label for="code">Stock Entry No * </label>
                                </div>
                            </div> 

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="stock_date" class="form-control stock_date" placeholder="Enter Stock Date" />
                                    <label for="stock_date">Stock Date * </label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="entry_type" name="entry_type" class="select2 form-select" data-placeholder="Select Entry Type">
                                        <option value="">Select Entry Type</option>
                                        <option value="Inward">Inward</option>
                                        <option value="Outward">Outward</option>
                                        <option value="Adjustment">Adjustment</option>
                                    </select>
                                    <label for="entry_type">Entry Type * <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <label class="form-label d-block">Select Type *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="raw_material" value="raw_material">
                                    <label class="form-check-label" for="raw_material">Raw Material</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="finished_goods" value="finished_goods">
                                    <label class="form-check-label" for="finished_goods">Finished Goods</label>
                                </div>
                            </div>
                        </div>

                        <!-- RAW MATERIAL SECTION -->
                        <div class="row mb-4" id="raw_material_section" style="display:none;">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_category" name="store_category" class="select2 form-select" data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        <option value="Fabric(MC001)">Fabric(MC001)</option>
                                        <option value="Accessories(MC002)">Accessories(MC002)</option>
                                        <option value="Trims(MC003)">Trims(MC003)</option>
                                        <option value="Thread(MC004)">Thread(MC004)</option>
                                        <option value="Buttons(MC005)">Buttons(MC005)</option>
                                    </select>
                                    <label for="store_category">Store Category * <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material" name="material" class="select2 form-select" data-placeholder="Select Material">
                                        <option value="">Select Material</option>
                                        <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                                        <option value="Zipper(M002)">Zipper(M002)</option>
                                        <option value="Lace(M003)">Lace(M003)</option>
                                        <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                                        <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                                    </select>
                                    <label for="material">Material * <span class="text-danger">*</span></label>
                                </div> 
                            </div>
                        </div>

                        <!-- FINISHED GOODS SECTION -->
                        <div class="row mb-4" id="finished_goods_section" style="display:none;">
                            <div class="col-lg-6 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="finished_item" name="finished_item" class="select2 form-select" data-placeholder="Select Finished Item">
                                        <option value="">Select Finished Item</option>
                                        <option value="ITEM001">Men’s Formal Cotton Shirt(ITEM001)</option>
                                        <option value="ITEM002">Men’s Casual Denim Shirt(ITEM002)</option>
                                        <option value="ITEM003">School Uniform Shirt(ITEM003)</option>
                                        <option value="ITEM004">Kids Polo Shirt(ITEM004)</option>
                                        <option value="ITEM005">Premium Linen Shirt(ITEM005)</option>
                                    </select>
                                    <label for="finished_item">Finished Goods Item * <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- RAW MATERIAL DETAILS FOR FINISHED GOODS -->
                        <div class="row mb-4" id="finished_raw_materials" style="display:none;">
                            <div class="col-lg-12">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-light">
                                        <strong>Raw Material Requirements</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 10%">S.No</th>
                                                        <th>Raw Material</th>
                                                        <th>UOM</th>
                                                        <th>Required Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="raw_material_table_body">
                                                    <!-- JS will populate rows dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="uom" name="uom" class="select2 form-select" data-placeholder="Select UOM">
                                        <option value="">Select UOM</option>
                                        <option value="MTR">MTR</option>
                                        <option value="PCS">PCS</option>
                                        <option value="ROLL">ROLL</option>
                                    </select>
                                    <label for="uom">UOM </label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="qty_in" class="form-control" placeholder="Enter Quantity In" />
                                    <label for="qty_in">Quantity In * </label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="qty_out" class="form-control" placeholder="Enter Quantity Out" />
                                    <label for="qty_out">Quantity Out * </label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="price" class="form-control" placeholder="Enter Price" />
                                    <label for="price">Price * </label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="location" class="form-control" placeholder="Enter Location/Store" />
                                    <label for="location">Location/Store * </label>
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control h-px-100" placeholder="Enter Remarks"></textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <label for="formFile" class="form-label">Reference Document</label>
                                <input type="file" class="form-control" id="formFile" name="reference_doc">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('stock_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Initialize date picker
    $('.stock_date').flatpickr({
        dateFormat: 'd-m-Y',
        defaultDate: 'today',
        minDate: 'today',
        allowInput: true
    });

    // Toggle Raw Material / Finished Goods sections
    $('input[name="entry_type_radio"]').on('change', function() {
        if ($(this).val() === 'raw_material') {
            $('#raw_material_section').show();
            $('#finished_goods_section, #finished_raw_materials').hide();
        } else if ($(this).val() === 'finished_goods') {
            $('#raw_material_section').hide();
            $('#finished_goods_section').show();
            $('#finished_raw_materials').hide();
        }
    });

    // Raw material data for each finished good
    const finishedGoodsMaterials = {
        'ITEM001': [
            { sno: 1, name: 'Cotton Fabric 60 GSM', uom: 'MTR', qty: 2.5 },
            { sno: 2, name: 'Buttons - White 12mm', uom: 'PCS', qty: 6 },
            { sno: 3, name: 'Thread - Polyester', uom: 'ROLL', qty: 0.2 },
        ],
        'ITEM002': [
            { sno: 1, name: 'Denim Fabric 200 GSM', uom: 'MTR', qty: 2.8 },
            { sno: 2, name: 'Metal Buttons', uom: 'PCS', qty: 6 },
            { sno: 3, name: 'Thread - Blue', uom: 'ROLL', qty: 0.15 },
        ],
        'ITEM003': [
            { sno: 1, name: 'Cotton Blend Fabric', uom: 'MTR', qty: 2.3 },
            { sno: 2, name: 'Buttons', uom: 'PCS', qty: 5 },
            { sno: 3, name: 'Thread - White', uom: 'ROLL', qty: 0.1 },
        ],
        'ITEM004': [
            { sno: 1, name: 'Pique Fabric', uom: 'MTR', qty: 1.5 },
            { sno: 2, name: 'Buttons - Plastic', uom: 'PCS', qty: 3 },
            { sno: 3, name: 'Thread - Red', uom: 'ROLL', qty: 0.05 },
        ],
        'ITEM005': [
            { sno: 1, name: 'Linen Fabric Premium', uom: 'MTR', qty: 2.4 },
            { sno: 2, name: 'Buttons - Pearl', uom: 'PCS', qty: 6 },
            { sno: 3, name: 'Thread - Beige', uom: 'ROLL', qty: 0.2 },
        ],
    };

    // When Finished Goods selected → show related raw materials
    $('#finished_item').on('change', function() {
        const selectedItem = $(this).val();
        const tableBody = $('#raw_material_table_body');
        tableBody.empty();

        if (selectedItem && finishedGoodsMaterials[selectedItem]) {
            finishedGoodsMaterials[selectedItem].forEach(row => {
                tableBody.append(`
                    <tr>
                        <td>${row.sno}</td>
                        <td>${row.name}</td>
                        <td>${row.uom}</td>
                        <td>${row.qty}</td>
                    </tr>
                `);
            });
            $('#finished_raw_materials').show();
        } else {
            $('#finished_raw_materials').hide();
        }
    });
});
</script>


@endsection
