@extends('layouts.common')
@section('title', 'View Salary Calculation - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Salary Calculation</h4>
                <a href="{{ url('salary_calculation') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Employee:</label>
                            <div class="text-muted">Ramesh Kumar(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Month/Year:</label>
                            <div class="text-muted">September 2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Basic Salary:</label>
                            <div class="text-muted">₹45,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">HRA:</label>
                            <div class="text-muted">₹10,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Allowances:</label>
                            <div class="text-muted">₹5,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Overtime Amount:</label>
                            <div class="text-muted">₹3,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bonus Amount:</label>
                            <div class="text-muted">₹2,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Gross Salary:</label>
                            <div class="text-muted">₹65,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Deductions:</label>
                            <div class="text-muted">₹5,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Net Salary:</label>
                            <div class="text-muted">₹60,000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection