@extends('layouts.common')
@section('title', 'Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Job Card Entry</h4>
                <a class="btn btn-primary" href="{{ url('add_job_card_entry') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Job Card Number</th>
                                    <th>Sales Order </th>
                                    <th>Customer / Buyer Name</th>
                                    <th>Delivery Date </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>JC20250924-001-K</td>
                                    <td>S0-1001</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>24-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>JC20250924-002-K</td>
                                    <td>SO-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>21-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>JC20250924-003-K</td>
                                    <td>SO-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>21-09-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="#" class="btn btn-item"><i class="icon-base ri ri-box-3-line"></i></i></a>
                                            <a href="{{ url('view_job_card_entry') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_job_card_entry') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
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
<div class="modal fade" id="jcItemsModal" tabindex="-1" aria-labelledby="jcItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="jcItemsModalLabel">Job Card Items</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Line#</th>
                            <th>Item Name</th>
                            <th>Size</th>
                            <th>Art No.</th>
                            <th>Qty To Issue</th>
                            <th>Qty Issued</th>
                            <th>UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="jc-item-row" data-url="{{ url('view_jc_item') }}">
                            <td>1</td>
                            <td><a href="{{ url('view_jc_item') }}">Men's Casual Denim Shirt</a></td>
                            <td>M</td>
                            <td>ART1001</td>
                            <td><input type="number" name="qty_to_issue[]" value="120" class="form-control text-center" min="0"></td>
                            <td><input type="number" name="qty_issued[]" value="80" class="form-control text-center" min="0"></td>
                            <td>PCS</td>
                        </tr>
                        <tr class="jc-item-row" data-url="{{ url('view_jc_item') }}">
                            <td>2</td>
                            <td><a href="{{ url('view_jc_item') }}">Men's Casual Denim Shirt</a></td>
                            <td>L</td>
                            <td>ART1002</td>
                            <td><input type="number" name="qty_to_issue[]" value="150" class="form-control text-center" min="0"></td>
                            <td><input type="number" name="qty_issued[]" value="100" class="form-control text-center" min="0"></td>
                            <td>PCS</td>
                        </tr>
                        <tr class="jc-item-row" data-url="{{ url('view_jc_item') }}">
                            <td>3</td>
                            <td><a href="{{ url('view_jc_item') }}">Formal Cotton Shirt</a></td>
                            <td>38</td>
                            <td>ART1005</td>
                            <td><input type="number" name="qty_to_issue[]" value="200" class="form-control text-center" min="0"></td>
                            <td><input type="number" name="qty_issued[]" value="120" class="form-control text-center" min="0"></td>
                            <td>PCS</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveJcItems" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
  $('.btn-item').on('click', function (e) {
    e.preventDefault();
    $('#jcItemsModal').modal('show');
  });

  $(document).on('click', '.jc-item-row', function (e) {
    if ($(e.target).is('input')) return; 
    let redirectUrl = $(this).data('url');
    window.location.href = redirectUrl;
  });

  $('#saveJcItems').on('click', function () {
    let data = [];
    $('#jcItemsModal tbody tr').each(function () {
      let row = $(this);
      data.push({
        item: row.find('td:nth-child(2)').text(),
        size: row.find('td:nth-child(3)').text(),
        art: row.find('td:nth-child(4)').text(),
        qty_to_issue: row.find('input[name="qty_to_issue[]"]').val(),
        qty_issued: row.find('input[name="qty_issued[]"]').val(),
      });
    });
    alert('JC Item quantities saved!');
  });
});
</script>


@endsection
