<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead class="bg-light">
            <tr>
                <th>CN No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Zone</th>
                <th>Sales Executive</th>
                <th>Reason</th>
                <th class="text-end">Sub Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax Amt</th>
                <th class="text-end">Other Chg</th>
                <th class="text-end">Grand Total</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($creditNotes) && $creditNotes->count() > 0)
                @foreach($creditNotes as $note)
                <tr>
                    <td><strong>{{ $note->note_no }}</strong></td>
                    <td>{{ $note->note_date ? $note->note_date->format('d-M-Y') : '-' }}</td>
                    <td>{{ $note->customer->name ?? '-' }}</td>
                    <td><span class="badge bg-label-info rounded-pill">{{ $note->zone->zone_name ?? '-' }}</span></td>
                    <td>{{ $note->salesAgent->name ?? '-' }}</td>
                    <td>{{ $note->reason ?? '-' }}</td>
                    <td class="text-end">₹{{ number_format($note->sub_total, 2) }}</td>
                    <td class="text-end text-danger">-₹{{ number_format($note->discount, 2) }}</td>
                    <td class="text-end">₹{{ number_format($note->tax_amount, 2) }}</td>
                    <td class="text-end">₹{{ number_format($note->other_charges, 2) }}</td>
                    <td class="text-end fw-bold text-success">₹{{ number_format($note->grand_total, 2) }}</td>
                    <td class="text-center">
                        @php
                            $badgeClass = 'bg-label-info';
                            if($note->status == 'Approved') $badgeClass = 'bg-label-success';
                            if($note->status == 'Cancelled') $badgeClass = 'bg-label-danger';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill">{{ $note->status }}</span>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
        @if(isset($creditNotes) && $creditNotes->count() > 0)
        <tfoot class="bg-light fw-bold">
            <tr>
                <td colspan="6" class="text-end">Total:</td>
                <td class="text-end">₹{{ number_format($creditNotes->sum('sub_total'), 2) }}</td>
                <td class="text-end text-danger">-₹{{ number_format($creditNotes->sum('discount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($creditNotes->sum('tax_amount'), 2) }}</td>
                <td class="text-end">₹{{ number_format($creditNotes->sum('other_charges'), 2) }}</td>
                <td class="text-end text-success">₹{{ number_format($creditNotes->sum('grand_total'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
