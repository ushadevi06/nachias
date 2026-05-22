@extends('layouts.common')
@section('title', 'Employee Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-4">
                <h4 class="mb-0">Employee Details</h4>
                <a href="{{ url('employees') }}" class="btn btn-secondary">
                    <i class="ri ri-arrow-left-line"></i> Back 
                </a>
            </div>
            <!-- Profile Header Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-column flex-sm-row gap-4">
                        <img src="{{ $employee->profile_image_url }}" alt="Profile Image" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;">
                        <div class="text-center text-sm-start">
                            <h3 class="mb-1">{{ $employee->name }}</h3>
                            <p class="text-muted mb-2"><i class="ri ri-id-card-line"></i> {{ $employee->emp_id }} | <span class="badge bg-label-primary">{{ $employee->role->name ?? '-' }}</span></p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">
                                <span class="badge {{ $employee->status == 'Active' ? 'bg-success' : 'bg-danger' }}">{{ $employee->status }}</span>
                                <span class="badge bg-info"><i class="ri ri-briefcase-line"></i> {{ $employee->department->department ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <!-- Personal & Contact Info -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-user-heart-line me-2"></i>Personal Info</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Email Address</span>
                                    <span>{{ $employee->email ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Phone Number</span>
                                    <span>{{ $employee->phone ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Date of Joining</span>
                                    <span>{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M, Y') : '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase">Blood Group</span>
                                    <span class="badge bg-label-danger">{{ $employee->bloodGroup->blood_grp_name ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Family & Organization -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-community-line me-2"></i>Family & Org</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Father's Name</span>
                                    <span>{{ $employee->father_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Father's Phone</span>
                                    <span>{{ $employee->father_phone ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Service Provider</span>
                                    <span>{{ $employee->serviceProvider->name ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase">Operation Stage</span>
                                    <span>{{ $employee->operationStage->operation_stage_name ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Address Details -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-map-pin-2-line me-2"></i>Address Details</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">State & City</span>
                                    <span>{{ $employee->state->state_name ?? '-' }}, {{ $employee->city->city_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Address Line 1</span>
                                    <span>{{ $employee->address_line1 ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Address Line 2</span>
                                    <span>{{ $employee->address_line2 ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase">Zipcode</span>
                                    <span>{{ $employee->zipcode ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Emergency Contact -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-phone-find-line me-2"></i>Emergency Contact</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Contact Person</span>
                                    <span>{{ $employee->contact_person_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Phone Number</span>
                                    <span>{{ $employee->contact_person_phone ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase">Email Address</span>
                                    <span>{{ $employee->contact_person_email ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Salary Structure -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-money-dollar-circle-line me-2"></i>Salary Structure</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Fixed Gross:</div>
                                <div class="col-6 text-end fw-bold">₹ {{ number_format($employee->fixed_gross, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Bus Fare:</div>
                                <div class="col-6 text-end fw-bold">₹ {{ number_format($employee->bus_fare, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">PF No:</div>
                                <div class="col-6 text-end fw-bold">{{ $employee->pf_no ?? '-' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">ESI No:</div>
                                <div class="col-6 text-end fw-bold">{{ $employee->esi_no ?? '-' }}</div>
                            </div>
                            {{-- <div class="row mb-2">
                                <div class="col-6 text-muted">HRA:</div>
                                <div class="col-6 text-end fw-bold">₹ {{ number_format($employee->hra, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Allowances:</div>
                                <div class="col-6 text-end fw-bold text-success">+ ₹ {{ number_format($employee->allowances, 2) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Deductions:</div>
                                <div class="col-6 text-end fw-bold text-danger">- ₹ {{ number_format($employee->deductions, 2) }}</div>
                            </div>
                            <hr>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Gross Salary:</div>
                                <div class="col-6 text-end fw-bold">₹ {{ number_format($employee->gross_salary, 2) }}</div>
                            </div>
                            <div class="row">
                                <div class="col-6 h6 mb-0">Net Salary:</div>
                                <div class="col-6 text-end h6 mb-0 fw-bold text-primary">₹ {{ number_format($employee->net_salary, 2) }}</div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <!-- Bank Details -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-bank-line me-2"></i>Bank Details</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">Bank Name</span>
                                    <span>{{ $employee->bank_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase">A/C Number</span>
                                    <span>{{ $employee->account_number ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase">IFSC Code</span>
                                    <span>{{ $employee->ifsc_code ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Documents Section -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0"><i class="ri ri-file-text-line me-2"></i>Documents & Compliance</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4 text-center">
                                <div class="col-md-3">
                                    <div class="p-3 border rounded">
                                        <p class="mb-2 fw-bold">ESI Document</p>
                                        @if($employee->esi_document)
                                            <a href="{{ $employee->esi_document_url }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                                <i class="ri ri-eye-line"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted italic">Not Uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 border rounded">
                                        <p class="mb-2 fw-bold">PF Document</p>
                                        @if($employee->pf_document)
                                            <a href="{{ $employee->pf_document_url }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                                <i class="ri ri-eye-line"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted italic">Not Uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 border rounded">
                                        <p class="mb-2 fw-bold">Aadhaar Card</p>
                                        @if($employee->aadhaar_document)
                                            <a href="{{ $employee->aadhaar_document_url }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                                <i class="ri ri-eye-line"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted italic">Not Uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 border rounded">
                                        <p class="mb-2 fw-bold">PAN Card</p>
                                        @if($employee->pan_document)
                                            <a href="{{ $employee->pan_document_url }}" target="_blank" class="btn btn-outline-info btn-sm w-100">
                                                <i class="ri ri-eye-line"></i> View Document
                                            </a>
                                        @else
                                            <span class="text-muted italic">Not Uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
