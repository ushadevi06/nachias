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
                    <form action="{{ url('profile') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-center mb-3">
                                    @php
                                        $profileImagePath = $user->profile_image 
                                            ? public_path('uploads/profile/' . $user->id . '/' . $user->profile_image)
                                            : null;
                                        $profileImageUrl = ($user->profile_image && file_exists($profileImagePath))
                                            ? url('uploads/profile/' . $user->id . '/' . $user->profile_image)
                                            : url('assets/images/user.jpg');
                                    @endphp
                                    <img id="profileImagePreview" src="{{ $profileImageUrl }}" alt="Profile Image" 
                                        class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ccc;">
                                </div>
                                <div class="text-center">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, GIF</small>
                                    @if($errors->has('profile_image'))
                                        <p class="text-danger mt-1 mb-0">{{ $errors->first('profile_image') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                                    <input type="text" class="form-control" id="Phone Number" placeholder="Enter Phone Number" name="phone" value="{{ old('phone', $user->phone) }}">
                                    <label for="name">Phone Number *</label>
                                </div>
                                <p class="text-danger">{{ $errors->first('phone') }}</p>
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

@section('scripts')
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection