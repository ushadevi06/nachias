@extends('layouts.common')
@section('title', 'Devices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Devices</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create devices'))
                <a class="btn btn-primary" href="{{ url('devices/add') }}">
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
                        <table class="table" id="deviceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Device Name</th>
                                    <th>Serial Number</th>
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
        $('#deviceTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            ajax: "{{ url('devices') }}",
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'device_name'
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>
@endsection
