<?php

namespace App\Services\Interfaces;

use App\Models\Product;
use Illuminate\Http\Request;

interface ICart extends IBase
{

    public function addItem(Request $request);

    public function updateItem(Request $request,$id);

    public function removeItem($id);

    public function removeAll();

    public function total();
}
