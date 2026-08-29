<div class="card-datatable">
    <table class="datatables-products table table-hover" id="dispatchReportTable">
        <thead>
            <tr>
                <th>Dispatch Date</th>
                <th>Order No</th>
                <th>Party Name</th>
                <th>Place</th>
                <th>Transporter/Vehicle</th>
                <th>LR No</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
window.initDispatchReportTable = function() {
    if ($.fn.DataTable.isDataTable('#dispatchReportTable')) {
        $('#dispatchReportTable').DataTable().clear().destroy();
    }
    $('#dispatchReportTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/dispatch-report') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'date', name: 'date', className: 'text-nowrap text-center' },
            { data: 'so_no', name: 'so_no', className: 'text-center' },
            { data: 'customer', name: 'customer' },
            { data: 'destination', name: 'destination', className: 'text-center' },
            { data: 'qty', name: 'qty', className: 'text-center' },
            { data: 'invoices', name: 'invoices', className: 'text-center', orderable: false },
            { data: 'status', name: 'status', className: 'text-center', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
};
function initDispatchReportTable() {
    window.initDispatchReportTable();
}

if ($('#dispatch').hasClass('active') || $('#dispatch-report').hasClass('active')) {
    window.initDispatchReportTable();
}
</script>