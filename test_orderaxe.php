<?php
$service = new \App\Services\OrderaxeService();
$orders = $service->fetchOrders(0, 1);
if (!$orders) {
    echo "No orders fetched\n";
    exit;
}

foreach ($orders as $orderData) {
    if (!empty($orderData['products'])) {
        foreach ($orderData['products'] as $product) {
            echo "Product keys: " . implode(", ", array_keys($product)) . "\n";
            if (isset($product['product_items'])) {
                echo "product_items exist! Count: " . count($product['product_items']) . "\n";
            } else {
                echo "product_items missing!\n";
            }
        }
    }
}
