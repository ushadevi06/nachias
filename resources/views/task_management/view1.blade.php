@extends('layouts.common')
@section('title', 'Task Management List - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Task Management List</h4>
                        <div class="d-flex gap-2">
                             <a href="{{ url('task_management/kanban') }}" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="ri-kanban-view me-1"></i> Kanban Board
                            </a>
                            <a href="{{ url('task_management/add') }}" class="btn btn-primary waves-effect waves-light">
                                <i class="ri-add-line me-1"></i> Add Task
                            </a>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Task No</th>
                                    <th>Assigned To</th>
                                    <th>Stage / Dept</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
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
    $(document).ready(function() {
        if ($('.datatables-products').length) {
            $('.datatables-products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('task_management') }}",
                },
                columns: [
                    {data: 'task_no', name: 'task_no'},
                    {data: 'assigned_to', name: 'assigned_to'},
                    {data: 'stage_dept', name: 'stage_dept'},
                    {data: 'progress', name: 'progress'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[0, 'desc']],
                dom: '<"card-header border-bottom p-1"<"head-label"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
            });
        }
    });

    function delete_data(url) {
        if (confirm("Are you sure you want to delete this task?")) {
            window.location.href = url;
        }
    }
</script>
@endsection
