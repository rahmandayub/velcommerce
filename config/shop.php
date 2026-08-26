<?php

return [

    'currency' => 'IDR',

    // Flat shipping cost in IDR (Fase 2: no real shipping API yet).
    'shipping_cost' => env('SHOP_SHIPPING_COST', 15000),

    // Products per page on the storefront catalog.
    'products_per_page' => 12,

    // Maximum quantity per cart line item.
    'cart_max_quantity' => 99,

    // Stock level at or below which a product/variant is considered "low stock".
    'low_stock_threshold' => env('SHOP_LOW_STOCK_THRESHOLD', 5),

];
