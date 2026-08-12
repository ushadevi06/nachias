@if(Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{ session('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(Session::get('danger'))
<div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
    <strong>{{ session('danger') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
    <strong>{!! session('error') !!}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(Session::get('info'))
<div class="alert alert-info alert-dismissible fade show mb-5" role="alert">
    <strong>{{ session('info') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
{{-- @if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-5" role="alert">
    <strong>Please fix the following:</strong>
    <ul class="mb-0">
        @foreach ($errors->all() as $message)
        <li>{{ $message }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif --}}