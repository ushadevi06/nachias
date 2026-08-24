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
                                <div class="col-md-4 col-lg-3 store_type">
                                    <select name="store_type_id" id="store_type_id" class="form-select select2"
                                        data-placeholder="Select Store Type">
                                        <option value="">Select Store Type</option>
                                        @foreach ($storeTypes as $storeType)
                                            <option value="{{ $storeType->id }}">{{ $storeType->store_type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3 status">
                                    <select name="status" id="status" class="form-select select2"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Dispatched">Dispatched</option>
                                        <option value="Received">Received</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <input type="text" id="po_date_range" class="form-control"
                                        placeholder="Select PO Date Range">
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
            let table = $('#purchaseOrderTable').DataTable({
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
                    url: "{{ url('purchase_orders') }}",
                    data: function (d) {
                        d.store_type_id = $('#store_type_id').val();
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

            $('#po_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'd-m-Y',
                allowInput: true
            });

            $('#filterBtn').click(function () {
                table.ajax.reload();
            });

            $('#resetBtn').click(function () {
                $('#status').val('').trigger('change');
                $('#po_date_range').val('');
                $('#store_type_id').val('').trigger('change');
                table.ajax.reload();
            });

            $(document).on('change', '.po-status-change', function () {
                let id = $(this).data('id');
                let status = $(this).val();
                let $select = $(this);
                let previousStatus = $select.data('previous-status');

                $.ajax({
                    url: "{{ url('purchase_orders/status') }}/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function (response) {
                        if (response.success === false || response.rate_missing) {
                            $select.val(previousStatus).trigger('change.select2');
                            let errorMsg = response.message || 'Update failed';
                            let msg = '<div class="text-danger small mt-1" style="font-size:10.5px; line-height:1.25; white-space:normal !important; width:100%; max-width:145px; word-break:break-word;"><i class="ri ri-alert-line me-1"></i> ' + errorMsg + '</div>';
                            $('.status_msg_' + id).html(msg).fadeIn();
                            setTimeout(function() {
                                $('.status_msg_' + id).fadeOut(function() {
                                    $(this).empty();
                                });
                            }, 4000);
                            return;
                        }
                        $select.data('previous-status', status);
                        let msg = '<div class="text-success bg-white border border-success rounded shadow p-1 mt-1" style="font-size:11px; text-align:center;"><i class="ri ri-check-line"></i> Status Changed</div>';
                        $('.status_msg_' + id).html(msg).fadeIn().delay(2000).fadeOut();
                        table.ajax.reload(null, false);
                    },
                    error: function () {
                        $select.val(previousStatus).trigger('change.select2');
                        let msg = '<div class="text-danger bg-white border border-danger rounded shadow p-1 mt-1" style="font-size:11px; text-align:center;"><i class="ri ri-alert-line"></i> Update failed</div>';
                        $('.status_msg_' + id).html(msg).fadeIn().delay(2000).fadeOut();
                    }
                });
            });

            $(document).on('change', '.po-self-close-toggle', function () {
                let id = $(this).data('id');
                let isChecked = $(this).is(':checked');

                $.ajax({
                    url: "{{ url('purchase_orders/toggle-self-close') }}/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        is_self_closed: isChecked
                    },
                    success: function (response) {
                        let msg = '<span class="text-success small">Self Close Updated</span>';
                        $('.self_close_msg_' + id).html(msg).fadeIn().delay(1000).fadeOut();
                        table.ajax.reload(null, false);
                    },
                    error: function () {
                        alert('Status update failed');
                    }
                });
            });
        });
    </script>
@endsection