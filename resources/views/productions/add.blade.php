@extends('layouts.common')
@section('title', 'Production Planning - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-11">
            <form action="" method="POST" class="common-form">
                
                {{-- MODULE 1: PRODUCTION PLANNING (HEADER) --}}
                <div class="card mb-4 border-0 shadow-sm erp-header-card">
                    <div class="card-header border-bottom py-3 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="ri ri-file-list-3-line fs-4 me-2 text-primary"></i>
                            <h5 class="mb-0 fw-bold">Module 1: Production (Planning & Control)</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="production_id" value="PROD-2026-001" readonly>
                                    <label>Production ID</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="job_card_no" class="select2 form-select" data-placeholder="Select Job Card">
                                        <option value="">Select Job Card</option>
                                        <option value="JC20260113-001-K">JC20260113-001-K</option>
                                    </select>
                                    <label>Job Card No *</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="work_order_no" value="WO-2026-001" readonly>
                                    <label>Work Order No</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="po_no" value="PO-2025-099" readonly>
                                    <label>Purchase Order No</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="plant" class="select2 form-select" data-placeholder="Select Plant">
                                        <option value="">Select Plant</option>
                                        <option value="Nachias fashion private limited" selected>Nachias Fashion Private Limited</option>
                                        <option value="Samayanallur">Samayanallur</option>
                                        <option value="Kalavasal">Kalavasal</option>
                                    </select>
                                    <label>Plant</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="production_unit" class="select2 form-select" data-placeholder="Select Unit">
                                        <option value="">Select Unit</option>
                                        <option value="NFPL - CUT">NFPL - CUT</option>
                                    </select>
                                    <label>Production Unit</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="process_group" class="select2 form-select" data-placeholder="Select Group">
                                        <option value="">Select Group</option>
                                        <option value="Others Full & Half Sleeves">Others Full & Half Sleeve</option>
                                    </select>
                                    <label>Process Group</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="planned_qty_fs" value="334">
                                    <label>Full Sleeve Qty (FS)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="planned_qty_hs" value="228">
                                    <label>Half Sleeve Qty (HS)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control bg-light" id="planned_qty" value="562" readonly>
                                    <label>Total Planned Qty</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="start_date">
                                    <label>Planned Start Date</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="end_date">
                                    <label>Planned End Date</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="completion_date">
                                    <label>Exp. Completion Date</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="issue_store" class="select2 form-select" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        <option value="Fabric Store">Fabric Store</option>
                                        <option value="Accessories Store">Accessories Store</option>
                                        <option value="Finished Goods">Finished Goods</option>
                                    </select>
                                    <label>Issue Store</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="receipt_store" class="select2 form-select" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        <option value="Finished Goods Store">Finished Goods Store</option>
                                    </select>
                                    <label>Receipt Store</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="reference_no">
                                    <label>Reference No</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="reference_date">
                                    <label>Reference Date</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="Draft">Draft</option>
                                        <option value="Planned">Planned</option>
                                        <option value="In-Progress">In-Progress</option>
                                    </select>
                                    <label>Status</label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="remarks">
                                    <label>Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODULE 2: PROCESS SCHEDULE (STEPPER STYLE) --}}
                <div class="card border-0 shadow-sm erp-header-card">
                    <div class="card-header border-bottom py-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="ri ri-node-tree me-2 text-info"></i> Module 2: Process Schedule
                            </h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- 1. Stepper Header --}}
                        <div class="bs-stepper wizard-numbered mt-2">
                            <div class="bs-stepper-header border-bottom p-4">
                                {{-- Step 1 --}}
                                <div class="step active" data-target="#stage-cutting">
                                    <button type="button" class="step-trigger">
                                        <span class="bs-stepper-circle bg-success text-white"><i class="ri ri-scissors-cut-line"></i></span>
                                        <span class="bs-stepper-label text-success">
                                            <span class="bs-stepper-title">Cutting</span>
                                            <span class="bs-stepper-subtitle">Completed</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                {{-- Step 2 --}}
                                <div class="step" data-target="#stage-stitching">
                                    <button type="button" class="step-trigger">
                                        <span class="bs-stepper-circle bg-warning text-white"><i class="ri ri-t-shirt-line"></i></span>
                                        <span class="bs-stepper-label text-warning">
                                            <span class="bs-stepper-title">Stitching</span>
                                            <span class="bs-stepper-subtitle">In Progress</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                {{-- Step 3 --}}
                                <div class="step" data-target="#stage-finishing">
                                    <button type="button" class="step-trigger">
                                        <span class="bs-stepper-circle bg-secondary text-white"><i class="ri ri-shirt-line"></i></span>
                                        <span class="bs-stepper-label">
                                            <span class="bs-stepper-title">Finishing</span>
                                            <span class="bs-stepper-subtitle">Pending</span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            {{-- 2. Stepper Content --}}
                            <div class="bs-stepper-content p-4">                              
                                {{-- Stage 1: Cutting Form --}}
                                <div id="stage-cutting" class="content active dstepper-block">
                                    <div class="row g-3">
                                        <div class="col-12 mb-2"><h6 class="fw-bold text-success">Stage 1 Details: Cutting</h6></div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" class="form-control" value="1000">
                                                <label>Planned Qty</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" value="PCS">
                                                <label>UOM</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Start Date">
                                                <label>Start Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="End Date">
                                                <label>End Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Due Date">
                                                <label>Due Date</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2">
                                                    <option value="Nachias fashion private limited">Nachias fashion private limited</option>
                                                </select>
                                                <label>Scheduled To</label>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2">
                                                    <option value="Samayanallur Unit">Samayanallur Unit</option>
                                                    <option value="Kalavasal Unit">Kalavasal Unit</option>
                                                </select>
                                                <label>Service Provider</label>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold text-success mb-3"><i class="ri ri-list-settings-line me-1"></i> Auto-Generated Services (Cutting)</h6>
                                            <div class="table-responsive border rounded">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Service Name</th>
                                                            <th>Applies To</th>
                                                            <th class="text-end">Qty</th>
                                                            <th>UOM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="services-cutting">
                                                        <!-- JS will populate this -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-12 text-end mt-4">
                                            <button type="button" class="btn btn-primary btn-next" onclick="showStep('#stage-stitching')">Next: Stitching <i class="ri ri-arrow-right-line ms-1"></i></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Stage 2: Stitching Form --}}
                                <div id="stage-stitching" class="content d-none">
                                    <div class="row g-3">
                                        <div class="col-12 mb-2"><h6 class="fw-bold text-warning">Stage 2 Details: Stitching</h6></div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" class="form-control" value="1000">
                                                <label>Planned Qty</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" value="PCS">
                                                <label>UOM</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Start Date">
                                                <label>Start Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="End Date">
                                                <label>End Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Due Date">
                                                <label>Due Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2">
                                                    <option value="Samayanallur Unit">Samayanallur Unit</option>
                                                </select>
                                                <label>Scheduled To</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2" data-placeholder="Select Service Provider">
                                                <option value="Int Unit">Internal Unit</option>
                                                </select>
                                                <label>Service Provider</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold text-warning mb-3"><i class="ri ri-list-settings-line me-1"></i> Auto-Generated Services (Stitching)</h6>
                                            <div class="table-responsive border rounded">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Service Name</th>
                                                            <th>Applies To</th>
                                                            <th class="text-end">Qty</th>
                                                            <th>UOM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="services-stitching">
                                                        <!-- JS will populate this -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-12 text-end mt-4">
                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="showStep('#stage-cutting')"><i class="ri ri-arrow-left-line me-1"></i> Prev</button>
                                            <button type="button" class="btn btn-primary btn-next" onclick="showStep('#stage-finishing')">Next: Finishing <i class="ri ri-arrow-right-line ms-1"></i></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Stage 3: Finishing Form --}}
                                <div id="stage-finishing" class="content d-none">
                                    <div class="row g-3">
                                        <div class="col-12 mb-2"><h6 class="fw-bold text-secondary">Stage 3 Details: Finishing</h6></div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" class="form-control" value="1000">
                                                <label>Planned Qty</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" value="PCS">
                                                <label>UOM</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Start Date">
                                                <label>Start Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="End Date">
                                                <label>End Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control date-picker" placeholder="Due Date">
                                                <label>Due Date</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select select2">
                                                     <option value="Kalavasal">Kalavasal</option>
                                                </select>
                                                <label>Scheduled To</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating form-floating-outline">
                                                 <select class="form-select select2">
                                                     <option value="Int Unit">Internal Unit</option>
                                                 </select>
                                                 <label>Service Provider</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold text-secondary mb-3"><i class="ri ri-list-settings-line me-1"></i> Auto-Generated Services (Finishing)</h6>
                                            <div class="table-responsive border rounded">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Service Name</th>
                                                            <th>Applies To</th>
                                                            <th class="text-end">Qty</th>
                                                            <th>UOM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="services-finishing">
                                                        <!-- JS will populate this -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-12 text-end mt-4">
                                            <button type="button" class="btn btn-outline-secondary me-2" onclick="showStep('#stage-stitching')"><i class="ri ri-arrow-left-line me-1"></i> Prev</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- MODULE 3: FINISHED GOODS RECEIPT --}}
                <div class="card border-0 shadow-sm erp-header-card mt-4">
                    <div class="card-header border-bottom py-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold d-flex align-items-center">
                                <i class="ri ri-inbox-archive-line me-2 text-primary"></i> Module 3: Finished Goods Receipt
                            </h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="prn_no" value="PRN-2026-001" readonly>
                                    <label>PRN#</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker" id="received_on">
                                    <label>Received On</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2">
                                         <option value="Nachias fashion private limited">Nachias fashion private limited</option>
                                    </select>
                                    <label>Plant</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2">
                                         <option value="Finished Goods Store">Finished Goods Store</option>
                                    </select>
                                    <label>Store</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="doc_no">
                                    <label>Doc#</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2">
                                         <option value="WOO">WOO</option>
                                    </select>
                                    <label>Doc. Type</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="consignee">
                                    <label>Consignee</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="receipt_reference">
                                    <label>Reference</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2">
                                         <option value="Nagendran">Nagendran</option>
                                    </select>
                                    <label>Staff</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2">
                                         <option value="JW001">JW001</option>
                                    </select>
                                    <label>Resource</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="receipt_remarks">
                                    <label>Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Action Buttons --}}
                 <div class="mt-4">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 me-2">
                            <i class="ri ri-save-line me-1"></i> Submit
                        </button>
                        <a href="{{ url('productions') }}" class="btn btn-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.date-picker').flatpickr({ dateFormat: 'd-m-Y' });
        $('.select2').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({ placeholder: "Select", allowClear: true, width: '100%' });
            }
        });
        $('.step-trigger').click(function() {
            var target = $(this).closest('.step').data('target');
            showStep(target);
        });

        // Auto-calculation logic for Services
        const serviceDefinitions = {
            cutting: [
                { name: 'Layering & Cutting', appliesTo: 'ALL' }
            ],
            stitching: [
                { name: 'CLR-ATT (Collar Attach)', appliesTo: 'ALL' },
                { name: 'CLR-LBL-ATT (Collar Label Attach)', appliesTo: 'ALL' },
                { name: 'TOWER', appliesTo: 'ALL' },
                { name: 'SLV-TOP', appliesTo: 'ALL' },
                { name: 'SLV-ATT', appliesTo: 'ALL' },
                { name: 'DEAL-SLV-ATT', appliesTo: 'ALL' },
                { name: 'BF-ATT', appliesTo: 'ALL' },
                { name: 'SID-ATT-FS', appliesTo: 'FS' },
                { name: 'SID-ATT-HS', appliesTo: 'HS' },
                { name: 'CUF-ATT', appliesTo: 'CUFF' },
                { name: 'CUF-TOP', appliesTo: 'FS' },
                { name: 'CUF-TURN', appliesTo: 'FS' },
                { name: 'CUF-TWR-MRK', appliesTo: 'FS' }
            ],
            finishing: [
                { name: 'BUT - OPR', appliesTo: 'ALL' },
                { name: 'BUT', appliesTo: 'ALL' },
                { name: 'KAJA-OPR', appliesTo: 'ALL' },
                { name: 'KAJA', appliesTo: 'ALL' },
                { name: 'TWR-TRI-CHK', appliesTo: 'ALL' },
                { name: 'ASS-TRI-CHK', appliesTo: 'ALL' },
                { name: 'CUF-IRON', appliesTo: 'FS' },
                { name: 'Ironing & Packing', appliesTo: 'ALL' }
            ]
        };

        function calculateServices() {
            const fsQty = parseInt($('#planned_qty_fs').val()) || 0;
            const hsQty = parseInt($('#planned_qty_hs').val()) || 0;
            const totalQty = fsQty + hsQty;
            
            $('#planned_qty').val(totalQty);

            // Populate each stage
            Object.keys(serviceDefinitions).forEach(stage => {
                const tbody = $(`#services-${stage}`);
                tbody.empty();

                serviceDefinitions[stage].forEach(service => {
                    let calculatedQty = 0;
                    let displayAppliesTo = service.appliesTo;

                    switch(service.appliesTo) {
                        case 'ALL': calculatedQty = totalQty; break;
                        case 'FS': calculatedQty = fsQty; break;
                        case 'HS': calculatedQty = hsQty; break;
                        case 'CUFF': 
                            calculatedQty = fsQty; 
                            displayAppliesTo = 'FS (1 Set)';
                            break;
                    }

                    tbody.append(`
                        <tr>
                            <td>${service.name}</td>
                            <td><span class="badge bg-label-info">${displayAppliesTo}</span></td>
                            <td class="text-end fw-bold">${calculatedQty}</td>
                            <td>PCS</td>
                        </tr>
                    `);
                });
            });
        }

        // Listen for quantity changes
        $('#planned_qty_fs, #planned_qty_hs').on('input', calculateServices);

        // Initial calculation
        calculateServices();
    });

    function showStep(targetId) {
        $('.bs-stepper-content .content').addClass('d-none').removeClass('active');
        $(targetId).removeClass('d-none').addClass('active');
        $('.bs-stepper-header .step').removeClass('active');
        $('.bs-stepper-header .step[data-target="' + targetId + '"]').addClass('active');
    }
</script>

<style>
    /* Stepper CSS */
    .bs-stepper-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .bs-stepper-header .line {
        flex: 1;
        height: 2px;
        background-color: #e9ecef;
        margin: 0 1rem;
    }
    .step-trigger {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
    }
    .bs-stepper-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }
    .bs-stepper-label {
        text-align: left;
    }
    .bs-stepper-title {
        display: block;
        font-weight: bold;
        color: #566a7f;
    }
    .bs-stepper-subtitle {
        display: block;
        font-size: 0.75rem;
        color: #a1acb8;
    }
    
    /* Active State */
    .step.active .bs-stepper-title { color: #696cff; }
    .step.active .bs-stepper-circle { box-shadow: 0 0 0 4px rgba(105, 108, 255, 0.15); }
</style>
@endsection
