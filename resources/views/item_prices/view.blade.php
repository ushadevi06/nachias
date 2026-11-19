@extends('layouts.common')
@section('title', 'Item Prices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Item Prices</h4>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-success" id="btn-export-excel" href="{{ url('item_prices/export-excel') }}">
                        <i class="icon-base ri ri-file-excel-line"></i> Export
                    </a>
                    @if(auth()->id() == 1 || auth()->user()->can('create item-prices'))
                    <button type="button" class="btn btn-secondary" id="btn-item-prices-import" data-bs-toggle="modal" data-bs-target="#importItemPricesModal">
                        <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                    </button>
                    @endif
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
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="date_range" class="form-control" placeholder="Select Effective Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary text-uppercase">FILTER</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary text-uppercase ms-1">RESET</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="itemPriceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Art No</th>
                                    <th>MRP</th>
                                    <th>Selling Price</th>
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

<!-- Import Modal -->
<div class="modal fade" id="importItemPricesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Item Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('item_prices/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Upload File (CSV, Excel)</label>
                        <input class="form-control" type="file" id="import_file" name="import_file"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    </div>
                    <div class="mb-2">
                        <a href="{{ url('item_prices/download-sample') }}" class="btn btn-sm btn-outline-info">
                            <i class="icon-base ri ri-download-2-line"></i> Download Sample Format
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(function() {
        // Ensure modal opens even if some scripts stop Bootstrap's data-api handlers
        $(document).on('click', '#btn-item-prices-import', function() {
            const el = document.getElementById('importItemPricesModal');
            if (!el || typeof bootstrap === 'undefined' || !bootstrap.Modal) return;
            bootstrap.Modal.getOrCreateInstance(el).show();
        });

        $('#date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        var table = $('#itemPriceTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: {
                url: "{{ url('item_prices') }}",
                data: function(d) {
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'item_name'
                },
                {
                    data: 'art_no'
                },
                {
                    data: 'selling_price'
                },
                {
                    data: 'unit_price'
                },
                {
                    data: 'effective_from'
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

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#resetBtn').on('click', function() {
            $('#date_range').val('');
            table.ajax.reload();
        });

        $('#btn-export-excel').on('click', function(e) {
            e.preventDefault();
            let url = "{{ url('item_prices/export-excel') }}";
            let dr = $('#date_range').val();
            if (dr) {
                url += '?date_range=' + encodeURIComponent(dr);
            }
            window.location.href = url;
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