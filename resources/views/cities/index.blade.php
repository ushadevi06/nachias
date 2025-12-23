@extends('layouts.common')
@section('title', 'Cities - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Cities</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create cities'))
                <a class="btn btn-primary" href="{{ url('cities/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="state" id="state" class="form-select select2"
                                    data-placeholder="Select State">
                                    <option value="">Select State</option>
                                    @foreach(\App\Models\State::where('status','Active')->get() as $state)
                                    <option value="{{ $state->state_code }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary filterBtn">Filter</button>
                                <button type="button" class="btn btn-secondary resetBtn">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="citiesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>State</th>
                                    <th>City Name</th>
                                    <th>City Code</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
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
        if ($.fn.DataTable.isDataTable('#citiesTable')) {
            $('#citiesTable').DataTable().destroy();
        }

        let table = $('#citiesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('cities') }}",
                data: function(d) {
                    d.state = $('#state').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'state'
                },
                {
                    data: 'city_name'
                },
                {
                    data: 'city_code'
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
            $('#state').val('').trigger('change');
            table.ajax.reload();
        });

        $(document).on('change', '.city-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "/cities/status/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {
                    let msg = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Inactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                }
            });
        });

    });
</script>
@endsection