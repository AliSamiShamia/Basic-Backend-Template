<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IProduct;
use App\Services\Interfaces\IType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductAdminController extends Controller
{
    private $product, $type;

    public function __construct(IProduct $product, IType $type)
    {
        $this->product = $product;
        $this->type = $type;
    }

    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->product->index($request);
            return ProductResource::paginable($res);
        } catch (Exception $ex) {
            return BaseResource::exception($ex);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate(_RuleHelper::getRule('product-create'));
            $res = $this->product->store($request);
            if ($res) {
                $categories = $request->categories;
                if ($categories) {
                    $res->Categories()->sync($categories);
                    DB::commit();
                }
                DB::rollBack();
                return ProductResource::create($res);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            DB::rollBack();
            return BaseResource::exception($exception);
        }
    }

    public function productParams(Request $request, $product_id)
    {

        try {
            DB::beginTransaction();
            $product = $this->product->getById($product_id);
            $rules = _RuleHelper::getRule('product_param');
            $request->validate($rules);
            if ($product) {
                $type_id = $request->type_id;
                $type = $this->type->getById($type_id);
                if ($type) {
                    $product->ProductParams()->create([
                        'type_id' => $type->id,
                        'value' => $request->input('value')
                    ]);
                    DB::commit();
                    return BaseResource::ok();
                }
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            DB::rollBack();
            return BaseResource::return();
        }
    }


    public function uploadMedia(Request $request, $id)
    {
        try {
            $rules = _RuleHelper::getRule('media');
            $request->validate($rules);
            $product = $this->product->getById($id);
            if ($product) {
                $check = $this->product->uploadImages($product, $request->files, 'image');
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
            $product = $this->product->getById($id);
            if ($product) {
                $res = $this->product->destroyMedia($product->id, $id);
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
     * Display the specified resource.
     *
     * @param int $id
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->product->getById($id);
            if ($res) {
                return ProductResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $res = $this->product->update($request, $id);
            $categories = $request->categories;
            if ($categories) {
                $res->Categories()->sync($categories);
            }
            if ($res) {
                DB::commit();
                return ProductResource::create($res);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            DB::rollBack();
            return BaseResource::exception($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return BaseResource|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $res = $this->product->delete($id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }
}
