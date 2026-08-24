
        $(document).ready(function () {
            let itemIndex = Number($('#itemIndex').val());
            $(document).on('click', '.add_item', function () {
                let selected = $('#supplier_id').find('option:selected');
                let supplierCgst = parseFloat(selected.data('cgst')) || parseFloat("{{ $web_settings->cgst ?? 0 }}") || 0;
                let supplierSgst = parseFloat(selected.data('sgst')) || parseFloat("{{ $web_settings->sgst ?? 0 }}") || 0;
                let supplierIgst = parseFloat(selected.data('igst')) || parseFloat("{{ $web_settings->igst ?? 0 }}") || 0;
                let rowHtml = `
                <tr class="item-row">
                    <td>
                        <select class="select2 form-select po_store_category" name="items[${itemIndex}][store_category_id]" data-placeholder="Select Store Category">
                            <option value="">Select Store Category</option>
                            @foreach($storeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}({{ $category->code }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="select2 form-select brand" name="items[${itemIndex}][brand_id]" data-placeholder="Select Brand">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }} ({{ $brand->code }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="select2 form-select material" name="items[${itemIndex}][raw_material_id]" data-placeholder="Select Raw Material">
                            <option value="">Select Raw Material</option>
                        </select>
                    </td>
                    <td class="td-style">
                        <select class="select2 form-select style" name="items[${itemIndex}][style_id]" data-placeholder="Select Style">
                            <option value="">Select Style</option>
                            @foreach($styles as $style)
                                <option value="{{ $style->id }}">{{ $style->style_name }}</option>
                            @endforeach
                        </select>
                        <span class="hyphen d-none">-</span>
                    </td>
                    <td class="td-fabric-width">
                        <select class="select2 form-select fabric_width" name="items[${itemIndex}][fabric_width_id]" data-placeholder="Select Width">
                            <option value="">Select Width</option>
                            @foreach($fabricSizes as $fabricSize)
                                <option value="{{ $fabricSize->id }}">{{ $fabricSize->width }}</option>
                            @endforeach
                        </select>
                        <span class="hyphen d-none">-</span>
                    </td>
                    <td class="td-fabric-type">
                        <select class="select2 form-select fabric_type" name="items[${itemIndex}][fabric_type_id]" data-placeholder="Select Fabric Type">
                            <option value="">Select Fabric Type</option>
                            @foreach($fabricTypes as $fabricType)
                                <option value="{{ $fabricType->id }}">{{ $fabricType->fabric_type }}</option>
                            @endforeach
                        </select>
                        <span class="hyphen d-none">-</span>
                    </td>
                    <td>
                        <input type="text" class="form-control supplier_design_name" name="items[${itemIndex}][supplier_design_name]" placeholder="Enter Supplier Design Name">
                    </td>
                    <td>
                        <select class="select2 form-select color" name="items[${itemIndex}][color_id]" data-placeholder="Select Color">
                            <option value="">Select Color</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="select2 form-select uom" name="items[${itemIndex}][uom_id]" disabled data-placeholder="Select UOM">
                            <option value="">Select UOM</option>
                            @foreach($uoms as $uom)
                                <option value="{{ $uom->id }}">{{ $uom->uom_code }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="items[${itemIndex}][uom_id]" value="" class="uom_hidden">
                    </td>
                    <td>
                        <input type="number" class="form-control quantity" name="items[${itemIndex}][quantity]" step="0.01" placeholder="Enter Quantity" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control rate" name="items[${itemIndex}][rate]" step="any" min="0" placeholder="Enter Rate">
                    </td>
                    <td class="td-gst td-cgst d-none">
                        <input type="number" class="form-control cgst_percent text-end" name="items[${itemIndex}][cgst_percent]" step="0.01" min="0" value="${supplierCgst}" readonly>
                    </td>
                    <td class="td-gst td-cgst d-none">
                        <input type="number" class="form-control cgst_amount text-end" name="items[${itemIndex}][cgst_amount]" step="0.01" min="0" value="" readonly>
                    </td>
                    <td class="td-gst td-sgst d-none">
                        <input type="number" class="form-control sgst_percent text-end" name="items[${itemIndex}][sgst_percent]" step="0.01" min="0" value="${supplierSgst}" readonly>
                    </td>
                    <td class="td-gst td-sgst d-none">
                        <input type="number" class="form-control sgst_amount text-end" name="items[${itemIndex}][sgst_amount]" step="0.01" min="0" value="" readonly>
                    </td>
                    <td class="td-gst td-igst d-none">
                        <input type="number" class="form-control igst_percent text-end" name="items[${itemIndex}][igst_percent]" step="0.01" min="0" value="${supplierIgst}" readonly>
                    </td>
                    <td class="td-gst td-igst d-none">
                        <input type="number" class="form-control igst_amount text-end" name="items[${itemIndex}][igst_amount]" step="0.01" min="0" value="" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control amount" name="items[${itemIndex}][amount]" step="0.01" min="0" placeholder="Amount">
                    </td>
                    <td>
                        <textarea class="form-control remarks" name="items[${itemIndex}][remarks]" style="height: 58px;" placeholder="Enter Remarks"></textarea>
                    </td>
                    <td>
                        <input type="file" class="form-control file-input" name="items[${itemIndex}][attached_file]" accept=".jpg,.jpeg,.png,.webp">
                        <input type="hidden" name="items[${itemIndex}][existing_file]" value="">
                        <div class="mt-2 preview-container"></div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger delete_item">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>`;

                $('#item-rows tbody').append(rowHtml);
                initSelect2Fields();
                
                let storeTypeId = $('#store_type_id').val();
                if (storeTypeId) {
                    $('#item-rows tbody tr:last').find('.po_store_category').val(storeTypeId).trigger('change.select2').trigger('change');
                }

                itemIndex++;
                toggleTaxDivs();
                calculateTotals();
            });

            function initSelect2Fields(context = document) {
                $(context).find('.select2').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }

                    $(this).select2({
                        dropdownParent: $(this).closest('.card-body').length ? $(this).closest('.card-body') : $('body'),
                        width: '100%'
                    });
                });
            }

            $(document).on('change', '.po_store_category', function () {
                let category_id = $(this).val();
                let row = $(this).closest('tr');
                let materialSelect = row.find('.material');
                let currentMaterialId = materialSelect.val();

                if (materialSelect.hasClass("select2-hidden-accessible")) {
                    materialSelect.select2('destroy');
                }

                materialSelect.empty().append('<option value="">Select Raw Material</option>');

                if (!category_id) {
                    materialSelect.select2({
                        dropdownParent: materialSelect.parent(),
                        placeholder: materialSelect.data('placeholder'),
                        width: '100%'
                    });
                    return;
                }

                $.ajax({
                    url: APP_URL + '/get-materials-by-category/' + category_id,
                    type: 'GET',
                    success: function (response) {
                        let materialsHtml = '<option value="">Select Raw Material</option>';
                        if (response.materials?.length) {
                            response.materials.forEach(material => {
                                let materialName = material.name;
                                if (typeof materialName === 'object' && materialName !== null) {
                                    materialName = materialName.en || materialName.value || Object.values(materialName)[0] || JSON.stringify(materialName);
                                }
                                let isSelected = (currentMaterialId == material.id) ? 'selected' : '';
                                materialsHtml += `<option value="${material.id}" data-uom-id="${material.uom_id}" ${isSelected}>${materialName} (${material.code})</option>`;
                            });
                        } else {
                            materialsHtml += '<option value="">No materials found</option>';
                        }

                        materialSelect.html(materialsHtml);
                        materialSelect.select2({
                            dropdownParent: materialSelect.parent(),
                            placeholder: materialSelect.data('placeholder'),
                            width: '100%'
                        });
                        
                        if (currentMaterialId && materialSelect.val() == currentMaterialId) {
                            materialSelect.trigger('change');
                        }

                    },
                    error: function () {
                        materialSelect.html('<option value="">Select Raw Material</option>').select2({
                            dropdownParent: materialSelect.parent(),
                            width: '100%'
                        });
                    }
                });
            });

            $(document).on('change', '.material', function () {
                let uom_id = $(this).find(':selected').data('uom-id');
                if (uom_id) {
                    let row = $(this).closest('tr');
                    row.find('.uom').val(uom_id).trigger('change');
                    row.find('.uom_hidden').val(uom_id);
                } else {
                    let row = $(this).closest('tr');
                    row.find('.uom').val('').trigger('change');
                    row.find('.uom_hidden').val('');
                }

            });

            $(document).on('click', '.delete_item', function () {
                if ($('#item-rows tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();

                } else {
                    alert('At least one item is required');
                }
            });

            $(document).on('input', '.quantity, .rate, .cgst_percent, .sgst_percent, .igst_percent', function () {
                let row = $(this).closest('tr');
                let isAccessories = $('#store_type_id').val() == 2;
                let qty = parseFloat(row.find('.quantity').val()) || 0;
                let rate = parseFloat(row.find('.rate').val()) || 0;

                let amountVal = (qty * rate).toFixed(2);
                row.find('.amount').val(amountVal > 0 ? amountVal : '');
                calculateTotals();
            });

            $(document).on('input', '.amount', function () {
                let row = $(this).closest('tr');


                let amount = parseFloat($(this).val()) || 0;
                let qty = parseFloat(row.find('.quantity').val()) || 0;

                if (qty > 0) {
                    let rate = amount / qty;
                    row.find('.rate').val(rate.toFixed(4));
                } else {
                    row.find('.rate').val('');
                }
                calculateTotals();
            });

            $(document).on('keypress', '#discount_percent, #commission, #cgst_percent, #sgst_percent, #igst_percent, .cgst_percent, .sgst_percent, .igst_percent, .quantity, .rate', function (e) {
                if (e.which === 45 || e.which === 43) {
                    e.preventDefault();
                }
            });
            $(document).on('paste', '#discount_percent, #commission, #cgst_percent, #sgst_percent, #igst_percent, .cgst_percent, .sgst_percent, .igst_percent', function (e) {
                let pasteData = e.originalEvent.clipboardData.getData('text');
                if (parseFloat(pasteData) < 0) {
                    e.preventDefault();
                }
            });
            $('form.common-form').on('submit', function (e) {
                calculateTotals();

                let discountPercent = parseFloat($('#discount_percent').val()) || 0;
                let commissionPercent = parseFloat($('#commission').val()) || 0;

                let status = $('#status').val();
                let totalAmount = parseFloat($('#total_amount').val()) || 0;
                if (status === 'Approved' && totalAmount <= 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Error',
                            text: 'Grand total must be greater than 0 to approve this purchase order.',
                            confirmButtonColor: '#8c57ff'
                        });
                    } else {
                        alert('Grand total must be greater than 0 to approve this purchase order.');
                    }
                    return false;
                }
            });

            $('#discount_percent').on('input', function () {
                calculateTotals();
            });


            $(document).on('input', '#discount_percent, #igst_percent, #cgst_percent, #sgst_percent, #round_off, input[name="commission"]', function () {
                calculateTotals();
            });

            $(document).on('change', 'input[name="round_off_type"]', function () {
                calculateTotals();
            });

            $(document).on('change', 'input[name="other_state"]', function () {
                toggleTaxDivs();
                calculateTotals();
            });

            function calculateTotals() {
                let subTotal = 0;
                let totalQty = 0;
                
                let storeTypeId = $('#store_type_id').val();
                let isAccessories = storeTypeId == 2;
                let otherState = $('input[name="other_state"]:checked').val();
                let orderIgst = parseFloat($('#igst_percent').val()) || 0;
                let orderCgst = parseFloat($('#cgst_percent').val()) || 0;
                let orderSgst = parseFloat($('#sgst_percent').val()) || 0;
                let itemsTaxTotal = 0;

                $('.item-row').each(function () {
                    let qty = parseFloat($(this).find('.quantity').val()) || 0;
                    let rate = parseFloat($(this).find('.rate').val()) || 0;
                    let amount;

                    if (isAccessories) {
                        amount = parseFloat($(this).find('.amount').val()) || 0;
                    } else {
                        amount = qty * rate;
                        $(this).find('.amount').val(amount.toFixed(2) : '');
                    }
                    
                    if (isAccessories) {
                        if (otherState === 'yes') {
                            let itemIgstStr = $(this).find('.igst_percent').val();
                            let itemIgstPercent = itemIgstStr !== '' ? parseFloat(itemIgstStr) : orderIgst;
                            if (itemIgstStr === '') {
                                $(this).find('.igst_percent').val(orderIgst);
                            }
                            let igstAmount = (amount * itemIgstPercent) / 100;
                            $(this).find('.igst_amount').val(igstAmount.toFixed(2));
                            itemsTaxTotal += igstAmount;
                        } else {
                            let itemCgstStr = $(this).find('.cgst_percent').val();
                            let itemSgstStr = $(this).find('.sgst_percent').val();
                            let itemCgstPercent = itemCgstStr !== '' ? parseFloat(itemCgstStr) : orderCgst;
                            let itemSgstPercent = itemSgstStr !== '' ? parseFloat(itemSgstStr) : orderSgst;
                            if (itemCgstStr === '') {
                                $(this).find('.cgst_percent').val(orderCgst);
                            }
                            if (itemSgstStr === '') {
                                $(this).find('.sgst_percent').val(orderSgst);
                            }
                            let cgstAmount = (amount * itemCgstPercent) / 100;
                            let sgstAmount = (amount * itemSgstPercent) / 100;
                            $(this).find('.cgst_amount').val(cgstAmount.toFixed(2));
                            $(this).find('.sgst_amount').val(sgstAmount.toFixed(2));
                            itemsTaxTotal += cgstAmount + sgstAmount;
                        }
                    }
                    
                    subTotal += amount;
                    totalQty += qty;
                });

                $('#total_qty').val(totalQty.toFixed(2));
                $('#sub_total').val(subTotal.toFixed(2));

                let commissionPercent = parseFloat($('input[name="commission"]').val()) || 0;
                let commissionAmount = 0;
                if (commissionPercent > 0) {
                    commissionAmount = (subTotal * commissionPercent) / 100;
                    $('#commission_amount_display').text(commissionAmount.toFixed(2));
                    $('#commission_row').removeClass('d-none');
                } else {
                    $('#commission_row').addClass('d-none');
                    $('#commission_amount_display').text('0.00');
                }

                let discountPercent = parseFloat($('#discount_percent').val()) || 0;
                let discountAmount = (subTotal * discountPercent) / 100;
                $('#discount_amount').val(discountAmount.toFixed(2));

                let taxableAmount = subTotal - discountAmount - commissionAmount;
                let displayTaxableAmount = taxableAmount >= 0 ? taxableAmount : 0;

                $('#taxable_amount').val(displayTaxableAmount.toFixed(2));

                // Show warning if net amount is negative
                if (taxableAmount < 0 && subTotal > 0 && (discountAmount > 0 || commissionAmount > 0)) {
                    let reasons = [];
                    if (discountAmount > 0 && commissionAmount > 0) {
                        reasons.push('Discount (₹' + discountAmount.toFixed(2) + ')');
                        reasons.push('Commission (₹' + commissionAmount.toFixed(2) + ')');
                    } else if (discountAmount > 0) {
                        reasons.push('Discount (₹' + discountAmount.toFixed(2) + ')');
                    } else if (commissionAmount > 0) {
                        reasons.push('Commission (₹' + commissionAmount.toFixed(2) + ')');
                    }
                    let warningText = '';
                    if (taxableAmount < 0) {
                        warningText = 'Net Amount is negative (₹' + taxableAmount.toFixed(2) + '). ' + reasons.join(' + ') + ' exceeds Sub Total (₹' + subTotal.toFixed(2) + '). Showing 0.00.';
                    } else {
                        warningText = 'Net Amount is 0.00. ' + reasons.join(' + ') + ' equals Sub Total (₹' + subTotal.toFixed(2) + '). Tax and Grand Total will be 0.00.';
                    }
                    $('#negative_net_warning_text').text(warningText);
                    $('#negative_net_warning').removeClass('d-none');
                    $('#taxable_amount').addClass('text-danger');
                    $('button[type="submit"]').prop('disabled', true);
                } else {
                    $('#negative_net_warning').addClass('d-none');
                    $('#taxable_amount').removeClass('text-danger');
                    $('button[type="submit"]').prop('disabled', false);
                }

                let taxAmount = 0;
                let cgstAmountDisplay = 0;
                let sgstAmountDisplay = 0;
                let igstAmountDisplay = 0;
                
                if (taxableAmount >= 0) {
                    if (otherState === 'yes') {
                        let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                        igstAmountDisplay = (displayTaxableAmount * igstPercent) / 100;
                        taxAmount = igstAmountDisplay;
                    } else {
                        let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                        let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                        cgstAmountDisplay = (displayTaxableAmount * cgstPercent) / 100;
                        sgstAmountDisplay = (displayTaxableAmount * sgstPercent) / 100;
                        taxAmount = cgstAmountDisplay + sgstAmountDisplay;
                    }
                }

                $('#summary_cgst_amount').val(cgstAmountDisplay.toFixed(2));
                $('#summary_sgst_amount').val(sgstAmountDisplay.toFixed(2));
                $('#summary_igst_amount').val(igstAmountDisplay.toFixed(2));
                $('#tax_amount').val(taxAmount.toFixed(2));

                let totalBeforeRoundOff = parseFloat((displayTaxableAmount + taxAmount).toFixed(2));

                let roundOffAmount = parseFloat($('#round_off').val()) || 0;
                let roundOffType = $('input[name="round_off_type"]:checked').val();
                let finalTotal = 0;

                if (roundOffType === 'Add') {
                    finalTotal = totalBeforeRoundOff + roundOffAmount;
                } else {
                    finalTotal = totalBeforeRoundOff - roundOffAmount;
                }

                if (finalTotal < 0) {
                    finalTotal = 0;
                }

                $('#total_amount').val(finalTotal.toFixed(2));

            }

            function toggleTaxDivs() {
                let otherState = $('input[name="other_state"]:checked').val();
                let storeTypeId = $('#store_type_id').val();
                let isAccessories = storeTypeId == 2;
                
                if (otherState === 'yes') {
                    $('.igst-field').removeClass('d-none');
                    $('.cgst-field, .sgst-field').addClass('d-none');
                    if (isAccessories) {
                        $('.th-igst, .td-igst').removeClass('d-none');
                        $('.th-cgst, .th-sgst, .td-cgst, .td-sgst').addClass('d-none');
                    } else {
                        $('.th-igst, .td-igst, .th-cgst, .th-sgst, .td-cgst, .td-sgst').addClass('d-none');
                    }
                } else {
                    $('.igst-field').addClass('d-none');
                    $('.cgst-field, .sgst-field').removeClass('d-none');
                    if (isAccessories) {
                        $('.th-igst, .td-igst').addClass('d-none');
                        $('.th-cgst, .th-sgst, .td-cgst, .td-sgst').removeClass('d-none');
                    } else {
                        $('.th-igst, .td-igst, .th-cgst, .th-sgst, .td-cgst, .td-sgst').addClass('d-none');
                    }
                }
            }

            $(document).on('change', '#supplier_id', function () {
                let selected = $(this).find(':selected');
                let supplierStateId = selected.data('state-id');
                let paymentTerms = selected.data('payment-terms');
                let companyStateId = "{{ $web_settings->state_id ?? '' }}";

                let supplierIgst = parseFloat(selected.data('igst')) || 0;
                let supplierCgst = parseFloat(selected.data('cgst')) || 0;
                let supplierSgst = parseFloat(selected.data('sgst')) || 0;

                if (paymentTerms !== undefined) {
                    $('#payment_terms').val(paymentTerms);
                }

                let storeTypeId = selected.data('store-id');
                if (storeTypeId) {
                    $('#store_type_id').val(storeTypeId).trigger('change.select2').trigger('change');
                } else {
                    $('#store_type_id').val('').trigger('change.select2').trigger('change');
                }

                if (supplierStateId && companyStateId) {
                    if (supplierStateId == companyStateId) {
                        $('#other_state_no').prop('checked', true).trigger('change');
                        
                        let cgst = supplierCgst > 0 ? supplierCgst : "{{ $web_settings->cgst ?? 0 }}";
                        let sgst = supplierSgst > 0 ? supplierSgst : "{{ $web_settings->sgst ?? 0 }}";
                        
                        $('#cgst_percent').val(cgst);
                        $('#sgst_percent').val(sgst);
                        $('#igst_percent').val(0);
                        
                        $('.cgst_percent').val(cgst);
                        $('.sgst_percent').val(sgst);
                        $('.igst_percent').val('');
                    } else {
                        $('#other_state_yes').prop('checked', true).trigger('change');
                        
                        let igst = supplierIgst > 0 ? supplierIgst : "{{ $web_settings->igst ?? 0 }}";
                        
                        $('#igst_percent').val(igst);
                        $('#cgst_percent').val(0);
                        $('#sgst_percent').val(0);
                        
                        $('.igst_percent').val(igst);
                        $('.cgst_percent').val('');
                        $('.sgst_percent').val('');
                    }
                    calculateTotals();
                }
            });
            let attachmentsDataTransfer = new DataTransfer();

            $(document).on('click', '.remove-existing-attachment', function () {
                let $item = $(this).closest('.attachment-item');
                if ($item.hasClass('new-attachment-preview')) {
                    let fileName = $item.attr('title');
                    let fileInput = document.getElementById('additional_attachments');
                    if (fileInput && fileInput.files) {
                        let dt = new DataTransfer();
                        for (let i = 0; i < fileInput.files.length; i++) {
                            if (fileInput.files[i].name !== fileName) {
                                dt.items.add(fileInput.files[i]);
                            }
                        }
                        fileInput.files = dt.files;
                        attachmentsDataTransfer = dt;
                    }
                }
                $item.remove();
            });

            $(document).on('change', '.file-input', function () {
                let file = this.files[0];
                let $container = $(this).siblings('.preview-container');

                $container.empty();

                if (file) {
                    const reader = new FileReader();
                    const isImage = file.type.startsWith('image/');
                    const extension = file.name.split('.').pop().toUpperCase();
                    const fileUrl = URL.createObjectURL(file);

                    const previewHtml = `<div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="${file.name}">
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded overflow-hidden js-loading">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                            </div>
                                        </div>`;

                    $container.html(previewHtml);

                    if (isImage) {
                        reader.onload = function (e) {
                            $container.html(`
                                                <div class="attachment-thumb bg-white position-relative">
                                                    <img src="${e.target.result}" class="preview-thumb view-image" data-image="${e.target.result}" style="width:50px;height:=50px;object-fit:cover;border-radius:8px;cursor:pointer;border:1px solid #ddd;" alt="Preview">
                                                </div>
                                            `);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $container.html(`
                                        <div class="attachment-thumb bg-white position-relative">
                                            <a href="${fileUrl}" target="_blank" class="d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary p-2 border" style="width: 50px; height: 50px;">
                                                <i class="ri ri-file-text-line fs-2"></i>
                                                <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">${extension}</span>
                                            </a>
                                        </div>
                                    `);
                    }
                }
            });

            $('#additional_attachments').on('change', function () {
                let existingServerCount = $('.attachment-item').not('.new-attachment-preview').length;
                let newFiles = this.files;
                
                if (newFiles.length === 0) return;

                let total = existingServerCount + attachmentsDataTransfer.items.length + newFiles.length;

                if (total > 5) {
                    alert('You can only upload a maximum of 5 attachments in total.');
                    this.files = attachmentsDataTransfer.files;
                    return;
                }

                for (let i = 0; i < newFiles.length; i++) {
                    attachmentsDataTransfer.items.add(newFiles[i]);
                }
                
                this.files = attachmentsDataTransfer.files;

                $('.new-attachment-preview').remove();

                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    const isImage = file.type.startsWith('image/');
                    const extension = file.name.split('.').pop().toUpperCase();
                    const fileUrl = URL.createObjectURL(file);

                    const previewId = `preview-${index}`;
                    const previewHtml = `<div id="${previewId}" class="attachment-item new-attachment-preview position-relative border rounded bg-white shadow-sm mb-2" title="${file.name}">
                                            <div class="d-flex align-items-center p-2">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                            </div>
                                        </div>`;

                    $('#attachment-list').append(previewHtml);

                    if (isImage) {
                        reader.onload = function (e) {
                            $(`#${previewId}`).html(`
                                <div class="d-flex align-items-center p-2">
                                    <img src="${e.target.result}" class="view-image" data-image="${e.target.result}" style="width:50px; height:50px; object-fit:cover; border-radius:8px; border:1px solid #ddd; cursor:pointer;" alt="Preview">
                                    <button type="button" class="btn btn-sm btn-link text-danger ms-2 remove-existing-attachment p-0">
                                        <i class="ri ri-close-line"></i>
                                    </button>
                                </div>
                            `);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $(`#${previewId}`).html(`
                            <div class="d-flex align-items-center p-2">
                                <a href="${fileUrl}" target="_blank" class="text-decoration-none text-primary d-flex align-items-center">
                                    <i class="ri ri-file-text-line fs-3 me-2"></i>
                                    <span class="badge bg-primary text-white">${extension}</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-link text-danger ms-2 remove-existing-attachment p-0">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            </div>
                        `);
                    }
                });
            });

            $(document).on('change', '#store_type_id', function() {
                let storeTypeId = $(this).val();
                if (storeTypeId) {
                    $('.po_store_category').val(storeTypeId).trigger('change.select2').trigger('change');
                } else {
                    $('.po_store_category').val('').trigger('change.select2').trigger('change');
                }
                toggleTaxDivs();
                calculateTotals();
            });

            $('#purchase_commission_agent_id').on('change', function() {
                if ($(this).val()) {
                    $('#commission').prop('required', true);
                    let label = $('label[for="commission"]');
                    if (label.find('.text-danger').length === 0) {
                        label.append('<span class="text-danger"> *</span>');
                    }
                } else {
                    $('#commission').prop('required', false);
                    $('label[for="commission"]').find('.text-danger').remove();
                }
            });
            if ($('#purchase_commission_agent_id').length) {
                $('#purchase_commission_agent_id').trigger('change');
            }

            $('#commission').on('input', function() {
                if (parseFloat($(this).val()) > 0) {
                    $('#purchase_commission_agent_id').prop('required', true);
                    let label = $('label[for="purchase_commission_agent_id"]');
                    if (label.find('.text-danger').length === 0) {
                        label.append('<span class="text-danger"> *</span>');
                    }
                } else {
                    $('#purchase_commission_agent_id').prop('required', false);
                    $('label[for="purchase_commission_agent_id"]').find('.text-danger').remove();
                }
            });
            if ($('#commission').length) {
                $('#commission').trigger('input');
            }

            initSelect2Fields();
            toggleTaxDivs();
            calculateTotals();
            
            // Re-run after Select2 is fully initialized to fix edit mode GST column visibility
            setTimeout(function() {
                toggleTaxDivs();
                calculateTotals();
            }, 300);

            setTimeout(function() {
                const refPicker = document.querySelector("#reference_date")?._flatpickr;
                const duePicker = document.querySelector("#due_date")?._flatpickr;
                
                if (refPicker && duePicker) {
                    const originalOnChange = refPicker.config.onChange;
                    refPicker.set('onChange', function(selectedDates, dateStr, instance) {
                        if (typeof originalOnChange === 'function') {
                            originalOnChange(selectedDates, dateStr, instance);
                        }
                        duePicker.set('minDate', dateStr);
                        
                        const currentDueDate = duePicker.selectedDates[0];
                        if (currentDueDate && selectedDates[0] && currentDueDate < selectedDates[0]) {
                            duePicker.clear();
                        }
                    });
                    
                    if (refPicker.selectedDates.length > 0) {
                        duePicker.set('minDate', refPicker.input.value);
                    }
                }
            }, 1000); 
        });
    