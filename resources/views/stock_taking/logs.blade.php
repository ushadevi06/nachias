@extends('layouts.common')
@section('title', 'Stock Taking Logs - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Stock Taking Logs</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('fg_stock_taking') }}" class="btn btn-primary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Stock Taking
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select id="history_brand_filter" class="form-select select2" data-placeholder="Select Brand">
                                    <option value="">All Brands</option>
                                    @if(isset($brands))
                                        @foreach($brands as $b)
                                            <option value="{{ $b->id }}">{{ $b->brand_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select id="history_status_filter" class="form-select select2" data-placeholder="Filter Status">
                                    <option value="all" selected>All Status</option>
                                    <option value="finalized">Finalized</option>
                                    <option value="in_progress">In Progress</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 d-flex gap-2">
                                <button type="button" id="logs_filter_btn" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="logs_reset_btn" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable">
                        <table class="stock-taking-logs-table table">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Reference No</th>
                                    <th>Date</th>
                                    <th>Brand / Scope</th>
                                    <th>Counted By</th>
                                    <th>Finalized By</th>
                                    <th>System / Scanned / Var</th>
                                    <th>Status</th>
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

    let currentStatusFilter = 'all';
    let currentBrandFilter = '';

    const table = $('.stock-taking-logs-table').DataTable({
        responsive: true,
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
            url: "{{ url('fg_stock_taking/sessions') }}",
            data: function (d) {
                d.status = currentStatusFilter;
                d.brand_id = currentBrandFilter;
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
                data: 'session_no',
                name: 'session_no',
                render: function (data, type, row) {
                    return `<a href="${escapeHtml(row.view_url)}" class="fw-bold text-primary">${escapeHtml(data)}</a>`;
                }
            },
            {
                data: 'stock_taking_date',
                name: 'stock_taking_date'
            },
            {
                data: 'brand_name',
                name: 'brand_name',
                orderable: false,
                render: function (data, type, row) {
                    return `<span class="fw-semibold">${escapeHtml(data)}</span><br><small class="text-muted">${escapeHtml(row.store_type_name)} | ${escapeHtml(row.warehouse_name)}</small>`;
                }
            },
            {
                data: 'counted_by',
                name: 'counted_by',
                orderable: false
            },
            {
                data: 'finalized_by',
                name: 'finalized_by',
                orderable: false,
                render: function (data, type, row) {
                    const isFinalized = row.raw_status !== 'In Progress';
                    const sub = isFinalized && row.completed_at && row.completed_at !== '-'
                        ? `<br><small class="text-muted">${escapeHtml(row.completed_at)}</small>`
                        : '';
                    return `${escapeHtml(data)}${sub}`;
                }
            },
            {
                data: 'total_system_qty',
                name: 'total_system_qty',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const sysQty = Number(row.total_system_qty || 0);
                    const scanQty = Number(row.total_scanned_qty || 0);
                    const varQty = Number(row.variance_qty || 0);
                    const varClass = varQty < -0.001 ? 'text-danger' : (varQty > 0.001 ? 'text-warning' : 'text-success');
                    const varSign = varQty > 0.001 ? '+' : '';
                    return `<span class="small">Sys: <strong>${sysQty.toLocaleString()}</strong> | Scan: <strong>${scanQty.toLocaleString()}</strong> | Var: <strong class="${varClass}">${varSign}${varQty.toLocaleString()}</strong></span>`;
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row) {
                    return row.raw_status !== 'In Progress'
                        ? '<span class="badge bg-dark">Finalized</span>'
                        : '<span class="badge bg-primary">In Progress</span>';
                }
            },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const deleteBtn = row.raw_status === 'In Progress' ? `
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-session" data-id="${row.id}" data-session-no="${escapeHtml(row.session_no)}" title="Delete Session">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    ` : '';

                    return `
                        <div class="d-flex gap-1 text-nowrap">
                            <a href="${escapeHtml(row.view_url)}" class="btn btn-sm btn-outline-primary" title="View Session">
                                <i class="ri ri-eye-line"></i>
                            </a>
                            <a href="${escapeHtml(row.export_url)}" class="btn btn-sm btn-outline-success" title="Download Excel">
                                <i class="ri ri-file-excel-line"></i>
                            </a>
                            <a href="${escapeHtml(row.summary_pdf_url || (row.pdf_url + '?mode=summary'))}" target="_blank" class="btn btn-sm btn-outline-info" title="Stock Audit Summary PDF">
                                <i class="ri ri-file-chart-line"></i>
                            </a>
                            <a href="${escapeHtml(row.pdf_url)}" target="_blank" class="btn btn-sm btn-outline-danger" title="Detailed PDF Report">
                                <i class="ri ri-file-pdf-line"></i>
                            </a>
                            ${deleteBtn}
                        </div>
                    `;
                }
            }
        ]
    });

    $('#logs_filter_btn').on('click', function () {
        currentStatusFilter = $('#history_status_filter').val() || 'all';
        currentBrandFilter = $('#history_brand_filter').val() || '';
        table.ajax.reload();
    });

    $('#logs_reset_btn').on('click', function () {
        $('#history_status_filter').val('all').trigger('change.select2');
        $('#history_brand_filter').val('').trigger('change.select2');
        currentStatusFilter = 'all';
        currentBrandFilter = '';
        table.search('').draw();
    });

    $(document).on('click', '.btn-delete-session', function () {
        const id = $(this).data('id');
        const sessionNo = $(this).data('session-no');
        const url = "{{ url('fg_stock_taking/session') }}/" + id;

        Swal.fire({
            title: 'Delete Session?',
            text: `Are you sure you want to delete session "${sessionNo}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message || 'Session deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: res.message || 'Unable to delete session.'
                            });
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || 'Error deleting session.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
