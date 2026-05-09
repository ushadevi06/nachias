@extends('layouts.common')
@section('title', 'Payments - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Payments</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create manage-payments'))
                <a class="btn btn-primary" href="{{ url('payments/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="payment_type_filter" id="payment_type_filter" class="form-select select2" data-placeholder="Select Payment Type">
                                        <option value="">Select Payment Type</option>
                                        <option value="Customer Collection">Customer Collection</option>
                                        <option value="Supplier Payment">Supplier Payment</option>
                                        <option value="Agent Commission">Agent Commission</option>
                                    </select>
                                    <label for="payment_type_filter">Payment Type</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filter_btn" class="btn btn-primary">Filter</button>
                                <button type="button" id="reset_btn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable">
                        <table class="datatables-payments table" id="payments_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment No</th>
                                    <th>Payment Type</th>
                                    <th>Reference</th>
                                    <th>Mode</th>
                                    <th>Amount</th>
                                    <th>Date</th>
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

@endsection

@section('scripts')
<script>
    $(function() {
        var table = $('#payments_table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('payments') }}",
                data: function(d) {
                    d.payment_type = $('#payment_type_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'payment_no', name: 'payment_no' },
                { data: 'payment_type', name: 'payment_type' },
                { data: 'reference', name: 'reference' },
                { data: 'payment_mode', name: 'payment_mode' },
                { data: 'amount', name: 'amount' },
                { data: 'payment_date', name: 'payment_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "_INPUT_",
            }
        });

        $('#filter_btn').click(function() {
            table.ajax.reload();
        });

        $('#reset_btn').click(function() {
            $('#payment_type_filter').val('').trigger('change');
            table.ajax.reload();
        });
    });

    function delete_data(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the payment and REVERT the invoice balance!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
@endsection