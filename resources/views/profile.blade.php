@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp
@extends('layouts.common')
@section('title', 'Edit Profile - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <div class="col-lg-12">
                        @include('flash_messages')
                    </div>
                    <form action="{{ url('profile') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" name="id" value="{{ isset($user->id) ? $user->id : '' }}">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="{{ old('name', $user->name) }}">
                                    <label for="name">Name *</label>
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email" value="{{ old('email', $user->email) }}">
                                    <label for="email">Email *</label>
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-lg-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="Phone Number" placeholder="Enter Phone Number" name="phn_no" value="{{ old('phn_no', $user->phone) }}">
                                    <label for="name">Phone Number *</label>
                                </div>
                                <p class="text-danger">{{ $errors->first('phn_no') }}</p>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-password-toggle form-control-validation">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" class="form-control" id="Password" placeholder="Enter Password" name="password" onkeypress="return event.charCode !== 32" value="{{ old('password') }}">
                                            <label for="name">Password *</label>
                                            <input type="hidden" name="password_old" value="{{ $user->password }}">
                                        </div>
                                        <span class="input-group-text cursor-pointer" id="form-alignment-password2"><i class="icon-base ri ri-eye-off-line"></i></span>
                                    </div>
                                </div>
                                <p class="text-danger">{{ $errors->first('password') }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection