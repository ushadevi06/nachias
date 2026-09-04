@extends('layouts.common')
@section('title', 'Task Management List - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Management List</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-3">
                                <select id="filter_status" class="form-select select2" data-placeholder="Select Status">
                                    <option value=""></option>
                                    <option value="Planned">Planned</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Hold">Hold</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter_btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Task No</th>
                                    <th>Plant</th>
                                    <th>Stage</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
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
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('task_management') }}",
                    data: function(d) {
                        d.status = $('#filter_status').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'task_no', name: 'task_no'},
                    {data: 'plant', name: 'plant'},
                    {data: 'stage_dept', name: 'stage_dept'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[1, 'desc']],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row pt-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
            });
        }

        $('#filter_btn').on('click', function() {
            $('.datatables-products').DataTable().ajax.reload();
        });

        $('#reset_btn').on('click', function() {
            $('#filter_status').val('').trigger('change');
            $('.datatables-products').DataTable().ajax.reload();
        });
    });

    function delete_data(url) {
        if (confirm("Are you sure you want to delete this task?")) {
            window.location.href = url;
        }
    }
</script>
@endsection