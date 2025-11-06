@extends('layouts.common')
@section('title', 'Add Stock Consumable & Sales Return - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="" method="POST">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Stock Consumable & Sales Return</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control" placeholder="Enter Issue No" value=""/>
                                <label for="code">Issue No * </label>
                            </div>
                        </div> 
                        <div class="col-lg-4 mb-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control form-control issue_date" placeholder="Enter Issue Date" />
                                <label for="code">Issue Date * </label>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="form-floating form-floating-outline">
                                <select id="issue_type" class="select2 form-select" data-placeholder="Select Issue Type">
                                    <option value="">Select Issue Type</option>
                                    <option value="Consumable Issue">Consumable Issue</option>
                                    <option value="Sales Return">Sales Return</option>
                                </select>
                                <label for="issue_type">Issue Type * <span class="text-danger">*</span></label>
                            </div> 
                        </div>
                        <div id="consumable_div" class="d-none">
                            <div class="row mb-5">
                                <div class="col-lg-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="production" class="select2 form-select" data-placeholder="Select Production">
                                            <option value="">Select Production</option>
                                            <option value="Cutting">Cutting</option>
                                            <option value="Stitching">Stitching</option>
                                            <option value="Stitching">Printing</option>
                                            <option value="Ironing">Ironing</option>
                                            <option value="Packing">Packing</option>
                                        </select>
                                        <label for="production">Production * <span class="text-danger">*</span></label>
                                    </div> 
                                </div>
                                 <div class="col-lg-4">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-px-100" id="remarks" placeholder="Enter Remarks"></textarea>
                                        <label for="remarks">Remarks</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-4">
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
                                <div class="col-lg-4 mb-4">
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
                                <div class="col-lg-4 mb-4">
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
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" placeholder="Enter Issued Quantity" />
                                        <label for="issue_qty">Issued Quantity </label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" id="return_qty" placeholder="Enter Returned Quantity" />
                                        <label for="return_qty">Returned Quantity</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" placeholder="Net Assumption" readonly />
                                        <label for="location">Net Assumption </label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="reason" class="select2 form-select" data-placeholder="Select Reason">
                                            <option value="">Select Reason</option>
                                            <option value="Excess">Excess</option>
                                            <option value="Damage">Damage</option>
                                            <option value="Quality Issue">Quality Issue</option>
                                        </select>
                                        <label for="reason">Reason </label>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" class="btn btn-primary add_consumable"><i class="icon-base ri ri-add-line"></i></button>
                                </div>
                            </div>
                        </div>
                        <div id="sales_return_div" class="d-none">
                            <div class="row">
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="item_category" class="select2 form-select" data-placeholder="Select Item Category">
                                            <option value="">Select Item Category</option>
                                            <option value="	Formal Shirts(IC001)">Formal Shirts(IC001)</option>
                                            <option value="Casual Shirts(IC002)">Casual Shirts(IC002)</option>
                                            <option value="Uniform Shirts(IC003)">Uniform Shirts(IC003)</option>
                                            <option value="Kids Shirts(IC004)">Kids Shirts(IC004)</option>
                                            <option value="Premium Shirts(IC004)">Premium Shirts(IC005)</option>
                                        </select>
                                        <label for="item_category">Item Category </label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                            <option value="">Select Item</option>
                                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                            <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)	</option>
                                            <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                            <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                        </select>
                                        <label for="item">Item </label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 form-select" data-placeholder="Select UOM">
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
                                        <input type="text" class="form-control form-control" placeholder="Enter Rate(per unit)" />
                                        <label for="rate">Rate(per unit)</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" id="sales_return_qty" placeholder="Enter Returned Quantity" />
                                        <label for="sales_return_qty">Returned Quantity</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" placeholder="Enter Sub Total" />
                                        <label for="sub_totl">Sub Total</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" placeholder="Enter GST Amount" readonly />
                                        <label for="gst_amt">GST Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control" placeholder="Enter Amount" />
                                        <label for="amount">Amount</label>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" class="btn btn-primary add_sales_return"><i class="icon-base ri ri-add-line"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('stock_consumables_returns') }}" class="btn btn-secondary">Cancel</a>
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
        $('.issue_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#issue_type').change(function() {
            var issue_type = $(this).val();
            if(issue_type == 'Consumable Issue') {
                $('#consumable_div').removeClass('d-none');
                $('#sales_return_div').addClass('d-none');
            } else {
                $('#consumable_div').addClass('d-none');
                $('#sales_return_div').removeClass('d-none');
            }
        });
        $(document).on('click', '.add_consumable', function() {
            var newRow = `
            <div class="row consumable-row mb-4 mt-3">
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Material Category">
                            <option value="">Select Material Category</option>
                            <option value="Fabric(MC001)">Fabric(MC001)</option>
                            <option value="Accessories(MC002)">Accessories(MC002)</option>
                            <option value="Trims(MC003)">Trims(MC003)</option>
                            <option value="Thread(MC004)">Thread(MC004)</option>
                            <option value="Buttons(MC005)">Buttons(MC005)</option>
                        </select>
                        <label>Material Category</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Material">
                            <option value="">Select Material</option>
                            <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                            <option value="Zipper(M002)">Zipper(M002)</option>
                            <option value="Lace(M003)">Lace(M003)</option>
                            <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                            <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                        </select>
                        <label>Material</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select UOM">
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
                        <input type="text" class="form-control" placeholder="Issued Quantity" />
                        <label>Issued Quantity</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" placeholder="Returned Quantity" />
                        <label>Returned Quantity</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control form-control" placeholder="Net Assumption" readonly />
                        <label for="location">Net Assumption </label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Reason">
                            <option value="">Select Reason</option>
                            <option value="Excess">Excess</option>
                            <option value="Damage">Damage</option>
                            <option value="Quality Issue">Quality Issue</option>
                        </select>
                        <label>Reason</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button type="button" class="btn btn-danger delete_consumable"><i class="ri ri-delete-bin-line"></i> </button>
                </div>
            </div>
            `;
            $('#consumable_div').append(newRow);
            $(".select2").select2();
        });
        
        $(document).on('click', '.add_sales_return', function() {
            var newRow = `
            <div class="row sales-return-row">
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Item Category">
                            <option value="">Select Item Category</option>
                            <option value="	Formal Shirts(IC001)">Formal Shirts(IC001)</option>
                            <option value="Casual Shirts(IC002)">Casual Shirts(IC002)</option>
                            <option value="Uniform Shirts(IC003)">Uniform Shirts(IC003)</option>
                            <option value="Kids Shirts(IC004)">Kids Shirts(IC004)</option>
                            <option value="Premium Shirts(IC004)">Premium Shirts(IC005)</option>
                        </select>
                        <label for="item_category">Item Category </label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select id="" class="select2 form-select" data-placeholder="Select Item">
                            <option value="">Select Item</option>
                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                            <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)	</option>
                            <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                            <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                        </select>
                        <label for="item">Item </label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select UOM">
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
                        <input type="text" class="form-control form-control" placeholder="Enter Rate(per unit)" />
                        <label for="rate">Rate(per unit)</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control form-control" id="sales_return_qty" placeholder="Enter Returned Quantity" />
                        <label for="sales_return_qty">Returned Quantity</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control form-control" placeholder="Enter Sub Total" />
                        <label for="sub_totl">Sub Total</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control form-control" placeholder="Enter GST Amount" readonly />
                        <label for="gst_amt">GST Amount</label>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control form-control" placeholder="Enter Amount" />
                        <label for="amount">Amount</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button type="button" class="btn btn-danger delete_sales_return"><i class="ri ri-delete-bin-line"></i> </button>
                </div>
            </div>`;
            $('#sales_return_div').append(newRow);
            $(".select2").select2();
        });
        $(document).on('click', '.delete_consumable', function(){
            $(this).closest('.consumable-row').remove();
        });
    });
</script>
@endsection