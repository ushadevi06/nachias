<?php

$file = 'resources/views/purchase_orders/add.blade.php';
$content = file_get_contents($file);

// 1. Add GST columns to table header
$headerSearch = '<th style="min-width: 120px;">Amount</th>';
$headerReplace = '<th style="min-width: 120px;">Amount</th>
                                        <th class="th-gst th-cgst d-none" style="min-width: 100px;">CGST %</th>
                                        <th class="th-gst th-cgst d-none" style="min-width: 100px;">CGST Amt</th>
                                        <th class="th-gst th-sgst d-none" style="min-width: 100px;">SGST %</th>
                                        <th class="th-gst th-sgst d-none" style="min-width: 100px;">SGST Amt</th>
                                        <th class="th-gst th-igst d-none" style="min-width: 100px;">IGST %</th>
                                        <th class="th-gst th-igst d-none" style="min-width: 100px;">IGST Amt</th>';
$content = str_replace($headerSearch, $headerReplace, $content);

// 2. Add GST columns to old items loop
$oldItemSearch = '<input type="text" class="form-control amount" value="{{ ($item[\'quantity\'] ?? 0) * ($item[\'rate\'] ?? 0) }}" readonly>
                                                </td>
                                                <td>
                                                    <textarea class="form-control remarks @error(\'items.\' . $index . \'.remarks\') is-invalid @enderror"';
$oldItemReplace = '<input type="text" class="form-control amount" value="{{ ($item[\'quantity\'] ?? 0) * ($item[\'rate\'] ?? 0) }}" readonly>
                                                </td>
                                                <td class="td-gst td-cgst d-none">
                                                    <input type="number" class="form-control cgst_percent text-end" name="items[{{ $index }}][cgst_percent]" step="0.01" min="0" value="{{ $item[\'cgst_percent\'] ?? 0 }}">
                                                </td>
                                                <td class="td-gst td-cgst d-none">
                                                    <input type="number" class="form-control cgst_amount text-end" name="items[{{ $index }}][cgst_amount]" step="0.01" min="0" value="{{ $item[\'cgst_amount\'] ?? 0 }}" readonly>
                                                </td>
                                                <td class="td-gst td-sgst d-none">
                                                    <input type="number" class="form-control sgst_percent text-end" name="items[{{ $index }}][sgst_percent]" step="0.01" min="0" value="{{ $item[\'sgst_percent\'] ?? 0 }}">
                                                </td>
                                                <td class="td-gst td-sgst d-none">
                                                    <input type="number" class="form-control sgst_amount text-end" name="items[{{ $index }}][sgst_amount]" step="0.01" min="0" value="{{ $item[\'sgst_amount\'] ?? 0 }}" readonly>
                                                </td>
                                                <td class="td-gst td-igst d-none">
                                                    <input type="number" class="form-control igst_percent text-end" name="items[{{ $index }}][igst_percent]" step="0.01" min="0" value="{{ $item[\'igst_percent\'] ?? 0 }}">
                                                </td>
                                                <td class="td-gst td-igst d-none">
                                                    <input type="number" class="form-control igst_amount text-end" name="items[{{ $index }}][igst_amount]" step="0.01" min="0" value="{{ $item[\'igst_amount\'] ?? 0 }}" readonly>
                                                </td>
                                                <td>
                                                    <textarea class="form-control remarks @error(\'items.\' . $index . \'.remarks\') is-invalid @enderror"';
$content = str_replace($oldItemSearch, $oldItemReplace, $content);

// 3. Add GST columns to item loop from $purchaseOrder->items
$poItemSearch = '<input type="text" class="form-control amount" value="{{ $item->amount }}" readonly>
                                                </td>
                                                <td>
                                                    <textarea class="form-control remarks @error(\'items.\' . $index . \'.remarks\') is-invalid @enderror" name="items[{{ $index }}][remarks]">{{ $item->remarks }}</textarea>';
