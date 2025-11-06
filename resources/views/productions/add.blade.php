@extends('layouts.common')
@section('title', 'Add Production - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Production</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Job Card Number">
                                        <option value="">Select Job Card Number</option>
                                        <option value="JC20250924-001-K">JC20250924-001-K</option>
                                        <option value="JC20250924-002-K">JC20250924-002-K</option>
                                        <option value="JC20250924-003-K">JC20250924-003-K</option>
                                    </select>
                                    <label for="job_card_no">Job Card Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
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
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="service_provider" id="service_provider" class="form-select select2" data-placeholder="Select Assigned Service Provider">
                                        <option value="">Select Assigned Service Provider</option>
                                        <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                        <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                        <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                        <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                    </select>
                                    <label for="Service Provider">Service Provider *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="planned_qty" placeholder="Enter Planned Quantity" name="planned_qty" disabled>
                                    <label for="planned_start">Planned Quantity * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="completed_qty" placeholder="Enter Completed Quantity" name="completed_qty">
                                    <label for="completed_qty">Completed Quantity * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="wip" placeholder="Enter WIP" name="wip" disabled>
                                    <label for="wip">WIP </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="wastage_qty" placeholder="Enter Wastage Quantity" name="wastage_qty">
                                    <label for="planned_start">Wastage Quantity * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Wastage Reason">
                                        <option value="">Select Wastage Reason</option>
                                        <option value="Fabric defect">Fabric defect</option>
                                        <option value="Stitching error">Stitching error</option>
                                        <option value="Printing issue">Printing issue</option>
                                        <option value="Dye problem">Dye problem</option>
                                    </select>
                                    <label for="wastage_reason">Wastage Reason * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    <label for="status">Status * </label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('productions') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#item-rows').on('click', '.add_item', function() {
            var html = `
        <div class="item-block mb-4 mt-3">
            <div class="row">
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select item" data-placeholder="Select Item">
                            <option value="">Select Item</option>
                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                        </select>
                        <label>Item</label>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control quantity" placeholder="Enter Quantity">
                        <label>Quantity *</label>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Size">
                            <option value="">Select Size</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                        <label for="size">Size * </label>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
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
                <div class="col-lg-2">
                    <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line"></i> </button>
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
                `<div class="row production-row mt-4">
            <div class="col-md-6 col-xl-4">
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
            <div class="col-lg-3">
                <div class="form-floating form-floating-outline">
                    <input type="text" class="form-control plan_start" id="planned_start" placeholder="Enter Planned Start Date" name="planned_start">
                    <label for="planned_start">Planned Start Date * </label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-floating form-floating-outline">
                    <input type="text" class="form-control plan_end" id="planned_end" placeholder="Enter Planned End Date" name="planned_end">
                    <label for="planned_end">Planned End Date * </label>
                </div>
            </div>
            <div class="col-lg-2">
                <button type="button" class="btn btn-danger delete_production"><i class="ri ri-delete-bin-line"></i> </button>
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
            $(this).closest('.production-row').remove();
        });
        $('.job_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.delivery_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
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
    });
</script>
@endsection