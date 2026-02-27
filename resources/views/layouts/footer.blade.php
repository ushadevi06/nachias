<div class="content-backdrop fade"></div>
</div>
</div>
</div>
</div>

<script src="{{ url('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('assets/js/bs-stepper.js') }}"></script>
<script src="{{ url('assets/js/form-wizard-numbered.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ url('assets/js/flatpickr.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
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
        var table = $('.datatables-products').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10
        });

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
</script>
</body>

</html>