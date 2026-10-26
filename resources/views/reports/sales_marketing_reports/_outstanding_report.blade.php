<!-- Action Toolbar for Outstanding Report -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 bg-light rounded-3 border">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
            <i class="ri ri-bank-card-line me-1"></i> Zonewise Outstanding
        </span>
        <span class="text-muted small d-none d-md-inline">
            <i class="ri ri-information-line text-info me-1"></i> Includes Opening Outstanding from Tally & Current Portal Invoices
        </span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ url('sales_marketing_reports/download_tally_template') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Download Excel template for Tally opening balance">
            <i class="ri ri-download-2-line me-1"></i> Download Template
        </a>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importTallyModal">
            <i class="ri ri-upload-cloud-2-line me-1"></i> Import Tally Outstanding
        </button>
    </div>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="outstandingReportTable">
        <thead class="bg-light">
            <tr>
                <th>Zone</th>
                <th>Customer</th>
                <th class="text-center">Pending Bills</th>
                <th class="text-end text-primary fw-bold">Open Outstanding</th>
                <th class="text-end">Total Sales</th>
                <th class="text-end">Received</th>
                <th class="text-end text-danger fw-bold">Total Outstanding</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-light fw-bold">
            <tr>
                <th colspan="2" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="outstanding_report_bills_count">0</th>
                <th class="text-end text-primary fw-bold" id="outstanding_report_open_outstanding">₹0.00</th>
                <th class="text-end text-primary fw-bold" id="outstanding_report_total_sales">₹0.00</th>
                <th class="text-end text-success fw-bold" id="outstanding_report_received">₹0.00</th>
                <th class="text-end text-danger fw-bold" id="outstanding_report_outstanding">₹0.00</th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Modal: Import Tally Opening Outstanding -->
<div class="modal fade" id="importTallyModal" tabindex="-1" aria-labelledby="importTallyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white d-flex align-items-center gap-2" id="importTallyModalLabel">
                    <i class="ri ri-upload-cloud-2-line fs-4"></i> Import Tally Opening Outstanding
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importTallyForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Excel / CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="import_file" id="tallyImportFileInput" accept=".xlsx,.xls,.csv,.txt" required>
                        <small class="text-muted d-block mt-1">Supported formats: .xlsx, .xls, .csv (Max: 10 MB)</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted">Need standard format?</span>
                        <a href="{{ url('sales_marketing_reports/download_tally_template') }}" class="small text-primary fw-semibold text-decoration-underline">
                            <i class="ri ri-file-excel-2-line me-1"></i> Download Sample CSV
                        </a>
                    </div>

                    <div id="importTallyAlert" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" id="importTallySubmitBtn">
                        <span id="importTallyBtnText"><i class="ri ri-upload-2-line me-1"></i> Upload & Import</span>
                        <span id="importTallyBtnSpinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#importTallyForm').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var submitBtn = $('#importTallySubmitBtn');
        var btnText = $('#importTallyBtnText');
        var btnSpinner = $('#importTallyBtnSpinner');
        var alertBox = $('#importTallyAlert');

        alertBox.hide().empty();
        submitBtn.prop('disabled', true);
        btnText.hide();
        btnSpinner.show();

        $.ajax({
            url: "{{ url('sales_marketing_reports/import_tally_outstanding') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                submitBtn.prop('disabled', false);
                btnText.show();
                btnSpinner.hide();

                if (res.status) {
                    alertBox.html('<div class="alert alert-success py-2 px-3 small mb-0"><i class="ri ri-checkbox-circle-line me-1"></i> ' + res.message + '</div>').fadeIn();

                    setTimeout(function() {
                        var modalEl = document.getElementById('importTallyModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) { modal.hide(); }
                        form.reset();
                        alertBox.hide();

                        if ($.fn.DataTable.isDataTable('#outstandingReportTable')) {
                            $('#outstandingReportTable').DataTable().ajax.reload(null, false);
                        }
                    }, 1200);
                } else {
                    alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0"><i class="ri ri-error-warning-line me-1"></i> ' + (res.message || 'Import failed.') + '</div>').fadeIn();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                btnText.show();
                btnSpinner.hide();

                var msg = 'An error occurred during file upload. Please verify file format and try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0"><i class="ri ri-error-warning-line me-1"></i> ' + msg + '</div>').fadeIn();
            }
        });
    });
});
</script>
