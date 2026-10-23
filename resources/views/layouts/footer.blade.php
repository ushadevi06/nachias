<div class="content-backdrop fade"></div>
</div>
</div>
</div>
</div>

<script src="{{ url('assets/js/select2.js') }}"></script>
<script src="{{ url('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('assets/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ url('assets/datatables/js/dataTables.responsive.min.js') }}"></script>

<!-- DataTables Buttons Extension -->
<script src="{{ url('assets/js/buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('assets/js/buttons/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ url('assets/js/buttons/jszip.min.js') }}"></script>
<script src="{{ url('assets/js/buttons/pdfmake.min.js') }}"></script>
<script src="{{ url('assets/js/buttons/vfs_fonts.js') }}"></script>
<script src="{{ url('assets/js/buttons/buttons.html5.min.js') }}"></script>
<script src="{{ url('assets/js/buttons/buttons.print.min.js') }}"></script>
<script src="{{ url('assets/js/bs-stepper.js') }}"></script>
<script src="{{ url('assets/js/form-wizard-numbered.js') }}"></script>
<script src="{{ url('assets/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ url('assets/js/flatpickr.js') }}"></script>
<script src="{{ url('assets/js/monthSelect/index.js') }}"></script>
<link rel="stylesheet" href="{{ url('assets/css/monthSelect/style.css') }}">
<script src="{{ url('assets/js/menu.js') }}"></script>
<script src="{{ url('assets/js/main.js') }}"></script>
<script src="{{ url('assets/js/common.js') }}"></script>
@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stepperEl = document.querySelector('.bs-stepper.wizard-numbered');
        if (stepperEl) {
            const stepper = new Stepper(stepperEl);
        }
    });
    const menuInner = document.querySelector('.menu-inner');
    const lastMenuItem = menuInner ? menuInner.lastElementChild : null;

    if (menuInner) {
        menuInner.style.marginLeft = '0';
    }

    if (lastMenuItem) {
        lastMenuItem.addEventListener('click', () => {
            lastMenuItem.scrollIntoView({
                behavior: 'smooth',
                inline: 'end'
            });
        });
    }
</script>
<script>
    $(document).ready(function() {
        $('.datatables-products').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                return; // Skip already initialized tables
            }
            var $tbl = $(this);
            var isWideReport = $tbl.closest('#despatch-report, #dispatch-report, #pending-report, #order-report').length > 0 
                || $tbl.is('#dispatchProductWiseTable, #dispatchPartyWiseTable, #pendingProductWiseTable, #pendingPartyWiseTable, #orderProductWiseTable, #orderPartyWiseTable, #despatchReportTable')
                || ($tbl.find('thead th').length > 10);

            var pdfButton = isWideReport ? {
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: ($tbl.find('thead th').length > 14 ? 'A3' : 'A4'),
                customize: function (doc) {
                    doc.pageOrientation = 'landscape';
                    var colCount = (doc.content && doc.content[1] && doc.content[1].table) ? doc.content[1].table.body[0].length : 12;
                    if (colCount > 14) {
                        doc.pageSize = 'A3';
                        doc.defaultStyle.fontSize = 6;
                        doc.styles.tableHeader.fontSize = 6.5;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [10, 15, 10, 15];
                    } else if (colCount > 8) {
                        doc.pageSize = 'A4';
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7.5;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.pageMargins = [12, 15, 12, 15];
                    } else {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8.5;
                        doc.pageMargins = [15, 20, 15, 20];
                    }
                    if (doc.content && doc.content[1] && doc.content[1].table) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('*');
                    }
                }
            } : 'pdf';

            $tbl.DataTable({
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
                dom: '<"d-none"B>lfrtip',
                buttons: [
                    'excel', 
                    pdfButton, 
                    'print'
                ],
                language: {
                    emptyTable: "No data found matching your filters",
                    zeroRecords: "No matching records found",
                    infoEmpty: "Showing 0 to 0 entries",
                }
            });
        });

        var table = $('.datatables-products').DataTable();

        table.on('responsive-display.dt', function(e, datatable, row, showHide, update) {
            if (showHide) {
                var $selects = row.child().find('.select2');
                $selects.each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
            }
        });

        $('#select2Basic').select2({
            placeholder: "Select a country",
            allowClear: false
        });
    });
</script>
<script src="{{ url('assets/js/datepicker-init.js') }}"></script>
<script>
    function delete_data(deleteUrl) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#8c57ff",
            cancelButtonColor: "#ff4c51",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        });
    }
    $(document).ready(function() {
        $(document).on('click', '.view-image', function() {
            let imageUrl = $(this).data('image');
            if (imageUrl) {
                $('#modalImage').attr('src', imageUrl);
                $('#imageModal').modal('show');
            }
        });
    });
</script>
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalImage" src="" class="rounded shadow-sm d-block mx-auto" style="max-width: 100%; max-height: 70vh; width: auto; height: auto; object-fit: contain;" alt="Preview">
            </div>
        </div>
    </div>
</div>
</body>

</html>