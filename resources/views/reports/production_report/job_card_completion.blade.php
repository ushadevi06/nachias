<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Jobcard No</th>
                <th>Unit</th>
                <th class="text-center">Quantity</th>
                <th>Target Date</th>
                <th>Completed Date</th>
                <th class="text-center">Days Taken</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobCardCompletion as $row)
            <tr>
                <td><strong>{{ $row['job_card_no'] }}</strong></td>
                <td>{{ $row['unit'] }}</td>
                <td class="text-center">{{ $row['quantity'] }}</td>
                <td>{{ $row['target_date'] }}</td>
                <td>{{ $row['completed_date'] }}</td>
                <td class="text-center">
                    <span class="badge bg-label-{{ $row['status_class'] }}">{{ $row['status_label'] }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
