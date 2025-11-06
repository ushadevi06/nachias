<div class="content-backdrop fade"></div>
</div>
</div>
</div>
</div>
<script src="{{ url('assets/js/config.js') }}"></script>
<script src="{{ url('assets/js/helpers.js') }}"></script>
<script src="{{ url('assets/js/menu.js') }}"></script>
<script src="{{ url('assets/js/main.js') }}"></script>
<script src="{{ url('assets/js/select2.js') }}"></script>
<script src="{{ url('assets/js/bootstrap.js') }}"></script>
<script src="{{ url('assets/js/bs-stepper.js') }}"></script>
<script src="{{ url('assets/js/form-wizard-numbered.js') }}"></script>
<script src="{{ url('assets/js/flatpickr.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ url('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ url('assets/js/dataTables.responsive.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stepper = new Stepper(document.querySelector('.bs-stepper.wizard-numbered'));
    });
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

        table.on('responsive-display.dt', function (e, datatable, row, showHide, update) {
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
        $('#generateReport').on('shown.bs.modal', function () {
            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "d-m-Y",
                allowInput: true,
                appendTo: document.getElementById('generateReport'),
                onOpen: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.style.zIndex = 1050; 
                }
            });
        });
        $('.po_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        }); 
        $('#doj').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        }); 
        $('#order_date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
        $('#reference_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });  
        flatpickr("#date_range", {
            mode: "range",     
            dateFormat: "d-m-Y",  
            allowInput: true   
        });

        $('.so_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.delivery_date').flatpickr({
            dateFormat: 'd-m-Y',
            minDate: 'today',
            allowInput: true
        });
        $('.inv_date').flatpickr({
            dateFormat: 'd-m-Y',
            minDate: 'today',
            allowInput: true
        });
        $('.due_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
    });
    $(document).on("click", ".delete-btn", function () {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#8c57ff",
            cancelButtonColor: "#ff4c51",
            confirmButtonText: "Yes, delete it!"
        });
    });
</script>
</body>
</html>