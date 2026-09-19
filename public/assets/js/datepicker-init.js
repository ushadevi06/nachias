$(document).ready(function () {
    // Standard Date Pickers
    $('.po_date').each(function() {
        $(this).flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true,
            defaultDate: $(this).val() || 'today',
        });
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

    $('.so_date').each(function() {
        $(this).flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: $(this).val() || 'today',
            allowInput: true
        });
    });

    $('.delivery_date').flatpickr({
        dateFormat: 'd-m-Y',
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

    $('.inv_date, .invoice_date, .bill_date, .note_date, .issue_date, .stock_date, .receipt-date, .doc-date, .grn_date, .sup_inv_date, .validity_date, .start_date:not([type="hidden"]), .end_date:not([type="hidden"]), .date-picker, .dynamic-stage-date, .issue-date, .due-date, .deadline-date, .cutting_date').flatpickr({
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

    $(".timepicker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
    });

    flatpickr("#date_range, #po_date_range", {
        mode: "range",
        dateFormat: "d-m-Y",
        allowInput: true
    });

    // Unified Report Date Range Handler
    $('.report_date_range, .report-date-range').each(function () {
        var $rangeInput = $(this);
        var $form = $rangeInput.closest('form');
        var $from = $form.find('.start_date, input[name="from_date"]');
        var $to = $form.find('.end_date, input[name="to_date"]');

        $rangeInput.flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true,
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    $from.val(instance.formatDate(selectedDates[0], 'd-m-Y'));
                    $to.val(instance.formatDate(selectedDates[1], 'd-m-Y'));
                } else if (selectedDates.length === 1) {
                    var single = instance.formatDate(selectedDates[0], 'd-m-Y');
                    $from.val(single);
                    $to.val(single);
                } else {
                    $from.val('');
                    $to.val('');
                }
            },
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    var single = instance.formatDate(selectedDates[0], 'd-m-Y');
                    $from.val(single);
                    $to.val(single);
                }
            }
        });

        $rangeInput.on('input change', function () {
            var val = $(this).val().trim();
            if (!val) {
                $from.val('');
                $to.val('');
            } else if (val.indexOf(' to ') !== -1) {
                var parts = val.split(' to ');
                $from.val(parts[0].trim());
                $to.val(parts[1].trim());
            } else {
                $from.val(val);
                $to.val(val);
            }
        });
    });

    $("#start_date:not([type=\"hidden\"]), #end_date:not([type=\"hidden\"]), #completion_date, #issue_date").flatpickr({
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
