<div class="card-datatable table-responsive">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <a href="{{ url('warehouse_reports') }}" class="btn btn-sm btn-outline-secondary me-2">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
            <span class="text-uppercase fw-bold">{{ $brand ? $brand->brand_name : 'BRAND' }}</span> - STOCK DETAILS
        </h5>
    </div>
    
    <table class="table table-hover table-bordered datatables-basic" id="brandDetailsTable">
        <thead class="table-light">
            <tr>
                <th class="fw-bold">STYLE</th>
                <th class="fw-bold">ART NO</th>
                <th class="text-center fw-bold">STOCK QTY</th>
                <th class="text-end fw-bold">STOCK VALUE</th>
            </tr>
        </thead>
        <tbody>
            {{-- Dummy Data for Static UI --}}
            <tr>
                <td>Half Sleeve Shirt</td>
                <td>ART-1001</td>
                <td class="text-center">150</td>
                <td class="text-end fw-bold">₹75,000.00</td>
            </tr>
            <tr>
                <td>Full Sleeve Shirt</td>
                <td>ART-1002</td>
                <td class="text-center">320</td>
                <td class="text-end fw-bold">₹1,60,000.00</td>
            </tr>
            <tr class="bg-light">
                <td>Formal Trousers</td>
                <td>ART-2005</td>
                <td class="text-center">85</td>
                <td class="text-end fw-bold">₹85,000.00</td>
            </tr>
            <tr>
                <td>Casual T-Shirt</td>
                <td>ART-3010</td>
                <td class="text-center">500</td>
                <td class="text-end fw-bold">₹2,00,000.00</td>
            </tr>
        </tbody>
        <tfoot class="table-light">
            <tr>
                <td colspan="2" class="text-end fw-bold">TOTAL:</td>
                <td class="text-center fw-bold">1055</td>
                <td class="text-end fw-bold">₹5,20,000.00</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
        if ($('#brandDetailsTable').length && !$.fn.DataTable.isDataTable('#brandDetailsTable')) {
            $('#brandDetailsTable').DataTable({
                "pageLength": 25,
                "ordering": true,
                "info": true,
                "searching": true,
                "lengthChange": true
            });
        }
    });
</script>
