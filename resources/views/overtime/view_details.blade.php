@extends('layouts.common')
@section('title', 'View Overtime - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Overtime</h4>
                <a href="{{ url('overtime') }}" class="btn btn-outline-secondary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h5 class="mb-0">Employee OT Details</h5>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Employee Code</th>
                                    <th>Employee Name</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    <th>OT Hours</th>
                                    {{-- <th>Remarks</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}
                                    </td>
                                    <td>{{ $record->emp_code }}</td>
                                    <td>{{ $record->employee_name }}</td>
                                    <td>
                                        {{ $record->in_time
                                            ? \Carbon\Carbon::parse($record->in_time)->format('h:i A')
                                            : '-' }}
                                    </td>
                                    <td>
                                        {{ $record->out_time
                                            ? \Carbon\Carbon::parse($record->out_time)->format('h:i A')
                                            : '-' }}
                                    </td>
                                    <td>{{ $otHours }}</td>
                                    {{-- <td>-</td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
