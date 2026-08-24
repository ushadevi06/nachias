@extends('layouts.common')
@section('title', 'Employees - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4 class="mb-0">Employees</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create employees'))
                <div class="d-flex gap-2">
                    {{-- <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                    </button> --}}
                    <a class="btn btn-outline-success" href="{{ url('employees/export-excel') }}">
                        <i class="menu-icon icon-base ri ri-file-excel-2-line"></i> Export
                    </a>
                    <a class="btn btn-primary" href="{{ url('employees/add') }}">
                        <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                    </a>
                </div>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-3">
                                <select id="department" class="form-select select2" data-placeholder="Select Department">
                                    <option value="">Select Department</option>
                                    @foreach(\App\Models\Department::where('status','Active')->get() as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select id="role" class="form-select select2" data-placeholder="Select Role">
                                    <option value="">Select Role</option>
                                    @foreach(\App\Models\Role::where('status','Active')->get() as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="filter-btn">
                                    Filter
                                </button>

                                <button type="button" class="btn btn-secondary" id="reset-btn">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="employeesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Service Provider</th>
                                    <th>Operation Stage</th>
                                    <th>Contact Info</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Employees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('employees/import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">
                            Upload File (CSV, Excel)
                        </label>
                        <input class="form-control"
                            type="file"
                            id="import_file"
                            name="import_file"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            required>
                    </div>
                    <!-- Note -->
                    <div class="alert alert-info py-2">
                        <small>
                            <strong>Note:</strong>
                            Import 500 employees at a time, not exceed.
                        </small>
                    </div>
                    <div class="mb-3">
                        <a href="{{ url('employees/download-sample') }}"
                            class="btn btn-sm btn-outline-info">
                            <i class="ri ri-download-2-line"></i>
                            Download Sample Format
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit"
                        class="btn btn-primary"
                        id="importBtn">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        let table = $('#employeesTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            ajax: {
                url: "{{ url('employees') }}",
                type: "GET",
                data: function(d) {
                    d.department = $('#department').val();
                    d.role = $('#role').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'image',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'role'
                },
                {
                    data: 'department'
                },
                {
                    data: 'service_provider'
                },
                {
                    data: 'operation_stage'
                },
                {
                    data: 'contact_info'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });

        $('#reset-btn').on('click', function() {
            $('#department').val('').trigger('change');
            $('#role').val('').trigger('change');
            table.ajax.reload();
        });
    });
    document.getElementById('importForm').addEventListener('submit', function () {
        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-1"></span>
            Loading...
        `;
    });
    $(document).on('change', '.employee-status-toggle', function() {

        let employeeId = $(this).data('id');
        let status = $(this).is(':checked') ? 'Active' : 'Inactive';

        $.ajax({
            url: "{{ url('employees/status') }}/" + employeeId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: status
            },
            success: function(response) {
                if (response.success) {

                    let msg = response.status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Deactivated</span>';

                    $('.status_msg_' + employeeId).html(msg).fadeIn().delay(1200).fadeOut();

                } else {
                    alert('Status update failed');
                }
            }
        });

    });
</script>

@endsection
