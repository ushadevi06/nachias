@extends('layouts.common')
@section('title', 'Settings - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Settings</h4>
                    </div>
                    <form action="{{ url('settings/update') }}" method="POST" enctype="multipart/form-data" class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" placeholder="Enter Company Name" name="company_name" value="{{ old('company_name', $setting->company_name ?? '') }}">
                                    <label for="company_name">Company Name *</label>
                                    @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email(s) (comma separated)" name="email" value="{{ old('email', $setting->email ?? '') }}">
                                    <label for="email">Email(s) * (Ex: info@example.com, support@example.com)</label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('logo') is-invalid @enderror" type="file" id="logo" name="logo" accept="image/*">
                                    <label for="logo" class="form-label">Logo (Min: 1MB, Max: 5MB)</label>
                                    @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(isset($setting) && $setting->logo)
                                    <div class="mt-2">
                                        <img src="{{ url('uploads/logo/' . $setting->logo) }}"
                                            alt="Current Logo"
                                            style="max-width: 100px; max-height: 100px;">
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('qr_code') is-invalid @enderror" type="file" id="qr_code" name="qr_code" accept="image/*">
                                    <label for="qr_code" class="form-label">QR Code Image</label>
                                    @error('qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(isset($setting) && $setting->qr_code)
                                    <div class="mt-2">
                                        <img src="{{ url('uploads/qr_code/' . $setting->qr_code) }}"
                                            alt="Current QR Code"
                                            style="max-width: 100px; max-height: 100px;">
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" placeholder="Enter Phone Number" name="phone_number" value="{{ old('phone_number', $setting->phone_number ?? '') }}">
                                    <label for="phone_number">Phone Number *</label>
                                    @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('toll_free_no') is-invalid @enderror" id="toll_free_no" placeholder="Enter Toll Free No (comma separated)" name="toll_free_no" value="{{ old('toll_free_no', $setting->toll_free_no ?? '') }}">
                                    <label for="toll_free_no">Toll Free No (Ex: 1234567890, 0987654321)</label>
                                    @error('toll_free_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="state_id" name="state_id" class="select2 form-select @error('state_id') is-invalid @enderror" data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id', $setting->state_id ?? '') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="state_id">State *</label>
                                    @error('state_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="city_id" name="city_id" class="select2 form-select @error('city_id') is-invalid @enderror" data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $setting->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="city_id">City *</label>
                                    @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('address') is-invalid @enderror" id="address" name="address" placeholder="Enter Address">{{ old('address', $setting->address ?? '') }}</textarea>
                                    <label for="address">Address *</label>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" placeholder="Enter Zip Code" name="zip_code" value="{{ old('zip_code', $setting->zip_code ?? '') }}">
                                    <label for="zip_code">Zip Code *</label>
                                    @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Prefix Settings:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('po_prefix') is-invalid @enderror" id="po_prefix" placeholder="PO Prefix (e.g., PO/24-25/)" name="po_prefix" value="{{ old('po_prefix', $setting->po_prefix ?? '') }}">
                                    <label for="po_prefix">PO Prefix *</label>
                                    @error('po_prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('so_prefix') is-invalid @enderror" id="so_prefix" placeholder="SO Prefix (e.g., SO/24-25/)" name="so_prefix" value="{{ old('so_prefix', $setting->so_prefix ?? '') }}">
                                    <label for="so_prefix">SO Prefix *</label>
                                    @error('so_prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>  

                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Tax Info:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('cgst') is-invalid @enderror" id="cgst" name="cgst" type="number" step="any" placeholder="Enter CGST (%)" value="{{ old('cgst', $setting->cgst ?? '') }}" />
                                    <label for="cgst">CGST (%) *</label>
                                    @error('cgst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('sgst') is-invalid @enderror" id="sgst" name="sgst" type="number" step="any" placeholder="Enter SGST (%)" value="{{ old('sgst', $setting->sgst ?? '') }}" />
                                    <label for="sgst">SGST (%) *</label>
                                    @error('sgst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('igst') is-invalid @enderror" id="igst" name="igst" type="number" step="any" placeholder="Enter IGST (%)" value="{{ old('igst', $setting->igst ?? '') }}" />
                                    <label for="igst">IGST (%) *</label>
                                    @error('igst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('pan_no') is-invalid @enderror" id="pan_no" name="pan_no" placeholder="Enter PAN No." value="{{ old('pan_no', $setting->pan_no ?? '') }}" />
                                    <label for="pan_no">PAN</label>
                                    @error('pan_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('gst_no') is-invalid @enderror" id="gst_no" name="gst_no" placeholder="Enter GST No." value="{{ old('gst_no', $setting->gst_no ?? '') }}" />
                                    <label for="gst_no">GST</label>
                                    @error('gst_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('cin_no') is-invalid @enderror" id="cin_no" name="cin_no" placeholder="Enter CIN No." value="{{ old('cin_no', $setting->cin_no ?? '') }}" />
                                    <label for="cin_no">CIN</label>
                                    @error('cin_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Bank Details:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" placeholder="Enter Bank Name" name="bank_name" value="{{ old('bank_name', $setting->bank_name ?? '') }}">
                                    <label for="bank_name">Bank Name</label>
                                    @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('branch_location') is-invalid @enderror" id="branch_location" placeholder="Enter Branch Location" name="branch_location" value="{{ old('branch_location', $setting->branch_location ?? '') }}">
                                    <label for="branch_location">Branch Location</label>
                                    @error('branch_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('account_no') is-invalid @enderror" id="account_no" placeholder="Enter Account No" name="account_no" value="{{ old('account_no', $setting->account_no ?? '') }}">
                                    <label for="account_no">Account No</label>
                                    @error('account_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" placeholder="Enter IFSC Code" name="ifsc_code" value="{{ old('ifsc_code', $setting->ifsc_code ?? '') }}">
                                    <label for="ifsc_code">IFSC Code</label>
                                    @error('ifsc_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('upi_id') is-invalid @enderror" id="upi_id" placeholder="Enter Merchant UPI ID" name="upi_id" value="{{ old('upi_id', $setting->upi_id ?? '') }}">
                                    <label for="upi_id">Merchant UPI ID (VPA)</label>
                                    @error('upi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Working Days & Time:</h6>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <label class="form-label mb-2">Working Days Range *</label>
                                    <div id="day-range-picker" class="d-flex flex-wrap gap-2">
                                        @php
                                            $daysList = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                            $currentRange = $setting->working_days ?? '';
                                            $rangeParts = explode(' - ', $currentRange);
                                            $startDay = $rangeParts[0] ?? '';
                                            $endDay = $rangeParts[1] ?? '';
                                        @endphp
                                        @foreach($daysList as $day)
                                            <div class="day-pill px-3 py-2 border rounded-pill cursor-pointer text-center" 
                                                 data-day="{{ $day }}" 
                                                 style="min-width: 90px; transition: all 0.3s ease; cursor: pointer; user-select: none;">
                                                {{ substr($day, 0, 3) }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="working_days" id="working_days_input" value="{{ $currentRange }}">
                                    @error('working_days')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <style>
                                .day-pill:hover {
                                    background-color: #f0eaff;
                                    border-color: #8c57ff !important;
                                }
                                .day-pill.active {
                                    background-color: #8c57ff !important;
                                    color: white !important;
                                    border-color: #8c57ff !important;
                                    box-shadow: 0 4px 12px rgba(140, 87, 255, 0.3);
                                }
                                .day-pill.range-mid {
                                    background-color: #e5d5ff !important;
                                    border-color: #8c57ff !important;
                                }
                            </style>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control timepicker @error('opening_time') is-invalid @enderror" id="opening_time" name="opening_time" placeholder="Opening Time" value="{{ old('opening_time', $setting->opening_time ?? '') }}" />
                                    <label for="opening_time">Opening Time</label>
                                    @error('opening_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control timepicker @error('closing_time') is-invalid @enderror" id="closing_time" name="closing_time" placeholder="Closing Time" value="{{ old('closing_time', $setting->closing_time ?? '') }}" />
                                    <label for="closing_time">Closing Time</label>
                                    @error('closing_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Other:</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('terms_and_conditions') is-invalid @enderror" id="terms_and_conditions" name="terms_and_conditions" placeholder="Enter Terms and Conditions">{{ old('terms_and_conditions', $setting->terms_and_conditions ?? '') }}</textarea>
                                    <label for="terms_and_conditions">Terms and Conditions *</label>
                                    @error('terms_and_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if(auth()->id() == 1 || auth()->user()->can('edit settings'))
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".timepicker").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
        });

        // Day Range Picker Logic
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        let startDay = null;
        let endDay = null;

        const rangeInput = $('#working_days_input');
        const pills = $('.day-pill');

        function updateUI() {
            pills.removeClass('active range-mid');
            
            if (startDay && endDay) {
                const startIndex = days.indexOf(startDay);
                const endIndex = days.indexOf(endDay);
                const min = Math.min(startIndex, endIndex);
                const max = Math.max(startIndex, endIndex);

                pills.each(function() {
                    const day = $(this).data('day');
                    const index = days.indexOf(day);
                    if (index === min || index === max) {
                        $(this).addClass('active');
                    } else if (index > min && index < max) {
                        $(this).addClass('range-mid');
                    }
                });
                
                const rangeStr = `${days[min]} - ${days[max]}`;
                rangeInput.val(rangeStr);
            } else if (startDay) {
                pills.filter(`[data-day="${startDay}"]`).addClass('active');
                rangeInput.val(startDay);
            } else {
                rangeInput.val('');
            }
        }

        // Initialize from existing value
        const initialValue = rangeInput.val();
        if (initialValue) {
            if (initialValue.includes(' - ')) {
                const parts = initialValue.split(' - ');
                startDay = parts[0];
                endDay = parts[1];
            } else {
                startDay = initialValue;
            }
            updateUI();
        }

        pills.on('click', function() {
            const clickedDay = $(this).data('day');

            if (!startDay || (startDay && endDay)) {
                startDay = clickedDay;
                endDay = null;
            } else {
                endDay = clickedDay;
            }
            updateUI();
        });
    });
</script>
@endsection
