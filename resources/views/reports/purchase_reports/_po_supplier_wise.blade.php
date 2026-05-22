<div class="table-responsive">
    <table class="table premium-table datatables-po-supplier mb-0">
        <thead>
            <tr>
                <th>PO NO</th>
                <th>PO DATE</th>
                <th>SUPPLIER</th>
                <th>ORDER {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>RECEIVED {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>PENDING {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrders as $po)
            <tr>
                <td class="fw-medium text-dark">{{ $po->po_number }}</td>
                <td>{{ $po->po_date ? $po->po_date->format('d-M-Y') : '-' }}</td>
                <td>{{ $po->supplier ? $po->supplier->name : '-' }}</td>
                <td>{{ number_format($po->total_ordered, 2) }}</td>
                <td>{{ number_format($po->total_received, 2) }}</td>
                <td>{{ number_format($po->total_pending, 2) }}</td>
                <td>
                    @if(strtolower($po->status) == 'closed' || $po->is_self_closed || $po->total_pending <= 0)
                        <span class="badge bg-label-success rounded-pill">Closed</span>
                    @else
                    <span class="badge bg-label-warning rounded-pill">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-po-supplier');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        $table.DataTable({
            destroy: true,
            pageLength: 10,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            language: {
                emptyTable: 'No purchase orders found.'
            }
        });
    })();
</script>
