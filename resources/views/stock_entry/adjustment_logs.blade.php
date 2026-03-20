@extends('layouts.common')
@section('title', 'Stock Adjustment Logs - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-12 text-end mb-3">
            <a href="{{ url('/stock_entries') }}" class="btn btn-outline-secondary">
                <i class="ri ri-arrow-left-line back-arrow"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Adjustment Logs</h4>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable table-responsive text-nowrap">
                        <table class="table table-hover table-striped datatables-logs">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date & Time</th>
                                    <th>Adj. No</th>
                                    <th>Stock Entry</th>
                                    <th>Material</th>
                                    <th>Prev. Stock</th>
                                    <th>Added Qty</th>
                                    <th>New Stock</th>
                                    <th>Approved By</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($logs->count() > 0)
                                @foreach($logs as $index => $log)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $log->created_at->format('d-m-Y') }}</span><br>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td><span class="badge bg-label-primary">{{ $log->adjustment_no }}</span></td>
                                    <td>
                                        <a href="{{ url('stock_entries/view/' . ($log->stockEntryItem->stock_entry_id ?? '')) }}" class="fw-medium">
                                            {{ $log->stockEntryItem->stockEntry->stock_entry_no ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $log->rawMaterial->name ?? '-' }}<br>
                                        <small class="text-muted">{{ $log->rawMaterial->code ?? '' }}</small>
                                    </td>
                                    <td>{{ number_format($log->previous_stock, 2) }}</td>
                                    <td><span class="text-success fw-bold">+{{ number_format($log->qty, 2) }}</span></td>
                                    <td>{{ number_format($log->new_stock, 2) }}</td>
                                    <td>{{ $log->approved_by }}</td>
                                    <td>
                                        <span class="text-wrap d-inline-block" style="max-width: 250px;">{{ $log->reason }}</span>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="ri-history-line ri-48px text-muted mb-3 d-block"></i>
                                            <p class="mb-0">No adjustment logs found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endif
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
    $(function() {
        const $table = $('.datatables-logs');
        const hasData = $table.find('tbody tr').length > 0 && !$table.find('tbody tr td[colspan]').length;
        
        if (hasData) {
            $table.DataTable({
                responsive: true,
                serverSide: false, 
                ajax: null,     
                paging: true,
                searching: true,
                order: [[1, 'desc']],
                pageLength: 25,
                buttons: [
                    {
                        extend: 'collection',
                        className: 'btn btn-label-secondary dropdown-toggle mx-3',
                        text: '<i class="ri-external-link-line me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                        buttons: [
                            {
                                extend: 'print',
                                text: '<i class="ri-printer-line me-1"></i>Print',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'csv',
                                text: '<i class="ri-file-text-line me-1"></i>Csv',
                                className: 'dropdown-item'
                            },
                            {
                                extend: 'excel',
                                text: '<i class="ri-file-excel-line me-1"></i>Excel',
                                className: 'dropdown-item'
                            }
                        ]
                    }
                ]
            });
            $('div.head-label').html('<h6 class="mb-0">History of Adjustments</h6>');
        } else {
            console.log('No adjustment logs found - DataTable not initialized');
        }
    });
</script>
<style>
    .text-wrap {
        white-space: normal !important;
    }
    .badge {
        text-transform: none;
    }
</style>
@endsection
