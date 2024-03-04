<?php

namespace App\Http\Controllers\Admin;

use App\Helper\_Core;
use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductParamResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IProductParam;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ParamAdminController extends Controller
{
    private IProductParam $param;

    /**
     * @param $param
     */
    public function __construct(IProductParam $param)
    {
        $this->param = $param;
    }


    /**
     * Display a listing of the resource.
     *
     * @return ProductParamResource|JsonResponse|Response
     */
    public function index(Request $request)
    {
        try {
            $res = $this->param->index($request);
            return ProductParamResource::collection($res);
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ProductParamResource|JsonResponse|Response
     */
    public function store(Request $request)
    {
        try {
            $rules = _RuleHelper::getRule('product_param');
            $request->validate($rules);
            $res = $this->param->store($request);
            if ($res) {
                return ProductParamResource::create($res);
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
     * @return ProductParamResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->param->getById($id);
            if ($res) {
                return ProductParamResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return ProductParamResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = _RuleHelper::getRule('product_param');
            $request->validate($rules);
            $res = $this->param->update($request, $id);

            if ($res) {
                return ProductParamResource::create($res);
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
            $res = $this->param->delete($id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return();
        }
    }
}
