@extends('layouts.common')
@section('title', 'Production Receipts - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4>Production Receipts</h4>
            <div class="d-flex gap-2">
                @if(auth()->id() == 1 || auth()->user()->can('view production-receipt-report'))
                <a href="{{ url('production_receipts/report') }}" class="btn btn-outline-info">
                    <i class="menu-icon icon-base ri ri-file-list-3-line me-1"></i> Report
                </a>
                @endif
                <a href="{{ url('production_receipts/export-excel') }}" class="btn btn-outline-success">
                    <i class="menu-icon icon-base ri ri-file-excel-line me-1"></i> Export
                </a>
                @if(auth()->id() == 1 || auth()->user()->can('create production-receipts'))
                <a href="{{ url('production_receipts/add') }}" class="btn btn-primary">
                    <i class="menu-icon icon-base ri ri-add-circle-line me-1"></i> Add
                </a>
                @endif
            </div>
        </div>

        <div class="col-lg-12">
            @include('flash_messages')
            
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="warehouse_id" id="warehouse_id" class="form-select select2" data-placeholder="Select Warehouse">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses ?? [] as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="store_type_id" id="store_type_id" class="form-select select2" data-placeholder="Select Store">
                                    <option value="">Select Store</option>
                                    @foreach($storeTypes ?? [] as $st)
                                        <option value="{{ $st->id }}">{{ $st->store_type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="date_range" class="form-control" placeholder="Select Receipt Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary text-uppercase">FILTER</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary text-uppercase ms-1">RESET</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="production-receipts-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card No</th>
                                    <th>Receipt Date</th>
                                    <th>Warehouse</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Store Location</th>
                                    <th>Action</th>
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
    $(function () {
        $('#date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        var table = $('#production-receipts-table').DataTable({
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
                url: '{{ url('production_receipts') }}',
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.store_type_id = $('#store_type_id').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'job_card_no', name: 'job_card_no' },
                { data: 'receipt_date', name: 'receipt_date' },
                { data: 'warehouse', name: 'warehouse' },
                { data: 'store', name: 'store' },
                { data: 'status', name: 'status' },
                { data: 'store_location', name: 'store_location' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'asc']]
        });

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#resetBtn').on('click', function() {
            $('#warehouse_id').val('').trigger('change');
            $('#store_type_id').val('').trigger('change');
            $('#date_range').val('');
            table.ajax.reload();
        });
    });
</script>
@endsection
