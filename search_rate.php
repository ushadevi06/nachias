<?php
$lines = file('resources/views/purchase_orders/add.blade.php');
foreach ($lines as $i => $line) {
    if (stripos($line, 'rate') !== false || stripos($line, 'step=') !== false) {
        echo ($i + 1) . ": " . trim($line) . "\n";
    }
}
