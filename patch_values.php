<?php

$file = 'resources/views/purchase_orders/add.blade.php';
$content = file_get_contents($file);

$content = str_replace('step="0.01" min="0" value="0">', 'step="0.01" min="0" value="">', $content);
$content = str_replace('step="0.01" min="0" value="0" readonly>', 'step="0.01" min="0" value="" readonly>', $content);

file_put_contents($file, $content);
echo "Done replacing 0 with empty.\n";

