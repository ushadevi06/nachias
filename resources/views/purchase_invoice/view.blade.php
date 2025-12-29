@extends('layouts.common')
@section('title', 'Purchase Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding mt-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Purchase Invoices</h4>
                <a class="btn btn-primary" href="{{ url('purchase_invoices/add') }}">
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
                            <div class="col-md-4 col-lg-3">
                                <select name="supplier_id" id="filter_supplier" class="form-select select2" data-placeholder="Select Supplier">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="invoice_status" id="filter_status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Unpaid/Credit">Unpaid/Credit</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Partially Paid">Partially Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter_btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="invoices-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Supplier Name</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Items Modal -->
<div class="modal fade" id="invoiceItemsModal" tabindex="-1" aria-labelledby="invoiceItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white" id="invoiceItemsModalLabel">Invoice Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Art no</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Qty Ordered</th>
                                <th>Qty Received</th>
                                <th>Qty Invoiced</th>
                                <th>Qty Invoice</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsTableBody"></tbody>
                        <tfoot>
                            <tr class="table-light fw-semibold">
                                <td colspan="4" class="text-end">Total Amount:</td>
                                <td id="modalTotalAmount" class="text-end">0.00</td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body')
        });

        var table = $('#invoices-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            ajax: {
                url: '{{ url("purchase_invoices") }}',
                data: function(d) {
                    d.supplier_id = $('#filter_supplier').val();
                    d.invoice_status = $('#filter_status').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no'
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date'
                },
                {
                    data: 'supplier_name',
                    name: 'supplier_name'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#filter_btn').on('click', function() {
            table.ajax.reload();
        });

        $('#reset_btn').on('click', function() {
            $('#filter_supplier').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });


        // Status change
        $(document).on('change', '.invoice-status-change', function() {
            var invoiceId = $(this).data('id');
            var status = $(this).val();

            $.ajax({
                url: '{{ url("purchase_invoices/update-status") }}/' + invoiceId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    let msg = '<span class="text-success">Status Changed</span>';
                    $('.status_msg_' + invoiceId).html(msg).fadeIn().delay(1200).fadeOut();
                    table.ajax.reload(null, false);
                },
                error: function() {
                    toastr.error('Failed to update status');
                }
            });
        });

        // Load invoice items in modal
        $(document).on('click', '.btn-invoice-items', function() {
            var invoiceId = $(this).data('id');

            $.ajax({
                url: APP_URL + '/purchase_invoices/get-items/' + invoiceId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var tbody = $('#invoiceItemsTableBody');
                        tbody.empty();

                        response.items.forEach(function(item, index) {
                            var row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.raw_material_name} <span class="text-muted">[HSN:${item.hsn_code}]</span></td>
                            <td>${item.uom_code}</td>
                            <td>${item.rate}</td>
                            <td class="text-end">${item.amount}</td>
                            <td>${item.qty_ordered}</td>
                            <td>${item.qty_received}</td>
                            <td>${item.qty_invoiced}</td>
                            <td>${item.quantity}</td>
                        </tr>`;
                            tbody.append(row);
                        });

                        $('#modalTotalAmount').text(response.total_amount);
                    }
                },
                error: function() {
                    alert('Failed to load invoice items');
                }
            });
        });
    });

    function delete_data(url) {
        if (confirm('Are you sure you want to delete this invoice?')) {
            window.location.href = url;
        }
    }
</script>
@endsection