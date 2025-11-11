@extends('layouts.common')
@section('title', 'Add Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Job Card Entry</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="job_card_no" placeholder="Enter Job Card Number" name="job_card_no" value="JC20250924-001-K">
                                    <label for="job_card_no">Job Card Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control issue_date" placeholder="Enter Issue Date" />
                                    <label for="code">Issue Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control delivery_date" placeholder="Enter Delivery Date" />
                                    <label for="code">Delivery Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season" name="season" class="form-select select2" data-placeholder="Select Season">
                                        <option value="">Select Season</option>
                                        <option value="Pongal">Pongal Season</option>
                                        <option value="Navaratri / Dussehra">Navaratri / Dussehra Season</option>
                                        <option value="Diwali">Diwali Season</option>
                                        <option value="Christmas / New Year">Christmas / New Year Season</option>
                                    </select>
                                    <label for="season">Season </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand" name="brand" class="form-select select2" data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        <option value="Casino Deal(CD)">Casino Deal(CD)</option>
                                        <option value="Casino Gold(CG)">Casino Gold(CS)</option>
                                        <option value="Casino Formal(CF)">Casino Formal(CF)</option>
                                        <option value="Casino Premium(CP)">Casino Premium(CP)</option>
                                    </select>
                                    <label for="brand">Brand </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="assign_unit" name="" class="form-select select2" data-placeholder="Select Assign Unit">
                                        <option value="">Select Assign Unit</option>
                                        <option value="Nachias Fashion Private Limited">Nachias Fashion Private Limited</option>
                                        <option value="Samayanallur">Samayanallur</option>
                                        <option value="Kalavasal">Kalavasal</option>
                                    </select>
                                    <label for="assign_unit">Assign Unit </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="receipt_store" name="" class="form-select select2" data-placeholder="Select Receipt Store">
                                        <option value="">Select Receipt Store</option>
                                        <option value="Finished Goods">Finished Goods</option>
                                    </select>
                                    <label for="receipt_store">Receipt Store </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline input-group">
                                    <input type="text" id="process_group" name="process_group" class="form-control" placeholder="Select Process Group" readonly>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#processGroupModal">
                                    <i class="ri ri-search-line"></i>
                                    </button>
                                    <label for="process_group">Process Group</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Urgent">Urgent</option>
                                        <option value="Normal">Normal</option>
                                    </select>
                                    <label for="status">Status </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details </h4>
                        </div>
                        <div id="item-rows">
                            <div class="item-block mb-4">
                                <div class="row g-4 item-row">
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                            <label for="item">Item </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="quantity" placeholder="Enter Quatity" name="quantity">
                                            <label for="quantity">Quantity * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select uom" id="uom" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="PCS">PCS</option>
                                                <option value="MTR">MTR</option>
                                                <option value="ROLL">ROLL</option>
                                                <option value="KG">KG</option>
                                                <option value="SET">SET</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="size" class="select2 form-select" data-placeholder="Select Size">
                                                <option value="">Select Size</option>
                                                <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                                                <option value="38,40 (5,2)">38,40 (5,2)</option>
                                                <option value="42,44 (5,7)">42,44 (5,7)</option>
                                                <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                                                <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                                            </select>
                                            <label for="size">Size * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="sleeve" class="select2 form-select" data-placeholder="Select Sleeve">
                                                <option value="">Select Sleeve</option>
                                                <option value="Full Sleeve">Full Sleeve</option>
                                                <option value="Half Sleeve">Sleeve</option>
                                                <option value="Full-half Sleeve">Full-half Sleeve</option>
                                            </select>
                                            <label for="sleeve">Sleeve * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="color" class="select2 form-select" data-placeholder="Select Color">
                                                <option value="">Select Color</option>
                                                <option value="White">White</option>
                                                <option value="Blue">Blue</option>
                                                <option value="Red">Red</option>
                                                <option value="Black">Black</option>
                                            </select>
                                            <label for="color">Color * </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 text-end">
                                        <button type="button" class="btn btn-primary add_item"><i class="icon-base ri ri-add-line"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered d-none mb-4" id="material_bom_table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Material</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row g-4 production-row mt-4">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Production Stages">
                                        <option value="">Select Production Stages</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stitching">Stitching</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="production_stages">Production Stages </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_start" id="planned_start" placeholder="Enter Planned Start Date" name="planned_start">
                                    <label for="planned_start">Planned Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_end" id="planned_end" placeholder="Enter Planned End Date" name="planned_end">
                                    <label for="planned_end">Planned End Date * </label>
                                </div>
                            </div>
                            <div class="col-lg-1 text-end">
                                <button type="button" class="btn btn-primary add_stage"><i class="icon-base ri ri-add-line"></i></button>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="processGroupModal" tabindex="-1" aria-labelledby="processGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="processGroupModalLabel">Select Process Group</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered align-middle text-center" id="processGroupTable">
                    <thead class="table-light">
                        <tr>
                            <th>Select</th>
                            <th>Code</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-F/S|Checked Full Sleeve"></td>
                            <td>CKD-F/S</td>
                            <td>Checked Full Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-FS/HS|Checked Full & Half Sleeve"></td>
                            <td>CKD-FS/HS</td>
                            <td>Checked Full & Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-H/S|Checked Half Sleeve"></td>
                            <td>CKD-H/S</td>
                            <td>Checked Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-F/S|Others Full Sleeve"></td>
                            <td>OTH-F/S</td>
                            <td>Others Full Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-FS/HS|Others Full & Half Sleeve"></td>
                            <td>OTH-FS/HS</td>
                            <td>Others Full & Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-H/S|Others Half Sleeve"></td>
                            <td>OTH-H/S</td>
                            <td>Others Half Sleeve</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmProcessGroup">Select</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#item-rows').on('click', '.add_item', function() {
            var html = `
        <div class="item-block mb-4 mt-3">
            <div class="row g-4">
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select item" data-placeholder="Select Item">
                            <option value="">Select Item</option>
                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                            <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)	</option>
                            <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                            <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                        </select>
                        <label>Item</label>
                    </div>
                </div>
                <div class="col-md-2 col-lg-1">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control quantity" placeholder="Enter Quantity">
                        <label>Quantity *</label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select uom"  data-placeholder="Select UOM">
                            <option value="">Select UOM</option>
                            <option value="PCS">PCS</option>
                            <option value="MTR">MTR</option>
                            <option value="ROLL">ROLL</option>
                            <option value="KG">KG</option>
                            <option value="SET">SET</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Size">
                            <option value="">Select Size</option>
                            <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                            <option value="38,40 (5,2)">38,40 (5,2)</option>
                            <option value="42,44 (5,7)">42,44 (5,7)</option>
                            <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                            <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                        </select>
                        <label for="size">Size * </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Sleeve">
                            <option value="">Select Sleeve</option>
                            <option value="Full Sleeve">Full Sleeve</option>
                            <option value="Half Sleeve">Sleeve</option>
                            <option value="Full-half Sleeve">Full-half Sleeve</option>
                        </select>
                        <label for="sleeve">Sleeve * </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Color">
                            <option value="">Select Color</option>
                            <option value="White">White</option>
                            <option value="Blue">Blue</option>
                            <option value="Red">Red</option>
                            <option value="Black">Black</option>
                        </select>
                        <label for="color">Color * </label>
                    </div>
                </div>
                <div class="col-lg-1 text-end">
                    <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line icon-base"></i> </button>
                </div>
            </div>
        </div>
        `;
            $('#item-rows').append(html);
            $(".select2").select2();
        });
        $('#item-rows').on('click', '.delete_item', function() {
            $(this).closest('.item-block').remove();
        });
        $(document).on("click", ".add_stage", function() {
            var production_row =
                `<div class="stage-block mt-3">
                    <div class="col-lg-12">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Production Stages">
                                        <option value="">Select Production Stages</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stitching">Stitching</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="production_stages">Production Stages </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_start" id="planned_start" placeholder="Enter Planned Start Date" name="planned_start">
                                    <label for="planned_start">Planned Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_end" id="planned_end" placeholder="Enter Planned End Date" name="planned_end">
                                    <label for="planned_end">Planned End Date * </label>
                                </div>
                            </div>
                            <div class="col-lg-1 text-end">
                                <button type="button" class="btn btn-danger delete_production"><i class="ri ri-delete-bin-line icon-base"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('.production-row').append(production_row);
            $(".select2").select2();
            $('.plan_start').flatpickr({
                dateFormat: 'd-m-Y',
                defaultDate: 'today',
                minDate: 'today',
                allowInput: true
            });

            $('.plan_end').flatpickr({
                dateFormat: 'd-m-Y',
                defaultDate: 'today',
                minDate: 'today',
                allowInput: true
            });
        });
        $(document).on("click", ".delete_production", function() {
            $(this).closest('.stage-block').remove();
        });
        $('.job_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.delivery_date').flatpickr({
            dateFormat: 'd-m-Y',
            minDate: 'today',
            allowInput: true
        });
        $('.issue_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });
        $('.plan_start').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.plan_end').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#item').change(function() {
            var item = $(this).val();
            if (item) {
                $('#material_bom_table').removeClass('d-none');
                var newRow = `<tr>
                <td>1</td>
                <td>Cotton Poplin 60 GSM(M001)</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Plastic Button 18L(M002)</td>
            </tr>`;
                $('#material_bom_table tbody').append(newRow);
            } else {
                $('#material_bom_table').addClass('d-none');
            }
        });
        $('#confirmProcessGroup').click(function() {
            var selectedValue = $('input[name="process_option"]:checked').val();
            if (selectedValue) {
                var parts = selectedValue.split('|');
                var code = parts[0];
                var desc = parts[1];
                $('#process_group').val(code + ' - ' + desc);
                $('#processGroupModal').modal('hide');
            } else {
                alert('Please select a Process Group first.');
            }
            });

            $('#processGroupTable tbody tr').on('click', function() {
            $('#processGroupTable tbody tr').removeClass('table-active');
            $(this).addClass('table-active');
            $(this).find('input[type="radio"]').prop('checked', true);
        });

    });
</script>
@endsection