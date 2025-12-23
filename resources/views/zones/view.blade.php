@extends('layouts.common')
@section('title', 'Zones - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4 class="mb-0">Zones</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create zones'))
                <a class="btn btn-primary" href="{{ url('zones/add') }}">
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
                    <div class="card-datatable">
                        <table class="table" id="zonesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Zone Name</th>
                                    <th>State</th>
                                    <th>Cities</th>
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
        // Initialize DataTable
        let table = $('#zonesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('zones') }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'zone_name'
                },
                {
                    data: 'state_name'
                },
                {
                    data: 'city_names'
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

        // Handle status toggle
        $(document).on('change', '.zone-status-toggle', function() {
            let zoneId = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('zones/status') }}/" + zoneId,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        let msg = response.status === 'Active' ?
                            '<span class="text-success">Activated</span>' :
                            '<span class="text-danger">Deactivated</span>';

                        $('.status_msg_' + zoneId).html(msg).fadeIn().delay(1200).fadeOut();
                    } else {
                        alert('Status update failed');
                    }
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });
    });
</script>
@endsection