@extends('layouts.common')
@section('title', 'Customers - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Customers</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create customers'))
                <a class="btn btn-primary" href="{{ url('customers/add') }}">
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
                    <div class="filter-box mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select id="category" class="form-select select2" data-placeholder="Select Category">
                                    <option value="">Select Category</option>
                                    <option value="Retailer">Retailer</option>
                                    <option value="Wholesaler">Wholesaler</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable">
                        <table id="customerTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Contact Info</th>
                                    <th>Location</th>
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
    $(function() {
        let table = $('#customerTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('customers') }}",
                data: function(d) {
                    d.category = $('#category').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'name'
                },
                {
                    data: 'category'
                },
                {
                    data: 'contact_info',
                    orderable: false,
                },
                {
                    data: 'location',
                    orderable: false,
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
                },
            ]

        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#category').val('').trigger('change');
            table.ajax.reload();
        });

        $(document).on('change', '.customer-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('customers/status') }}/" + id,
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