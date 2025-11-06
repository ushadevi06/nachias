@extends('layouts.common')
@section('title', 'View Overtime / Bonus - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="row">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ url('overtime') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line me-1"></i>Back</a>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">View Overtime / Bonus</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Employee:</label>
                            <div class="text-muted">Ramesh Kumar(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Overtime Hours:</label>
                            <div class="text-muted">10</div>
                        </div> 
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Overtime Rate:</label>
                            <div class="text-muted">₹300</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Overtime Amount:</label>
                            <div class="text-muted">₹3,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Bonus Type:</label>
                            <div class="text-muted">Festival</div>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold mb-2 text-black">Bonus Amount:</label>
                            <div class="text-muted">₹2,000</div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <table class="table table-bordered" id="emp_table">
                            <thead>
                                <tr>
                                    <th>Basic Salary</th>
                                    <th>HRA</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Gross Salary + Overtime Amount + Bonus Amount</th>
                                    <th>Net Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>₹45,000</td>
                                    <td>₹10,000</td>
                                    <td>₹5,000</td>
                                    <td>₹5,000</td>
                                    <td class="text-center">₹65,000</td>    
                                    <td>₹60,000</td>
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