$poItemReplace = '<input type="text" class="form-control amount" value="{{ $item->amount }}" readonly>
                                                </td>
                                                <td class="td-gst td-cgst d-none">
                                                    <input type="number" class="form-control cgst_percent text-end" name="items[{{ $index }}][cgst_percent]" step="0.01" min="0" value="{{ $item->cgst_percent }}">
                                                </td>
                                                <td class="td-gst td-cgst d-none">
                                                    <input type="number" class="form-control cgst_amount text-end" name="items[{{ $index }}][cgst_amount]" step="0.01" min="0" value="{{ $item->cgst_amount }}" readonly>
                                                </td>
                                                <td class="td-gst td-sgst d-none">
                                                    <input type="number" class="form-control sgst_percent text-end" name="items[{{ $index }}][sgst_percent]" step="0.01" min="0" value="{{ $item->sgst_percent }}">
                                                </td>
                                                <td class="td-gst td-sgst d-none">
                                                    <input type="number" class="form-control sgst_amount text-end" name="items[{{ $index }}][sgst_amount]" step="0.01" min="0" value="{{ $item->sgst_amount }}" readonly>
                                                </td>
                                                <td class="td-gst td-igst d-none">
                                                    <input type="number" class="form-control igst_percent text-end" name="items[{{ $index }}][igst_percent]" step="0.01" min="0" value="{{ $item->igst_percent }}">
                                                </td>
                                                <td class="td-gst td-igst d-none">
                                                    <input type="number" class="form-control igst_amount text-end" name="items[{{ $index }}][igst_amount]" step="0.01" min="0" value="{{ $item->igst_amount }}" readonly>
                                                </td>
                                                <td>
                                                    <textarea class="form-control remarks @error(\'items.\' . $index . \'.remarks\') is-invalid @enderror" name="items[{{ $index }}][remarks]">{{ $item->remarks }}</textarea>';
$content = str_replace($poItemSearch, $poItemReplace, $content);

// 4. Add GST columns to empty row
$emptyRowSearch = '<input type="text" class="form-control amount" readonly>
                                            </td>
                                            <td>
                                                <textarea class="form-control remarks" name="items[0][remarks]" style="height: 58px;" placeholder="Enter Remarks"></textarea>';
$emptyRowReplace = '<input type="text" class="form-control amount" readonly>
                                            </td>
                                            <td class="td-gst td-cgst d-none">
                                                <input type="number" class="form-control cgst_percent text-end" name="items[0][cgst_percent]" step="0.01" min="0" value="0">
                                            </td>
                                            <td class="td-gst td-cgst d-none">
                                                <input type="number" class="form-control cgst_amount text-end" name="items[0][cgst_amount]" step="0.01" min="0" value="0" readonly>
                                            </td>
                                            <td class="td-gst td-sgst d-none">
                                                <input type="number" class="form-control sgst_percent text-end" name="items[0][sgst_percent]" step="0.01" min="0" value="0">
                                            </td>
                                            <td class="td-gst td-sgst d-none">
                                                <input type="number" class="form-control sgst_amount text-end" name="items[0][sgst_amount]" step="0.01" min="0" value="0" readonly>
                                            </td>
                                            <td class="td-gst td-igst d-none">
                                                <input type="number" class="form-control igst_percent text-end" name="items[0][igst_percent]" step="0.01" min="0" value="0">
                                            </td>
                                            <td class="td-gst td-igst d-none">
                                                <input type="number" class="form-control igst_amount text-end" name="items[0][igst_amount]" step="0.01" min="0" value="0" readonly>
                                            </td>
                                            <td>
                                                <textarea class="form-control remarks" name="items[0][remarks]" style="height: 58px;" placeholder="Enter Remarks"></textarea>';
$content = str_replace($emptyRowSearch, $emptyRowReplace, $content);

// 5. Add GST columns to JS add_item template
$jsRowSearch = '<input type="text" class="form-control amount" readonly>
                    </td>
                    <td>
                        <textarea class="form-control remarks" name="items[${itemIndex}][remarks]" style="height: 58px;" placeholder="Enter Remarks"></textarea>';
