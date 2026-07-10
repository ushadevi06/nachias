<?php
$file = 'd:/xampp/htdocs/nachias/resources/views/sales_invoice/pdf.blade.php';
$content = file_get_contents($file);

// 1. Column Width Definitions
$search1 = "\$colWidths = [
    'sno' => 4,
    'desc' => \$showAmount ? 22 : 28,
    'color' => 10,
    'art' => 10,
    'uom' => 6,
    'size' => 8,
    'qty' => 8,
];
if (\$showMrp) {
    \$colWidths['mrp'] = 10;
}
if (\$showPrice) {
    \$colWidths['price'] = 10;
}";

$replace1 = "\$colWidths = [
    'sno' => 4,
    'desc' => \$showAmount ? 30 : 42,
    'art' => 18,
    'uom' => 6,
    'size' => 6,
    'qty' => 8,
];
if (\$showMrp) {
    \$colWidths['mrp'] = 8;
}
if (\$showPrice) {
    \$colWidths['price'] = 8;
}";
$content = str_replace($search1, $replace1, $content);

// 2. Width variables
$search2 = "\$w_1_6 = \$colWidths['sno'] + \$colWidths['desc'] + \$colWidths['color'] + \$colWidths['art'] + \$colWidths['uom'] + \$colWidths['size'];
\$w1_7 = \$w_1_6 + \$colWidths['qty'];

\$w_1_5 = \$colWidths['sno'] + \$colWidths['desc'] + \$colWidths['color'] + \$colWidths['art'] + \$colWidths['uom'];
\$w_6_7 = \$colWidths['size'] + \$colWidths['qty'];";

$replace2 = "\$w_1_5 = \$colWidths['sno'] + \$colWidths['desc'] + \$colWidths['art'] + \$colWidths['uom'] + \$colWidths['size'];
\$w1_6 = \$w_1_5 + \$colWidths['qty'];

\$w_1_4 = \$colWidths['sno'] + \$colWidths['desc'] + \$colWidths['art'] + \$colWidths['uom'];
\$w_5_6 = \$colWidths['size'] + \$colWidths['qty'];";
$content = str_replace($search2, $replace2, $content);

// 3. Table Header
$search3 = "                    <th width=\"{{ \$showAmount ? '22%' : '28%' }}\">Description</th>
                    <th width=\"10%\">Art</th>
                    <th width=\"10%\">Color</th>
                    <th width=\"6%\">UOM</th>
                    <th width=\"8%\">Size</th>
                    <th width=\"8%\">Qty</th>
                    @if(\$showMrp)
                    <th width=\"10%\">MRP</th>
                    @endif
                    @if(\$showPrice)
                    <th width=\"10%\">Price</th>";

$replace3 = "                    <th width=\"{{ \$showAmount ? '30%' : '42%' }}\">Description</th>
                    <th width=\"18%\">Art</th>
                    <th width=\"6%\">UOM</th>
                    <th width=\"6%\">Size</th>
                    <th width=\"8%\">Qty</th>
                    @if(\$showMrp)
                    <th width=\"8%\">MRP</th>
                    @endif
                    @if(\$showPrice)
                    <th width=\"8%\">Price</th>";
$content = str_replace($search3, $replace3, $content);

// 4. Table Body (Item loop)
$search4 = "                        <td class=\"text-center\">{{ \$item->art_no }}</td>
                        <td class=\"text-center\">{{ \$item->api_color ?: (\$item->color ? \$item->color->color_name : '-') }}</td>
                        <td class=\"text-center\">{{ \$item->uom->uom_code ?? 'PCS' }}</td>
                        <td class=\"text-center\">{{ \$item->sizeRatio ? \$item->sizeRatio->size : (\$item->size ?? '-') }}</td>
                        <td class=\"text-center\">{{ number_format(\$item->quantity, 2) }}</td>
                        @if(\$showMrp)
                        <td class=\"text-right\">{{ number_format(\$item->mrp, 2) }}</td>
                        @endif
                        @if(\$showPrice)
                        <td class=\"text-right\">{{ number_format(\$item->rate, 2) }}</td>";

$replace4 = "                        <td class=\"text-center\">{{ \$item->art_no }}</td>
                        <td class=\"text-center\">{{ \$item->uom->uom_code ?? 'PCS' }}</td>
                        <td class=\"text-center\">{{ \$item->sizeRatio ? \$item->sizeRatio->size : (\$item->size ?? '-') }}</td>
                        <td class=\"text-center\">{{ number_format(\$item->quantity, 2) }}</td>
                        @if(\$showMrp)
                        <td class=\"text-right\">{{ number_format(\$item->mrp, 0) }}</td>
                        @endif
                        @if(\$showPrice)
                        <td class=\"text-right\">{{ number_format(\$item->rate, 0) }}</td>";
$content = str_replace($search4, $replace4, $content);

// 5. Padding Rows
$search5 = "                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @if(\$showMrp)<td>&nbsp;</td>@endif";

$replace5 = "                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @if(\$showMrp)<td>&nbsp;</td>@endif";
$content = str_replace($search5, $replace5, $content);

// 6. Footer Total Quantity Row
$search6 = "                    <td style=\"border-top: none;\"></td> 
                    <td style=\"border-top: none;\"></td> 
                    <td style=\"border-top: none;\"></td>
                    <td style=\"border-top: none;\"></td> 
                    <td style=\"border-top: none;\"></td> 
                    <td class=\"text-center bold\" style=\"border-top: 1px solid #000000;\">{{ number_format(\$invoice->items->sum('quantity'), 2) }}</td>";
$replace6 = "                    <td style=\"border-top: none;\"></td> 
                    <td style=\"border-top: none;\"></td> 
                    <td style=\"border-top: none;\"></td>
                    <td style=\"border-top: none;\"></td> 
                    <td class=\"text-center bold\" style=\"border-top: 1px solid #000000;\">{{ number_format(\$invoice->items->sum('quantity'), 2) }}</td>";
$content = str_replace($search6, $replace6, $content);

// 7. Colspan and Width replacements in bottom blocks
$content = str_replace("colspan=\"7\" style=\"width: {{ \$w1_7 }}%", "colspan=\"6\" style=\"width: {{ \$w1_6 }}%", $content);
$content = str_replace("width: {{ (\$w_1_5 / \$w1_7) * 100 }}%", "width: {{ (\$w_1_4 / \$w1_6) * 100 }}%", $content);
$content = str_replace("width: {{ (\$w_6_7 / \$w1_7) * 100 }}%", "width: {{ (\$w_5_6 / \$w1_6) * 100 }}%", $content);
$content = str_replace("colspan=\"7\" style=\"padding: 8px;", "colspan=\"6\" style=\"padding: 8px;", $content);

file_put_contents($file, $content);
echo "Modifications done.\n";
