<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-performance" style="width:100%">
        <thead>
            <tr>
                
                <th>SUPPLIER NAME</th>
                <th>PO COUNT</th>
                <th>TOTAL PO VALUE</th>
                <th>DEBIT NOTE COUNT</th>
                <th>RETURN RATE (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceData as $data)
            <tr>

                <td>{{ $data['supplier_name'] }}</td>
                <td class="text-center">{{ $data['po_count'] }}</td>
                <td class="text-end fw-semibold">₹{{ number_format($data['total_po_value'], 2) }}</td>
                <td class="text-center">{{ $data['dn_count'] }}</td>
                <td class="text-center">
                    @if($data['return_rate'] > 10)
                        <span class="badge bg-label-danger">{{ $data['return_rate'] }}%</span>
                    @elseif($data['return_rate'] > 0)
                        <span class="badge bg-label-warning">{{ $data['return_rate'] }}%</span>
                    @else
                        <span class="badge bg-label-success">{{ $data['return_rate'] }}%</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    if ($.fn.DataTable.isDataTable('.datatables-performance')) {
        $('.datatables-performance').DataTable().destroy();
    }
    $('.datatables-performance').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        buttons: [
            { extend: 'excelHtml5', className: 'buttons-excel d-none', title: 'Supplier Performance Report' },
            { extend: 'pdfHtml5', className: 'buttons-pdf d-none', title: 'Supplier Performance Report' },
            { extend: 'print', className: 'buttons-print d-none', title: 'Supplier Performance Report' }
        ]
    });
</script>
