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