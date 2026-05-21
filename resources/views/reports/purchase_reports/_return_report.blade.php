<div class="table-responsive">
    <table class="table premium-table mb-0 datatable-return" style="width:100%">
        <thead>
            <tr>
                <th>RETURN DATE</th>
                <th>DEBIT NOTE NO</th>
                <th>SUPPLIER NAME</th>
                <th>ITEM NAME</th>
                <th>RETURNED QTY</th>
                <th>RATE</th>
                <th>TOTAL AMOUNT</th>
                <th>REASON</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returnGoodsData as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->return_date)->format('d-m-Y') }}</td>
                    <td>{{ $row->return_no }}</td>
                    <td>{{ $row->supplier_name }}</td>
                    <td>{{ $row->item_name }}</td>
                    <td>{{ number_format($row->quantity, 2) }}</td>
                    <td>{{ number_format($row->rate, 2) }}</td>
                    <td>{{ number_format($row->amount, 2) }}</td>
                    <td>{{ $row->reason ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#return-report .datatable-return')) {
        $('#return-report .datatable-return').DataTable().destroy();
    }
    
    $('#return-report .datatable-return').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: 'Return Goods'
            },
            {
                extend: 'pdfHtml5',
                className: 'buttons-pdf d-none',
                title: 'Return Goods'
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                title: 'Return Goods'
            }
        ]
    });
});
</script>
