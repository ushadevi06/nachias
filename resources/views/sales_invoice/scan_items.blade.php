@extends('layouts.common')
@section('title', 'Scan Items - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-3">
                <h4>Scan Items for Delivery Order</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('sales_invoices/view/' . $invoice->id) }}" class="btn btn-secondary text-white">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Invoice
                    </a>
                    @if($invoice->delivery_status !== 'Dispatched' && strtolower($invoice->einvoice_status) !== 'cancelled')
                        <button class="btn btn-success" id="complete_dispatch_btn" disabled>
                            <i class="ri ri-check-double-line me-1"></i> Complete Dispatch
                        </button>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <h5 class="mb-0">Invoice: <strong>{{ $invoice->inv_no }}</strong></h5>
                        <h5 class="mb-0">Customer: <strong>{{ $invoice->customer->name ?? '-' }}</strong></h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Required</h6>
                                <h3 id="stat_required">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card border-success">
                                <h6 class="text-success">Scanned</h6>
                                <h3 id="stat_scanned" class="text-success">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Remaining</h6>
                                <h3 id="stat_remaining">0</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Completed Items</h6>
                                <h3 id="stat_completed">0</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Scan Progress</span>
                            <span id="progress_text" class="fw-bold text-success">0%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="progress_bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <hr>

                        <div class="col-md-12 text-center">
                            <label class="form-label fw-bold">Scan Barcode</label>
                            @if($invoice->delivery_status === 'Dispatched')
                                <div class="alert alert-success">This invoice has already been dispatched. No further scanning is allowed.</div>
                            @elseif(strtolower($invoice->einvoice_status) === 'cancelled')
                                <div class="alert alert-danger"><i class="ri-close-circle-line me-1"></i> <strong>E-Invoice Cancelled:</strong> This invoice has been cancelled. No further scanning is allowed.</div>
                            @else
                                <div class="input-group mb-2 mx-auto" style="max-width: 500px;">
                                    <input type="text" id="barcode_input" class="form-control border-primary" placeholder="Scan item barcode here..." autofocus autocomplete="off">
                                    <button class="btn btn-outline-primary" type="button" id="btn_camera_scan">
                                        <i class="ri-camera-line me-1"></i> Camera
                                    </button>
                                </div>
                                <div id="reader" class="rounded overflow-hidden mb-3 mx-auto" style="display: none; width: 100%; max-width: 500px; border: 1px solid #00bcd4;"></div>
                            @endif
                            
                            <div id="scan_alert" class="alert alert-danger mt-3 mx-auto" style="display: none; max-width: 500px;">
                                <span id="scan_msg"></span>
                            </div>
                        </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white fw-bold">
                    Items Table
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="items_table">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th>SKU / Barcode</th>
                                    <th>Req. Qty</th>
                                    <th>Scanned</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totReq = 0;
                                    $totScan = 0;
                                @endphp
                                @foreach($invoice->items as $item)
                                    @php
                                        $brandName = '';
                                        $styleName = '';
                                        $sleeve = '';
                                        $resolved = false;
                                        
                                        $seItem = $item->stockEntryItem;
                                        $soItem = null;
                                        if ($invoice->so_id) {
                                            $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)->where('sku', $item->sku)->first();
                                            if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
                                                $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                                            }
                                        }

                                        if ($seItem) {
                                            if ($seItem->brand) {
                                                $brandName = $seItem->brand->brand_name;
                                            }
                                            if ($seItem->style) {
                                                $styleName = $seItem->style->style_name;
                                            }
                                            
                                            if (empty($brandName) && !empty($seItem->finished_item_code)) {
                                                $seParts = explode('-', $seItem->finished_item_code);
                                                if (count($seParts) > 0) {
                                                    $dbBrand = \App\Models\Brand::where('code', trim($seParts[0]))->first();
                                                    if ($dbBrand) {
                                                        $brandName = $dbBrand->brand_name;
                                                    }
                                                }
                                            }
                                            
                                            if (!empty($brandName) || !empty($styleName)) {
                                                $resolved = true;
                                            }
                                            if (empty($sleeve) && !empty($seItem->sleeve_type)) {
                                                $sleeve = $seItem->sleeve_type;
                                            }
                                        }

                                        $codeToParse = '';
                                        if ($soItem) {
                                            $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
                                            if (empty($codeToParse) || $codeToParse === '-') {
                                                $codeToParse = $soItem->finished_item_code;
                                            }
                                        }
                                        if (empty($codeToParse) || $codeToParse === '-') {
                                            if ($seItem) {
                                                $codeToParse = $seItem->finished_item_code;
                                            } elseif ($item->item) {
                                                $codeToParse = $item->item->code;
                                            }
                                        }
                                        if (empty($codeToParse) || $codeToParse === '-') {
                                            $codeToParse = $item->sku ?? '';
                                        }
                                        
                                        if (!empty($item->sleeve_type)) {
                                            $sleeveValUpper = strtoupper(trim($item->sleeve_type));
                                            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                $sleeve = 'F/S';
                                            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                $sleeve = 'H/S';
                                            } else {
                                                $sleeve = $item->sleeve_type;
                                            }
                                        } elseif (!empty($sleeve)) {
                                            $sleeveValUpper = strtoupper(trim($sleeve));
                                            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                $sleeve = 'F/S';
                                            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                $sleeve = 'H/S';
                                            }
                                        }

                                        if ((empty($brandName) || empty($styleName)) && !empty($codeToParse) && $codeToParse !== '-') {
                                            $parts = explode('-', $codeToParse);
                                            
                                            if (count($parts) >= 2) {
                                                $brandCode = trim($parts[0]);
                                                $styleCode = trim($parts[1]);
                                                
                                                $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                                                $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                                
                                                if ($dbBrand && empty($brandName)) {
                                                    $brandName = $dbBrand->brand_name;
                                                }
                                                if ($dbStyle && empty($styleName)) {
                                                    $styleName = $dbStyle->style_name;
                                                }
                                                if (!empty($brandName) || !empty($styleName)) {
                                                    $resolved = true;
                                                }
                                                
                                                if (empty($sleeve) && isset($parts[2])) {
                                                    $sleeveVal = trim($parts[2]);
                                                    $sleeveValUpper = strtoupper($sleeveVal);
                                                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                        $sleeve = 'F/S';
                                                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                        $sleeve = 'H/S';
                                                    } else {
                                                        $sleeve = $sleeveVal;
                                                    }
                                                }
                                            }
                                            
                                            if ((empty($brandName) || empty($styleName)) && count($parts) >= 1) {
                                                $mainCode = trim($parts[0]);
                                                $dbBrand = null;
                                                $brandCode = '';
                                                $allBrands = \App\Models\Brand::all();
                                                foreach ($allBrands as $brand) {
                                                    $bCode = trim($brand->code);
                                                    if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                                                        $dbBrand = $brand;
                                                        $brandCode = $bCode;
                                                        break;
                                                    }
                                                }
                                                
                                                if ($dbBrand) {
                                                    if (empty($brandName)) {
                                                        $brandName = $dbBrand->brand_name;
                                                    }
                                                    $styleCode = substr($mainCode, strlen($brandCode));
                                                    $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                                    if ($dbStyle && empty($styleName)) {
                                                        $styleName = $dbStyle->style_name;
                                                    }
                                                    $resolved = true;
                                                    
                                                    if (empty($sleeve) && isset($parts[1])) {
                                                        $sleeveVal = trim($parts[1]);
                                                        $sleeveValUpper = strtoupper($sleeveVal);
                                                        if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                            $sleeve = 'F/S';
                                                        } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                            $sleeve = 'H/S';
                                                        } else {
                                                            $sleeve = $sleeveVal;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if ($resolved) {
                                            $itemName = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
                                        } else {
                                            $itemName = $codeToParse;
                                        }
                                        $req = $item->quantity;
                                        $scan = $item->scanned_qty ?? 0;
                                        $rem = $req - $scan;
                                        $totReq += $req;
                                        $totScan += $scan;
                                        
                                        $statusBadge = '<span class="badge bg-secondary">Pending</span>';
                                        
                                        if ($scan >= $req) {
                                            $statusBadge = '<span class="badge bg-success">Completed</span>';
                                        } elseif ($scan > 0) {
                                            $statusBadge = '<span class="badge bg-warning text-dark">In Progress</span>';
                                        }
                                        
                                        $sizeStr = $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-');
                                        $colorStr = $item->api_color ?: ($item->color ? $item->color->color_name : '-');
                                    @endphp
                                    <tr class="item-row" data-item-id="{{ $item->id }}" data-sku="{{ $item->sku }}" data-req="{{ $req }}" data-scan="{{ $scan }}" data-item-name="{{ $itemName }}" data-size="{{ $sizeStr }}" data-color="{{ $colorStr }}">
                                        <td>
                                            {{ $itemName ?: ($item->art_no ?: 'Unknown Item') }}
                                            <br>
                                            <small class="text-muted">
                                                Size: {{ $sizeStr }} | 
                                                Color: {{ $colorStr }}
                                            </small>
                                        </td>
                                        <td>{{ $item->sku ?? '-' }}</td>
                                        <td>{{ number_format($req, 0) }}</td>
                                        <td class="scan-val fw-bold">{{ number_format($scan, 0) }}</td>
                                        <td class="rem-val">{{ number_format($rem, 0) }}</td>
                                        <td class="status-cell">{!! $statusBadge !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<style>
    .stat-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        background: #f8f9fa;
    }
    .stat-card h6 {
        margin-bottom: 5px;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .stat-card h3 {
        margin: 0;
        font-weight: bold;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        function updateStats() {
            let tReq = 0;
            let tScan = 0;
            let completedItems = 0;
            
            $('.item-row').each(function() {
                let r = parseInt($(this).attr('data-req')) || 0;
                let s = parseInt($(this).attr('data-scan')) || 0;
                tReq += r;
                tScan += s;
                if (s >= r && r > 0) {
                    completedItems++;
                }
            });
            
            let tRem = tReq - tScan;
            
            $('#stat_required').text(tReq);
            $('#stat_scanned').text(tScan);
            $('#stat_remaining').text(tRem);
            $('#stat_completed').text(completedItems);
            
            let pct = tReq > 0 ? Math.round((tScan / tReq) * 100) : 0;
            $('#progress_bar').css('width', pct + '%').attr('aria-valuenow', pct);
            $('#progress_text').text(pct + '%');
            
            if (tScan >= tReq && tReq > 0) {
                $('#complete_dispatch_btn').prop('disabled', false);
            } else {
                $('#complete_dispatch_btn').prop('disabled', true);
            }
        }

        updateStats();
        
        $('#barcode_input').on('keypress', function(e) {
            if(e.which == 13) {
                let rawSku = $(this).val().trim();
                if(rawSku === '') return;
                
                let sku = rawSku.split('|')[0].trim();
                
                $(this).val('');
                $('#scan_alert').hide();
                
                let row = $('#items_table tbody tr[data-sku="'+sku+'"]');
                if(row.length > 0) {
                    let req = parseInt(row.attr('data-req'));
                    let scan = parseInt(row.attr('data-scan'));
                    let iname = row.attr('data-item-name');
                    let isize = row.attr('data-size');
                    let icolor = row.attr('data-color');
                    
                    if(scan < req) {
                        scan++;
                        row.attr('data-scan', scan);
                        row.find('.scan-val').text(scan);
                        row.find('.rem-val').text(req - scan);
                        
                        if(scan >= req) {
                            row.find('.status-cell').html('<span class="badge bg-success">Completed</span>');
                        } else {
                            row.find('.status-cell').html('<span class="badge bg-warning text-dark">In Progress</span>');
                        }
                        
                        updateStats();
                        
                        let shortName = iname.split(' ')[0] + ' ' + (iname.split(' ')[1] || '');
                        $('#scan_alert').removeClass('alert-danger').addClass('alert-success').html('<i class="ri-check-line me-1"></i> <strong>Success:</strong> Scanned ' + shortName + ' (Size ' + isize + ')').show();
                        
                        setTimeout(function() {
                            $('#scan_alert').fadeOut('fast', function() {
                                $(this).removeClass('alert-success');
                            });
                        }, 2500);
                        
                        playBeep(800, 0.2);
                        
                        saveProgress();
                    } else {
                        $('#scan_alert').removeClass('alert-success').addClass('alert-danger').html('<strong>Error:</strong> Required quantity already scanned for ' + sku).show();
                        playBeep(150, 0.3, 'sawtooth');
                    }
                } else {
                    $('#scan_alert').removeClass('alert-success').addClass('alert-danger').html('<strong>Error:</strong> Barcode not found in this Sales Invoice').show();
                    playBeep(150, 0.3, 'sawtooth');
                }
            }
        });
        
        function saveProgress() {
            let items = [];
            $('.item-row').each(function() {
                items.push({
                    id: $(this).attr('data-item-id'),
                    scanned_qty: $(this).attr('data-scan')
                });
            });
            
            $.ajax({
                url: "{{ route('sales_invoices.save_scan_progress', $invoice->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    items: items
                },
                success: function(response) {
                    console.log('Progress auto-saved.');
                },
                error: function(err) {
                    console.error('Failed to auto-save progress.');
                }
            });
        }
        
        $('#complete_dispatch_btn').click(function() {
            Swal.fire({
                title: 'Complete Dispatch?',
                text: 'Are you sure you want to complete this dispatch? No further scanning will be allowed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Complete Dispatch'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('sales_invoices.complete_dispatch', $invoice->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire(
                                'Dispatched!',
                                'Dispatch completed successfully.',
                                'success'
                            ).then(() => {
                                window.location.href = "{{ url('sales_invoices/view/' . $invoice->id) }}";
                            });
                        },
                        error: function(err) {
                            Swal.fire(
                                'Error!',
                                'Error completing dispatch. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        function playBeep(freq, duration, type = 'sine') {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = type;
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
                gain.gain.linearRampToValueAtTime(0, ctx.currentTime + duration);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + duration);
            } catch(err) {}
        }
        
        // Keep focus, but ignore clicks on camera scanner elements
        $(document).on('click', function(e) {
            if(!$(e.target).closest('input, button, a, .scan-history-list, #reader').length) {
                if(!isCameraOpen) {
                    $('#barcode_input').focus();
                }
            }
        });

        // Camera scanning logic
        let html5QrCode = null;
        let isCameraOpen = false;

        $('#btn_camera_scan').click(function() {
            if (isCameraOpen && html5QrCode) {
                // Stop scanning
                html5QrCode.stop().then((ignore) => {
                    $('#reader').hide();
                    isCameraOpen = false;
                    $(this).html('<i class="ri-camera-line me-1"></i> Camera');
                    $('#barcode_input').focus();
                }).catch((err) => {
                    console.error("Failed to stop camera:", err);
                });
            } else {
                // Start scanning
                $('#reader').show();
                html5QrCode = new Html5Qrcode("reader");
                
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 100 },
                        aspectRatio: 1.0
                    },
                    (decodedText, decodedResult) => {
                        console.log(`Scan result: ${decodedText}`);
                        $('#barcode_input').val(decodedText);
                        
                        html5QrCode.stop().then((ignore) => {
                            $('#reader').hide();
                            isCameraOpen = false;
                            $('#btn_camera_scan').html('<i class="ri-camera-line me-1"></i> Camera');
                            
                            var e = $.Event("keypress");
                            e.which = 13;
                            $('#barcode_input').trigger(e);
                        }).catch((err) => {
                            console.error("Failed to stop camera after scan:", err);
                        });
                    },
                    (errorMessage) => {
                    }
                ).then(() => {
                    isCameraOpen = true;
                    $(this).html('<i class="ri-close-line me-1"></i> Close Camera');
                }).catch((err) => {
                    console.error("Error starting camera", err);
                    $('#scan_alert').removeClass('alert-success').addClass('alert-danger').show();
                    $('#scan_msg').html('<strong>Error:</strong> Could not start camera. Please ensure camera permissions are granted.');
                    $('#reader').hide();
                });
            }
        });
    });
</script>
@endsection
