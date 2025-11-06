@extends('layouts.common')
@section('title', 'Add Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="" method="POST">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Stock Entry</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Stock Entry No" value=""/>
                                <label for="code">Stock Entry No * </label>
                            </div>
                        </div> 
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control stock_date" placeholder="Enter Stock Date" />
                                <label for="code">Stock Date * </label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <select id="entry_type" class="select2 form-select" data-placeholder="Select Entry Type">
                                    <option value="">Select Entry Type</option>
                                    <option value="Inward">Inward</option>
                                    <option value="Outward">Outward</option>
                                    <option value="Adjustment">Adjustment</option>
                                </select>
                                <label for="entry_type">Entry Type * <span class="text-danger">*</span></label>
                            </div> 
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <select id="material_category" class="select2 form-select" data-placeholder="Select  Material Category">
                                    <option value="">Select Material Category</option>
                                    <option value="Fabric(MC001)">Fabric(MC001)</option>
                                    <option value="Accessories(MC002)">Accessories(MC002)</option>
                                    <option value="Trims(MC003)">Trims(MC003)</option>
                                    <option value="Thread(MC004)">Thread(MC004)</option>
                                    <option value="Buttons(MC005)">Buttons(MC005)</option>
                                </select>
                                <label for="material_category">Material Category * <span class="text-danger">*</span></label>
                            </div> 
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <select id="material" class="select2 form-select" data-placeholder="Select Material">
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
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <select id="uom" class="select2 form-select" data-placeholder="Select UOM">
                                    <option value="">Select UOM</option>
                                    <option value="MTR">MTR</option>
                                    <option value="PCS">PCS</option>
                                    <option value="ROLL">ROLL</option>
                                </select>
                                <label for="uom">UOM </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Quantity In" />
                                <label for="qty_in">Quantity In * </label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Quantity Out" />
                                <label for="qty_out">Quantity Out * </label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Price" />
                                <label for="price">Price * </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-lg-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Location/Store" />
                                <label for="location">Location/Store * </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <textarea name="" id="" class="form-control h-px-100" placeholder="Enter Remarks"></textarea>
                                <label for="remarks">Remarks</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label for="formFile" class="form-label">Reference Document</label>
                            <input type="file" class="form-control" id="discount" name="taxes">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('stock_entries') }}" class="btn btn-secondary">Cancel</a>
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
        $('.stock_date').flatpickr({
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
        $('#po_no').on('change', function () {
            let po_no = $(this).val();
            if(po_no && po_no.length > 0){
                $('#show_item_det').removeClass('d-none');
            } else {
                $('#show_item_det').addClass('d-none');
            }
        });

    });
</script>
@endsection