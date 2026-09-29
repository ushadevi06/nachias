@extends('layouts.common')
@section('title', 'Old Sales Invoices (Archive) - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">Old Sales Invoices (Archive)</h4>
                    <span class="badge bg-label-info">Excel Imports</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                        <i class="ri ri-file-excel-2-line me-1"></i> Import Excel File
                    </button>
                    <a href="{{ url('sales_invoices') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Sales Invoices
                    </a>
                </div>
            </div>

            <div class="col-lg-12">
                @include('flash_messages')
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small">Customer</label>
                                <select name="filter_customer_id" id="filter_customer_id" class="form-select select2" data-placeholder="All Customers">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Brand</label>
                                <select name="filter_brand_id" id="filter_brand_id" class="form-select select2" data-placeholder="All Brands">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->brand_name }} ({{ $b->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Date Range</label>
                                <input type="text" id="filter_date_range" class="form-control" placeholder="Select Date Range">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="button" id="filterBtn" class="btn btn-primary w-100">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary w-100">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="card-datatable table-responsive">
                        <table class="table table-hover w-100 nowrap" id="old_invoices_table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">S.No</th>
                                    <th>Doc No</th>
                                    <th>Doc Date</th>
                                    <th>Customer Name</th>
                                    <th>GSTIN</th>
                                    <th>Brand</th>
                                    <th class="text-center">Total Qty</th>
                                    <th class="text-end">Total Amount</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
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

<!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ url('old_sales_invoices/import') }}" method="POST" enctype="multipart/form-data" id="importExcelForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">
                        <i class="ri ri-file-excel-2-line text-success me-1"></i> Import Old Invoices Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="file_size_error_alert" class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center" style="display: none !important;">
                        <i class="ri ri-error-warning-fill fs-5 me-2 flex-shrink-0"></i>
                        <div id="file_size_error_text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Select Excel File (.xls, .xlsx, .csv) <span class="text-danger">*</span> <span class="badge bg-label-primary ms-1">Max 10 MB</span></label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xls,.xlsx,.csv" required>
                        <small class="text-muted d-block mt-1">Upload the standard Old Software export file with columns: S.No, Doc No, Doc Date, Customer, IRN, Description, Price, Quantity, etc.</small>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 12px;">
                        <i class="ri ri-information-line me-1"></i> <strong>Note:</strong> Items will be grouped by <code>Doc No</code>, and Brand & Style (Plain/Print/Checked) will be auto-mapped from Art numbers.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="importSubmitBtn">
                        <i class="ri ri-upload-cloud-2-line me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        let fromDate = '';
        let toDate = '';

        if ($('#filter_date_range').length) {
            $('#filter_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd-m-Y',
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        fromDate = instance.formatDate(selectedDates[0], 'Y-m-d');
                        toDate = instance.formatDate(selectedDates[1], 'Y-m-d');
                    } else {
                        fromDate = '';
                        toDate = '';
                    }
                }
            });
        }

        let table = $('#old_invoices_table').DataTable({
            processing: true,
            serverSide: true,
            responsive:true,
            ajax: {
                url: "{{ url('old_sales_invoices') }}",
                data: function (d) {
                    d.customer_id = $('#filter_customer_id').val();
                    d.brand_id = $('#filter_brand_id').val();
                    d.from_date = fromDate;
                    d.to_date = toDate;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'doc_no', name: 'doc_no' },
                { data: 'doc_date', name: 'doc_date' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'gstin_reg_no', name: 'gstin_reg_no' },
                { data: 'brand_name', name: 'brand_name' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center' },
                { data: 'total_amount', name: 'total_amount', className: 'text-end' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[1, 'desc']]
        });

        $('#filterBtn').click(function () {
            table.draw();
        });

        $('#resetBtn').click(function () {
            $('#filter_customer_id').val('').trigger('change');
            $('#filter_brand_id').val('').trigger('change');
            let fp = $('#filter_date_range')[0]._flatpickr;
            if (fp) fp.clear();
            fromDate = '';
            toDate = '';
            table.draw();
        });

        $('#excel_file').on('change', function () {
            let file = this.files[0];
            let maxMb = 10;
            let $errAlert = $('#file_size_error_alert');
            let $errText = $('#file_size_error_text');
            let $submitBtn = $('#importSubmitBtn');

            if (file) {
                let sizeInMb = file.size / (1024 * 1024);
                if (sizeInMb > maxMb) {
                    $errText.html('<strong>File Too Large!</strong> Selected file is <strong>' + sizeInMb.toFixed(2) + ' MB</strong>. Maximum allowed limit is <strong>' + maxMb + ' MB</strong>.');
                    $errAlert.removeAttr('style').show();
                    $(this).addClass('is-invalid');
                    $(this).val('');
                    $submitBtn.prop('disabled', true);
                } else {
                    $errAlert.attr('style', 'display: none !important;').hide();
                    $(this).removeClass('is-invalid');
                    $submitBtn.prop('disabled', false);
                }
            } else {
                $errAlert.attr('style', 'display: none !important;').hide();
                $(this).removeClass('is-invalid');
                $submitBtn.prop('disabled', false);
            }
        });

        $('#importExcelModal').on('hidden.bs.modal', function () {
            $('#file_size_error_alert').attr('style', 'display: none !important;').hide();
            $('#excel_file').removeClass('is-invalid').val('');
            $('#importSubmitBtn').prop('disabled', false).html('<i class="ri ri-upload-cloud-2-line me-1"></i> Start Import');
        });

        $('#importExcelForm').on('submit', function () {
            $('#importSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');
        });
    });
</script>
@endsection
