<!-- Job Card Tracking DataTable -->
<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="jobCardTrackingTable">
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Job Card No.</th>
                <th>Unit (From)</th>
                <th>Warehouse (To)</th>
                <th>Store Location</th>
                <th class="text-center">Receipt Date</th>
                <th class="text-end">Job Qty (Plan)</th>
                <th class="text-end">Received Qty</th>
                <th class="text-center">Pending Qty</th>
                <th>Reason For Pending</th>
                <th class="text-center">Status</th>
                <th class="text-center" style="width: 70px;">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="jobCardTrackingTfoot">
            <tr class="bg-light fw-bold">
                <th colspan="6" class="text-end fw-bold">TOTAL</th>
                <th class="text-end fw-bold text-dark" id="jcFootPlanQty">0</th>
                <th class="text-end fw-bold text-primary" id="jcFootRecQty">0</th>
                <th class="text-center fw-bold text-danger" id="jcFootPendQty">0</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Modal: Job Card Tracking & Reason Management -->
<div class="modal fade" id="jcTrackingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0">
                        <i class="ri ri-file-list-3-line me-2"></i>Job Card Inward & Pending Tracking
                    </h5>
                    <small class="text-white-50" id="jcModalSubTitle">-</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Overview Stats Bar -->
                <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Job Card No:</span>
                        <strong class="text-primary fs-6" id="modalJcNo">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Unit (Plant):</span>
                        <strong class="text-dark" id="modalJcUnit">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Brand:</span>
                        <strong class="text-dark" id="modalJcBrand">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Planned Qty:</span>
                        <strong class="text-dark fs-6" id="modalJcPlanned">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Received Qty:</span>
                        <strong class="text-success fs-6" id="modalJcReceived">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="text-muted small d-block">Pending Qty:</span>
                        <strong class="text-danger fs-6" id="modalJcPending">-</strong>
                    </div>
                </div>

                <!-- Tabs for Details -->
                <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jc-receipts" role="tab">
                            <i class="ri ri-inbox-line me-1"></i> Inward Receipts History (<span id="modalReceiptCount">0</span>)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-jc-reasons" role="tab">
                            <i class="ri ri-feedback-line me-1"></i> Pending Reasons (<span id="modalReasonCount">0</span>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-2">
                    <!-- Tab 1: Receipts History -->
                    <div class="tab-pane fade show active" id="tab-jc-receipts" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt No</th>
                                        <th class="text-center">Receipt Date</th>
                                        <th>Warehouse</th>
                                        <th>Store Location</th>
                                        <th class="text-end">Received Qty</th>
                                        <th>Responsible Person</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="modalReceiptsTbody">
                                    <tr><td colspan="7" class="text-center text-muted py-3">No receipts found for this job card.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Pending Reasons -->
                    <div class="tab-pane fade" id="tab-jc-reasons" role="tabpanel">
                        <div class="card border mb-3 shadow-none">
                            <div class="card-header bg-light py-2">
                                <strong class="small text-primary"><i class="ri ri-add-circle-line me-1"></i> Log New Pending Reason</strong>
                            </div>
                            <div class="card-body py-3">
                                <form id="formAddPendingReason">
                                    <input type="hidden" name="job_card_id" id="reason_job_card_id" value="">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Reason <span class="text-danger">*</span></label>
                                            <select class="form-select form-select-sm" name="reason" id="new_reason_select" required>
                                                <option value="">Select Reason</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Alteration">Alteration</option>
                                                <option value="Spl. Order Dispatch">Spl. Order Dispatch</option>
                                                <option value="QC Hold">QC Hold</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Qty <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="qty" id="new_reason_qty" placeholder="0" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold">Remarks</label>
                                            <input type="text" class="form-control form-control-sm" name="remarks" id="new_reason_remarks" placeholder="Optional notes / details">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-sm btn-primary w-100" id="btnSaveReason">
                                                <i class="ri ri-save-line me-1"></i> Add Reason
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reason</th>
                                        <th class="text-end">Qty</th>
                                        <th>Remarks</th>
                                        <th class="text-center">Logged Date</th>
                                        <th class="text-center" style="width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="modalReasonsTbody">
                                    <tr><td colspan="5" class="text-center text-muted py-3">No pending reasons logged yet.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
window.initJobCardTrackingTable = function() {
    if ($.fn.DataTable.isDataTable('#jobCardTrackingTable')) {
        $('#jobCardTrackingTable').DataTable().clear().destroy();
    }

    var dt = $('#jobCardTrackingTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/job-card-tracking') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.warehouse_id = $('#filter_warehouse_id').val();
                d.unit_id = $('#filter_unit_id').val();
                d.tracking_status = $('#filter_tracking_status').val();
            }
        },
        columns: [
            { data: 'index', name: 'index', className: 'text-center', orderable: false, searchable: false },
            { data: 'job_card_no', name: 'jc.job_card_no' },
            { data: 'unit_from', name: 'sp.name' },
            { data: 'warehouse_to', name: 'wh.warehouse_name', className: 'text-center' },
            { data: 'store_location', name: 'sl.store_location', className: 'text-center' },
            { data: 'receipt_date', name: 'pr.receipt_date', className: 'text-center' },
            { data: 'job_qty', name: 'planned_qty', className: 'text-end fw-bold text-dark' },
            { data: 'received_qty', name: 'total_received_qty', className: 'text-end fw-bold text-primary' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'reasons', name: 'reasons', orderable: false, searchable: false },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', className: 'text-center', orderable: false, searchable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        order: [],
        drawCallback: function (settings) {
            var json = settings.json;
            if (json && json.kpi) {
                $('#jcFootPlanQty').text(parseFloat(json.kpi.total_job_qty || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }));
                $('#jcFootRecQty').text(parseFloat(json.kpi.total_received_qty || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }));
                $('#jcFootPendQty').text(parseFloat(json.kpi.total_pending_qty || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }));
            }
        }
    });
};

