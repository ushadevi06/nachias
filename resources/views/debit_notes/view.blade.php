@extends('layouts.common')
@section('title', 'Debit Notes - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Debit Notes</h4>
                <a class="btn btn-primary" href="{{ url('add_debit_note') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
 @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3 status">
                                <select name="supplier_id" id="supplier_id" class="form-select select2" data-placeholder="Select Supplier">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter_btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable">
                        <table class="datatables-debit-notes table nowrap w-100" id="debit_notes_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Debit Note No.</th>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
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

<script>
    $(document).ready(function() {
        let table = $('#debit_notes_table').DataTable({
            processing: true,
            serverSide: false, // Since we are getting all data at once in controller for now
            ajax: {
                url: "{{ url('debit_notes') }}",
                data: function(d) {
                    d.supplier_id = $('#supplier_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'debit_note_no', name: 'debit_note_no' },
                { data: 'debit_note_date', name: 'debit_note_date' },
                { data: 'purchase_invoice_no', name: 'purchase_invoice_no' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'grand_total', name: 'grand_total' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#filter_btn').click(function() {
            table.ajax.reload();
        });

        $('#reset_btn').click(function() {
            $('#supplier_id').val('').trigger('change');
            table.ajax.reload();
        });
    });

    function delete_data(url) {
        if (confirm('Are you sure you want to delete this debit note?')) {
            window.location.href = url;
        }
    }
</script>
@endsection
