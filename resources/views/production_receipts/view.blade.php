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
             <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="production-receipts-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card No</th>
                                    <th>Receipt Date</th>
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
        $('#production-receipts-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            serverSide: true,
            ajax: '{{ url('production_receipts') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'job_card_no', name: 'job_card_no' },
                { data: 'receipt_date', name: 'receipt_date' },
                { data: 'store', name: 'store' },
                { data: 'status', name: 'status' },
                { data: 'store_location', name: 'store_location' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            responsive: true,
            order: [[0, 'asc']]
        });
    });
</script>
@endsection
