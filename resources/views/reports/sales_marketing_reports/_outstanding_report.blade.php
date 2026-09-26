<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="outstandingReportTable">
        <thead class="bg-light">
            <tr>
                <th>Zone</th>
                <th>Customer</th>
                <th class="text-center">Pending Bills</th>
                <th class="text-end">Total Sales</th>
                <th class="text-end">Received</th>
                <th class="text-end">Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @if(count($outstandingReport) > 0)
            @foreach($outstandingReport as $data)
            <tr>
                <td><span class="badge bg-label-secondary">{{ $data['zone'] }}</span></td>
                <td>
                    <div class="d-flex flex-column">
                        <span class="fw-bold">{{ $data['customer_name'] }}</span>
                        <small class="text-muted">{{ $data['customer_code'] }}</small>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge rounded-pill bg-label-info">{{ $data['bills_count'] }}</span>
                </td>
                <td class="text-end">₹{{ number_format($data['total_sales'], 2) }}</td>
                <td class="text-end text-success">₹{{ number_format($data['received'], 2) }}</td>
                <td class="text-end">
                    <span class="fw-bold text-danger">₹{{ number_format($data['outstanding'], 2) }}</span>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot class="bg-light fw-bold">
            <tr>
                <th colspan="2" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="outstanding_report_bills_count">{{ count($outstandingReport) > 0 ? number_format(array_sum(array_column($outstandingReport, 'bills_count'))) : 0 }}</th>
                <th class="text-end text-primary fw-bold" id="outstanding_report_total_sales">₹{{ count($outstandingReport) > 0 ? number_format(array_sum(array_column($outstandingReport, 'total_sales')), 2) : '0.00' }}</th>
                <th class="text-end text-success fw-bold" id="outstanding_report_received">₹{{ count($outstandingReport) > 0 ? number_format(array_sum(array_column($outstandingReport, 'received')), 2) : '0.00' }}</th>
                <th class="text-end text-danger fw-bold" id="outstanding_report_outstanding">₹{{ count($outstandingReport) > 0 ? number_format(array_sum(array_column($outstandingReport, 'outstanding')), 2) : '0.00' }}</th>
            </tr>
        </tfoot>
    </table>
</div>
