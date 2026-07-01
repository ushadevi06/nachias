@extends('layouts.common')
@section('title', 'Barcode Label Designer')
@section('content')
<div class="container-xxl section-padding">

    <div class="designer-wrapper">
        <!-- Header -->
        <div class="designer-header">
            <div class="designer-title">
                <h2>Barcode Label Designer</h2>
                <div class="breadcrumb">Matrix / Designer</div>
            </div>
            <div class="action-btns">
                <button type="button" onclick="window.close()" class="btn btn-cancel">CANCEL</button>
                <button type="button" id="finalPrintBtn" class="btn btn-print">PRINT NOW</button>
            </div>
        </div>

        <!-- Page Setup -->
        <div class="setup-section mt-4">
            <div class="section-title">PAGE SETUP</div>
            <div class="setup-grid">
                <div class="form-group">
                    <label>Label Format</label>
                    <select id="labelFormat" class="form-select">
                        <option value="tag" {{ (request('format') ?? 'tag') === 'tag' ? 'selected' : '' }}>Price Tag (45 x 85 mm)</option>
                        <option value="sticker" {{ request('format') === 'sticker' ? 'selected' : '' }}>Price Sticker (50 x 70 mm)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Field Ordering -->
        <div class="ordering-container">
            <div class="section-title">FIELD ORDERING (DRAG TO REORDER)</div>
            <div id="sortableFields" class="sortable-list">
                <div class="sortable-item" data-id="product"><span class="field-key">PRODUCT:</span> <span class="field-val">{{ $labelData['product_name'] }}</span></div>
                <div class="sortable-item" data-id="brand"><span class="field-key">BRAND NAME:</span> <span class="field-val">{{ $labelData['brand_name'] }}</span></div>
                <div class="sortable-item" data-id="art"><span class="field-key">ART NO:</span> <span class="field-val">{{ $labelData['design'] }}</span></div>
                <div class="sortable-item" data-id="color"><span class="field-key">COLOUR:</span> <span class="field-val">{{ $labelData['color'] }}</span></div>
                <div class="sortable-item" data-id="fabric"><span class="field-key">FABRIC:</span> <span class="field-val">{{ $labelData['fabric'] }}</span></div>
                <div class="sortable-item" data-id="size"><span class="field-key">SIZE:</span> <span class="field-val">{{ $labelData['size'] }}</span></div>
                <div class="sortable-item" data-id="mrp"><span class="field-key">MRP:</span> <span class="field-val">₹ {{ $labelData['price'] }}</span></div>
                <div class="sortable-item" data-id="mfg"><span class="field-key">MFG/LOT:</span> <span class="field-val">{{ $labelData['mfg_date'] }} / {{ $labelData['lot_no'] }}</span></div>
            </div>
        </div>
    </div>
</div>
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Inter', sans-serif !important;
        }

        .designer-wrapper {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .designer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .designer-title h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            font-size: 24px;
        }

        .breadcrumb {
            font-size: 13px;
            color: #888;
        }

        .action-btns .btn {
            border-radius: 6px;
            font-weight: 700;
            padding: 10px 25px;
            font-size: 13px;
            text-transform: uppercase;
        }

        .btn-cancel {
            background: #fff;
            border: 1px solid #ddd;
            color: #666;
            margin-right: 10px;
        }

        .btn-print {
            background: #6f42c1;
            border: none;
            color: #fff;
        }

        .btn-print:hover {
            background: #5a32a3;
            color: #fff;
        }

        .section-title {
            font-weight: 800;
            font-size: 13px;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .setup-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .form-control,
        .form-select {
            background: #fdfdfd;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            color: #333;
        }

        .form-control:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.1);
        }

        .form-control-color {
            padding: 4px;
            height: 45px;
        }

        .switch-group {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        .form-check-input:checked {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .switch-label {
            font-weight: 800;
            color: #6f42c1;
            font-size: 12px;
            margin-left: 10px;
            text-transform: uppercase;
        }

        .ordering-container {
            margin-top: 40px;
        }

        .ordering-hint {
            font-size: 11px;
            color: #999;
            margin-bottom: 15px;
        }

        .sortable-list {
            background: transparent;
        }

        .sortable-item {
            padding: 12px 20px;
            font-weight: 600;
            font-size: 15px;
            color: #2d3748;
            cursor: move;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            text-transform: uppercase;
            border: 1px solid #edf2f7;
            background: #f8fafc;
            border-radius: 10px;
            margin-bottom: 10px;
            font-family: 'Inter', sans-serif;
        }

        .sortable-item:hover {
            color: #6f42c1;
            background: #fff;
            border-color: #6f42c1;
            transform: translateX(5px);
        }

        .sortable-ghost {
            opacity: 0.3;
            color: #6f42c1;
        }

        .field-key {
            color: #555;
            margin-right: 5px;
        }

        .field-val {
            color: #555;
        }

        .special-field {
            color: #6f42c1;
            font-style: italic;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortableContainer = document.getElementById('sortableFields');
        const finalPrintBtn = document.getElementById('finalPrintBtn');

        // Initialize Sortable
        new Sortable(sortableContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost'
        });

        // Final Print Logic
        finalPrintBtn.addEventListener('click', function() {
            const format = document.getElementById('labelFormat').value;
            let orientation = 'portrait';
            let w = 45;
            let h = 85;

            if (format === 'sticker') {
                orientation = 'landscape';
                w = 70; 
                h = 50; 
            }

            const margin = 0; 
            const bgColor = encodeURIComponent('#ffffff'); 
            const vAlign = 'top'; 
            const order = Array.from(sortableContainer.children).map(child => child.dataset.id).join(',');

            let baseUrl = format === 'tag' 
                ? `{{ route('job_card_entries.print_label_tag', $labelData['id']) }}` 
                : `{{ route('job_card_entries.print_label_sticker', $labelData['id']) }}`;

            const printUrl = baseUrl + `?` + 
                `size={{ urlencode($labelData['size']) }}&` +
                `sleeve={{ urlencode($labelData['sleeve']) }}&` +
                `bulk_print={{ request('bulk_print') ? 1 : 0 }}&` +
                `format=${format}&` +
                `orientation=${orientation}&` +
                `width=${w}&` +
                `height=${h}&` +
                `margin=${margin}&` +
                `bg_color=${bgColor}&` +
                `v_align=${vAlign}&` +
                `order=${order}`;

            window.open(printUrl, '_blank');
        });

        window.addEventListener('beforeunload', function() {
            if (window.opener && !window.opener.closed) {
                try {
                    window.opener.location.reload();
                } catch (e) {
                    console.log("Could not refresh opener");
                }
            }
        });
    });
    </script>
@endsection
