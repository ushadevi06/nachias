<div class="mb-3 d-flex align-items-center" id="brandwiseBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderBrandLevel()" id="btnBackToBrands">
        <i class="ri ri-arrow-left-line"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="breadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table id="brandwiseStockTable" class="datatables-products table table-hover">
        <thead class="table-light">
            <tr>
                <th class="fw-bold" id="brandwiseStockCol1">BRAND</th>
                <th class="text-center fw-bold">STOCK</th>
                <th class="text-center fw-bold">LOW STOCK QTY</th>
                <th class="text-center fw-bold">EXCESS STOCK QTY</th>
                <th class="text-center fw-bold">STOCK DAYS</th>
                <th class="text-end fw-bold">STOCK VALUE</th>
            </tr>
        </thead>
        <tbody id="brandwiseStockTbody">
        </tbody>
    </table>
</div>

<style>
    /* Styling for hover effect to indicate it's clickable */
    #brandwiseStockTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
    let currentBrandId = null;
    let currentBrandName = '';
    let dtInstance = null;
    let isRootLevel = true;

    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function formatCurrency(num) {
        return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function reinitDataTable() {
        if ($.fn.DataTable.isDataTable('#brandwiseStockTable')) {
            $('#brandwiseStockTable').DataTable().destroy();
        }
        $('#brandwiseStockTbody').empty();
    }

    function applyDataTable(showExtraColumns = false) {
        $('#brandwiseStockTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            columnDefs: [
                { targets: [2, 3, 4], visible: showExtraColumns }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: ['excel', 'pdf', 'print'],
            language: {
                emptyTable: "No data found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            }
        });
    }
    
    function applyServerSideDataTable() {
        isRootLevel = true;
        dtInstance = $('#brandwiseStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/ajax/brandwise-stock') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                    d.store_id = $('select[name="store_id"]').val();
                }
            },
            columns: [
                { data: 'brand', name: 'brand', width: '45%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '15%' },
                { data: 'low_stock', name: 'low_stock', className: 'text-center', orderable: false, visible: false, width: '10%' },
                { data: 'excess_stock', name: 'excess_stock', className: 'text-center', orderable: false, visible: false, width: '10%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, visible: false, width: '10%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '25%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No data found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            }
        });
    }

    function renderBrandLevel() {
        $('#brandwiseBreadcrumbs').attr('style', 'display: none !important;');
        $('#brandwiseStockCol1').text('BRAND');
        
        reinitDataTable();
        applyServerSideDataTable();
    }
    
    $(document).ready(function() {
        applyServerSideDataTable();
    });

    function drillDownToStyle(brandId, brandName) {
        isRootLevel = false;
        currentBrandId = brandId;
        currentBrandName = brandName;
        
        let storeId = $('select[name="store_id"]').val();
        let fromDate = $('.start_date').val();
        let toDate = $('.end_date').val();

        reinitDataTable();
        $('#brandwiseStockTbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        
        $.ajax({
            url: "{{ url('warehouse_reports/brandwise_styles') }}",
            type: "GET",
            data: {
                brand_id: brandId,
                store_id: storeId,
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                $('#brandwiseBreadcrumbs').attr('style', 'display: flex !important;');
                $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);
                $('#btnBackToBrands').attr('onclick', 'renderBrandLevel()');
                $('#brandwiseStockCol1').text('STYLE');
                
                let html = '';
                if(response && response.length > 0) {
                    response.forEach(function(item) {
                        let totalQty = parseFloat(item.total_qty || 0);
                        let totalMinStock = parseFloat(item.total_min_stock || 0);
                        let lowStock = Math.max(0, totalMinStock - totalQty);
                        let excessStock = Math.max(0, totalQty - (totalMinStock * 2));
                        
                        let lowStockDisplay = '-';
                        let excessStockDisplay = '-';

                        html += `<tr style="cursor: pointer;" onclick="drillDownToArtNo(${brandId}, '${brandName.replace(/'/g, "\\'")}', ${item.style_id}, '${(item.style_name || '-').replace(/'/g, "\\'")}')">
                            <td class="text-uppercase"><strong>${item.style_name || '-'}</strong></td>
                            <td class="text-center">${formatNumber(totalQty)}</td>
                            <td class="text-center">${lowStockDisplay}</td>
                            <td class="text-center">${excessStockDisplay}</td>
                            <td class="text-center">-</td>
                            <td class="text-end fw-bold">${formatCurrency(item.stock_value)}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No styles found.</td></tr>';
                }
                $('#brandwiseStockTbody').html(html);
                applyDataTable(false);
            },
            error: function() {
                $('#brandwiseStockTbody').html('<tr><td colspan="6" class="text-center text-danger">Error fetching styles.</td></tr>');
            }
        });
    }

    function drillDownToArtNo(brandId, brandName, styleId, styleName) {
        let storeId = $('select[name="store_id"]').val();
        let fromDate = $('.start_date').val();
        let toDate = $('.end_date').val();

        reinitDataTable();
        $('#brandwiseStockTbody').html('<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        
        $.ajax({
            url: "{{ url('warehouse_reports/brandwise_art_nos') }}",
            type: "GET",
            data: {
                brand_id: brandId,
                style_id: styleId,
                store_id: storeId,
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')">${brandName}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${styleName}</span>`);
                $('#btnBackToBrands').attr('onclick', `drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')`);
                $('#brandwiseStockCol1').text('ART NO');
                
                let html = '';
                if(response && response.length > 0) {
                    response.forEach(function(item) {
                        let totalQty = parseFloat(item.total_qty || 0);
                        let displayQty = totalQty < 0 ? 0 : totalQty; 
                        let totalMinStock = parseFloat(item.total_min_stock || 0);
                        let lowStock = Math.max(0, totalMinStock - displayQty);
                        let excessStock = Math.max(0, displayQty - (totalMinStock * 2));
                        let stockValue = parseFloat(item.stock_value || 0);
                        let displayStockValue = stockValue < 0 ? 0 : stockValue;
                        
                        let lowStockDisplay = lowStock > 0 ? `<span class="text-danger fw-bold">${formatNumber(lowStock)}</span>` : '-';
                        let excessStockDisplay = excessStock > 0 ? `<span class="text-success fw-bold">${formatNumber(excessStock)}</span>` : '-';

                        html += `<tr>
                            <td class="text-uppercase"><strong>${item.art_no || '-'}</strong></td>
                            <td class="text-center">${formatNumber(displayQty)}</td>
                            <td class="text-center">${lowStockDisplay}</td>
                            <td class="text-center">${excessStockDisplay}</td>
                            <td class="text-center">${item.stock_days || '-'}</td>
                            <td class="text-end fw-bold">${formatCurrency(displayStockValue)}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center text-muted">No Art Nos found.</td></tr>';
                }
                $('#brandwiseStockTbody').html(html);
                applyDataTable(true);
            },
            error: function() {
                $('#brandwiseStockTbody').html('<tr><td colspan="6" class="text-center text-danger">Error fetching Art Nos.</td></tr>');
            }
        });
    }
</script>
