@extends('layouts.common')
@section('title', 'Shifts - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Shifts</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create shifts'))
                <a class="btn btn-primary" href="{{ url('shifts/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="shiftTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Shift Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dummy Data for UI Preview --}}
                                <tr>
                                    <td>1</td>
                                    <td>I</td>
                                    <td>09:00 AM</td>
                                    <td>06:00 PM</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="#" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>II</td>
                                    <td>09:00 AM</td>
                                    <td>06:00 PM</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="#" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>III</td>
                                    <td>09:00 AM</td>
                                    <td>06:00 PM</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="#" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        // Placeholder for DataTable init
        $('#shiftTable').DataTable();
    });
</script>
@endsection
