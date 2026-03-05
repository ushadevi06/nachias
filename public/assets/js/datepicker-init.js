$(document).ready(function () {
    // Standard Date Pickers
    $('.po_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true,
        defaultDate: 'today',
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

    $('.request_date').flatpickr({
        dateFormat: 'd-m-Y',
        defaultDate: 'today',
        allowInput: true
    });

    $('.est_dispatch_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });

    $('.inv_date, .invoice_date, .bill_date, .note_date, .issue_date, .stock_date, .receipt-date, .doc-date, .grn_date, .sup_inv_date, .validity_date, .start_date, .end_date, .date-picker, .dynamic-stage-date, .issue-date, .due-date, .deadline-date, .cutting_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });

    $('.due_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });

    $('#month_year').flatpickr({
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "m-Y",
                altFormat: "F Y",
                theme: "light"
            })
        ]
    });

    // Time Picker
    $(".timepicker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
    });

    // Date Range Pickers
    flatpickr("#date_range, #po_date_range", {
        mode: "range",
        dateFormat: "d-m-Y",
        allowInput: true
    });

    // Specific IDs
    $("#start_date, #end_date, #completion_date, #issue_date").flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });

    // Global flatpickr class
    $(".flatpickr").flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });

    // Modal Specific (if any)
    $('#generateReport').on('shown.bs.modal', function () {
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "d-m-Y",
            allowInput: true,
            appendTo: document.getElementById('generateReport'),
            onOpen: function (selectedDates, dateStr, instance) {
                instance.calendarContainer.style.zIndex = 1050;
            }
        });
    });

    // Global fix to disable browser autocomplete on all datepicker inputs
    $(document).on('focus', '.flatpickr-input, .issue_date, .delivery_date, .cutting_date, .issue-date, .deadline-date, .po_date, .so_date, .request_date, .est_dispatch_date', function () {
        $(this).attr('autocomplete', 'off');
    });
});
