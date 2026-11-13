@extends('layouts.common')
@section('title', 'Open Stock Entry Logs - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <!-- Header matching standard Nachias style -->
            <div class="table-header-box d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Open Stock Entry Logs</h4>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('fg_stock_taking/open-stock-entry') }}" class="btn btn-outline-success">
                        <i class="ri ri-inbox-archive-line me-1"></i> Add Open Stock Entry
                    </a>
                    <button type="button" id="btn_export_all_excel" class="btn btn-success">
                        <i class="ri ri-file-excel-line me-1"></i> Export Excel
                    </button>
                    <a href="{{ url('fg_stock_taking') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Stock Taking
                    </a>
                </div>
            </div>

            <!-- Standard Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Standard Filter Box -->
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>

                            <div class="col-md-3">
                                <select id="filter_brand_id" class="form-select select2" data-placeholder="All Brands">
                                    <option value="" selected>All Brands</option>
                                    @foreach($allBrands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select id="filter_warehouse_id" class="form-select select2" data-placeholder="All Warehouses">
                                    <option value="" selected>All Warehouses</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" id="filter_from_date" class="form-control" placeholder="From Date">
                            </div>

                            <div class="col-md-2">
                                <input type="date" id="filter_to_date" class="form-control" placeholder="To Date">
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="button" id="btn_filter" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="btn_reset" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Datatable -->
                    <div class="card-datatable table-responsive">
                        <table class="open-stock-logs-table table">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Entry No</th>
                                    <th>Brand</th>
                                    <th>Product Name</th>
                                    <th>Art No</th>
                                    <th>Style</th>
                                    <th>Size</th>
                                    <th>Sleeve</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Store Location</th>
                                    <th>Warehouse</th>
                                    <th>SKU / Barcode</th>
                                    <th>Logged By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function () {
    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
    }

    if ($.fn.select2) {
        $('#filter_brand_id, #filter_warehouse_id').select2({
            width: '100%',
            allowClear: true
        });
    }

    const table = $('.open-stock-logs-table').DataTable({
        responsive: false,
        paging: true,
        autoWidth: false,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        ajax: {
            url: "{{ url('fg_stock_taking/open-stock-logs-data') }}",
            data: function (d) {
                d.brand_id = $('#filter_brand_id').val();
                d.warehouse_id = $('#filter_warehouse_id').val();
                d.from_date = $('#filter_from_date').val();
                d.to_date = $('#filter_to_date').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'entry_date',
                name: 'entry_date'
            },
            {
                data: 'entry_no',
                name: 'entry_no',
                render: function (data) {
                    return `<strong>${escapeHtml(data)}</strong>`;
                }
            },
            {
                data: 'brand_name',
                name: 'brand_id'
            },
            {
                data: 'product_name',
                name: 'product_name',
                render: function (data, type, row) {
                    const rem = row.remarks && row.remarks !== '-' ? `<br><small class="text-muted">${escapeHtml(row.remarks)}</small>` : '';
                    return `<strong>${escapeHtml(data)}</strong>${rem}`;
                }
            },
            {
                data: 'art_no',
                name: 'art_no'
            },
            {
                data: 'style_name',
                name: 'style_name',
                orderable: false
            },
            {
                data: 'size',
                name: 'size'
            },
            {
                data: 'sleeve_type',
                name: 'sleeve_type'
            },
            {
                data: 'qty',
                name: 'qty',
                render: function (data) {
                    return `<strong>${Number(data || 0).toLocaleString()}</strong>`;
                }
            },
            {
                data: 'price',
                name: 'price',
                render: function (data) {
                    return data !== '-' ? `₹${data}` : '-';
                }
            },
            {
                data: 'store_location',
                name: 'store_location',
                orderable: false
            },
            {
                data: 'warehouse',
                name: 'warehouse',
                orderable: false
            },
            {
                data: 'sku',
                name: 'sku'
            },
            {
                data: 'created_by',
                name: 'created_by',
                orderable: false,
                render: function (data, type, row) {
                    return `${escapeHtml(data)}<br><small class="text-muted">${escapeHtml(row.created_at)}</small>`;
                }
            },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-log" data-id="${row.id}" data-entry-no="${escapeHtml(row.entry_no)}" data-sku="${escapeHtml(row.sku)}" title="Delete Log Entry">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#btn_filter').on('click', function () {
        table.ajax.reload();
    });

    $('#btn_reset').on('click', function () {
        $('#filter_brand_id').val('').trigger('change.select2');
        $('#filter_warehouse_id').val('').trigger('change.select2');
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        table.search('').draw();
    });

    $('#btn_export_all_excel').on('click', function () {
        const params = new URLSearchParams({
            brand_id: $('#filter_brand_id').val() || '',
            warehouse_id: $('#filter_warehouse_id').val() || '',
            from_date: $('#filter_from_date').val() || '',
            to_date: $('#filter_to_date').val() || '',
            search: table.search() || ''
        });
        window.location.href = "{{ url('fg_stock_taking/open-stock-logs/export') }}?" + params.toString();
    });

    $(document).on('click', '.btn-delete-log', function () {
        const id = $(this).data('id');
        const entryNo = $(this).data('entry-no');
        const sku = $(this).data('sku');

        Swal.fire({
            title: 'Delete Log Item?',
            html: `Are you sure you want to remove item <strong>${escapeHtml(sku)}</strong> from Entry <strong>${escapeHtml(entryNo)}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#8592a3',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('fg_stock_taking/open-stock-logs') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message || 'Log item deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Could not delete item.', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
