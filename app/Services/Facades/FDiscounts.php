<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Discount;
use App\Services\Interfaces\IDiscounts;

class FDiscounts extends FBase implements IDiscounts
{
    public function __construct()
    {
        $this->model = Discount::class;
        $this->rules = [
            'name' => _RuleHelper::_Rule_Require,
            'description' => _RuleHelper::_Rule_Require,
            'discount_percent' => _RuleHelper::_Rule_Require,
            'active' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['name', 'description','discount_percent','active'];
    }
}
