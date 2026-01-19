@extends('layouts.common')
@section('title', 'Production Services - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Production Services</h4>
                 {{-- @if(auth()->id() == 1 || auth()->user()->can('create services')) --}}
                <a class="btn btn-primary" href="{{ url('production_services/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                 {{-- @endif --}}
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="serviceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Service Name</th>
                                    <th>Service Code</th>
                                    <th>Status</th>
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
    $(function() {
        $('#serviceTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ url('production_services') }}",
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'service_name' },
                { data: 'service_code' },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Status Toggle Placeholder - Functionality not requested
    });
</script>
@endsection
