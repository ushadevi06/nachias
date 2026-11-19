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
        <div class="modal-content">
            <div class="modal-header border-bottom py-2">
                <h5 class="modal-title fs-6" id="modalPendingReasonTitle">
                    Pending Inward Reasons &bull; <span id="pr_modal_jc_no" class="fw-semibold">-</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <!-- Simple Clean Summary -->
                <div class="p-2 mb-3 bg-light rounded border d-flex justify-content-between align-items-center flex-wrap gap-2 text-dark small">
                    <div>Job Planned: <strong id="pr_modal_planned">0</strong> pcs</div>
                    <div class="text-muted">|</div>
                    <div>Received: <strong id="pr_modal_received">0</strong> pcs</div>
                    <div class="text-muted">|</div>
                    <div>Pending: <strong id="pr_modal_pending">0</strong> pcs</div>
                    <div class="text-muted">|</div>
                    <div>Allocated: <strong id="pr_modal_allocated">0</strong> pcs</div>
                </div>

                <!-- Simple Header with Add Row button -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted">
                        Unallocated Qty: <strong id="pr_modal_unallocated" class="text-dark">0</strong> pcs
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn_pr_add_reason_row">
                        <i class="ri ri-add-line me-1"></i> Add Reason
                    </button>
                </div>

                <form id="form_pending_reasons">
                    <input type="hidden" id="pr_modal_job_card_id" value="">
                    <input type="hidden" id="pr_modal_receipt_id" value="">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0" id="table_pr_reasons">
                            <thead class="table-light">
                                <tr>
                                    <th>Reason *</th>
                                    <th style="width: 130px;">Qty (Pcs) *</th>
                                    <th>Remarks</th>
                                    <th style="width: 45px;" class="text-center">Action</th>
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
                <span class="text-muted small" id="pr_modal_status_hint"></span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btn_pr_save_reasons">
                        Save Reasons
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #modalPendingReasonBreakdown .select2-container {
        width: 100% !important;
    }
    #modalPendingReasonBreakdown .select2-container .select2-selection--single {
        height: 31px !important;
        font-size: 0.8125rem;
    }
    #modalPendingReasonBreakdown .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
    }
    #modalPendingReasonBreakdown .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px;
    }
    #modalPendingReasonBreakdown .select2.is-invalid + .select2-container .select2-selection {
        border-color: #dc3545 !important;
    }