$jsRowReplace = '<input type="text" class="form-control amount" readonly>
                    </td>
                    <td class="td-gst td-cgst d-none">
                        <input type="number" class="form-control cgst_percent text-end" name="items[${itemIndex}][cgst_percent]" step="0.01" min="0" value="0">
                    </td>
                    <td class="td-gst td-cgst d-none">
                        <input type="number" class="form-control cgst_amount text-end" name="items[${itemIndex}][cgst_amount]" step="0.01" min="0" value="0" readonly>
                    </td>
                    <td class="td-gst td-sgst d-none">
                        <input type="number" class="form-control sgst_percent text-end" name="items[${itemIndex}][sgst_percent]" step="0.01" min="0" value="0">
                    </td>
                    <td class="td-gst td-sgst d-none">
                        <input type="number" class="form-control sgst_amount text-end" name="items[${itemIndex}][sgst_amount]" step="0.01" min="0" value="0" readonly>
                    </td>
                    <td class="td-gst td-igst d-none">
                        <input type="number" class="form-control igst_percent text-end" name="items[${itemIndex}][igst_percent]" step="0.01" min="0" value="0">
                    </td>
                    <td class="td-gst td-igst d-none">
                        <input type="number" class="form-control igst_amount text-end" name="items[${itemIndex}][igst_amount]" step="0.01" min="0" value="0" readonly>
                    </td>
                    <td>
                        <textarea class="form-control remarks" name="items[${itemIndex}][remarks]" style="height: 58px;" placeholder="Enter Remarks"></textarea>';
$content = str_replace($jsRowSearch, $jsRowReplace, $content);