function initJobCardTrackingTable() {
    window.initJobCardTrackingTable();
}

// Open Details Modal
$(document).on('click', '.btn-view-jc-tracking', function(e) {
    e.preventDefault();
    var jcId = $(this).data('id');
    $('#reason_job_card_id').val(jcId);

    $.ajax({
        url: "{{ url('warehouse_reports/job_card_tracking_details') }}/" + jcId,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#modalReceiptsTbody').html('<tr><td colspan="7" class="text-center py-3"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Loading details...</td></tr>');
            $('#modalReasonsTbody').html('<tr><td colspan="5" class="text-center py-3"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Loading reasons...</td></tr>');
            $('#jcTrackingDetailModal').modal('show');
        },
        success: function(res) {
            if (res.success) {
                var jc = res.job_card;
                $('#modalJcNo').text(jc.job_card_no);
                $('#modalJcUnit').text(jc.unit_name);
                $('#modalJcBrand').text(jc.brand_name);
                $('#modalJcPlanned').text(jc.planned_qty);
                $('#modalJcReceived').text(jc.received_qty);
                $('#modalJcPending').text(jc.pending_qty);
                $('#jcModalSubTitle').text('Job Card Date: ' + jc.job_card_date + ' | Status: ' + jc.status);

                $('#new_reason_qty').val(jc.raw_pending_qty > 0 ? jc.raw_pending_qty : '');

                // Populate Receipts
                var rHtml = '';
                if (res.receipts && res.receipts.length > 0) {
                    $('#modalReceiptCount').text(res.receipts.length);
                    res.receipts.forEach(function(r) {
                        rHtml += '<tr>' +
                            '<td><strong>' + r.receipt_no + '</strong></td>' +
                            '<td class="text-center">' + r.receipt_date + '</td>' +
                            '<td><span class="badge bg-label-info">' + r.warehouse + '</span></td>' +
                            '<td>' + r.store_location + '</td>' +
                            '<td class="text-end fw-bold text-primary">' + r.received_qty + '</td>' +
                            '<td>' + r.employee + '</td>' +
                            '<td class="text-center"><span class="badge bg-label-success">' + r.status + '</span></td>' +
                            '</tr>';
                    });
                } else {
                    $('#modalReceiptCount').text('0');
                    rHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No production receipts recorded yet.</td></tr>';
                }
                $('#modalReceiptsTbody').html(rHtml);

                // Populate Reasons
                renderModalReasons(res.pending_reasons);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load tracking details.',
                confirmButtonColor: '#8c57ff'
            });
        }
    });
});

