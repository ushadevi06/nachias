<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-return" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th class="text-nowrap">RETURN DATE</th>
                <th class="text-nowrap">DEBIT NOTE NO</th>
                <th>SUPPLIER NAME</th>
                <th>ITEM NAME</th>
                <th class="text-nowrap text-end">{{ !empty($isFabric) ? 'RETURNED METERS' : 'RETURNED QTY' }}</th>
                <th class="text-end">RATE</th>
                <th class="text-end text-nowrap">SUB TOTAL</th>
                <th class="text-end">DISCOUNT</th>
                <th class="text-end text-nowrap">TAXABLE VALUE</th>
                <th class="text-center text-nowrap">CGST %</th>
                <th class="text-end text-nowrap">CGST VALUE</th>
                <th class="text-center text-nowrap">SGST %</th>
                <th class="text-end text-nowrap">SGST VALUE</th>
                <th class="text-center text-nowrap">IGST %</th>
                <th class="text-end text-nowrap">IGST VALUE</th>
                <th class="text-end text-nowrap">ROUND OFF</th>
                <th class="text-end text-nowrap">TOTAL AMOUNT</th>
                <th>REASON</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="5" class="text-end">TOTAL</td>
                <td id="footer-return-qty" class="text-end">0.00</td>
                <td></td>
                <td id="footer-return-subtotal" class="text-end">0.00</td>
                <td id="footer-return-discount" class="text-end">0.00</td>
                <td id="footer-return-taxable" class="text-end">0.00</td>
                <td></td>
                <td id="footer-return-cgst" class="text-end">0.00</td>
                <td></td>
                <td id="footer-return-sgst" class="text-end">0.00</td>
                <td></td>
                <td id="footer-return-igst" class="text-end">0.00</td>
                <td id="footer-return-roundoff" class="text-end">0.00</td>
                <td id="footer-return-total" class="text-end">0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
        const $table = $('.datatables-return');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        $table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pageLength: 10,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            ajax: {
                url: window.location.pathname,
                data: function(d) {
                    d.report_type = 'return-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'return_date', className: 'text-nowrap' },
                { data: 'return_no', className: 'text-nowrap fw-semibold' },
                { data: 'supplier_name' },
                { data: 'item_name' },
                { data: 'quantity', className: 'text-end' },
                { data: 'rate', className: 'text-end' },
                { data: 'sub_total', className: 'text-end' },
                { data: 'discount', className: 'text-end' },
                { data: 'taxable_value', className: 'text-end' },
                { data: 'cgst_percent', className: 'text-center text-nowrap' },
                { data: 'cgst_amount', className: 'text-end text-nowrap' },
                { data: 'sgst_percent', className: 'text-center text-nowrap' },
                { data: 'sgst_amount', className: 'text-end text-nowrap' },
                { data: 'igst_percent', className: 'text-center text-nowrap' },
                { data: 'igst_amount', className: 'text-end text-nowrap' },
                { data: 'round_off', className: 'text-end text-nowrap' },
                { data: 'total_amount', className: 'text-end fw-bold text-nowrap' },
                { data: 'reason' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-return-qty').html(json.totals.quantity);
                    $('#footer-return-subtotal').html(json.totals.sub_total);
                    $('#footer-return-discount').html(json.totals.discount);
                    $('#footer-return-taxable').html(json.totals.taxable_value);
                    $('#footer-return-cgst').html(json.totals.cgst_amount);
                    $('#footer-return-sgst').html(json.totals.sgst_amount);
                    $('#footer-return-igst').html(json.totals.igst_amount);
                    $('#footer-return-roundoff').html(json.totals.round_off);
                    $('#footer-return-total').html(json.totals.grand_total);
                }
            },
            language: {
                emptyTable: 'No return goods found.'
            }
        });
    });
</script>
