@extends('layouts.common')
@section('title', ($role ? 'Edit' : 'Add') . ' Role - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $role->id ? 'Edit Role' : 'Add Role' }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ $role->id ? url('roles/add/'.$role->id) : url('roles/add') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Role Name" value="{{ old('name', $role->name) }}">
                                    <label for="name">Role Name <span class="text-danger">*</span> </label>
                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 15%;">Allow All</th>
                                                <th>Module</th>
                                                <th>Add</th>
                                                <th>Edit</th>
                                                <th>View</th>
                                                <th>Delete</th>
                                                <th>View Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($permissions as $main => $submodules)
                                            <tr class="table-primary">
                                                <td colspan="7">
                                                    <strong>{{ ucwords(str_replace('-', ' ', $main)) }}</strong>
                                                </td>
                                            </tr>
                                            @php
                                                $grouped = $submodules->groupBy('action');
                                                $availablePermissions = [];
                                                if(isset($grouped['create'])) $availablePermissions[] = $grouped['create'][0]->name;
                                                if(isset($grouped['edit'])) $availablePermissions[] = $grouped['edit'][0]->name;
                                                if(isset($grouped['view'])) $availablePermissions[] = $grouped['view'][0]->name;
                                                if(isset($grouped['delete'])) $availablePermissions[] = $grouped['delete'][0]->name;
                                                if(isset($grouped['view_details'])) $availablePermissions[] = $grouped['view_details'][0]->name;
                                                $checkedPermissions = array_intersect($availablePermissions, $rolePermissions ?? []);
                                                $allChecked = count($availablePermissions) > 0 && count($availablePermissions) === count($checkedPermissions);
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                        class="allow-all-sub"
                                                        data-sub="{{ $main }}"
                                                        {{ $allChecked ? 'checked' : '' }}>

                                                </td>
                                                <td class="ps-4">
                                                    {{ ucwords(str_replace(['-', '_'], ' ', $main)) }}
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($grouped['create']))
                                                    <input type="checkbox" class="permission"
                                                        data-sub="{{ $main }}"
                                                        name="permissions[]"
                                                        value="create {{ $main }}"
                                                        {{ in_array("create $main", $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($grouped['edit']))
                                                    <input type="checkbox"
                                                        class="permission"
                                                        data-sub="{{ $main }}"
                                                        name="permissions[]"
                                                        value="edit {{ $main }}"
                                                        {{ in_array("edit $main", $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($grouped['view']))
                                                    <input type="checkbox"
                                                        class="permission"
                                                        data-sub="{{ $main }}"
                                                        name="permissions[]"
                                                        value="view {{ $main }}"
                                                        {{ in_array("view $main", $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($grouped['delete']))
                                                    <input type="checkbox"
                                                        class="permission"
                                                        data-sub="{{ $main }}"
                                                        name="permissions[]"
                                                        value="delete {{ $main }}"
                                                        {{ in_array("delete $main", $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(isset($grouped['view_details']))
                                                    <input type="checkbox"
                                                        class="permission"
                                                        data-sub="{{ $main }}"
                                                        name="permissions[]"
                                                        value="view_details {{ $main }}"
                                                        {{ in_array("view_details $main", $rolePermissions ?? []) ? 'checked' : '' }}>
                                                    @else
                                                    —
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('roles') }}" class="btn btn-secondary">Cancel</a>
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