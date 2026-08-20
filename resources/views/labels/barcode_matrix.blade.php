@extends('layouts.common')
@section('title', 'Barcode Matrix - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-white"><i class="ri ri-barcode-box-line me-2"></i> Barcode Print Matrix</h4>
                    <a href="{{ route('job_card_entries.view-item', $jobCard->id) }}" class="btn btn-sm btn-light">
                        <i class="ri ri-arrow-left-line me-1"></i> Back to Job Card
                    </a>
                </div>
                <div class="card-body p-4 bg-light-subtle">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border border-primary shadow-sm h-100">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px;">Job Card No</small>
                                <span class="fw-bold text-primary fs-5">{{ $jobCard->job_card_no }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border border-primary shadow-sm h-100">
                                <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 10px;">Brand</small>
                                <span class="fw-bold text-primary fs-5">{{ $jobCard->brand->brand_name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach($fabrics as $fabric)
            @php 
                $issueItem = $fabric['issueItem'];
                $records = $fabric['records'];
            @endphp
            <div class="card border-0 shadow-sm overflow-hidden mb-4 {{ $fabric['is_current'] ? 'border-start border-4 border-success' : '' }}" id="fabric-{{ $issueItem->id }}">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 text-dark">
                                <i class="ri ri-scissors-cut-line me-2 text-primary"></i>
                                {{ $issueItem->rawMaterial->name ?? 'Fabric/Accessory' }}
                                @if($fabric['is_current'])
                                    <span class="badge bg-success ms-2" style="font-size: 10px;">SELECTED</span>
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            {{-- <div class="btn-group me-2">
                                <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri ri-printer-line me-1"></i> Bulk Print
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('job_card_entries.barcode_preview', $issueItem->id) }}?bulk_print=1&format=tag" target="_blank">
                                            <i class="ri ri-price-tag-3-line me-2"></i> Price Tag
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('job_card_entries.barcode_preview', $issueItem->id) }}?bulk_print=1&format=sticker" target="_blank">
                                            <i class="ri ri-sticky-note-line me-2"></i> Price Sticker
                                        </a>
                                    </li>
                                </ul>
                            </div> --}}
                            <span class="badge bg-label-info px-3 py-2 me-2">ART: {{ $issueItem->fabricDetail->art_no ?? $issueItem->rawMaterial->code ?? '-' }}</span>
                            {{-- <span class="badge bg-label-secondary px-3 py-2">COLOR: {{ $issueItem->stockEntryItem->color->color_name ?? ($issueItem->stockEntryItem->grnEntryItem->color->color_name ?? '-') }}</span> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 25%;">Size</th>
                                    <th style="width: 25%;">Sleeve Type</th>
                                    <th class="text-center" style="width: 25%;">Recorded Qty</th>
                                    <th class="text-end pe-4" style="width: 25%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                @if($record['size'] != 'Bulk')
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-label-primary px-3 py-2" style="font-size: 14px;">SIZE: {{ $record['size'] }}</span>
                                    </td>
                                    <td>
                                        @if($record['sleeve'] == 'F/S')
                                            <span class="badge bg-label-success px-3 py-2"><i class="ri ri-shirt-line me-1"></i> FULL SLEEVE</span>
                                        @elseif($record['sleeve'] == 'H/S')
                                            <span class="badge bg-label-warning px-3 py-2"><i class="ri ri-t-shirt-2-line me-1"></i> HALF SLEEVE</span>
                                        @else
                                            <span class="badge bg-label-secondary px-3 py-2">COMMON</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ number_format($record['qty'], 0) }}</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="{{ route('job_card_entries.barcode_preview', $issueItem->id) }}?size={{ urlencode($record['size']) }}&sleeve={{ urlencode($record['sleeve']) }}&format=tag" 
                                               target="_blank" 
                                               class="btn btn-primary btn-sm">
                                                <i class="ri ri-price-tag-3-line me-1"></i> Tag
                                            </a>
                                            <a href="{{ route('job_card_entries.barcode_preview', $issueItem->id) }}?size={{ urlencode($record['size']) }}&sleeve={{ urlencode($record['sleeve']) }}&format=sticker" 
                                               target="_blank" 
                                               class="btn btn-info btn-sm">
                                                <i class="ri ri-sticky-note-line me-1"></i> Sticker
                                            </a>
                                            <a href="{{ route('job_card_entries.print_label_square', $issueItem->id) }}?size={{ urlencode($record['size']) }}&sleeve={{ urlencode($record['sleeve']) }}" 
                                               target="_blank" 
                                               class="btn btn-warning btn-sm">
                                                <i class="ri ri-layout-grid-line me-1"></i> 45x45 STICKER
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
            
            <div class="alert alert-info border-info border-dashed">
                <div class="d-flex align-items-center">
                    <i class="ri ri-information-line me-2 fs-4"></i>
                    <div>
                        <strong>Tip:</strong> Each section above represents a different fabric issued to this job card. You can print labels for all of them from this single page.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .print-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .bg-light-subtle {
        background-color: #f8f9fa !important;
    }
    .bg-label-primary {
        background-color: #e7e7ff !important;
        color: #696cff !important;
    }
    .bg-label-success {
        background-color: #e8fadf !important;
        color: #71dd37 !important;
    }
    .bg-label-warning {
        background-color: #fff2e2 !important;
        color: #ffab00 !important;
    }
    .bg-label-info {
        background-color: #d7f5fc !important;
        color: #03c3ec !important;
    }
    .bg-label-secondary {
        background-color: #ebeef1 !important;
        color: #8592a3 !important;
    }
    .sortable-ghost { opacity: 0.1; background-color: #696cff !important; border-radius: 4px; }
    .field-item { 
        cursor: move; 
        transition: all 0.2s ease; 
        border: 1px solid #e0e0e0; 
        background: #fff;
        padding: 8px 12px;
        margin-bottom: 4px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        font-size: 11px;
        text-transform: uppercase;
        color: #333;
    }
    .field-item { 
        cursor: move; 
        transition: all 0.2s ease; 
        border: 1px solid #e0e0e0; 
        background: #fdfdfd;
        padding: 10px 15px;
        margin-bottom: 5px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        font-family: 'Courier New', Courier, monospace;
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase;
        color: #444;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .field-item:hover { border-color: #696cff; background-color: #f8f9ff; }
    .field-item i { color: #696cff; margin-right: 15px; font-size: 16px; }
    .field-label { width: 120px; color: #888; font-size: 11px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentFabric = document.querySelector('.border-success');
    if (currentFabric) {
        currentFabric.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

@endsection