</style>
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

        const allowedReasons = ['Damaged', 'Alteration', 'Spl. Order Dispatch', 'QC Hold', 'Other'];
        let currentJobCardPendingQty = 0;

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
        }

        function showFieldError($input, msg) {
            $input.addClass('is-invalid');
            const $container = $input.closest('td');
            let $err = $container.find('.field-error-text');
            if (!$err.length) {
                $err = $('<div class="field-error-text text-danger small mt-1" style="font-size: 0.75rem; line-height: 1.2;"></div>');
                $container.append($err);
            }
            $err.text(msg).show();
        }

        function clearFieldError($input) {
            $input.removeClass('is-invalid');
            const $container = $input.closest('td');
            $container.find('.field-error-text').text('').hide();
        }

        function buildReasonRow(reasonVal = '', qtyVal = '', remarksVal = '') {
            const optionsHtml = allowedReasons.map(r =>
                `<option value="${escapeHtml(r)}" ${r === reasonVal ? 'selected' : ''}>${escapeHtml(r)}</option>`
            ).join('');

            const formattedQty = (qtyVal !== '' && qtyVal !== null && !isNaN(qtyVal)) ? Math.round(Number(qtyVal)) : '';

            return `
                <tr class="pr-reason-row">
                    <td class="align-top">
                        <select class="select2 form-select form-select-sm pr-reason-select" required>
                            <option value="" ${!reasonVal ? 'selected disabled' : ''}>-- Select Reason --</option>
                            ${optionsHtml}
                        </select>
                        <div class="field-error-text text-danger small mt-1" style="display: none; font-size: 0.75rem; line-height: 1.2;"></div>
                    </td>
                    <td class="align-top">
                        <input type="text" inputmode="numeric" class="form-control form-control-sm text-end pr-reason-qty" value="${formattedQty}" placeholder="Enter Qty" required>
                        <div class="field-error-text text-danger small mt-1 text-end" style="display: none; font-size: 0.75rem; line-height: 1.2;"></div>
                    </td>
                    <td class="align-top">
                        <input type="text" class="form-control form-control-sm pr-reason-remarks" value="${escapeHtml(remarksVal || '')}" placeholder="Optional note">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-pr-remove-reason" title="Remove row">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        function recalculateModalQuantities() {
            let totalAllocated = 0;
            let hasEmptyQty = false;

            $('#tbody_pr_reasons .pr-reason-row').each(function() {
                const qtyVal = $(this).find('.pr-reason-qty').val().trim();
                if (qtyVal === '') {
                    hasEmptyQty = true;
                } else {
                    totalAllocated += (parseInt(qtyVal, 10) || 0);
                }
            });

            totalAllocated = Math.round(totalAllocated * 100) / 100;
            const unallocated = Math.max(0, Math.round((currentJobCardPendingQty - totalAllocated) * 100) / 100);

            $('#pr_modal_allocated').text(totalAllocated.toLocaleString());
            $('#pr_modal_unallocated').text(unallocated.toLocaleString());

            if (totalAllocated > currentJobCardPendingQty) {
                // Show error under the rows that exceed
                let runningTotal = 0;
                $('#tbody_pr_reasons .pr-reason-row').each(function() {
                    const $qtyEl = $(this).find('.pr-reason-qty');
                    const q = parseInt($qtyEl.val(), 10) || 0;
                    runningTotal += q;
                    if (runningTotal > currentJobCardPendingQty) {
                        showFieldError($qtyEl, `Exceeds pending qty (${currentJobCardPendingQty} pcs)`);
                    }
                });
                $('#pr_modal_status_hint').html('');
                $('#btn_pr_save_reasons').prop('disabled', true);
            } else if (hasEmptyQty) {
                $('#pr_modal_status_hint').html('');
                $('#btn_pr_save_reasons').prop('disabled', false);
            } else if (totalAllocated === currentJobCardPendingQty && currentJobCardPendingQty > 0) {
                $('#pr_modal_status_hint').html('<span class="text-success small fw-semibold"><i class="ri ri-check-line me-1"></i>Pending quantity fully allocated.</span>');
                $('#btn_pr_save_reasons').prop('disabled', false);
            } else {
                $('#pr_modal_status_hint').text('Remaining to allocate: ' + unallocated + ' pcs');
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
                    rowsHtml = buildReasonRow('', currentJobCardPendingQty > 0 ? currentJobCardPendingQty : '', '');
                }

                $('#tbody_pr_reasons').html(rowsHtml);
                initReasonSelect2($('#tbody_pr_reasons'));
                recalculateModalQuantities();
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error loading details.', 'error');
                modalObj.hide();
            });
        });

        function initReasonSelect2($container) {
            if ($.fn.select2) {
                const $targets = $container ? $container.find('.pr-reason-select') : $('#tbody_pr_reasons .pr-reason-select');
                $targets.each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            dropdownParent: $('#modalPendingReasonBreakdown'),
                            width: '100%',
                            placeholder: '-- Select Reason --'
                        });
                    }
                });
            }
        }

        $('#btn_pr_add_reason_row').on('click', function() {
            let totalAllocated = 0;
            $('#tbody_pr_reasons .pr-reason-qty').each(function() {
                totalAllocated += (parseInt($(this).val(), 10) || 0);
            });
            const unallocated = Math.max(0, currentJobCardPendingQty - totalAllocated);
            if (unallocated <= 0) {
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: `Pending qty (${currentJobCardPendingQty} pcs) is already 100% allocated.`,
                        showConfirmButton: false,
                        timer: 2200
                    });
                }
                return;
            }
            const defaultQty = unallocated > 0 ? unallocated : '';
            $('#tbody_pr_reasons').append(buildReasonRow('', defaultQty, ''));
            initReasonSelect2($('#tbody_pr_reasons'));
            recalculateModalQuantities();
        });

        $(document).on('click', '.btn-pr-remove-reason', function() {
            if ($('#tbody_pr_reasons .pr-reason-row').length <= 1) {
                $('#tbody_pr_reasons .pr-reason-row').find('input').val('');
                $('#tbody_pr_reasons .pr-reason-row').find('select').val('').trigger('change');
            } else {
                if ($.fn.select2) {
                    $(this).closest('tr').find('.pr-reason-select').select2('destroy');
                }
                $(this).closest('tr').remove();
            }
            recalculateModalQuantities();
        });

        $(document).on('change', '.pr-reason-select', function() {
            if ($(this).val()) {
                clearFieldError($(this));
            }
            recalculateModalQuantities();
        });

        $(document).on('keypress', '.pr-reason-qty', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
                return false;
            }
        });

        $(document).on('input', '.pr-reason-qty', function() {
            let $currentInput = $(this);
            let val = $currentInput.val().replace(/[^0-9]/g, '');

            let otherRowsTotal = 0;
            $('#tbody_pr_reasons .pr-reason-qty').not($currentInput).each(function() {
                otherRowsTotal += (parseInt($(this).val(), 10) || 0);
            });
            let maxAllowed = Math.max(0, currentJobCardPendingQty - otherRowsTotal);

            if (val !== '') {
                let num = parseInt(val, 10);
                if (num > maxAllowed) {
                    num = maxAllowed;
                    val = num.toString();
                    showFieldError($currentInput, `Max remaining qty is ${maxAllowed} pcs`);
                } else if (num <= 0) {
                    showFieldError($currentInput, 'Must be greater than 0');
                } else {
                    clearFieldError($currentInput);
                }
                $currentInput.val(val);
            } else {
                $currentInput.val('');
                showFieldError($currentInput, 'Quantity is required');
            }
            recalculateModalQuantities();
        });

        $(document).on('blur', '.pr-reason-qty', function() {
            const $currentInput = $(this);
            const val = $currentInput.val().trim();

            let otherRowsTotal = 0;
            $('#tbody_pr_reasons .pr-reason-qty').not($currentInput).each(function() {
                otherRowsTotal += (parseInt($(this).val(), 10) || 0);
            });
            let maxAllowed = Math.max(0, currentJobCardPendingQty - otherRowsTotal);

            if (val === '') {
                showFieldError($currentInput, 'Quantity is required');
            } else {
                const num = parseInt(val, 10);
                if (num <= 0) {
                    showFieldError($currentInput, 'Must be greater than 0');
                } else if (num > maxAllowed) {
                    $currentInput.val(maxAllowed);
                    showFieldError($currentInput, `Max remaining qty is ${maxAllowed} pcs`);
                } else {
                    clearFieldError($currentInput);
                }
            }
            recalculateModalQuantities();
        });

        $('#btn_pr_save_reasons').on('click', function(e) {
            e.preventDefault();
            const jcId = $('#pr_modal_job_card_id').val();
            const receiptId = $('#pr_modal_receipt_id').val();
            if (!jcId) return;

            const reasons = [];
            let hasError = false;

            const $rows = $('#tbody_pr_reasons .pr-reason-row');
            if ($rows.length === 0) {
                Swal.fire('Validation Error', 'Please add at least one pending reason row.', 'warning');
                return;
            }

            $rows.each(function() {
                const $row = $(this);
                const $reasonEl = $row.find('.pr-reason-select');
                const $qtyEl = $row.find('.pr-reason-qty');

                const reason = $reasonEl.val();
                const qtyStr = $qtyEl.val().trim();
                const remarks = $row.find('.pr-reason-remarks').val();

                if (!reason) {
                    showFieldError($reasonEl, 'Please select a reason');
                    hasError = true;
                } else {
                    clearFieldError($reasonEl);
                }

                if (qtyStr === '') {
                    showFieldError($qtyEl, 'Quantity is required');
                    hasError = true;
                } else {
                    const qty = parseInt(qtyStr, 10);
                    if (isNaN(qty) || qty <= 0) {
                        showFieldError($qtyEl, 'Must be greater than 0');
                        hasError = true;
                    } else if (qty > currentJobCardPendingQty) {
                        showFieldError($qtyEl, `Cannot exceed ${currentJobCardPendingQty} pcs`);
                        hasError = true;
                    } else {
                        clearFieldError($qtyEl);
                        if (reason) {
                            reasons.push({
                                reason: reason,
                                qty: qty,
                                remarks: remarks
                            });
                        }
                    }
                }
            });

            if (hasError) {
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
