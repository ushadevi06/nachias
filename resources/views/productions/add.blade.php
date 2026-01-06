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
                                    <input type="text" class="form-control prod_date" id="production_date" placeholder="DD-MM-YYYY" name="production_date">
                                    <label for="production_date">Production Date *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="job_card_no" class="select2 form-select" data-placeholder="Select Job Card Number">
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
                                    <select id="production_stage" class="select2 form-select" data-placeholder="Select Production Stage">
                                        <option value="">Select Production Stage</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stitching">Stitching</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="production_stage">Production Stage </label>
                                </div>
                            </div>

                            {{-- Row 2 --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="service_provider" id="service_provider" class="form-select select2" data-placeholder="Select Service Provider">
                                        <option value="">Select Service Provider</option>
                                        <option value="Fast Print Works">Fast Print Works(SP002)</option>
                                        <option value="In-House Cutting">In-House Cutting(SP003)</option>
                                        <option value="Vendor A Stitching">Vendor A Stitching(SP004)</option>
                                    </select>
                                    <label for="service_provider">Service Provider *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="shift" id="shift" class="form-select select2" data-placeholder="Select Shift">
                                        <option value="">Select Shift</option>
                                        <option value="Day">Day</option>
                                        <option value="Night">Night</option>
                                        <option value="Evening">Evening</option>
                                    </select>
                                    <label for="shift">Shift *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control qty-input" id="planned_qty" placeholder="0" name="planned_qty" value="100">
                                    <label for="planned_qty">Planned Quantity * </label>
                                </div>
                            </div>

                            {{-- Row 3 --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control qty-input" id="completed_qty" placeholder="0" name="completed_qty">
                                    <label for="completed_qty">Completed Quantity * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control qty-input" id="wastage_qty" placeholder="0" name="wastage_qty">
                                    <label for="wastage_qty">Wastage Quantity </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control qty-input" id="rework_qty" placeholder="0" name="rework_qty">
                                    <label for="rework_qty">Rework Quantity </label>
                                </div>
                            </div>

                            {{-- Row 4 --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control bg-light" id="balance_qty" placeholder="0" name="balance_qty" readonly>
                                    <label for="balance_qty">Balance Quantity (Auto)</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="wip" placeholder="0" name="wip">
                                    <label for="wip">WIP </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="wastage_reason" data-placeholder="Select Wastage Reason">
                                        <option value="">Select Wastage Reason</option>
                                        <option value="Fabric defect">Fabric defect</option>
                                        <option value="Stitching error">Stitching error</option>
                                        <option value="Printing issue">Printing issue</option>
                                    </select>
                                    <label for="wastage_reason">Wastage Reason </label>
                                </div>
                            </div>

                            {{-- Row 5 --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="status" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    <label for="status">Status * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="supervisor" placeholder="Enter Supervisor Name" name="supervisor">
                                    <label for="supervisor">Supervisor *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter Remarks" style="height: 100px;"></textarea>
                                    <label for="remarks">Remarks</label>
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
        // Initialize date picker
        $('.prod_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });

        // Auto-calculation logic
        function calculateBalance() {
            let planned = parseFloat($('#planned_qty').val()) || 0;
            let completed = parseFloat($('#completed_qty').val()) || 0;
            let wastage = parseFloat($('#wastage_qty').val()) || 0;
            let rework = parseFloat($('#rework_qty').val()) || 0;

            let balance = planned - (completed + wastage + rework);
            $('#balance_qty').val(balance);
        }

        $('.qty-input').on('input', function() {
            calculateBalance();
        });

        // Initial calculation
        calculateBalance();
    });
</script>
@endsection
