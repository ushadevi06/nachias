@extends('layouts.common')
@section('title', 'Production Receipt Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4>Production Receipt Report</h4>
            <div class="d-flex gap-2">
                @if(auth()->id() == 1 || auth()->user()->can('export production-receipt-report'))
                <button type="button" class="btn btn-outline-success" id="export-excel-btn">
                    <i class="menu-icon icon-base ri ri-file-excel-line me-1"></i> Export Excel
                </button>
                @endif
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" id="date_range" name="date_range" class="form-control" placeholder="Select Date Range (dd-mm-yyyy to dd-mm-yyyy)">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                <button type="button" id="reset-btn" class="btn btn-secondary w-100">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="report-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Amount</th>
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
@endsection

@section('scripts')
<script>
    $(function () {
        var table = $('#report-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: false, // Using custom filters instead
            ordering: false, // We order by ID desc natively
            info: true,
            lengthChange: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('production_receipts/report') }}',
                data: function(d) {
                    d.date_range = $('#date_range').val();
                },
                dataSrc: function(json) {
                    if (json.data.length === 0) {
                        $('#export-excel-btn').prop('disabled', true).text('No Production Receipt records found');
                    } else {
                        $('#export-excel-btn').prop('disabled', false).html('<i class="menu-icon icon-base ri ri-file-excel-line me-1"></i> Export Excel');
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'date', name: 'date' },
                { data: 'item', name: 'item' },
                { data: 'quantity', name: 'quantity' },
                { data: 'price', name: 'price' },
                { data: 'amount', name: 'amount' },
            ]
        });

        $('#date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });

        $('#reset-btn').on('click', function() {
            $('#filter-form')[0].reset();
            table.draw();
        });

        $('#export-excel-btn').on('click', function() {
            if ($(this).is(':disabled')) return;
            var queryParams = $.param({
                date_range: $('#date_range').val()
            });
            window.location.href = '{{ url('production_receipts/report/export') }}?' + queryParams;
        });
    });
</script>
@endsection
