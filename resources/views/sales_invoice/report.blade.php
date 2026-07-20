@extends('layouts.common')
@section('title', 'Sales Invoice Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Invoice Report</h4>
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="filter-box">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <h5>Filter</h5>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <input type="text" id="inv_date_range" name="inv_date_range" class="form-control" placeholder="Select Date Range (dd-mm-yyyy to dd-mm-yyyy)">
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-floating form-floating-outline">
                                        <select name="customer_id" id="customer_id" class="form-select select2" data-placeholder="Select Customer">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-floating form-floating-outline">
                                        <select name="brand_id" id="brand_id" class="form-select select2" data-placeholder="Select Brand">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <input type="text" id="inv_no" name="inv_no" class="form-control" placeholder="Invoice Number">
                                </div>
                                <div class="col-md-12 text-end">
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                    @if(auth()->id() == 1 || auth()->user()->can('export sales-invoice-report'))
                                    <button type="button" id="exportBtn" class="btn btn-success"><i class="menu-icon ri ri-file-excel-2-line"></i> Export Excel</button>
                                    <button type="button" id="exportItemsBtn" class="btn btn-info"><i class="menu-icon ri ri-file-excel-2-line"></i> Export Items</button>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <div id="inv_no_error" class="text-danger fw-bold" style="display: none;">No Sales Invoice found for the entered invoice number</div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-datatable">
                        <table id="salesInvoiceReportTable" class="table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer Name</th>
                                    <th>Brand</th>
                                    <th>Total Qty</th>
                                    <th>Grand Total</th>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#salesInvoiceReportTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('sales_invoices/report') }}",
                data: function (d) {
                    d.customer_id = $('#customer_id').val();
                    d.brand_id = $('#brand_id').val();
                    d.inv_no = $('#inv_no').val();
                    d.inv_date_range = $('#inv_date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'inv_no' },
                { data: 'inv_date' },
                { data: 'customer_name' },
                { data: 'brand_name' },
                { data: 'total_qty' },
                { data: 'grand_total' }
            ]
        });

        $('#inv_date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        table.on('draw', function () {
            if ($('#inv_no').val() && table.rows().count() === 0) {
                $('#inv_no_error').show();
            } else {
                $('#inv_no_error').hide();
            }

            if (table.rows().count() === 0) {
                $('#exportBtn').prop('disabled', true);
                $('#exportItemsBtn').prop('disabled', true);
            } else {
                $('#exportBtn').prop('disabled', false);
                $('#exportItemsBtn').prop('disabled', false);
            }
        });

        $('#resetBtn').click(function() {
            $('#customer_id').val('').trigger('change');
            $('#brand_id').val('').trigger('change');
            $('#inv_no').val('');
            $('#inv_date_range').val('');
            table.ajax.reload();
        });

        $('#exportBtn').click(function() {
            let customer_id = $('#customer_id').val() || '';
            let brand_id = $('#brand_id').val() || '';
            let inv_no = $('#inv_no').val() || '';
            let inv_date_range = $('#inv_date_range').val() || '';

            let url = "{{ url('sales_invoices/report/export') }}?customer_id=" + encodeURIComponent(customer_id) +
                      "&brand_id=" + encodeURIComponent(brand_id) +
                      "&inv_no=" + encodeURIComponent(inv_no) +
                      "&inv_date_range=" + encodeURIComponent(inv_date_range);

            window.location.href = url;
        });

        $('#exportItemsBtn').click(function() {
            let customer_id = $('#customer_id').val() || '';
            let brand_id = $('#brand_id').val() || '';
            let inv_no = $('#inv_no').val() || '';
            let inv_date_range = $('#inv_date_range').val() || '';

            let url = "{{ url('sales_invoices/report/export_items') }}?customer_id=" + encodeURIComponent(customer_id) +
                      "&brand_id=" + encodeURIComponent(brand_id) +
                      "&inv_no=" + encodeURIComponent(inv_no) +
                      "&inv_date_range=" + encodeURIComponent(inv_date_range);

            window.location.href = url;
        });
    });
</script>
@endsection
