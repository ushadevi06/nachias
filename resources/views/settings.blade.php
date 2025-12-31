@extends('layouts.common')
@section('title', 'Settings - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Settings</h4>
                    </div>
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
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
                                        <img src="{{ asset('uploads/logo/' . $setting->logo) }}"
                                            alt="Current Logo"
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

                            <div class="col-md-6 col-xl-4">
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

                            <div class="col-md-6 col-xl-4">
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

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('address') is-invalid @enderror" id="address" name="address" placeholder="Enter Address">{{ old('address', $setting->address ?? '') }}</textarea>
                                    <label for="address">Address *</label>
                                    @error('address')
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
                                    <label for="po_prefix">PO Prefix</label>
                                    @error('po_prefix')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('purchase_invoice_prefix') is-invalid @enderror" id="purchase_invoice_prefix" placeholder="Inv Prefix (e.g., INV/24-25/)" name="purchase_invoice_prefix" value="{{ old('purchase_invoice_prefix', $setting->purchase_invoice_prefix ?? '') }}">
                                    <label for="purchase_invoice_prefix">Purchase Invoice Prefix</label>
                                    @error('purchase_invoice_prefix')
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
                                    <input class="form-control @error('cgst') is-invalid @enderror" id="cgst" name="cgst" type="number" min="0" max="100" placeholder="Enter CGST (%)" value="{{ old('cgst', $setting->cgst ?? '') }}" />
                                    <label for="cgst">CGST (%) *</label>
                                    @error('cgst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('sgst') is-invalid @enderror" id="sgst" name="sgst" type="number" min="0" max="100" placeholder="Enter SGST (%)" value="{{ old('sgst', $setting->sgst ?? '') }}" />
                                    <label for="sgst">SGST (%) *</label>
                                    @error('sgst')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('igst') is-invalid @enderror" id="igst" name="igst" type="number" min="0" max="100" placeholder="Enter IGST (%)" value="{{ old('igst', $setting->igst ?? '') }}" />
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

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
