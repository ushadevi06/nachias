@extends('layouts.common')
@section('title', 'Stock Consumables & Return Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Consumables & Return Management</h4>
                {{-- <a class="btn btn-primary" href="{{ url('add_stock_consumables_return') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a> --}}
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
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->operation_name }}">{{ $stage->operation_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table id="consumablesInventoryTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Production ID</th>
                                    <th>Job Card ID</th>
                                    <th>Production Stage</th>
                                    <th>Art No</th>
                                    <th>Material Name</th>
                                    <th>UOM</th>
                                    <th>F/S Qty</th>
                                    <th>H/S Qty</th>
                                    <th>Total Issue Qty</th>
                                    <th>Issued Date</th>
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
        if ($.fn.DataTable.isDataTable('#consumablesInventoryTable')) {
            $('#consumablesInventoryTable').DataTable().destroy();
        }
        $('#consumablesInventoryTable').DataTable({
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
                { data: 'production_no', name: 'production_no' },
                { data: 'job_card_no', name: 'job_card_no' },
                { data: 'production_stage', name: 'production_stage' },
                { data: 'art_no', name: 'art_no' },
                { data: 'material_name', name: 'material_name' },
                { data: 'uom', name: 'uom' },
                { data: 'fs_qty', name: 'fs_qty' },
                { data: 'hs_qty', name: 'hs_qty' },
                { data: 'total_issue_qty', name: 'total_issue_qty' },
                { data: 'issued_date', name: 'issued_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endsection