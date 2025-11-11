@extends('layouts.common')
@section('title', 'Add Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Item</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand" class="select2 form-select" data-placeholder="Select Brand">
                                        <option value=""></option>
                                        <option value="Hero Mens Wear">Hero Mens Wear</option>
                                        <option value="Unlimited Fashion">Unlimited Fashion</option>
                                        <option value="Classic Apparel">Classic Apparel</option>
                                    </select>
                                    <label for="brand">Brand</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand_category" class="select2 form-select" data-placeholder="Select Brand Category">
                                        <option value="">Select Brand Category</option>
                                        <option value="	Formal Shirts(IC001)">Formal Shirts(IC001)</option>
                                        <option value="Casual Shirts(IC002)">Casual Shirts(IC002)</option>
                                        <option value="Uniform Shirts(IC003)">Uniform Shirts(IC003)</option>
                                        <option value="Kids Shirts(IC004)">Kids Shirts(IC004)</option>
                                        <option value="Premium Shirts(IC004)">Premium Shirts(IC005)</option>
                                    </select>
                                    <label for="brand_category">Brand Category </label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <label class="form-label d-block">Select Type *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="raw_material" value="raw_material">
                                    <label class="form-check-label" for="raw_material">Raw Material</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="items" value="items">
                                    <label class="form-check-label" for="items">Items</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" required>
                                    <label for="name">Name *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Code" name="code">
                                    <label for="code">Code *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="style" class="select2 form-select" data-placeholder="Select Style">
                                        <option value=""></option>
                                        <option value="Plain">Plain</option>
                                        <option value="Print">Print</option>
                                        <option value="Checked">Checked</option>
                                    </select>
                                    <label for="style">Style</label>
                                </div>
                            </div>
                             <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="fabric_type" class="select2 form-select" data-placeholder="Select Fabric Type">
                                        <option value="">Select Fabric Type</option>
                                        <option value="Polyester">Polyester</option>
                                        <option value="Polycotton">Polycotton</option>
                                    </select>
                                    <label for="fabric_type">Fabric Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="design_art_no" placeholder="Enter Design Art Number" name="name" required>
                                    <label for="design_art_no">Design Art Number</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="UOM" class="select2 form-select" data-placeholder="Select UOM">
                                        <option value=""></option>
                                        <option value="MTR">MTR</option>
                                        <option value="PCS">PCS</option>
                                        <option value="ROLL">ROLL</option>
                                    </select>
                                    <label for="UOM">UOM</label>
                                </div>
                            </div>
                           <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline position-relative">
                                    <input type="text" id="selected_size_ratio" class="form-control pe-5" placeholder="Select Size/Ratio" readonly>
                                    <label for="selected_size_ratio">Size / Ratio *</label>
                                    <button type="button" class="btn btn-outline-primary position-absolute end-0 top-0 h-100 rounded-0" data-bs-toggle="modal" data-bs-target="#sizeRatioModal" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Select
                                    </button>
                                </div>
                                <input type="hidden" name="size_ratio" id="size_ratio_input">
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="color" class="select2 form-select" data-placeholder="Select Color" multiple="multiple">
                                        <option value="">Select Color</option>
                                        <option value="Blue">Blue</option>
                                        <option value="White">White</option>
                                        <option value="Red">Red</option>
                                    </select>
                                    <label for="color">Color</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="product_barcode" placeholder="Enter Product Barcode" name="product_barcode" value="890100000001" disabled>
                                    <label for="product_barcode">Product Barcode</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="standard_costing" placeholder="Enter Standard Costing" name="standard_costing" value="">
                                    <label for="standard_costing">Standard Costing</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Material Consumption and Cost (BOM)</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material_category" class="select2 form-select" data-placeholder="Select Raw Material Category">
                                        <option value="">Select Raw Material Category</option>
                                        <option value="Fabric(MC001)">Fabric(MC001)</option>
                                        <option value="Accessories(MC002)">Accessories(MC002)</option>
                                        <option value="Trims(MC003)">Trims(MC003)</option>
                                        <option value="Thread(MC004)">Thread(MC004)</option>
                                        <option value="Buttons(MC005)">Buttons(MC005)</option>
                                    </select>
                                    <label for="material_category">Raw Material Category </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material" class="select2 form-select" data-placeholder="Select Material">
                                        <option value="">Select Material</option>
                                        <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                                        <option value="Zipper(M002)">Zipper(M002)</option>
                                        <option value="Lace(M003)">Lace(M003)</option>
                                        <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                                        <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                                    </select>
                                    <label for="material">Material</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <table class="table table-bordered d-none" id="bom_table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Category</th>
                                            <th>Material</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <label class="d-block mb-4">Operation Stages *</label>
                                <div class="row g-4">
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value="1" name="stages" id="Cutting" />
                                            <label class="form-check-label" for="Cutting">Cutting</label>
                                        </div>
                                        <select class="select2 form-select flex-grow-1" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                            <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                            <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                            <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value="1" name="stages" id="Stitching" />
                                            <label class="form-check-label" for="Stitching">Stitching</label>
                                        </div>
                                        <select class="select2 form-select flex-grow-1" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                            <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                            <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                            <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value="1" name="stages" id="Printing" />
                                            <label class="form-check-label" for="Printing">Printing</label>
                                        </div>
                                        <select class="select2 form-select flex-grow-1" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                            <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                            <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                            <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value="1" name="stages" id="Ironing" />
                                            <label class="form-check-label" for="Ironing">Ironing</label>
                                        </div>
                                        <select class="select2 form-select flex-grow-1" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                            <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                            <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                            <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center mb-3">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value="1" name="stages" id="Packing" />
                                            <label class="form-check-label" for="Packing">Packing</label>
                                        </div>
                                        <select class="select2 form-select flex-grow-1" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                            <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                            <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                            <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Pricing</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="wholesalePrice" placeholder="Enter WholeSale Price" name="wholesalePrice" value="">
                                    <label for="wholesalePrice">WholeSale Price</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="retailPrice" placeholder="Enter Retail Price" name="retailPrice" value="">
                                    <label for="retailPrice">Retail Price</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="exportPrice" placeholder="Enter Export Price" name="exportPrice" value="">
                                    <label for="exportPrice">Export Price</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('items') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sizeRatioModal" tabindex="-1" aria-labelledby="sizeRatioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title text-white" id="sizeRatioModalLabel">Select Size / Ratio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <table class="table align-middle mb-0" id="sizeRatioTable">
            <thead class="table-light">
            <tr>
                <th>Select</th>
                <th>Sizes</th>
                <th>Ratios</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="radio" name="size_ratio_option" value="38,40,42|1,2,1"></td>
                <td>38, 40, 42</td>
                <td>1, 2, 1</td>
            </tr>
            <tr>
                <td><input type="radio" name="size_ratio_option" value="38,40|1,9"></td>
                <td>38, 40</td>
                <td>1, 9</td>
            </tr>
            <tr>
                <td><input type="radio" name="size_ratio_option" value="38,40,42,44|2,1,2,3"></td>
                <td>38, 40, 42, 44</td>
                <td>2, 1, 2, 3</td>
            </tr>
            <tr>
                <td><input type="radio" name="size_ratio_option" value="38,40,42,44,46|3,1,1,1,2"></td>
                <td>38, 40, 42, 44, 46</td>
                <td>3, 1, 1, 1, 2</td>
            </tr>
            </tbody>
        </table>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmSizeRatio">Confirm</button>
        </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function() {
        var selectedCategory = '';

        $('#material_category').change(function() {
            selectedCategory = $(this).val();
            $('#material').val('').trigger('change');
        });

        $('#material').change(function() {
            var material = $(this).val();
            var category_text = $('#material_category option:selected').text();

            if (selectedCategory && material) {
                $('#bom_table').removeClass('d-none');
                var newRow = `<tr>
                <td>${category_text}</td>
                <td>${material}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove_row"><i class="ri ri-delete-bin-line"></i></button></td>
            </tr>`;

                $('#bom_table tbody').append(newRow);
                $('#material').val('').trigger('change');
            }
        });

        $(document).on('click', '.remove_row', function() {
            $(this).closest('tr').remove();
            if ($('#bom_table tbody tr').length == 0) {}
        });

        $('#confirmSizeRatio').click(function() {
            var selectedValue = $('input[name="size_ratio_option"]:checked').val();
            if (selectedValue) {
                var parts = selectedValue.split('|');
                var sizes = parts[0];
                var ratios = parts[1];
                $('#selected_size_ratio').val(sizes + ' - ' + ratios);
                $('#size_ratio_input').val(selectedValue);
                $('#sizeRatioModal').modal('hide');
            } else {
                alert('Please select a Size / Ratio first.');
            }
        });

        $('#sizeRatioTable tbody tr').on('click', function() {
            $('#sizeRatioTable tbody tr').removeClass('table-active');
            $(this).addClass('table-active');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    });
</script>

@endsection