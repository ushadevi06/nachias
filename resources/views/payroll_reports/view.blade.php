@extends('layouts.common')
@section('title', 'Payroll Reports - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-3 d-flex flex-wrap justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Payroll Reports</h4>
                <button type="button" id="btnSharePayrollSummary" class="btn btn-success d-inline-flex align-items-center fw-semibold shadow-sm">
                    <i class="ri ri-whatsapp-line me-1 fs-5"></i> Send Report to WhatsApp
                </button>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label fw-bold">Unit</label>
                                <select name="unit" id="filter_unit" class="form-select select2" data-placeholder="Select Unit">
                                    <option value="">All Units</option>
                                    @if(isset($units))
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label class="form-label fw-bold">Month</label>
                                <input type="month" id="filter_month" class="form-control" value="{{ date('Y-m') }}" placeholder="Select Month">
                            </div>
                            <div class="col-md-4 col-lg-6 d-flex flex-wrap gap-2 justify-content-lg-end">
                                <button type="button" id="filterBtn" class="btn btn-primary">
                                    <i class="ri ri-filter-3-line me-1"></i> Filter
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">
                                    <i class="ri ri-refresh-line me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-datatable table-responsive">
                        <table class="table nowrap w-100" id="payrollTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Month/Year</th>
                                    <th>Employee</th>
                                    <th>Unit</th>
                                    <th>Department</th>
                                    <th>Working Days</th>
                                    <th>Gross Salary</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Share Modal (Option 2) -->
<div class="modal fade" id="modalWhatsAppShare" tabindex="-1" aria-labelledby="waModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center mb-0" id="waModalTitle">
                    <i class="ri ri-whatsapp-line fs-4 me-2"></i> Share via WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- PDF File Info Box -->
                <div class="d-flex align-items-center p-3 mb-3 bg-light rounded border">
                    <i class="ri ri-file-pdf-2-line fs-2 text-danger me-3"></i>
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="mb-0 fw-bold text-truncate text-dark" id="waModalFileName">Nachias_Payroll_Report.pdf</h6>
                        <small class="text-muted" id="waModalFileSize">PDF Document &bull; Ready to share</small>
                    </div>
                    <a href="#" id="waModalViewLink" target="_blank" class="btn btn-sm btn-outline-primary ms-2" title="View PDF in New Tab">
                        <i class="ri ri-eye-line me-1"></i> View PDF
                    </a>
                    <a href="#" id="waModalDownloadLink" target="_blank" class="btn btn-sm btn-outline-secondary ms-1" title="Direct Download PDF">
                        <i class="ri ri-download-2-line"></i>
                    </a>
                </div>

                <!-- Recipient Mobile Number -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">
                        WhatsApp Mobile Number <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white fw-bold text-secondary">+91</span>
                        <input type="text" id="waModalPhone" class="form-control" placeholder="Enter 10-digit mobile number" maxlength="10">
                    </div>
                    <small class="text-muted">Enter 10-digit number (e.g. 916382112269 or 9876543210)</small>
                </div>

                <!-- Message Preview -->
                <div class="mb-2">
                    <label class="form-label fw-bold text-dark">Message Preview</label>
                    <textarea id="waModalMessage" class="form-control" rows="3" style="font-size: 0.88rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 d-flex flex-wrap justify-content-between gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" id="waBtnNativeShare" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="ri ri-share-forward-line me-1"></i> Native Share (Attach PDF)
                    </button>
                    <button type="button" id="waBtnOpenWeb" class="btn btn-success d-inline-flex align-items-center">
                        <i class="ri ri-whatsapp-line me-1"></i> Open WhatsApp Web
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    if ($.fn.select2) {
        $('#filter_unit').select2();
    }

    let table = $('#payrollTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('payroll_reports') }}",
            data: function (d) {
                d.unit = $('#filter_unit').val();
                d.month = $('#filter_month').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'month_year' },
            { data: 'employee' },
            { data: 'unit' },
            { data: 'department' },
            { data: 'days' },
            { data: 'gross_salary' },
            { data: 'deductions' },
            { data: 'net_salary' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#filterBtn').click(function () {
        table.ajax.reload();
    });

    $('#filter_unit, #filter_month').on('change', function () {
        table.ajax.reload();
    });

    $('#resetBtn').click(function () {
        $('#filter_unit').val('').trigger('change');
        $('#filter_month').val('');
        table.ajax.reload();
    });

    // Global object to keep active share file details
    window.activeShareData = {
        title: '',
        filename: '',
        pdfUrl: '',
        phone: '',
        message: '',
        fileBlob: null
    };

    /**
     * Helper to trigger Web Share API or open modal
     */
    async function triggerShareFlow(data, btnTrigger) {
        window.activeShareData = data;
        let originalBtnHtml = '';
        if (btnTrigger) {
            originalBtnHtml = $(btnTrigger).html();
            $(btnTrigger).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        }

        try {
            // Fetch PDF as Blob
            const response = await fetch(data.pdfUrl);
            if (!response.ok) throw new Error('Could not fetch PDF');
            const blob = await response.blob();
            window.activeShareData.fileBlob = blob;
            const file = new File([blob], data.filename, { type: 'application/pdf' });

            // Check if native Web Share with files is supported
            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                if (btnTrigger) $(btnTrigger).prop('disabled', false).html(originalBtnHtml);
                await navigator.share({
                    files: [file],
                    title: data.title,
                    text: data.message
                });
                return; // Shared via native sheet successfully!
            }
        } catch (err) {
            console.log('Native share bypassed or error:', err);
            if (err.name === 'AbortError') {
                if (btnTrigger) $(btnTrigger).prop('disabled', false).html(originalBtnHtml);
                return; // User intentionally closed the native share dialog
            }
        } finally {
            if (btnTrigger) $(btnTrigger).prop('disabled', false).html(originalBtnHtml);
        }

        // Fallback: Show the Bootstrap Modal
        openShareModal();
    }

    function openShareModal() {
        const d = window.activeShareData;
        $('#waModalTitle').text(d.title || 'Share via WhatsApp');
        $('#waModalFileName').text(d.filename);
        $('#waModalDownloadLink').attr('href', d.pdfUrl);
        $('#waModalViewLink').attr('href', d.viewUrl || d.pdfUrl);
        $('#waModalPhone').val(d.phone || '');
        $('#waModalMessage').val(d.message || '');
        
        let modalEl = document.getElementById('modalWhatsAppShare');
        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    // 1. Top Button: Send Monthly Payroll Summary Report to WhatsApp
    $('#btnSharePayrollSummary').on('click', function () {
        let monthVal = $('#filter_month').val();
        let month = {{ date('n') }};
        let year = {{ date('Y') }};
        if (monthVal && monthVal.indexOf('-') !== -1) {
            let parts = monthVal.split('-');
            year = parseInt(parts[0], 10);
            month = parseInt(parts[1], 10);
        }
        let padMonth = String(month).padStart(2, '0');
        let filename = `Nachias_Payroll_Report_${year}-${padMonth}.pdf`;
        let pdfUrl = `{{ url('dashboard/payroll-summary-pdf') }}?month=${month}&year=${year}`;
        let viewUrl = `{{ url('view-payroll-report-pdf') }}?month=${month}&year=${year}`;
        let message = `Nachias Fashion Payroll Summary Report for ${month}/${year}. View PDF: ${viewUrl}`;

        triggerShareFlow({
            title: `Nachias Payroll Report (${padMonth}/${year})`,
            filename: filename,
            pdfUrl: pdfUrl,
            viewUrl: viewUrl,
            phone: '',
            message: message
        }, this);
    });
            phone: '',
            message: message
        }, this);
    });

    // Modal Button: Native Share (Attach PDF)
    $('#waBtnNativeShare').on('click', async function () {
        const d = window.activeShareData;
        if (!d.fileBlob) {
            alert('PDF is loading, please try again.');
            return;
        }
        const file = new File([d.fileBlob], d.filename, { type: 'application/pdf' });
        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            try {
                await navigator.share({
                    files: [file],
                    title: d.title,
                    text: $('#waModalMessage').val()
                });
            } catch (e) {
                console.log('Share canceled or error:', e);
            }
        } else {
            alert('Native file sharing is not supported on this browser version. Please use "Open WhatsApp Web" instead.');
        }
    });

    // Modal Button: Open WhatsApp Web
    $('#waBtnOpenWeb').on('click', function () {
        let rawPhone = $('#waModalPhone').val().trim().replace(/[^0-9]/g, '');
        if (rawPhone.length < 10) {
            alert('Please enter a valid 10-digit WhatsApp phone number.');
            $('#waModalPhone').focus();
            return;
        }
        if (rawPhone.length === 10) {
            rawPhone = '91' + rawPhone;
        }
        let message = encodeURIComponent($('#waModalMessage').val());
        let waUrl = `https://api.whatsapp.com/send?phone=${rawPhone}&text=${message}`;
        window.open(waUrl, '_blank');
    });
});
</script>
@endsection
