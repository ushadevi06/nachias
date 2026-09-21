@extends('layouts.common')
@section('title', 'Logs & Audit Log - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Logs & Audit Log</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label font-weight-semibold">Date Range</label>
                                <input type="text" id="date_range" class="form-control" placeholder="Select Date Range">
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <button type="button" id="filterBtn" class="btn btn-primary me-2"><i class="icon-base ri ri-filter-3-line me-1"></i>Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary"><i class="icon-base ri ri-refresh-line me-1"></i>Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="logs-table table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Action Type</th>
                                    <th>Employee</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>Date & Time</th>
                                    <th>Action</th>
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
        $('#date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        let table = $('.logs-table').DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: "{{ url('logs') }}",
                data: function(d) {
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action_type'
                },
                {
                    data: 'user_name'
                },
                {
                    data: 'module'
                },
                {
                    data: 'description'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#date_range').val('');
            table.ajax.reload();
        });
    });
</script>
@endsection