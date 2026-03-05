<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #666; padding-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; color: #7367f0; margin: 0; }
        .po-title { font-size: 18px; font-weight: bold; text-align: right; margin: 0; }
        .details-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details-table td { width: 50%; vertical-align: top; }
        .section-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; color: #444; border-bottom: 1px solid #ddd; }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .item-table th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px; text-align: left; font-size: 11px; }
        .item-table td { border: 1px solid #dee2e6; padding: 8px; font-size: 11px; }
        .text-right { text-align: right; }
        .totals-table { width: 300px; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 5px; border-bottom: 1px solid #eee; }
        .grand-total { font-weight: bold; font-size: 16px; color: #7367f0; border-top: 2px solid #7367f0 !important; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-draft { background-color: #eee; color: #666; }
        .status-approved { background-color: #e8fadf; color: #71dd37; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="company-name">{{ $web_settings->site_title ?? 'NACHIAS' }}</h1>
                    <div style="font-size: 10px; color: #666;">
                        {{ $web_settings->address ?? 'Chennai, Tamil Nadu' }}
                    </div>
                </td>
                <td class="text-right">
                    <h2 class="po-title">PURCHASE ORDER</h2>
                    <div><strong>PO #:</strong> {{ $purchaseOrder->po_number }}</div>
                    <div><strong>Date:</strong> {{ $purchaseOrder->po_date->format('d-m-Y') }}</div>
                    <div><strong>Status:</strong> {{ strtoupper($purchaseOrder->status) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <tr>
            <td>
                <div class="section-title">Supplier Details</div>
                <div><strong>{{ $purchaseOrder->supplier->name }}</strong></div>
                <div>{{ $purchaseOrder->supplier->address }}</div>
                <div>{{ $purchaseOrder->supplier->city }} - {{ $purchaseOrder->supplier->state }}</div>
                <div>Contact: {{ $purchaseOrder->supplier->contact_no }}</div>
                @if($purchaseOrder->supplier->gst_no)
                <div>GST: {{ $purchaseOrder->supplier->gst_no }}</div>
                @endif
            </td>
            <td style="padding-left: 20px;">
                <div class="section-title">Order Info</div>
                <div><strong>Ref No:</strong> {{ $purchaseOrder->reference_no ?? 'N/A' }}</div>
                <div><strong>Ref Date:</strong> {{ $purchaseOrder->reference_date ? $purchaseOrder->reference_date->format('d-m-Y') : 'N/A' }}</div>
                <div><strong>Due Date:</strong> {{ $purchaseOrder->due_date->format('d-m-Y') }}</div>
                <div><strong>Store Type:</strong> {{ $purchaseOrder->storeType->store_type_name }}</div>
                @if($purchaseOrder->purchaseCommissionAgent)
                <div><strong>Agent:</strong> {{ $purchaseOrder->purchaseCommissionAgent->agent_name }} ({{ $purchaseOrder->commission }}%)</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th>Sr.#</th>
                <th>Item / Description</th>
                <th>Art No</th>
                <th class="text-right">Quantity</th>
                <th>UOM</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->rawMaterial->name }}</strong><br>
                    <small>
                        Cat: {{ $item->storeCategory->category_name }}
                        @if($item->style) | Style: {{ $item->style->style_name }} @endif
                        @if($item->color) | Color: {{ $item->color->color_name }} @endif
                    </small>
                </td>
                <td>{{ $item->supplier_design_name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td>{{ $item->uom->uom_code }}</td>
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Sub-Total:</td>
            <td class="text-right">{{ number_format($purchaseOrder->sub_total, 2) }}</td>
        </tr>
        @if($purchaseOrder->discount_amount > 0)
        <tr>
            <td>Discount ({{ $purchaseOrder->discount_percent }}%):</td>
            <td class="text-right">-{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
        </tr>
        @endif
        
        @if($purchaseOrder->igst_amount > 0)
        <tr>
            <td>IGST ({{ $purchaseOrder->igst_percent }}%):</td>
            <td class="text-right">{{ number_format($purchaseOrder->igst_amount, 2) }}</td>
        </tr>
        @elseif($purchaseOrder->cgst_amount > 0 || $purchaseOrder->sgst_amount > 0)
        <tr>
            <td>CGST ({{ $purchaseOrder->cgst_percent }}%):</td>
            <td class="text-right">{{ number_format($purchaseOrder->cgst_amount, 2) }}</td>
        </tr>
        <tr>
            <td>SGST ({{ $purchaseOrder->sgst_percent }}%):</td>
            <td class="text-right">{{ number_format($purchaseOrder->sgst_amount, 2) }}</td>
        </tr>
        @endif

        @if($purchaseOrder->round_off != 0)
        <tr>
            <td>Round Off:</td>
            <td class="text-right">{{ number_format($purchaseOrder->round_off, 2) }}</td>
        </tr>
        @endif

        <tr class="grand-total">
            <td><strong>Grand Total:</strong></td>
            <td class="text-right"><strong>{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <div class="section-title">Terms & Conditions / Remarks</div>
        <div style="font-size: 11px;">
            {{ $purchaseOrder->remarks ?? 'No specific terms provided.' }}
        </div>
    </div>

    <div class="footer">
        This is a computer-generated document. System Time: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
