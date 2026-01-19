@extends('layouts.common')

@section('content')
<div class="container-xxl section-padding">
    
    {{-- 🚀 TOP BAR: TASK HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm erp-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-1 text-primary">TASK-2025-001 (Cutting)</h3>
                            <p class="text-muted mb-0">
                                <span class="badge bg-label-secondary px-2 py-1 me-2">Task Execution</span>
                                <i class="ri ri-arrow-right-s-line"></i>
                                <span class="ms-2">Job Card: <span class="fw-bold text-dark">JC20250924-001-K</span></span>
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-loader-4-line me-1"></i> In Progress
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 KPI TILES ROW --}}
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Issued</small>
                            <h4 class="fw-bold mb-0 text-primary">1,200</h4>
                        </div>
                        <div class="avatar bg-label-primary p-2 rounded-3">
                            <i class="ri ri-upload-2-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Received</small>
                            <h4 class="fw-bold mb-0 text-success">800</h4>
                        </div>
                        <div class="avatar bg-label-success p-2 rounded-3">
                            <i class="ri ri-download-2-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Adjustments</small>
                            <h4 class="fw-bold mb-0 text-danger">-50</h4>
                        </div>
                        <div class="avatar bg-label-danger p-2 rounded-3">
                            <i class="ri ri-scales-3-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 📋 LEFT SIDEBAR: TABS --}}
        <div class="col-lg-3 mb-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Transaction Type</h6>
                    </div>
                    <div class="card-body px-0 py-2">
                        <div class="nav flex-column nav-pills custom-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start py-3 px-4 d-flex align-items-center" id="tab-issue" data-bs-toggle="pill" data-bs-target="#content-issue" type="button" role="tab">
                                <i class="ri ri-upload-2-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Issue</span>
                                    <small class="text-muted font-small">Issue materials/tasks</small>
                                </div>
                            </button>
                            <button class="nav-link text-start py-3 px-4 d-flex align-items-center" id="tab-receive" data-bs-toggle="pill" data-bs-target="#content-receive" type="button" role="tab">
                                <i class="ri ri-download-2-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Receive</span>
                                    <small class="text-muted font-small">Receive completed work</small>
                                </div>
                            </button>
                            <button class="nav-link text-start py-3 px-4 d-flex align-items-center" id="tab-adjustment" data-bs-toggle="pill" data-bs-target="#content-adjustment" type="button" role="tab">
                                <i class="ri ri-equalizer-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Adjustment</span>
                                    <small class="text-muted font-small">Corrections & Wastage</small>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📝 MAIN CONTENT: TAB PANES --}}
        <div class="col-lg-9">
            <div class="tab-content sdk-tab-content p-0" id="v-pills-tabContent">
                
                {{-- 🟦 TAB 1: TASK ISSUE --}}
                <div class="tab-pane fade show active" id="content-issue" role="tabpanel">
                    <form action="" class="common-form">
                        <div class="card border-0 shadow-sm section-card">
                            <div class="card-header border-bottom py-3 bg-label-primary bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon bg-primary text-white me-3"><i class="ri ri-upload-2-line"></i></div>
                                    <h5 class="mb-0 fw-bold text-primary">New Task Issue</h5>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="ISS-2026-884" readonly>
                                            <label>Task Issue ID (Auto)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="JC20250924-001-K" readonly>
                                            <label>Job Card No</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="Cutting (Stage 1)" readonly>
                                            <label>Stage Code</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control date-picker" placeholder="DD-MM-YYYY">
                                            <label>Issue Date *</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12"><hr class="my-2"></div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" id="issue_type">
                                                <option value="In-house">In-house</option>
                                                <option value="Outside">Outside (Vendor)</option>
                                            </select>
                                            <label>Issue Type *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="">Select Employee/Vendor</option>
                                                <option value="Emp-101">John Doe (Employee)</option>
                                                <option value="Ven-505">Fast Prints (Vendor)</option>
                                            </select>
                                            <label>Issued To *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" placeholder="0">
                                            <label>Issue Quantity *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Store A">Main Store</option>
                                                <option value="Store B">Fabric Store</option>
                                            </select>
                                            <label>Issue Store *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" style="height: 80px;"></textarea>
                                            <label>Remarks</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary fw-bold px-4">
                                            <i class="ri ri-save-line me-1"></i> Save Issue
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 🟩 TAB 2: TASK RECEIVE --}}
                <div class="tab-pane fade" id="content-receive" role="tabpanel">
                    <form action="" class="common-form">
                        <div class="card border-0 shadow-sm section-card">
                            <div class="card-header border-bottom py-3 bg-label-success bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon bg-success text-white me-3"><i class="ri ri-download-2-line"></i></div>
                                    <h5 class="mb-0 fw-bold text-success">New Task Receipt</h5>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="REC-2026-102" readonly>
                                            <label>Task Receive ID (Auto)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="">Select Issue ID</option>
                                                <option value="ISS-2026-884">ISS-2026-884 (Cutting - John)</option>
                                            </select>
                                            <label>Link Task Issue ID *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control date-picker" value="{{ date('d-m-Y') }}">
                                            <label>Receive Date *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Store A">Finished Goods Store</option>
                                                <option value="Store B">WIP Store</option>
                                            </select>
                                            <label>Receipt Store *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12"><hr class="my-2"></div>

                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control border-success" placeholder="0">
                                            <label class="text-success fw-bold">Completed Qty (Good)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control border-warning" placeholder="0">
                                            <label class="text-warning fw-bold">Rework Qty</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control border-danger" placeholder="0">
                                            <label class="text-danger fw-bold">Wastage Qty</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Pending">Pending QC</option>
                                                <option value="Pass">QC Pass</option>
                                                <option value="Fail">QC Fail</option>
                                            </select>
                                            <label>QC Status</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" style="height: 80px;"></textarea>
                                            <label>Remarks</label>
                                        </div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success fw-bold px-4">
                                            <i class="ri ri-check-double-line me-1"></i> Save Receipt
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- 🟧 TAB 3: ADJUSTMENT --}}
                <div class="tab-pane fade" id="content-adjustment" role="tabpanel">
                    <form action="" class="common-form">
                        <div class="card border-0 shadow-sm section-card">
                            <div class="card-header border-bottom py-3 bg-label-warning bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon bg-warning text-white me-3"><i class="ri ri-equalizer-line"></i></div>
                                    <h5 class="mb-0 fw-bold text-warning">Inventory Adjustment</h5>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="ADJ-2026-005" readonly>
                                            <label>Adjustment ID (Auto)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" value="JC20250924-001-K" readonly>
                                            <label>Job Card Reference</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Cutting">Cutting</option>
                                                <option value="Sewing">Sewing</option>
                                            </select>
                                            <label>Affected Stage *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Rework">Rework (Material Return)</option>
                                                <option value="Loss">Loss / Damage (Reduce Stock)</option>
                                                <option value="Excess">Excess Found (Add Stock)</option>
                                            </select>
                                            <label>Adjustment Type *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" placeholder="0">
                                            <label>Adjustment Quantity *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="Supervisor Name">
                                            <label>Approved By</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" style="height: 100px;" placeholder="Mandatory reason..."></textarea>
                                            <label>Reason for Adjustment *</label>
                                        </div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-warning fw-bold px-4 text-white">
                                            <i class="ri ri-alert-line me-1"></i> Post Adjustment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Init Plugins
        $('.date-picker').flatpickr({ dateFormat: 'd-m-Y' });
        // Only init select2 if it hasn't been initialized to prevent errors
        $('.select2').each(function() {
             if (!$(this).data('select2')) {
                $(this).select2({ placeholder: "Select", allowClear: true, width: '100%' });
             }
        });
    });
</script>

<style>
    :root {
        --erp-primary: #696cff;
        --erp-bg: #f5f5f9;
        --erp-text: #435971;
        --erp-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    }
    .text-primary { color: var(--erp-primary) !important; }
    .bg-primary { background-color: var(--erp-primary) !important; }
    .btn-primary { background-color: var(--erp-primary); border-color: var(--erp-primary); }

    .erp-header-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .kpi-card { border-radius: 0.75rem; transition: 0.3s; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.18) !important; }

    /* Custom Pills Sidebar */
    .custom-pills .nav-link { 
        color: var(--erp-text); 
        border-radius: 0.5rem; 
        margin-bottom: 0.5rem; 
        transition: 0.2s; 
        border: 1px solid transparent;
    }
    .custom-pills .nav-link:hover { background-color: rgba(105, 108, 255, 0.05); color: var(--erp-primary); }
    .custom-pills .nav-link.active { 
        background-color: rgba(105, 108, 255, 0.1); 
        color: var(--erp-primary); 
        border-color: rgba(105, 108, 255, 0.2); 
    }
    
    .font-small { font-size: 0.8rem; }
    .section-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .section-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    .sticky-sidebar { position: sticky; top: 100px; }
    .avatar { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
</style>
@endsection
