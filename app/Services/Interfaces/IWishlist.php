<?php

namespace App\Services\Interfaces;

use App\Models\Product;
use Illuminate\Http\Request;

interface IWishlist extends IBase
{

    public function addItem(Request $request);

    public function removeItem($id);

    public function removeAll();
    public function moveToCart($id);

}
