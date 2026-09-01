<!-- Breadcrumb Bar for Level 2 Drilldown -->
<div class="mb-3 d-flex align-items-center" id="poSupplierBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderPoSupplierLevel()" id="btnBackToPoSupplier">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Purchase Orders
    </button>
    <span class="text-dark fw-bold" id="poSupplierBreadcrumbText">All Purchase Orders</span>
</div>

<!-- Level 1: PO Summary Table -->
<div id="poSupplierContainer" class="table-responsive">
    <table id="poSupplierTable" class="table premium-table datatables-po-supplier mb-0">
        <thead>
            <tr>
                <th style="width: 45px;">#</th>
                <th>PO NO</th>
                <th>PO DATE</th>
                <th>SUPPLIER</th>
                <th>ORDER {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>RECEIVED {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>BALANCE {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>ORDER DATE</th>
                <th>EXPECTED DELIVERY</th>
                <th>DELAY</th>
                <th>REMARKS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="4" class="text-end">TOTAL</td>
                <td id="footer-po-ordered" class="text-end">0.00</td>
                <td id="footer-po-received" class="text-end">0.00</td>
                <td id="footer-po-pending" class="text-end">0.00</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Level 2: PO Items Drilldown Table -->
<div id="poItemsContainer" class="table-responsive" style="display: none;">
    <div class="card shadow-sm border mb-3">
        <div class="card-body p-0">
            <table id="poItemsTable" class="table premium-table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>MATERIAL NAME</th>
                        <th class="text-end" style="width: 180px;">ORDERED {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                        <th class="text-end" style="width: 180px;">RECEIVED {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                        <th class="text-end" style="width: 180px;">BALANCE {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr class="fw-bold" style="background: #f1f5f9;">
                        <td colspan="2" class="text-end">TOTAL</td>
                        <td id="footer-po-item-ordered" class="text-end">0.00</td>
                        <td id="footer-po-item-received" class="text-end">0.00</td>
                        <td id="footer-po-item-balance" class="text-end">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    #poSupplierTable tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    #poSupplierTable tbody tr:hover {
        background-color: rgba(105, 108, 255, 0.08) !important;
    }
</style>

<script>
    var poSupplierIsRoot = true;

    function renderPoSupplierLevel() {
        poSupplierIsRoot = true;
        $('#poSupplierBreadcrumbs').attr('style', 'display: none !important;');
        $('#poItemsContainer').hide();
        $('#poSupplierContainer').show();

        if ($.fn.DataTable.isDataTable('#poSupplierTable')) {
            $('#poSupplierTable').DataTable().columns.adjust();
        }
    }

    function drillDownToPoItems(poNumber, supplierName, items) {
        poSupplierIsRoot = false;
        $('#poSupplierBreadcrumbs').attr('style', 'display: flex !important;');
        $('#poSupplierBreadcrumbText').html(`All Purchase Orders &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${poNumber}</span> &nbsp; <span class="text-muted small">(${supplierName})</span>`);

        $('#poSupplierContainer').hide();
        $('#poItemsContainer').show();

        if ($.fn.DataTable.isDataTable('#poItemsTable')) {
            $('#poItemsTable').DataTable().clear().destroy();
        }
        $('#poItemsTable tbody').empty();

        let sumOrd = 0, sumRec = 0, sumBal = 0;
        (items || []).forEach(function(item) {
            sumOrd += parseFloat((item.ordered || '0').replace(/,/g, '')) || 0;
            sumRec += parseFloat((item.received || '0').replace(/,/g, '')) || 0;
            sumBal += parseFloat((item.balance || '0').replace(/,/g, '')) || 0;
        });

        $('#footer-po-item-ordered').text(sumOrd.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#footer-po-item-received').text(sumRec.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#footer-po-item-balance').text(sumBal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('#poItemsTable').DataTable({
            data: items || [],
            autoWidth: false,
            columns: [
                { data: 'sno', className: 'text-center' },
                { data: 'material_name', className: 'fw-semibold text-dark' },
                { data: 'ordered', className: 'text-end fw-bold text-dark' },
                { data: 'received', className: 'text-end text-primary fw-bold' },
                { data: 'balance', className: 'text-end text-danger fw-bold' }
            ],
            dom: '<"row p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row p-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            pageLength: 10,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            language: {
                emptyTable: 'No items found for this purchase order.'
            }
        });
    }

    (function() {
        const $table = $('#poSupplierTable');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        const dt = $table.DataTable({
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
                    d.report_type = 'po-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'po_number' },
                { data: 'po_date', className: 'text-center' },
                { data: 'supplier_name' },
                { data: 'total_ordered', className: 'text-end' },
                { data: 'total_received', className: 'text-end' },
                { data: 'total_pending', className: 'text-end' },
                { data: 'order_date', className: 'text-center' },
                { data: 'expected_delivery', className: 'text-center' },
                { data: 'delay', className: 'text-center' },
                { data: 'remarks' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-po-ordered').html(json.totals.total_ordered);
                    $('#footer-po-received').html(json.totals.total_received);
                    $('#footer-po-pending').html(json.totals.total_pending);
                }
            },
            language: {
                emptyTable: 'No purchase orders found.'
            }
        });

        // Click row to drilldown to items table (like brandwise stock)
        $table.find('tbody').off('click', 'tr').on('click', 'tr', function(e) {
            if ($(e.target).is('a, button, input')) return;

            var tr = $(this);
            var row = dt.row(tr);

            if (!row || !row.data()) return;
            var data = row.data();

            drillDownToPoItems(data.po_number_raw || data.po_number, data.supplier_name, data.items || []);
        });
    })();
</script>
