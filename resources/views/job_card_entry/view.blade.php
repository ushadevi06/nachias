@extends('layouts.common')
@section('title', 'Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>Job Card Entry</h4>
                    @if(auth()->id() == 1 || auth()->user()->can('create job-card'))
                    <a class="btn btn-primary" href="{{ url('job_card_entries/add') }}">
                        <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                    </a>
                    @endif
                </div>
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="filter-box mb-4">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <h5>Filter</h5>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter_brand" class="form-select select2" data-placeholder="Select Brand">
                                        <option value=""></option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter_season" class="form-select select2" data-placeholder="Select Season">
                                        <option value=""></option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}">{{ $season->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter_job_card_type" class="form-select select2" data-placeholder="Select Job Card Type">
                                        <option value=""></option>
                                        <option value="Regular">Regular</option>
                                        <option value="Urgent">Urgent</option>
                                        <option value="Sample">Sample</option>
                                        <option value="Special Order">Special Order</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter_status" class="form-select select2" data-placeholder="Select Status">
                                        <option value=""></option>
                                        <option value="Hold">Hold</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="filter_btn">Filter</button>
                                    <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-datatable table-responsive">
                            <table class="table datatables-jc">
                                <thead>
                                    <tr>
                                         <th>#</th>
                                        <th>Job Card No</th>
                                        <th>Date</th>
                                        <th>Plant</th>
                                        <th>Brand</th>
                                        <th>Season</th>
                                        <th>Process Group</th>
                                        <th>Total Meter</th>
                                        <th>Total QTY</th>
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

    <script>
        $(document).ready(function () {
            let table = $('.datatables-jc').DataTable({
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
                    url: "{{ url('job_card_entries') }}",
                    data: function(d) {
                        d.brand_id = $('#filter_brand').val();
                        d.season_id = $('#filter_season').val();
                        d.job_card_type = $('#filter_job_card_type').val();
                        d.status = $('#filter_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'job_card_no' },
                    { data: 'job_card_date' },
                    { data: 'plant' },
                    { data: 'brand' },
                    { data: 'season' },
                    { data: 'process_group' },
                    { data: 'total_meter' },
                    { data: 'total_qty' },
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

            $('#filter_btn').on('click', function() {
                table.ajax.reload();
            });

            $('#reset_btn').on('click', function() {
                $('#filter_brand').val('').trigger('change');
                $('#filter_season').val('').trigger('change');
                $('#filter_job_card_type').val('').trigger('change');
                $('#filter_status').val('').trigger('change');
                table.ajax.reload();
            });
        });
    </script>
@endsection