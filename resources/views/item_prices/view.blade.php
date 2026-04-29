@extends('layouts.common')
@section('title', 'Item Prices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Item Prices</h4>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-success" href="{{ url('item_prices/export-excel') }}">
                        <i class="icon-base ri ri-file-excel-line"></i> Export
                    </a>
                    @if(auth()->id() == 1 || auth()->user()->can('create item-prices'))
                    <a class="btn btn-primary" href="{{ url('item_prices/add') }}">
                        <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="itemPriceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Art No</th>
                                    <th>Selling Price</th>
                                    <th>Unit Price</th>
                                    <th>Effective From</th>
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
    $(function() {
        $('#itemPriceTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: "{{ url('item_prices') }}",
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'item_name' },
                { data: 'art_no' },
                { data: 'selling_price' },
                { data: 'unit_price' },
                { data: 'effective_from' },
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

        $(document).on('change', '.status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('item_prices/status') }}/" + id,
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
