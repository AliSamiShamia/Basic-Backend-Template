<?php

namespace App\Helper;

use Illuminate\Support\Str;

class _SKUHelper
{
    public static function generateSKU($productName, $productId): string
    {
        // You can modify this logic based on your requirements
        $productNameSlug = Str::slug($productName); // Convert product name to slug
        $prefix = "JW-";
        // Generate a unique SKU combining product attributes
        return strtoupper($prefix . "-" . $productNameSlug . "-" . $productId);
    }

}
