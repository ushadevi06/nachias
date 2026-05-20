<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead class="bg-light">
            <tr>
                <th>Customer</th>
                <th>Item</th>
                <th class="text-center">Ord. Qty</th>
                <th class="text-center">Bal. Qty</th>
            </tr>
        </thead>
        <tbody>
            @if($orders->count() > 0)
                @foreach($orders->where('fulfillment_status', '!=', 'Completed') as $order)
                <tr>
                    <td>{{ $order->customer->name ?? '-' }} ({{ $order->customer->code ?? '-' }})</td>
                    <td>
                        @if($order->items->count() > 0)
                            @php
                                $firstItem = $order->items->first();
                                $sleeve = is_array($firstItem->sleeve) ? ($firstItem->sleeve[0] ?? '') : $firstItem->sleeve;
                                $sleeveDisplay = ($sleeve == 'Full' || $sleeve == 'Full Sleeve') ? 'F/S' : (($sleeve == 'Half' || $sleeve == 'Half Sleeve') ? 'H/S' : $sleeve ?? '-');
                                $sizeDisplay = $firstItem->size_id ?? '-';
                                $variantInfo = $sizeDisplay . ' ' . $sleeveDisplay;

                                $orderItemsData = $order->items->map(function($item) {
                                    $s = is_array($item->sleeve) ? ($item->sleeve[0] ?? "") : $item->sleeve;
                                    $sd = ($s == "Full" || $s == "Full Sleeve") ? "F/S" : (($s == "Half" || $s == "Half Sleeve") ? "H/S" : $s);
                                    return [
                                        "name" => $item->item_name,
                                        "size" => $item->size_id ?? "-",
                                        "sleeve" => $sd,
                                        "qty" => number_format($item->qty, 0)
                                    ];
                                });
                            @endphp
                            <span>{{ $firstItem->item_name }} ({{ trim($variantInfo) }})</span>
                            @if($order->items->count() > 1)
                                <span class="badge bg-label-primary rounded-pill cursor-pointer view-order-items" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#orderItemsModal" data-order-no="{{ $order->so_no }}" data-items='@json($orderItemsData)'>
                                    +{{ $order->items->count() - 1 }} more
                                </span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center fw-medium">{{ number_format($order->total_qty, 0) }}</td>
                    <td class="text-center text-danger fw-bold">{{ number_format($order->pending_qty ?? ($order->total_qty - ($order->delivered_qty ?? 0)), 0) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
