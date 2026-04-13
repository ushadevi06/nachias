@extends('layouts.common')
@section('title', 'Document Repository - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Document Repository</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create document-repository'))
                <a class="btn btn-primary" href="{{ url('document_repository/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="document-repository-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document Name</th>
                                    <th>Document Type</th>
                                    <th>Department</th>
                                    <th>Validity Date</th>
                                    <th>Status</th>
                                    <th>File</th>
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

        let table = $('#document-repository-table').DataTable({
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
                url: "{{ url('document_repository') }}"
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'document_name'
                },
                {
                    data: 'document_type'
                },
                {
                    data: 'department'
                },
                {
                    data: 'validity_date'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'file',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });


    });
</script>
@endsection