@extends('layouts.common')
@section('title', 'Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Job Card Entry</h4>
                <a class="btn btn-primary" href="{{ url('job_card_entries/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable table-responsive">
                        <table class="table datatables-jc">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card No</th>
                                    <th>Date</th>
                                    <th>PO Number</th>
                                    <th>Brand</th>
                                    <th>Season</th>
                                    <th>Process Group</th>
                                    <th>Total QTY</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datatables-jc').DataTable({
        responsive: true,
        paging: true,
        autoWidth: false,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        ajax: "{{ url('job_card_entries') }}",
        columns: [
            { data: 'DT_RowIndex' },
            { data: 'job_card_no' },
            { data: 'job_card_date' },
            { data: 'po_number' },
            { data: 'brand' },
            { data: 'season' },
            { data: 'process_group' },
            { data: 'total_qty' },
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
});
</script>
@endsection
