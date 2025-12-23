@extends('layouts.common')
@section('title', 'Places - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Places (Service Points)</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create service-points'))
                <a class="btn btn-primary" href="{{ url('places/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="state" id="state_id" class="form-select select2"
                                    data-placeholder="Select State">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ request('state') == $state->id ? 'selected' : '' }}>
                                        {{ $state->state_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="city" id="city_id" class="form-select select2"
                                    data-placeholder="Select City">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary filterBtn">Filter</button>
                                <a href="{{ url('places') }}" class="btn btn-secondary resetBtn">Reset</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="placesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Place (Service Point)</th>
                                    <th>Place Type</th>
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
    $(document).ready(function() {

        let table = $('#placesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('places') }}",
                data: function(d) {
                    d.state = $('#state_id').val();
                    d.city = $('#city_id').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'state'
                },
                {
                    data: 'city'
                },
                {
                    data: 'place_name'
                },
                {
                    data: 'place_type'
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
            ]
        });

        $('.filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('.resetBtn').on('click', function() {
            $('#state_id').val('').trigger('change');
            $('#city_id').val('').trigger('change');
            table.ajax.reload();
        });


        $(document).on('change', '.place-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "/places/status/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {
                    let msg = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Inactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1000).fadeOut();
                }
            });
        });

    });
</script>
@endsection