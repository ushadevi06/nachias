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
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modal_import_white_stock">
                        <i class="ri ri-file-upload-line me-1"></i> Import Stock Excel (White / Dhoti)
                    </button>
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

                            <div class="col-md-3">
                                <input type="text" id="filter_date_range" class="form-control" placeholder="Select Date Range">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" id="btn_filter" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="btn_reset" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Datatable -->
                    <div class="card-datatable table-responsive">
                        <table class="open-stock-logs-table table nowrap">
                            <thead class="table">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Date</th>
                                    <th>Entry No</th>
                                    <th>Brand</th>
                                    <th>Warehouse</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-end">Total Qty</th>
                                    <th class="text-end">Total Amount</th>
                                    <th>Store Location</th>
                                    <th>Remarks</th>
                                    <th>Logged By</th>
                                    <th class="text-center" style="min-width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <th colspan="6" class="text-end">Total:</th>
                                    <th class="text-end" id="footer-total-qty">0</th>
                                    <th class="text-end" id="footer-total-amount">₹0.00</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Store Locations Modal -->
<div class="modal fade" id="modalLogsStoreLocations" tabindex="-1" aria-labelledby="modalLogsStoreLocationsTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalLogsStoreLocationsTitle">
                    <i class="ri ri-map-pin-2-fill text-primary fs-4"></i>
                    <span>Store Locations</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between p-2 bg-light rounded">
                    <div>
                        <small class="text-muted d-block">Entry No</small>
                        <strong id="modal_logs_loc_title" class="text-dark fs-6">-</strong>
                    </div>
                    <div>
                        <span class="badge bg-primary text-white" id="modal_logs_loc_total_badge">0 Locations</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 p-1" id="modal_logs_loc_badges_container" style="max-height: 350px; overflow-y: auto;">
                    <!-- locations list -->
                </div>
            </div>
            <div class="modal-footer py-2 border-top">
                <button type="button" class="btn btn-secondary text-uppercase btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Stock Excel Matrix Modal (White / Dhoti) -->
