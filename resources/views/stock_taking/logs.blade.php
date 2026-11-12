@extends('layouts.common')
@section('title', 'Stock Taking Logs - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Stock Taking Logs</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('fg_stock_taking') }}" class="btn btn-primary text-white">
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
                                <select id="history_status_filter" class="form-select select2" data-placeholder="Filter Status">
                                    <option value="all" selected>All Status</option>
                                    <option value="finalized">Finalized</option>
                                    <option value="in_progress">In Progress</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 d-flex gap-2">
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
<script>
$(function () {
    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
    }

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
                d.status = $('#history_status_filter').val() || 'all';
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
                    return `
                        <div class="d-flex gap-1 text-nowrap">
                            <a href="${escapeHtml(row.view_url)}" class="btn btn-sm btn-outline-primary">
                                <i class="ri ri-eye-line me-1"></i> View
                            </a>
                            <a href="${escapeHtml(row.export_url)}" class="btn btn-sm btn-outline-success" title="Export Excel">
                                <i class="ri ri-file-excel-line me-1"></i> Excel
                            </a>
                        </div>
                    `;
                }
            }
        ]
    });

    $('#history_status_filter').on('change', function () {
        table.ajax.reload();
    });

    $('#logs_reset_btn').on('click', function () {
        $('#history_status_filter').val('all').trigger('change.select2');
        table.search('').draw();
    });
});
</script>
@endsection
