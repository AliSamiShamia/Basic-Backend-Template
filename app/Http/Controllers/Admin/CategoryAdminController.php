<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\ICategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CategoryAdminController extends Controller
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
        $res = $this->category->index($request);
        try {
            return CategoryResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return CategoryResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $res = $this->category->store($request);
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return CategoryResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $res = $this->category->update($request, $id);
            if ($res) {
                return CategoryResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    public function uploadMedia(Request $request, $id)
    {
        try {
            $rules = _RuleHelper::getRule('media');
            $request->validate($rules);
            $category = $this->category->getById($id);
            if ($category) {
                $check = $this->category->uploadImages($category, $request->files, 'image');
                if ($check) {
                    return BaseResource::ok();
                }
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return();
        }
    }

    public function deleteMedia($product_id, $id)
    {
        try {
            $category = $this->category->getById($id);
            if ($category) {
                $res = $this->category->destroyMedia($category->id, $id);
                if ($res) {
                    return BaseResource::ok();
                }
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return BaseResource|JsonResponse
     */
    public function destroy($id)
    {
        try {
            $res = $this->category->delete($id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }
}