<div class="modal fade" id="modal_import_white_stock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ url('fg_stock_taking/import-white-stock-matrix') }}" method="POST" enctype="multipart/form-data" class="modal-content shadow">
            @csrf
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold text-white mb-0">
                    <i class="ri ri-file-excel-line me-1"></i> Import Stock Excel Matrix (White / Dhoti)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="ri ri-information-line me-1"></i> Upload the matrix stock sheet (e.g. <code>white stock.xlsx</code> or <code>Dhoti Stock.xls</code> with columns: Item Name, Sleeve / S/L, 36, 38, 40, 42, 44, 46, 48).
                    It will auto-map products, resolve WSP/MRP prices, and attach standard barcodes.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Brand <span class="text-danger">*</span></label>
                    <select name="brand_id" id="modal_import_brand_id" class="form-select" required>
                        <option value="10" selected>CASINO DHOTI SET (CDS)</option>
                        <option value="7">CASINO WHITE (CW)</option>
                    </select>
                    <small class="text-muted">Choose <strong>CASINO DHOTI SET</strong> for Dhoti Stock, or <strong>CASINO WHITE</strong> for White Stock.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Excel File (.xlsx, .xls) <span class="text-danger">*</span></label>
                    <input type="file" name="matrix_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Warehouse <span class="text-danger">*</span></label>
                    <select name="warehouse_id" class="form-select" required>
                        @foreach(\App\Models\Warehouse::whereNull('deleted_at')->get() as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Store Type <span class="text-danger">*</span></label>
                    <select name="store_type_id" class="form-select" required>
                        @foreach(\App\Models\StoreType::whereNull('deleted_at')->where('store_type_name', 'like', '%FINISHED%')->get() as $st)
                            <option value="{{ $st->id }}" selected>{{ $st->store_type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="ri ri-upload-cloud-line me-1"></i> Upload & Import
                </button>
            </div>
        </form>
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

    if ($.fn.flatpickr) {
        $('#filter_date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true,
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    instance.setDate([selectedDates[0], selectedDates[0]], true);
                }
            }
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
            url: "{{ url('fg_stock_taking/open-stock-logs') }}",
            data: function (d) {
                d.brand_id = $('#filter_brand_id').val();
                d.warehouse_id = $('#filter_warehouse_id').val();
                d.date_range = $('#filter_date_range').val();
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
                data: 'warehouse',
                name: 'warehouse',
                orderable: false
            },
            {
                data: 'total_items',
                name: 'total_items',
                className: 'text-center',
                render: function (data) {
                    return `<span class="badge bg-label-info">${Number(data || 0).toLocaleString()} items</span>`;
                }
            },
            {
                data: 'total_qty',
                name: 'total_qty',
                className: 'text-end',
                render: function (data) {
                    return `<strong>${Number(data || 0).toLocaleString()}</strong>`;
                }
            },
            {
                data: 'total_amount',
                name: 'total_amount',
                className: 'text-end',
                render: function (data) {
                    return `₹${Number(data || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
            },
            {
                data: 'store_location',
                name: 'store_location',
                orderable: false,
                render: function (data, type, row) {
                    if (data && data !== '-') {
                        const parts = String(data).split(',').map(s => s.trim()).filter(Boolean);
                        if (parts.length === 0) {
                            return '<span class="text-muted">-</span>';
                        }
                        if (parts.length <= 3) {
                            return parts.map(p => `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(p)}</span>`).join('');
                        }
                        const firstThree = parts.slice(0, 3).map(p => `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(p)}</span>`).join('');
                        const remainingCount = parts.length - 3;
                        const allLocsEscaped = escapeHtml(JSON.stringify(parts));
                        const entryTitle = escapeHtml(row.entry_no || 'Entry Locations');
                        const moreBtn = `<button type="button" class="btn btn-xs btn-outline-primary btn-show-more-locs px-2 py-0 align-middle mb-1" data-title="${entryTitle}" data-locations="${allLocsEscaped}" title="View all ${parts.length} locations" style="font-size: 0.72rem; line-height: 1.5; border-radius: 4px; font-weight: 600;">+${remainingCount} more</button>`;
                        return firstThree + moreBtn;
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'remarks',
                name: 'remarks',
                orderable: false,
                render: function (data) {
                    return data && data !== '-' ? `<span class="text-muted small">${escapeHtml(data)}</span>` : '-';
                }
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
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="button-box justify-content-center">
                            <a href="${escapeHtml(row.view_url)}" class="btn btn-view" title="View Details">
                                <i class="icon-base ri ri-eye-line"></i>
                            </a>
                            <a href="${escapeHtml(row.edit_url)}" class="btn btn-edit" title="Edit Entry">
                                <i class="icon-base ri ri-edit-box-line"></i>
                            </a>
                            <button type="button" class="btn btn-delete btn-delete-entry" data-entry-no="${escapeHtml(row.entry_no)}" data-count="${row.total_items}" title="Delete Entry">
                                <i class="icon-base ri ri-delete-bin-line"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let json = api.ajax.json();

            let intVal = function (i) {
                return typeof i === 'string'
                    ? parseFloat(i.replace(/[\₹,]/g, '')) || 0
                    : typeof i === 'number'
                    ? i
                    : 0;
            };

            if (json && typeof json.overallTotalQty !== 'undefined') {
                let totalQty = json.overallTotalQty || 0;
                let totalAmount = json.overallTotalAmount || 0;

                $('#footer-total-qty').html(Number(totalQty).toLocaleString('en-IN'));
                $('#footer-total-amount').html('₹' + Number(totalAmount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            } else {
                let pageTotalQty = api
                    .column(6, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                let pageTotalAmount = api
                    .column(7, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $('#footer-total-qty').html(Number(pageTotalQty).toLocaleString('en-IN'));
                $('#footer-total-amount').html('₹' + Number(pageTotalAmount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        }
    });

    $('#btn_filter').on('click', function () {
        table.ajax.reload();
    });

    $('#btn_reset').on('click', function () {
        $('#filter_brand_id').val('').trigger('change.select2');
        $('#filter_warehouse_id').val('').trigger('change.select2');
        $('#filter_date_range').val('');
        table.search('').draw();
    });

    $('#btn_export_all_excel').on('click', function () {
        const params = new URLSearchParams({
            brand_id: $('#filter_brand_id').val() || '',
            warehouse_id: $('#filter_warehouse_id').val() || '',
            date_range: $('#filter_date_range').val() || '',
            search: table.search() || ''
        });
        window.location.href = "{{ url('fg_stock_taking/open-stock-logs/export') }}?" + params.toString();
    });

    $(document).on('click', '.btn-delete-entry', function () {
        const entryNo = $(this).data('entry-no');
        const count = $(this).data('count') || '';

        Swal.fire({
            title: 'Delete Entry?',
            html: `Are you sure you want to delete Open Stock Entry <strong>${escapeHtml(entryNo)}</strong>${count ? ` (${count} items)` : ''}? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete Entry',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#8592a3',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('fg_stock_taking/open-stock-logs') }}/0",
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        entry_no: entryNo
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message || 'Entry deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Could not delete entry.', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-show-more-locs', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const title = $(this).data('title') || 'Store Locations';
        let locs = [];
        try {
            locs = JSON.parse($(this).attr('data-locations') || '[]');
        } catch (err) {
            locs = [];
        }

        $('#modal_logs_loc_title').text(title);
        $('#modal_logs_loc_total_badge').text(locs.length + (locs.length === 1 ? ' Location' : ' Locations'));

        const badgesHtml = locs.map(l =>
            `<span class="badge bg-light text-dark border p-2 fs-6"><i class="ri ri-map-pin-2-line text-primary me-1"></i>${escapeHtml(l)}</span>`
        ).join('');

        $('#modal_logs_loc_badges_container').html(badgesHtml || '<span class="text-muted">No locations found</span>');

        const locModalEl = document.getElementById('modalLogsStoreLocations');
        if (locModalEl) {
            const modalObj = bootstrap.Modal.getOrCreateInstance(locModalEl);
            modalObj.show();
        }
    });
});
</script>
@endsection
