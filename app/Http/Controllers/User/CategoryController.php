<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\CategoryResource;
use App\Services\Interfaces\ICategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    private $category;

    /**
     * @param ICategory $category
     */
    public function __construct(ICategory $category)
    {
        $this->category = $category;
    }


    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->category->index($request);
            return CategoryResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return CategoryResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->category->getById($id);
            if ($res) {
                return CategoryResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return CategoryResource|JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $res = $this->category->getBySlug($slug);
            if ($res) {
                return CategoryResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

}
