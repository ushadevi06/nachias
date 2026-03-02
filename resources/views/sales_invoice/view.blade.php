@extends('layouts.common')
@section('title', 'Sales Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Invoices</h4>
                <a class="btn btn-primary" href="{{ url('sales_invoices/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="customer_id" id="customer_id" class="form-select select2" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->customer_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Unpaid/Credit">Unpaid/Credit</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Partially Paid">Partially Paid</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="inv_date_range" class="form-control" placeholder="Select Invoice Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table id="salesInvoiceTable" class="table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer Name / Buyer </th>
                                    <th>Linked SO No.</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
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
        let table = $('#salesInvoiceTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            ajax: {
                url: "{{ url('sales_invoices') }}",
                data: function(d) {
                    d.status = $('#status').val();
                    d.customer_id = $('#customer_id').val();
                    d.inv_date_range = $('#inv_date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'inv_no' },
                { data: 'inv_date' },
                { data: 'customer_name' },
                { data: 'so_no' },
                { data: 'total_items' },
                { data: 'grand_total' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
            ]
        });
        $('#inv_date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#status').val('').trigger('change');
            $('#customer_id').val('').trigger('change');
            $('#inv_date_range').val('');
            table.ajax.reload();
        });

        $(document).on('change', '.inv-status-change', function() {
            let id = $(this).data('id');
            let status = $(this).val();

            $.ajax({
                url: "{{ url('sales_invoices/status') }}/" + id,
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
                    alert('Failed to update status');
                }
            });
        });
    });
</script>
@endsection