<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Category;
use App\Models\ParamType;
use App\Models\ProductParma;
use App\Services\Interfaces\ICategory;

class FCategory extends FBase implements ICategory
{
    public function __construct()
    {
        $this->model = Category::class;
        $this->slug = true;
        $this->slugging = 'name';
        $this->rules = [
            'name' => _RuleHelper::_Rule_Require,
        ];
        $this->columns = ['name', 'parent_id'];
    }




}
