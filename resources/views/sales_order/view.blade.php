@extends('layouts.common')
@section('title', 'Sales Orders - ' . env('WEBSITE_NAME'))

@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>Sales Orders</h4>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-info" href="{{ url('sales_orders/sync-orderaxe') }}">
                            <i class="icon-base ri ri-refresh-line"></i> Sync Orderaxe
                        </a>
                        <a class="btn btn-primary" href="{{ url('sales_orders/add') }}">
                            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                        </a>
                    </div>
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
                                    <select name="customer_id" id="filter_customer" class="form-select select2"
                                        data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        @foreach(\App\Models\Customer::active()->orderBy('id', 'desc')->get() as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <select name="status" id="filter_status" class="form-select select2"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        @foreach(['Draft', 'Approved', 'Pending', 'In Production', 'Dispatched', 'Cancelled'] as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <input type="text" id="so_date_range" class="form-control"
                                        placeholder="Select SO Date Range">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-datatable">
                            <table id="salesOrderTable" class="table nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>SO Number</th>
                                        <th>SO Date</th>
                                        <th>Customer Name</th>
                                        <th>Customer PO Ref</th>
                                        <th>Total Qty</th>
                                        <th>Sales Executive</th>
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
        $(function () {
            let table = $('#salesOrderTable').DataTable({
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('sales_orders') }}",
                    data: function (d) {
                        d.customer_id = $('#filter_customer').val();
                        d.status = $('#filter_status').val();
                        d.so_date_range = $('#so_date_range').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'so_no', render: function (data) { return data; } },
                    { data: 'so_date' },
                    { data: 'customer_name' },
                    { data: 'customer_po_ref' },
                    { data: 'total_qty' },
                    { data: 'sales_agent' },
                    { 
                        data: 'status', 
                        orderable: false, 
                        searchable: true,
                        render: function (data, type, row) {
                            if (type === 'filter') {
                                return row.status_text;
                            }
                            return data;
                        }
                    },
                    { data: 'total_amount' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

            $('#so_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'd-m-Y',
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 1) {
                        instance.setDate([selectedDates[0], selectedDates[0]], true);
                    }
                }
            });

            $('#filterBtn').click(function () { table.ajax.reload(); });
            $('#resetBtn').click(function () {
                $('#filter_customer').val('').trigger('change');
                $('#filter_status').val('').trigger('change');
                $('#so_date_range').val('');
                table.ajax.reload();
            });

            $(document).on('change', '.so-status-change', function () {
                let id = $(this).data('id');
                let status = $(this).val();
                $.ajax({
                    url: "{{ url('sales_orders/status') }}/" + id,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", status: status },
                    success: function (response) {
                        let msg = '<span class="text-success">Status Changed</span>';
                        $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                        table.ajax.reload(null, false);
                    }
                });
            });
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                if (confirm('Are you sure you want to delete this sale order?')) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endsection