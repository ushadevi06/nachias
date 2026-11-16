@extends('layouts.common')
@section('title', 'Production Receipts - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4>Production Receipts</h4>
            <div class="d-flex gap-2">
                @if(auth()->id() == 1 || auth()->user()->can('view production-receipt-report'))
                <a href="{{ url('production_receipts/report') }}" class="btn btn-outline-info">
                    <i class="menu-icon icon-base ri ri-file-list-3-line me-1"></i> Report
                </a>
                @endif
                <a href="{{ url('production_receipts/export-excel') }}" class="btn btn-outline-success">
                    <i class="menu-icon icon-base ri ri-file-excel-line me-1"></i> Export
                </a>
                @if(auth()->id() == 1 || auth()->user()->can('create production-receipts'))
                <a href="{{ url('production_receipts/add') }}" class="btn btn-primary">
                    <i class="menu-icon icon-base ri ri-add-circle-line me-1"></i> Add
                </a>
                @endif
            </div>
        </div>

        <div class="col-lg-12">
            @include('flash_messages')
            
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="warehouse_id" id="warehouse_id" class="form-select select2" data-placeholder="Select Warehouse">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses ?? [] as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="store_type_id" id="store_type_id" class="form-select select2" data-placeholder="Select Store">
                                    <option value="">Select Store</option>
                                    @foreach($storeTypes ?? [] as $st)
                                        <option value="{{ $st->id }}">{{ $st->store_type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="date_range" class="form-control" placeholder="Select Receipt Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary text-uppercase">FILTER</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary text-uppercase ms-1">RESET</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table" id="production-receipts-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card No</th>
                                    <th>Receipt Date</th>
                                    <th>Warehouse</th>
                                    <th>Store</th>
                                    <th>Store Location</th>
                                    <th>Responsible Person</th>
                                    <th>Total Qty</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

<!-- Modal Pending Reason Breakdown -->
<div class="modal fade" id="modalPendingReasonBreakdown" tabindex="-1" aria-labelledby="modalPendingReasonTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalPendingReasonTitle">
                    <i class="ri ri-error-warning-fill text-warning fs-4"></i>
                    <span>Pending Inward Reasons &bull; <strong class="text-primary" id="pr_modal_jc_no">-</strong></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <!-- KPI Summary -->
                <div class="row g-2 mb-3 text-center">
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Job Planned Qty</small>
                            <span class="fs-6 fw-bold text-dark" id="pr_modal_planned">0</span> pcs
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Received Qty</small>
                            <span class="fs-6 fw-bold text-success" id="pr_modal_received">0</span> pcs
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Balance Pending</small>
                            <span class="fs-6 fw-bold text-danger" id="pr_modal_pending">0</span> pcs
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Allocated Reason Qty</small>
                            <span class="fs-6 fw-bold text-primary" id="pr_modal_allocated">0</span> pcs
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning py-2 px-3 small d-flex align-items-center justify-content-between mb-3" id="pr_modal_alert_balance">
                    <div>
                        <i class="ri ri-information-line me-1"></i>
                        Unallocated Pending Qty: <strong id="pr_modal_unallocated" class="fs-6">0</strong> pcs
                    </div>
                    <button type="button" class="btn btn-sm btn-primary py-1 px-3 text-uppercase" id="btn_pr_add_reason_row">
                        <i class="ri ri-add-line me-1"></i> Add More Reason
                    </button>
                </div>

                <form id="form_pending_reasons">
                    <input type="hidden" id="pr_modal_job_card_id" value="">
                    <input type="hidden" id="pr_modal_receipt_id" value="">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="table_pr_reasons">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th style="min-width: 220px;">Reason <span class="text-danger">*</span></th>
                                    <th style="width: 150px;">Qty (Pcs) <span class="text-danger">*</span></th>
                                    <th style="min-width: 180px;">Remarks (Optional)</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_pr_reasons">
                                <!-- dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small" id="pr_modal_status_hint">
                    Select reasons (Damaged, Alteration, Spl. Order Dispatch, QC Hold) and enter quantities.
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary text-uppercase btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary text-uppercase btn-sm px-4" id="btn_pr_save_reasons">
                        <i class="ri ri-save-line me-1"></i> Save Reasons
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        var table = $('#production-receipts-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('production_receipts') }}',
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.store_type_id = $('#store_type_id').val();
                    d.date_range = $('#date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'job_card_no', name: 'job_card_no' },
                { data: 'receipt_date', name: 'receipt_date' },
                { data: 'warehouse', name: 'warehouse' },
                { data: 'store', name: 'store' },
                { data: 'store_location', name: 'store_location' },
                { data: 'responsible_person', name: 'responsible_person' },
                { data: 'total_qty', name: 'total_qty' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'asc']]
        });

        $('#filterBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#resetBtn').on('click', function() {
            $('#warehouse_id').val('').trigger('change');
            $('#store_type_id').val('').trigger('change');
            $('#date_range').val('');
            table.ajax.reload();
        });

        // ==========================================
        // Pending Reasons Management Logic
        // ==========================================
        const allowedReasons = ['Damaged', 'Alteration', 'Spl. Order Dispatch', 'QC Hold', 'Other'];
        let currentJobCardPendingQty = 0;

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
        }

        function buildReasonRow(reasonVal = '', qtyVal = '', remarksVal = '') {
            const optionsHtml = allowedReasons.map(r =>
                `<option value="${escapeHtml(r)}" ${r === reasonVal ? 'selected' : ''}>${escapeHtml(r)}</option>`
            ).join('');

            return `
                <tr class="pr-reason-row">
                    <td>
                        <select class="form-select form-select-sm pr-reason-select" required>
                            <option value="">-- Select Reason --</option>
                            ${optionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end pr-reason-qty" min="0.01" step="any" value="${qtyVal}" placeholder="e.g. 10" required>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm pr-reason-remarks" value="${escapeHtml(remarksVal || '')}" placeholder="Optional note">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger p-1 btn-pr-remove-reason" title="Remove row">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        function recalculateModalQuantities() {
            let totalAllocated = 0;
            $('#tbody_pr_reasons .pr-reason-qty').each(function() {
                const q = parseFloat($(this).val()) || 0;
                totalAllocated += q;
            });

            totalAllocated = Math.round(totalAllocated * 100) / 100;
            const unallocated = Math.max(0, Math.round((currentJobCardPendingQty - totalAllocated) * 100) / 100);

            $('#pr_modal_allocated').text(totalAllocated.toLocaleString());
            $('#pr_modal_unallocated').text(unallocated.toLocaleString());

            if (totalAllocated > currentJobCardPendingQty) {
                $('#pr_modal_allocated').removeClass('text-primary').addClass('text-danger');
                $('#pr_modal_alert_balance').removeClass('alert-warning alert-success').addClass('alert-danger');
                $('#pr_modal_status_hint').html('<span class="text-danger fw-bold"><i class="ri ri-error-warning-line me-1"></i>Allocated reason quantity exceeds balance pending qty!</span>');
                $('#btn_pr_save_reasons').prop('disabled', true);
            } else if (totalAllocated === currentJobCardPendingQty && currentJobCardPendingQty > 0) {
                $('#pr_modal_allocated').removeClass('text-danger').addClass('text-success');
                $('#pr_modal_alert_balance').removeClass('alert-danger alert-warning').addClass('alert-success');
                $('#pr_modal_status_hint').html('<span class="text-success fw-bold"><i class="ri ri-checkbox-circle-line me-1"></i>100% of pending quantity fully allocated!</span>');
                $('#btn_pr_save_reasons').prop('disabled', false);
            } else {
                $('#pr_modal_allocated').removeClass('text-danger text-success').addClass('text-primary');
                $('#pr_modal_alert_balance').removeClass('alert-danger alert-success').addClass('alert-warning');
                $('#pr_modal_status_hint').text('You can allocate the remaining ' + unallocated + ' pcs across reasons.');
                $('#btn_pr_save_reasons').prop('disabled', false);
            }
        }

        $(document).on('click', '.btn-pending-reason', function(e) {
            e.preventDefault();
            const jcId = $(this).data('job-card-id');
            const receiptId = $(this).data('receipt-id');
            const jcNo = $(this).data('job-card-no') || '';

            if (!jcId) return;

            $('#pr_modal_job_card_id').val(jcId);
            $('#pr_modal_receipt_id').val(receiptId || '');
            $('#pr_modal_jc_no').text(jcNo);
            $('#tbody_pr_reasons').html('<tr><td colspan="4" class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading pending reasons...</td></tr>');

            const modalEl = document.getElementById('modalPendingReasonBreakdown');
            const modalObj = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalObj.show();

            $.get('{{ url("production_receipts/pending-reasons") }}/' + jcId, function(res) {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Unable to load pending reasons.', 'error');
                    modalObj.hide();
                    return;
                }

                currentJobCardPendingQty = Number(res.pending_qty || 0);
                $('#pr_modal_planned').text(Number(res.planned_qty || 0).toLocaleString());
                $('#pr_modal_received').text(Number(res.received_qty || 0).toLocaleString());
                $('#pr_modal_pending').text(currentJobCardPendingQty.toLocaleString());

                const saved = res.saved_reasons || [];
                let rowsHtml = '';
                if (saved.length > 0) {
                    saved.forEach(function(r) {
                        rowsHtml += buildReasonRow(r.reason, r.qty, r.remarks);
                    });
                } else {
                    rowsHtml = buildReasonRow('Damaged', currentJobCardPendingQty > 0 ? currentJobCardPendingQty : '', '');
                }

                $('#tbody_pr_reasons').html(rowsHtml);
                recalculateModalQuantities();
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error loading details.', 'error');
                modalObj.hide();
            });
        });

        $('#btn_pr_add_reason_row').on('click', function() {
            let totalAllocated = 0;
            $('#tbody_pr_reasons .pr-reason-qty').each(function() {
                totalAllocated += (parseFloat($(this).val()) || 0);
            });
            const unallocated = Math.max(0, Math.round((currentJobCardPendingQty - totalAllocated) * 100) / 100);
            const defaultQty = unallocated > 0 ? unallocated : '';
            $('#tbody_pr_reasons').append(buildReasonRow('Alteration', defaultQty, ''));
            recalculateModalQuantities();
        });

        $(document).on('click', '.btn-pr-remove-reason', function() {
            if ($('#tbody_pr_reasons .pr-reason-row').length <= 1) {
                $('#tbody_pr_reasons .pr-reason-row').find('input').val('');
                $('#tbody_pr_reasons .pr-reason-row').find('select').val('');
            } else {
                $(this).closest('tr').remove();
            }
            recalculateModalQuantities();
        });

        $(document).on('input change', '.pr-reason-qty', function() {
            recalculateModalQuantities();
        });

        $('#btn_pr_save_reasons').on('click', function(e) {
            e.preventDefault();
            const jcId = $('#pr_modal_job_card_id').val();
            const receiptId = $('#pr_modal_receipt_id').val();
            if (!jcId) return;

            const reasons = [];
            let hasError = false;

            $('#tbody_pr_reasons .pr-reason-row').each(function(idx) {
                const reason = $(this).find('.pr-reason-select').val();
                const qty = parseFloat($(this).find('.pr-reason-qty').val()) || 0;
                const remarks = $(this).find('.pr-reason-remarks').val();

                if (!reason && qty > 0) {
                    $(this).find('.pr-reason-select').addClass('is-invalid');
                    hasError = true;
                } else {
                    $(this).find('.pr-reason-select').removeClass('is-invalid');
                }

                if (reason && qty <= 0) {
                    $(this).find('.pr-reason-qty').addClass('is-invalid');
                    hasError = true;
                } else {
                    $(this).find('.pr-reason-qty').removeClass('is-invalid');
                }

                if (reason && qty > 0) {
                    reasons.push({
                        reason: reason,
                        qty: qty,
                        remarks: remarks
                    });
                }
            });

            if (hasError) {
                Swal.fire('Validation Error', 'Please select a reason and enter a valid quantity for each row.', 'warning');
                return;
            }

            let totalAllocated = reasons.reduce((sum, r) => sum + r.qty, 0);
            if (totalAllocated > currentJobCardPendingQty) {
                Swal.fire('Limit Exceeded', `Total reason quantity (${totalAllocated} pcs) cannot exceed pending quantity (${currentJobCardPendingQty} pcs).`, 'error');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: '{{ url("production_receipts/pending-reasons") }}/' + jcId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    reasons: reasons,
                    production_receipt_id: receiptId
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="ri ri-save-line me-1"></i> Save Reasons');
                    if (res.success) {
                        const modalEl = document.getElementById('modalPendingReasonBreakdown');
                        const modalObj = bootstrap.Modal.getInstance(modalEl);
                        if (modalObj) modalObj.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: res.message || 'Pending reasons updated successfully.',
                            timer: 1600,
                            showConfirmButton: false
                        });

                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Unable to save pending reasons.', 'error');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="ri ri-save-line me-1"></i> Save Reasons');
                    Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred while saving.', 'error');
                }
            });
        });
    });
</script>
@endsection
