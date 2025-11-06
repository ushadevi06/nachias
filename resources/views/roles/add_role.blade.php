@extends('layouts.common')
@section('title', 'Add Role - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Role</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Role Name" name="name">
                                    <label for="name">Role Name * </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 10%;">Allow All</th>
                                                <th>Module</th>
                                                <th>Add</th>
                                                <th>Edit</th>
                                                <th>View</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modules as $main => $submodules)
                                                <tr class="table-primary">
                                                    <td colspan="6"><strong>{{ $main }}</strong></td>
                                                </tr>

                                                @foreach ($submodules as $sub)
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type="checkbox" class="allow-all-sub" data-sub="{{ $sub }}">
                                                        </td>

                                                        <td class="ps-4">{{ $sub }}</td>

                                                        {{-- Dashboard: Only View --}}
                                                        @if ($main == 'Dashboard')
                                                            <td class="text-center">—</td>
                                                            <td class="text-center">—</td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][view]">
                                                            </td>
                                                            <td class="text-center">—</td>

                                                        {{-- Settings: Only Edit + View --}}
                                                        @elseif ($main == 'Settings')
                                                            <td class="text-center">—</td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][edit]">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][view]">
                                                            </td>
                                                            <td class="text-center">—</td>

                                                        {{-- Other Modules: Full CRUD --}}
                                                        @else
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][add]">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][edit]">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][view]">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="permission" data-sub="{{ $sub }}" name="permissions[{{ $sub }}][delete]">
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="button-box">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ url('roles') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.allow-all-sub').forEach(allowAll => {
        allowAll.addEventListener('change', function() {
            const sub = this.getAttribute('data-sub');
            const isChecked = this.checked;
            document.querySelectorAll(`.permission[data-sub="${sub}"]`).forEach(cb => {
                cb.checked = isChecked;
            });
        });
    });
    document.querySelectorAll('.permission').forEach(perm => {
        perm.addEventListener('change', function() {
            const sub = this.getAttribute('data-sub');
            const all = document.querySelectorAll(`.permission[data-sub="${sub}"]`);
            const allChecked = Array.from(all).every(cb => cb.checked);
            const allowAll = document.querySelector(`.allow-all-sub[data-sub="${sub}"]`);
            if (allowAll) allowAll.checked = allChecked;
        });
    });
});
</script>

@endsection