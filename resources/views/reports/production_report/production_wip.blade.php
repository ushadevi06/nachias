<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Jobcard No</th>
                <th>Stage</th>
                <th class="text-center">Opening</th>
                <th class="text-center">Inward</th>
                <th class="text-center">Outward</th>
                <th class="text-center">Current WIP</th>
            </tr>
        </thead>
        <tbody>
            @if (count($productionWip) > 0)
                @foreach ($productionWip as $row)
                    <tr>
                        <td><strong>{{ $row['job_card_no'] }}</strong></td>
                        <td>{{ $row['process'] }}</td>
                        <td class="text-center text-muted">{{ number_format($row['opening']) }}</td>
                        <td class="text-center text-success">{{ number_format($row['inward']) }}</td>
                        <td class="text-center text-primary">{{ number_format($row['outward']) }}</td>
                        <td class="text-center fw-bold">{{ number_format($row['current_wip']) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No WIP data found for the selected criteria.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
