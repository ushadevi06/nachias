<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
    <div class="card-body bg-white p-4">
        <!-- Summary Card -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light h-100">
                    <p class="text-muted small text-uppercase mb-1 font-weight-bold">Job Card Number</p>
                    <h5 class="mb-0 text-primary font-weight-bold">{{ $jobCard->job_card_no }}</h5>
                    <hr class="my-2">
                    <p class="text-muted small text-uppercase mb-1 font-weight-bold">Product / Item</p>
                    <h5 class="mb-0">{{ $jobCard->item->name ?? 'N/A' }}</h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded bg-light h-100">
                    <p class="text-muted small text-uppercase mb-1 font-weight-bold">Total Target Qty</p>
                    <h5 class="mb-0 font-weight-bold">{{ number_format($jobCard->grand_total_qty, 0) }} PCS</h5>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="small font-weight-bold text-muted">FS: {{ (int)$jobCard->total_qty_fs }}</span>
                        <span class="small font-weight-bold text-muted">HS: {{ (int)$jobCard->total_qty_hs }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-4 rounded h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); color: white;">
                    <p class="text-uppercase small mb-1 font-weight-bold">Estimated Cost Per Shirt</p>
                    <h2 class="mb-0 font-weight-bold">₹{{ number_format($grandTotal, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Fabric Breakdown -->
        <h5 class="mb-3 font-weight-bold text-dark border-bottom pb-2">
            <i class="ri-scissors-line mr-2 text-primary"></i> 1. Fabric Consumption
        </h5>
        <div class="table-responsive mb-5">
            <table class="table table-hover border">
                <thead class="bg-light">
                    <tr>
                        <th>Article / Description</th>
                        <th>Consumption (Per PC)</th>
                        <th class="text-right">Unit Price (Mtr)</th>
                        <th class="text-right">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costing['fabric'] as $f)
                    <tr>
                        <td class="font-weight-bold text-primary">{{ $f['name'] }}</td>
                        <td>{{ number_format($f['qty'], 3) }} {{ $f['uom'] }}</td>
                        <td class="text-right">₹{{ number_format($f['price'], 2) }}</td>
                        <td class="text-right font-weight-bold text-dark">₹{{ number_format($f['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted italic">No fabric consumption data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Consumables Breakdown -->
        <h5 class="mb-3 font-weight-bold text-dark border-bottom pb-2">
            <i class="ri-box-3-line mr-2 text-primary"></i> 2. Consumables (Based on Service Multipliers)
        </h5>
        <div class="table-responsive">
            <table class="table table-hover border">
                <thead class="bg-light">
                    <tr>
                        <th>Stage</th>
                        <th>Description / Item</th>
                        <th>Qty (Multiplier)</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costing['consumables'] as $c)
                    <tr>
                        <td><span class="badge badge-secondary px-2">{{ $c['stage'] }}</span></td>
                        <td><b>{{ $c['material'] }}</b> <small class="text-muted d-block">({{ $c['service'] }})</small></td>
                        <td>{{ number_format($c['qty'], 2) }} {{ $c['uom'] }}</td>
                        <td class="text-right">₹{{ number_format($c['price'], 2) }}</td>
                        <td class="text-right font-weight-bold text-dark">₹{{ number_format($c['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted italic">No consumable multipliers found for this production plan.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light">
                    <tr style="font-size: 1.1rem;">
                        <td colspan="4" class="text-right font-weight-bold py-3">GRAND TOTAL PER UNIT:</td>
                        <td class="text-right font-weight-bold py-3 text-primary">₹{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="alert alert-info mt-4 small">
            <i class="ri-information-line mr-1"></i> 
            <strong>Note:</strong> Unit prices are pulled from the <b>latest stock records</b>. Fabric consumption is based on the averages computed in the Job Card.
        </div>
    </div>
</div>
