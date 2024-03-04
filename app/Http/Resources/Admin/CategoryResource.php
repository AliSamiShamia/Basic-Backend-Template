<?php

namespace App\Http\Resources\Admin;

use App\Helper\_MediaHelper;
use App\Http\Resources\BaseResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class CategoryResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $parent = $this->Parent()->first();
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'media' => $this->media($this),
            'parent_id' => $parent ? $parent->name : null,
            'products' => $this->ListOfProducts($this),
        ];
    }

    private function ListOfProducts($category)
    {
        return $category->Products()->get()->count();
    }

    public function media($obj)
    {
        $media = $obj->allMedia()->get();
        $data = [];
        foreach ($media as $item) {
            $data[] = [
                'id' => $item->id,
                'url' => _MediaHelper::getURL($item->url, $item->type),
            ];
        }
        return $data;
    }
}
