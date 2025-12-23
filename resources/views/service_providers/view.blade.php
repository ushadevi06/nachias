@extends('layouts.common')
@section('title', 'Service Providers - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Service Providers</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create service-providers'))
                <a class="btn btn-primary" href="{{ url('service_providers/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h5>Filter</h5>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <select name="service_type" id="service_type" class="form-select select2" data-placeholder="Select Service Type">
                                    <option value="">Select Service Type</option>
                                    @foreach($service_types as $type)
                                    <option value="{{ $type->id }}">{{ $type->service_type_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <select name="service_rate" id="service_rate" class="form-select select2" data-placeholder="Select Service Rate">
                                    <option value="">Select Service Rate</option>
                                    <option value="Per Agent">Per Agent</option>
                                    <option value="Job Type">Job Type</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="filter-btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable mt-3">
                        <table class="datatables-service-providers table" id="service-providers-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Service Type</th>
                                    <th>Service Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
        var table = $('#service-providers-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('service_providers') }}",
                type: "GET",
                data: function(d) {
                    d.service_type = $('#service_type').val();
                    d.service_rate = $('#service_rate').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'mobile'
                },
                {
                    data: 'email'
                },
                {
                    data: 'service_type'
                },
                {
                    data: 'service_rate'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });

        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });

        $('#reset-btn').on('click', function() {
            $('#service_type').val('').trigger('change');
            $('#service_rate').val('').trigger('change');
            table.ajax.reload();
        });

        $(document).on('change', '.service-provider-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('service_provider/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {

                    let msg = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Deactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                }
            });
        });

    });
</script>
@endsection