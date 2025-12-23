@extends('layouts.common')
@section('title', 'Purchase Orders - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Purchase Orders</h4>
                <a class="btn btn-primary" href="{{ url('purchase_orders/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Dispatched">Dispatched</option>
                                    <option value="Received">Received</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="po_date_range" class="form-control" placeholder="Select PO Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table id="purchaseOrderTable" class="table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PO Number</th>
                                    <th>PO Date</th>
                                    <th>Supplier Name</th>
                                    <th>Reference No.</th>
                                    <th>Due Date</th>
                                    <th>Store Type</th>
                                    <th>Total Order Qty</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        let table = $('#purchaseOrderTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            ajax: {
                url: "{{ url('purchase_orders') }}",
                data: function(d) {
                    d.status = $('#status').val();
                    d.po_date_range = $('#po_date_range').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'po_number'
                },
                {
                    data: 'po_date'
                },
                {
                    data: 'supplier_name'
                },
                {
                    data: 'reference_no'
                },
                {
                    data: 'due_date'
                },
                {
                    data: 'delivery_location'
                },
                {
                    data: 'total_qty'
                },
                {
                    data: 'order_date'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'total_amount'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Initialize Flatpickr
        $('#po_date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#status').val('').trigger('change');
            $('#po_date_range').val('');
            table.ajax.reload();
        });

        $(document).on('change', '.po-status-change', function() {
            let id = $(this).data('id');
            let status = $(this).val();

            $.ajax({
                url: "{{ url('purchase_orders/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    let msg = '<span class="text-success">Status Changed</span>';
                    $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                    table.ajax.reload(null, false);
                },
                error: function() {
                    toastr.error('Failed to update status');
                }
            });
        });
    });
</script>
@endsection