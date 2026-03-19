@extends('layouts.common')
@section('title', 'Production Receipts - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4>Production Receipts</h4>
            <a href="{{ url('production_receipts/add') }}" class="btn btn-primary">
                <i class="ri ri-add-circle-line me-1"></i> Add
            </a>
        </div>
        <div class="col-lg-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="production-receipts-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt No</th>
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
            pageLength: 10,
            processing: true,
            ajax: '{{ url('production_receipts') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'receipt_no', name: 'receipt_no' },
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
