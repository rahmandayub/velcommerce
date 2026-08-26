<?php

namespace App\Exceptions;

use App\Models\Product;
use App\Models\ProductVariant;
use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly Product $product, public readonly ?ProductVariant $variant = null)
    {
        $label = $variant !== null ? $variant->name : $product->name;

        parent::__construct("Insufficient stock for [{$label}].");
    }
}
