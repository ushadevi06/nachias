@extends('layouts.common')
@section('title', 'View Production - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Production</h4>
                <a href="{{ url('productions') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Job Card Number: </label>
                            <div class="text-muted">JC20250924-001-K</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Production Stage:</label>
                            <div class="text-muted">Cutting</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Service Provider:</label>
                            <div class="text-muted">In-House Cutting(SP003)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Planned Quantity:</label>
                            <div class="text-muted">5</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Completed Quantity:</label>
                            <div class="text-muted">3</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">WIP:</label>
                            <div class="text-muted">1</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Wastage Quantity:</label>
                            <div class="text-muted">5</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Wastage Reason:</label>
                            <div class="text-muted">Fabric Defect</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted"><span class="badge bg-warning">In Progress</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection