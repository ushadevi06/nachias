@extends('layouts.common')
@section('title', 'Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Billing</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create billing'))
                <a class="btn btn-primary" href="{{ url('billing/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 country">
                                <div class="form-floating form-floating-outline">
                                    <select name="bill_type" id="bill_type" class="form-select select2" data-placeholder="Select Bill Type">
                                        <option value="">Select Bill Type</option>
                                        <option value="Purchase">Purchase</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Service">Service</option>
                                        <option value="Job Work">Job Work</option>
                                        <option value="Transport">Transport</option>
                                    </select>
                                    <label for="bill_type">Bill Type</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="table" id="billingTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bill Number</th>
                                    <th>Bill Type</th>
                                    <th>Bill Date</th>
                                    <th>Amount</th>
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
@endsection

@section('scripts')
    <script>
        $(function() {
            var table = $('#billingTable').DataTable({
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
                    url: "{{ url('billing') }}",
                    data: function(d) {
                        d.bill_type = $('#bill_type').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'bill_no' },
                    { data: 'billing_type' },
                    { data: 'bill_date' },
                    { data: 'amount' },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Trigger reload on filter click
            $('#filterBtn').click(function() {
                console.log("Filter button clicked. Value:", $('#bill_type').val());
                table.ajax.reload();
            });

            $('#resetBtn').click(function() {
                $('#bill_type').val('').trigger('change');
                table.ajax.reload();
            });

            $(document).on('change', '.status-dropdown', function() {
                var id = $(this).data('id');
                var status = $(this).val();
                var $msg = $('.status-msg-' + id);

                $.ajax({
                    url: "{{ url('billing/update-status') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        $msg.html('<span class="text-success">Status Changed!</span>').fadeIn().delay(3000).fadeOut();
                    },
                    error: function() {
                        $msg.html('<span class="text-danger">Error!</span>').fadeIn().delay(3000).fadeOut();
                    }
                });
            });
        });
    </script>
@endsection