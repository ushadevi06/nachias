@extends('layouts.common')
@section('title', 'Sales Agents - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="table-header-box">
                <h4>Sales Agents</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create sales-agents'))
                <a class="btn btn-primary" href="{{ url('sales_agents/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">

                    <!-- ✅ FILTER -->
                    <div class="filter-box mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="agent_type" id="agent_type" class="form-select select2">
                                    <option value="">Select Agent Type</option>
                                    <option value="Direct Sales Agent">Direct Sales Agent</option>
                                    <option value="Commission Agent">Commission Agent</option>
                                    <option value="Export Agent">Export Agent</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="filter-btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ DATATABLE -->
                    <div class="card-datatable">
                        <table class="datatables-sales-agents table" id="sales-agents-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Agent Type</th>
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
    $(document).ready(function() {

        var table = $('#sales-agents-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('sales_agents') }}",
                type: "GET",
                data: function(d) {
                    d.agent_type = $('#agent_type').val();
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
                    data: 'agent_type'
                },
                {
                    data: 'contact_info'
                }, // ✅ EMAIL + PHONE IN ONE COLUMN
                {
                    data: 'location'
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

        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });

        $('#reset-btn').on('click', function() {
            $('#agent_type').val('').trigger('change');
            table.ajax.reload();
        });

        $('#agent_type').on('change', function() {
            table.ajax.reload();
        });

        // ✅ STATUS TOGGLE AJAX
        $(document).on('change', '.sales-agent-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('sales_agent/status') }}/" + id,
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