@extends('layouts.common')
@section('title', 'Credit Notes - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Credit Notes</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create credit-notes'))
                <a class="btn btn-primary" href="{{ url('credit_notes/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="card-datatable table-responsive">
                        <table class="table" id="creditNoteTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Note No.</th>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
        var table = $('#creditNoteTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: "{{ url('credit_notes') }}",
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'note_no' },
                { data: 'note_date' },
                { data: 'sales_invoice_no' },
                { data: 'customer_name' },
                { data: 'grand_total' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
            ]
        });

        $(document).on('change', '.status-dropdown', function() {
            var id = $(this).data('id');
            var status = $(this).val();
            var $msg = $('.status_msg_' + id);

            $.ajax({
                url: "{{ url('credit_notes/status') }}/" + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    $msg.html('<span class="text-success small">Updated Successfully!</span>').fadeIn().delay(1000).fadeOut();
                }
            });
        });
    });
</script>
@endsection
