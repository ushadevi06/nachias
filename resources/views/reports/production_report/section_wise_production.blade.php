<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Job Card No</th>
                <th>Service Name</th>
                <th>Process Name</th>
                <th class="text-center text-primary">Task Plan</th>
                <th class="text-center text-warning">Inprocess</th>
                <th class="text-center text-success">Completed</th>
                <th class="text-center text-danger">Hold</th>
            </tr>
        </thead>
        <tbody>
            @if($sectionWiseProduction && count($sectionWiseProduction) > 0)
                @foreach($sectionWiseProduction as $row)
                <tr>
                    <td><strong>{{ $row['job_card_no'] }}</strong></td>
                    <td>{{ $row['service_name'] }}</td>
                    <td>{{ $row['process_name'] }}</td>
                    <td class="text-center">{{ $row['task_plan'] }}</td>
                    <td class="text-center">{{ $row['inprocess'] }}</td>
                    <td class="text-center text-success">{{ $row['completed'] }}</td>
                    <td class="text-center text-danger">{{ $row['hold'] }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
