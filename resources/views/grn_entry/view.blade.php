@extends('layouts.common')
@section('title', 'GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>GRN Entry</h4>
                <a class="btn btn-primary" href="{{ url('grn_entries/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="supplier_id" id="supplier_id" class="form-select select2" data-placeholder="Select Supplier">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}({{ $supplier->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="qc_status" id="qc_status" class="form-select select2" data-placeholder="Select QC Status">
                                    <option value="">Select QC Status</option>
                                    <option value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                    <option value="Hold">Hold</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                                <button type="button" class="btn btn-secondary" id="btn-reset">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="grn-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GRN No.</th>
                                    <th>GRN Date</th>
                                    <th>PO Invoice No.</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Invoice No.</th>
                                    <th>Total Items</th>
                                    <th>Amount (₹)</th>
                                    <th>QC Status</th>
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
@endsection

@section('scripts')
<script>
    $(function() {
        let table = $('#grn-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('grn_entries') }}",
                data: function(d) {
                    d.supplier_id = $('#supplier_id').val();
                    d.status = $('#qc_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'grn_number', name: 'grn_number'},
                {data: 'grn_date', name: 'grn_date'},
                {data: 'po_invoice_no', name: 'po_invoice_no'},
                {data: 'supplier_name', name: 'supplier_name'},
                {data: 'supplier_invoice_no', name: 'supplier_invoice_no'},
                {data: 'total_items', name: 'total_items'},
                {data: 'amount', name: 'amount'},
                {data: 'qc_status', name: 'qc_status', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $('#btn-filter').click(function() {
            table.ajax.reload();
        });

        $('#btn-reset').click(function() {
            $('#supplier_id').val('').trigger('change');
            $('#qc_status').val('').trigger('change');
            table.ajax.reload();
        });

        // Status toggle removed in favor of workflow badges
    });

    function delete_data(url) {
        if (confirm('Are you sure you want to delete this GRN entry?')) {
            $.post(url, {
                _token: "{{ csrf_token() }}",
            }, function(res) {
                if (res.success) {
                    $('#grn-table').DataTable().ajax.reload();
                }
            });
        }
    }
</script>
@endsection