function renderModalReasons(reasons) {
    var prHtml = '';
    if (reasons && reasons.length > 0) {
        $('#modalReasonCount').text(reasons.length);
        reasons.forEach(function(pr) {
            prHtml += '<tr>' +
                '<td><span class="badge bg-label-warning">' + pr.reason + '</span></td>' +
                '<td class="text-end fw-bold">' + pr.qty + '</td>' +
                '<td>' + pr.remarks + '</td>' +
                '<td class="text-center text-muted small">' + pr.created_at + '</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-reason" data-id="' + pr.id + '" title="Delete Reason"><i class="ri ri-delete-bin-line"></i></button>' +
                '</td>' +
                '</tr>';
        });
    } else {
        $('#modalReasonCount').text('0');
        prHtml = '<tr><td colspan="5" class="text-center text-muted py-3">No pending reasons logged yet.</td></tr>';
    }
    $('#modalReasonsTbody').html(prHtml);
}

// Add Pending Reason Form Submit
$('#formAddPendingReason').on('submit', function(e) {
    e.preventDefault();
    var $btn = $('#btnSaveReason');
    var originalText = $btn.html();
    $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...').prop('disabled', true);

    $.ajax({
        url: "{{ url('warehouse_reports/job_card_tracking_reason/save') }}",
        type: 'POST',
        data: $(this).serialize() + '&_token={{ csrf_token() }}',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#new_reason_select').val('');
                $('#new_reason_remarks').val('');
                // Refresh modal details
                var jcId = $('#reason_job_card_id').val();
                $.get("{{ url('warehouse_reports/job_card_tracking_details') }}/" + jcId, function(data) {
                    if (data.success) {
                        renderModalReasons(data.pending_reasons);
                    }
                });
                // Reload main table
                if ($.fn.DataTable.isDataTable('#jobCardTrackingTable')) {
                    $('#jobCardTrackingTable').DataTable().ajax.reload(null, false);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error saving reason';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg,
                confirmButtonColor: '#8c57ff'
            });
        },
        complete: function() {
            $btn.html(originalText).prop('disabled', false);
        }
    });
});

// Delete Pending Reason
$(document).on('click', '.btn-delete-reason', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var reasonId = $(this).data('id');

    Swal.fire({
        title: 'Delete Reason?',
        text: 'Are you sure you want to remove this pending reason?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('warehouse_reports/job_card_tracking_reason/delete') }}/" + reasonId,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        var jcId = $('#reason_job_card_id').val();
                        if (jcId) {
                            $.get("{{ url('warehouse_reports/job_card_tracking_details') }}/" + jcId, function(data) {
                                if (data.success) {
                                    renderModalReasons(data.pending_reasons);
                                }
                            });
                        }
                        if ($.fn.DataTable.isDataTable('#jobCardTrackingTable')) {
                            $('#jobCardTrackingTable').DataTable().ajax.reload(null, false);
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete pending reason.',
                        confirmButtonColor: '#8c57ff'
                    });
                }
            });
        }
    });
});

if ($('#job-card-tracking').hasClass('active')) {
    window.initJobCardTrackingTable();
}
</script>
