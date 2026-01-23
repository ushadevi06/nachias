@extends('layouts.common')
@section('title', 'Productions - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Productions</h4>
                <a class="btn btn-primary" href="{{ url('productions/add') }}">
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
                    <div class="card-datatable">
                        <table class="datatables-productions table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Production ID</th>
                                    <th>Production Date</th>
                                    <th>Job Card No</th>
                                    <th>Planned Quantity</th>
                                    <th>Planned Start Date</th>
                                    <th>Planned End Date</th>
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
    $(document).ready(function() {
        $('.datatables-productions').DataTable({
            processing: true,
            ajax: "{{ url('productions') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'production_id', name: 'production_id' },
                { data: 'production_date', name: 'production_date' },
                { data: 'job_card_no', name: 'job_card_no' },
                { data: 'planned_qty', name: 'planned_qty' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true,
            order: [[0, 'asc']]
        });
    });
</script>
@endsection