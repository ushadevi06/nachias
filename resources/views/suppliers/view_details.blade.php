@extends('layouts.common')
@section('title', 'View Supplier - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Supplier</h4>
                <a href="{{ url('suppliers') }}" class="btn btn-primary"><i
                        class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Supplier Name:</label>
                            <div class="text-muted">{{ $supplier->name }} <strong>({{ $supplier->code }})</strong></div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $supplier->mobile_no }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $supplier->email ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Website URL:</label>
                            <div class="text-muted">{{ $supplier->website_url ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Transport Name:</label>
                            <div class="text-muted">{{ $supplier->transport_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Booking Area:</label>
                            <div class="text-muted">{{ $supplier->booking_area ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Store:</label>
                            <div class="text-muted">{{ $supplier->stores ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="{{ $supplier->status === 'Active' ? 'text-success' : 'text-danger' }}">
                                {{ $supplier->status }}
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <div class="col-lg-12">
                            <h6>Location Information:</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Country:</label>
                            <div class="text-muted">{{ $supplier->country ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">State:</label>
                            <div class="text-muted">{{ $supplier->state->state_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">City:</label>
                            <div class="text-muted">{{ $supplier->city->city_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Place:</label>
                            <div class="text-muted">{{ $supplier->place->place_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">{{ $supplier->address_line_1 ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">{{ $supplier->address_line_2 ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Address Line 3:</label>
                            <div class="text-muted">{{ $supplier->address_line_3 ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">{{ $supplier->zip_code ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">{{ $supplier->contact_person_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">{{ $supplier->designation ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $supplier->contact_mobile_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $supplier->contact_email ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <div class="col-lg-12">
                            <h6>Commission Information:</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Purchase Commission Agent:</label>
                            <div class="text-muted">{{ $supplier->purchaseCommissionAgent->name ?? '-' }}
                                {{ $supplier->purchaseCommissionAgent ? '(' . $supplier->purchaseCommissionAgent->code . ')' : '' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Commission (%):</label>
                            <div class="text-muted">
                                {{ $supplier->commission_percentage ? $supplier->commission_percentage . '%' : '-' }}
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <div class="col-lg-12">
                            <h6>Tax & Compliance:</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">{{ $supplier->gst_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Tax Type:</label>
                            <div class="text-muted">{{ $supplier->taxType->item_name ?? '-' }}
                                {{ $supplier->taxType ? '(' . $supplier->taxType->tax_rate . '%)' : '' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">PAN NO:</label>
                            <div class="text-muted">{{ $supplier->pan_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">ECC No:</label>
                            <div class="text-muted">{{ $supplier->ecc_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Credit Limit:</label>
                            <div class="text-muted">
                                {{ $supplier->credit_limit ? '₹' . number_format($supplier->credit_limit, 2) : '-' }}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">{{ $supplier->payment_terms ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <div class="col-lg-12">
                            <h6>Bank Information:</h6>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Bank Name:</label>
                            <div class="text-muted">{{ $supplier->bank_name ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Branch:</label>
                            <div class="text-muted">{{ $supplier->branch ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">Account Number:</label>
                            <div class="text-muted">{{ $supplier->account_number ?? '-' }}</div>
                        </div>

                        <div class="col-md-3">
                            <label class="detail-title">IFSC Code:</label>
                            <div class="text-muted">{{ $supplier->ifsc_code ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection