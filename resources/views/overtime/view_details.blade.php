@extends('layouts.common')
@section('title', 'View Overtime / Bonus - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Overtime / Bonus</h4>
                <a href="{{ url('overtime') }}" class="btn btn-outline-secondary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">OT Date:</label>
                            <div class="text-muted">17-03-2024</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Department:</label>
                            <div class="text-muted">Checking</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Employees:</label>
                            <div class="text-muted">3</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Total OT Hours:</label>
                            <div class="text-muted">23.50</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Total Late Hours:</label>
                            <div class="text-muted">0.00</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bonus Type:</label>
                            <div class="text-muted">Nil</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bonus Amount:</label>
                            <div class="text-muted">Rs.0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h5 class="mb-0">Employee-wise OT Details</h5>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Emp No</th>
                                    <th>Employee</th>
                                    <th>OT Hours</th>
                                    <th>Late Hours</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>10</td>
                                    <td>RENUKADEVI.R</td>
                                    <td>8.00</td>
                                    <td>0.00</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>70</td>
                                    <td>ANITHA.M</td>
                                    <td>7.50</td>
                                    <td>0.00</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>71</td>
                                    <td>SRIDHAR.K</td>
                                    <td>8.00</td>
                                    <td>0.00</td>
                                    <td>-</td>
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
