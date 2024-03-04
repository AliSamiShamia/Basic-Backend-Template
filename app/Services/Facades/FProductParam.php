<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\ProductParma;
use App\Services\Interfaces\IProductParam;

class FProductParam extends FBase implements IProductParam
{
    public function __construct()
    {
        $this->model = ProductParma::class;
        $this->rules = [
            'value' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['type_id', 'product_id', 'value'];
    }
}
