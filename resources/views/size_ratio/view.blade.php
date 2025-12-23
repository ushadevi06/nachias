@extends('layouts.common')
@section('title', 'Size/Ratio - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Size/Ratio</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create size-ratio'))

                <a class="btn btn-primary" href="{{ url('size_ratio/add') }}">
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
                        <table class="table sizeratioTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Size</th>
                                    <th>Ratio</th>
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

        $('.sizeratioTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('size_ratio') }}",
                type: "GET",
                dataSrc: 'data'
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'size'
                },
                {
                    data: 'ratio'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ]
        });

        $(document).on('change', '.size-ratio-status-toggle', function() {
            let ratioId = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('size_ratio/status') }}/" + ratioId,
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

                        $('.status_msg_' + ratioId).html(msg).fadeIn().delay(1200).fadeOut();
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