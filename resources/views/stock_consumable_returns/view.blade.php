@extends('layouts.common')
@section('title', 'Stock Consumables & Return Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Consumables & Return Management</h4>
                <a class="btn btn-primary" href="{{ url('add_stock_consumables_return') }}">
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
                                <select name="production" id="production" class="form-select select2" data-placeholder="Select Production">
                                    <option value="">Select Production</option>
                                    <option value="Cutting">Cutting</option>
                                    <option value="Stitching">Stitching</option>
                                    <option value="Stitching">Printing</option>
                                    <option value="Ironing">Ironing</option>
                                    <option value="Packing">Packing</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Issue No.</th>
                                    <th>Issue Date</th>
                                    <th>Production</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
    $(document).ready(function() {
        $('#consumablesTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            processing: true,
            ajax: "{{ url('stock_consumables_returns') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'issue_no', name: 'issue_no' },
                { data: 'issue_date', name: 'issue_date' },
                { data: 'production', name: 'production' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endsection