// 6. Fix JS calculation and visibility toggle
$jsUpdate1Search = 'function toggleTaxDivs() {
                let otherState = $(\'input[name="other_state"]:checked\').val();
                if (otherState === \'yes\') {
                    $(\'.igst-field\').removeClass(\'d-none\');
                    $(\'.cgst-field, .sgst-field\').addClass(\'d-none\');
                } else {
                    $(\'.igst-field\').addClass(\'d-none\');
                    $(\'.cgst-field, .sgst-field\').removeClass(\'d-none\');
                }
            }';

$jsUpdate1Replace = 'function toggleTaxDivs() {
                let otherState = $(\'input[name="other_state"]:checked\').val();
                let storeTypeId = $(\'#store_type_id\').val();
                let isAccessories = storeTypeId == 2;
                
                if (otherState === \'yes\') {
                    $(\'.igst-field\').removeClass(\'d-none\');
                    $(\'.cgst-field, .sgst-field\').addClass(\'d-none\');
                    if (isAccessories) {
                        $(\'.th-igst, .td-igst\').removeClass(\'d-none\');
                        $(\'.th-cgst, .th-sgst, .td-cgst, .td-sgst\').addClass(\'d-none\');
                    } else {
                        $(\'.th-igst, .td-igst, .th-cgst, .th-sgst, .td-cgst, .td-sgst\').addClass(\'d-none\');
                    }
                } else {
                    $(\'.igst-field\').addClass(\'d-none\');
                    $(\'.cgst-field, .sgst-field\').removeClass(\'d-none\');
                    if (isAccessories) {
                        $(\'.th-igst, .td-igst\').addClass(\'d-none\');
                        $(\'.th-cgst, .th-sgst, .td-cgst, .td-sgst\').removeClass(\'d-none\');
                    } else {
                        $(\'.th-igst, .td-igst, .th-cgst, .th-sgst, .td-cgst, .td-sgst\').addClass(\'d-none\');
                    }
                }
            }';
$content = str_replace($jsUpdate1Search, $jsUpdate1Replace, $content);

$jsUpdate2Search = '$(\'#store_type_id\').change(function () {
                let storeType = $(this).find(\'option:selected\').text().trim();
                if (storeType === \'Accessories\') {';

$jsUpdate2Replace = '$(\'#store_type_id\').change(function () {
                toggleTaxDivs();
                calculateTotals();
                let storeType = $(this).find(\'option:selected\').text().trim();
                if (storeType === \'Accessories\') {';
$content = str_replace($jsUpdate2Search, $jsUpdate2Replace, $content);

$jsUpdate3Search = 'function calculateTotals() {
                let subTotal = 0;
                let totalQty = 0;

                $(\'.item-row\').each(function () {
                    let qty = parseFloat($(this).find(\'.quantity\').val()) || 0;
                    let rate = parseFloat($(this).find(\'.rate\').val()) || 0;
                    let amount = qty * rate;
                    $(this).find(\'.amount\').val(amount.toFixed(2));
                    subTotal += amount;
                    totalQty += qty;
                });';

$jsUpdate3Replace = 'function calculateTotals() {
                let subTotal = 0;
                let totalQty = 0;
                
                let storeTypeId = $(\'#store_type_id\').val();
                let isAccessories = storeTypeId == 2;
                let otherState = $(\'input[name="other_state"]:checked\').val();
                let orderIgst = parseFloat($(\'#igst_percent\').val()) || 0;
                let orderCgst = parseFloat($(\'#cgst_percent\').val()) || 0;
                let orderSgst = parseFloat($(\'#sgst_percent\').val()) || 0;
                let itemsTaxTotal = 0;

                $(\'.item-row\').each(function () {
                    let qty = parseFloat($(this).find(\'.quantity\').val()) || 0;
                    let rate = parseFloat($(this).find(\'.rate\').val()) || 0;
                    let amount = qty * rate;
                    $(this).find(\'.amount\').val(amount.toFixed(2));
                    
                    if (isAccessories) {
                        if (otherState === \'yes\') {
                            let itemIgstPercent = parseFloat($(this).find(\'.igst_percent\').val()) || orderIgst;
                            $(this).find(\'.igst_percent\').val(itemIgstPercent);
                            let igstAmount = (amount * itemIgstPercent) / 100;
                            $(this).find(\'.igst_amount\').val(igstAmount.toFixed(2));
                            itemsTaxTotal += igstAmount;
                        } else {
                            let itemCgstPercent = parseFloat($(this).find(\'.cgst_percent\').val()) || orderCgst;
                            let itemSgstPercent = parseFloat($(this).find(\'.sgst_percent\').val()) || orderSgst;
                            $(this).find(\'.cgst_percent\').val(itemCgstPercent);
                            $(this).find(\'.sgst_percent\').val(itemSgstPercent);
                            let cgstAmount = (amount * itemCgstPercent) / 100;
                            let sgstAmount = (amount * itemSgstPercent) / 100;
                            $(this).find(\'.cgst_amount\').val(cgstAmount.toFixed(2));
                            $(this).find(\'.sgst_amount\').val(sgstAmount.toFixed(2));
                            itemsTaxTotal += cgstAmount + sgstAmount;
                        }
                    }
                    
                    subTotal += amount;
                    totalQty += qty;
                });';
$content = str_replace($jsUpdate3Search, $jsUpdate3Replace, $content);

$jsUpdate4Search = 'let taxableAmount = subTotal - discountAmount - commissionAmount;

                $(\'#taxable_amount\').val(taxableAmount.toFixed(2));

                let otherState = $(\'input[name="other_state"]:checked\').val();
                let taxAmount = 0;

                if (otherState === \'yes\') {
                    let igstPercent = parseFloat($(\'#igst_percent\').val()) || 0;
                    taxAmount = (taxableAmount * igstPercent) / 100;
                } else {
                    let cgstPercent = parseFloat($(\'#cgst_percent\').val()) || 0;
                    let sgstPercent = parseFloat($(\'#sgst_percent\').val()) || 0;
                    taxAmount = (taxableAmount * (cgstPercent + sgstPercent)) / 100;
                }

                $(\'#tax_amount\').val(taxAmount.toFixed(2));';

$jsUpdate4Replace = 'let taxableAmount = subTotal - discountAmount - commissionAmount;

                $(\'#taxable_amount\').val(taxableAmount.toFixed(2));

                let taxAmount = 0;
                
                if (isAccessories) {
                    taxAmount = itemsTaxTotal;
                } else {
                    if (otherState === \'yes\') {
                        let igstPercent = parseFloat($(\'#igst_percent\').val()) || 0;
                        taxAmount = (taxableAmount * igstPercent) / 100;
                    } else {
                        let cgstPercent = parseFloat($(\'#cgst_percent\').val()) || 0;
                        let sgstPercent = parseFloat($(\'#sgst_percent\').val()) || 0;
                        taxAmount = (taxableAmount * (cgstPercent + sgstPercent)) / 100;
                    }
                }

                $(\'#tax_amount\').val(taxAmount.toFixed(2));';
$content = str_replace($jsUpdate4Search, $jsUpdate4Replace, $content);


// Wait, I need to make sure the item level inputs trigger `calculateTotals()` when changed.
$jsUpdate5Search = '$(document).on(\'input\', \'.quantity, .rate\', function () {
                calculateTotals();
            });';
$jsUpdate5Replace = '$(document).on(\'input\', \'.quantity, .rate, .cgst_percent, .sgst_percent, .igst_percent\', function () {
                calculateTotals();
            });';
$content = str_replace($jsUpdate5Search, $jsUpdate5Replace, $content);

file_put_contents($file, $content);
echo "File updated successfully.\n";

