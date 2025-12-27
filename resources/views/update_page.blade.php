@php
use Illuminate\Support\Facades\Auth;
$user = Auth::user();
@endphp

@extends('layouts.common')
@section('title', 'Update Page - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm mt-5">
                <div class="card-body text-center py-5">

                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="ri ri-megaphone-line" style="font-size: 60px; color: #696cff;"></i>
                    </div>

                    <!-- Title -->
                    <h3 class="mb-3 fw-semibold">🚀 Feature Coming Soon</h3>

                    <!-- Description -->
                    <p class="text-muted fs-6 mb-3">
                        This page is currently under development and will be available soon.
                    </p>

                    <p class="text-muted">
                        We are working hard to bring you this feature with a better experience.
                        Please check back later.
                    </p>

                    <!-- Optional Info Box -->
                    <div class="alert alert-info mt-4">
                        <strong>Note:</strong> The update page will be released in the next version update.
                    </div>

                    <!-- Back Button -->
                    <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">
                        <i class="ri ri-arrow-left-line"></i> Go Back
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection