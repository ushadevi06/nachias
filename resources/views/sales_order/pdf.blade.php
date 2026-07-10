<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Order - {{ $salesOrder->so_no }}</title>
    <style>
    @font-face {
        font-family: "dejavusans-regular";
        src: url("{{ asset('assets/fonts/DejaVuSans.ttf') }}");
      }
      @font-face {
        font-family: "dejavusans-bold";
        src: url("{{ asset('assets/fonts/DejaVuSans-Bold.ttf') }}");
      }
        body {
            font-family:'dejavusans-regular', sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 16px;
            margin: 0;
            padding: 0;
        }
        strong{
             font-family:'dejavusans-bold', sans-serif;  
        }
        .container {
            padding: 0px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: middle;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .bold {
            font-weight: bold;
        }
        .no-border th, .no-border td {
            border: none;
        }
        /* Dark header style exactly matching screenshot */
        .dark-header th {
            background-color: #444444 !important;
            color: #ffffff !important;
            font-weight: bold;
            text-align: center;
        }
        .table-border {
            border: 2px solid #000;
        }
    </style>
</head>
<body>
    @php
        $sizeColumns = [];
        $groupedItems = [];
        $categoryTotals = [];
        
        foreach($salesOrder->items as $item) {
            $categoryName = $item->category_name ?: 'Plain Shirt F/s';
            $fullCatPath = $item->categories_path_val ?: "Core>{$categoryName}";
            
            $sleeveVal = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;
            $fit = strtoupper(trim($sleeveVal));
            if ($fit === 'FULL' || $fit === 'F/S' || $fit === 'FS') {
                $fit = 'Fs';
            } elseif ($fit === 'HALF' || $fit === 'H/S' || $fit === 'HS') {
                $fit = 'Hs';
            }
            
            $sizeName = $item->size->size ?? $item->size_id ?? '-';
            if (!in_array($sizeName, $sizeColumns)) {
                $sizeColumns[] = $sizeName;
            }
            
            $productName = $item->item_name ?: ($item->art_no ?: '-');
            $colorName = $item->api_color ?: ($item->color->color_name ?? '-');
            $minRate = $item->rate;
            $mrp = $item->mrp;
            
            $groupKey = $productName . '|' . ($item->art_no ?? '-') . '|' . $fullCatPath . '|' . $colorName . '|' . $fit . '|' . $minRate . '|' . $mrp;
            
            if (!isset($groupedItems[$groupKey])) {
                $groupedItems[$groupKey] = [
                    'product' => $productName,
                    'art_no' => $item->art_no ?? '-',
                    'category_path' => $fullCatPath,
                    'category_name' => $categoryName,
                    'color' => $colorName,
                    'fit' => $fit,
                    'min_rate' => $minRate,
                    'mrp' => $mrp,
                    'sizes' => [],
                    'total_qty' => 0,
                    'amount' => 0
                ];
            }
            
            if (!isset($groupedItems[$groupKey]['sizes'][$sizeName])) {
                $groupedItems[$groupKey]['sizes'][$sizeName] = 0;
            }
            $groupedItems[$groupKey]['sizes'][$sizeName] += $item->qty;
            $groupedItems[$groupKey]['total_qty'] += $item->qty;
            $groupedItems[$groupKey]['amount'] += $item->amount;
            
            if (!isset($categoryTotals[$fullCatPath])) {
                $categoryTotals[$fullCatPath] = ['total_qty' => 0, 'sizes' => []];
            }
            if (!isset($categoryTotals[$fullCatPath]['sizes'][$sizeName])) {
                $categoryTotals[$fullCatPath]['sizes'][$sizeName] = 0;
            }
            $categoryTotals[$fullCatPath]['sizes'][$sizeName] += $item->qty;
            $categoryTotals[$fullCatPath]['total_qty'] += $item->qty;
        }
        
        ksort($groupedItems);
        sort($sizeColumns);
    @endphp

    <div class="container">
        <!-- HEADER EXACT MATCH -->
        <table style="width: 100%; border: none; margin-bottom: 10px;">
            <tr>
                <td style="width: 15%; border: none; vertical-align: middle;">
                    <img src="{{ isset($is_print) && $is_print ? url('assets/images/fav.jpeg') : public_path('assets/images/fav.jpeg') }}" style="width: 60px;">
                </td>
                <td style="width: 85%; border: none; text-align: center; line-height: 1.3;">
                    <div style="font-size: 25px; font-weight: bold; margin-bottom: 2px;">CASINO</div>
                    <div style="font-size: 20px; font-weight: bold; margin-bottom: 4px;">Nachias Fashion Pvt Ltd</div>
                    <div style="font-size: 10px;">
                        {{ $setting->address ?? '272/2,somu Nagar,sringeri Nagar, Byepass Road, Madurai, Tamil Nadu, India, 625016' }}<br>
                        <strong>Tel No.</strong> {!! implode(', ', array_map('trim', explode(',', $setting->toll_free_no ?? '91-8489938077'))) !!}, 
                        <strong>Email</strong> {!! implode(', ', array_map('trim', explode(',', $setting->email ?? 'srinachias@yahoo.in'))) !!}, 
                        <strong>GST No.</strong> {{ $setting->gst_no ?? '33AADFA4747M1ZD' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- INFO TABLES EXACT MATCH -->
        <table class="table-border" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
            <thead class="dark-header">
                <tr>
                    <th style="width: 33.33%;">Details of Receiver</th>
                    <th style="width: 33.33%;">Details of Intermediate</th>
                    <th style="width: 33.33%;">Order Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 6px; vertical-align: top; line-height: 1.4;">
                        <div style="font-weight: bold;  margin-bottom: 2px;">{{ $salesOrder->customer->name ?? '-' }}</div>
                        <div>{!! $salesOrder->billing_address ? nl2br(e(\App\Models\SalesInvoice::cleanAddress($salesOrder->billing_address))) : '-' !!}</div>
                        <div>{{ isset($salesOrder->customer->state) ? $salesOrder->customer->state->state_name : '-' }}, India</div>
                        <div>{{ $salesOrder->customer->zip_code ?? '-' }}</div>
                        <div>Phone <strong style="font-size: 13px;">{{ $salesOrder->customer->mobile_no ?? '-' }}</strong></div>
                        <div>GST <strong style="font-size: 13px;">{{ $salesOrder->customer->gst_no ?? '-' }}</strong></div>
                        @if($salesOrder->orderaxe_ref_id != '')
                            <div>Ref Id <strong style="font-size: 13px;">{{ $salesOrder->orderaxe_ref_id ?? '-' }}</strong></div>
                        @endif
                    </td>
                    <td style="padding: 6px; vertical-align: top; line-height: 1.4;">
                        <div>{{ $salesOrder->salesAgent->name ?? '-' }}</div>
                        <div>{{ $salesOrder->zone->zone_name ?? '-' }}</div>
                        <div>Chennai</div>
                        <div>Tamil Nadu</div>
                        <div>India</div>
                        <div>Phone <strong style="font-size: 13px;">{{ $salesOrder->salesAgent->mobile_no ?? '-' }}</strong></div>
                        <div>Email <strong style="font-size: 13px;">{{ $salesOrder->salesAgent->email ?? '-' }}</strong></div>
                        <div>Ref Id <strong style="font-size: 13px;">{{ $salesOrder->zone->zone_name ?? '-' }}</strong></div>
                    </td>
                    <td style="padding: 6px; vertical-align: top; line-height: 1.4;">
                        <div>Sales Order No.</div>
                        <div style="font-weight: bold; font-size: 12px; margin-bottom: 2px;">{{ !empty($salesOrder->order_no) ? $salesOrder->order_no : $salesOrder->so_no }}</div>
                        <div>Sales Order Date <strong style="font-size: 13px;">{{ $salesOrder->so_date->format('M, d, Y') }}</strong></div>
                        <div>Submitted By <strong style="font-size: 13px;">{{ $salesOrder->submitted_by ?? $salesOrder->salesAgent->name ?? '-' }}</strong></div>
                        @if($salesOrder->delivery_date != '')
                            <div>Delivery Date <strong style="font-size: 13px;">{{ $salesOrder->delivery_date->format('M, d, Y') }}</strong></div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- REMARKS EXACT MATCH -->
        <div style="margin-bottom: 10px; font-size: 12px;">
            <strong>Remarks</strong> {{ $salesOrder->internal_remarks ?? '-' }}
        </div>

        <!-- MATRIX ITEMS TABLE EXACT MATCH -->
        <table class="table-border" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead class="dark-header">
                <tr>
                    <th style="width: 5%;">S No.</th>
                    <th style="width: 15%;">Product</th>
                    <th style="width: 10%;">Art No</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 8%;">Color</th>
                    <th style="width: 8%;">Fit</th>
                    @foreach($sizeColumns as $sz)
                        <th style="width: 6%;">{{ $sz }}</th>
                    @endforeach
                    <th style="width: 10%;">MRP (INR)</th>
                    <th style="width: 10%;">Min.Rate (INR)</th>
                    <th style="width: 10%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $sn = 1; 
                    $colTotals = array_fill_keys($sizeColumns, 0);
                    $grandQty = 0;
                    $grandAmount = 0;
                @endphp
                @foreach($groupedItems as $group)
                    <tr>
                        <td class="text-center">{{ $sn++ }}</td>
                        <td class="bold">{{ $group['product'] }}</td>
                        <td class="text-center">{{ $group['art_no'] }}</td>
                        <td>{{ $group['category_name'] }}</td>
                        <td class="text-center">{{ $group['color'] }}</td>
                        <td class="text-center">{{ $group['fit'] }}</td>
                        @foreach($sizeColumns as $sz)
                            @php 
                                $q = $group['sizes'][$sz] ?? 0; 
                                $colTotals[$sz] += $q;
                            @endphp
                            <td class="text-center">{{ $q > 0 ? $q : '' }}</td>
                        @endforeach
                        <td class="text-center">{{ number_format($group['mrp'], 2) }}</td>
                        <td class="text-center">{{ number_format($group['min_rate'], 2) }}</td>
                        <td class="text-center bold">{{ $group['total_qty'] }}</td>
                    </tr>
                    @php 
                        $grandQty += $group['total_qty'];
                        $grandAmount += $group['amount'];
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr class="dark-header">
                    <th colspan="6" class="text-right" style="padding-right: 15px;">Total</th>
                    @foreach($sizeColumns as $sz)
                        <th class="text-center">{{ $colTotals[$sz] > 0 ? $colTotals[$sz] : '' }}</th>
                    @endforeach
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                    <th class="text-center">{{ $grandQty }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- CATEGORY HIERARCHY EXACT MATCH -->
        <table class="table-border" style="width: 100%; border-collapse: collapse;">
            <thead class="dark-header">
                <tr>
                    <th style="width: 50%;">Category Hierarchy</th>
                    @foreach($sizeColumns as $sz)
                        <th>{{ $sz }}</th>
                    @endforeach
                    <th>Total Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryTotals as $catName => $catData)
                    <tr>
                        <td class="text-center" style="line-height: 1.4;">
                            <div class="bold">{{ $catName }}</div>
                            <div>{{ str_replace('>', ' > ', $catName) }}</div>
                        </td>
                        @foreach($sizeColumns as $sz)
                            @php $q = $catData['sizes'][$sz] ?? 0; @endphp
                            <td class="text-center">{{ $q > 0 ? $q : '' }}</td>
                        @endforeach
                        <td class="text-center bold">{{ $catData['total_qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="dark-header">
                    <th class="text-center">Total</th>
                    @foreach($sizeColumns as $sz)
                        <th class="text-center">{{ $colTotals[$sz] > 0 ? $colTotals[$sz] : '' }}</th>
                    @endforeach
                    <th class="text-center">{{ $grandQty }}</th>
                </tr>
            </tfoot>
        </table>

    </div>

    @if(isset($is_print) && $is_print)
        <script>
            window.onload = function () {
                window.print();
            }
        </script>
    @endif
</body>
</html>