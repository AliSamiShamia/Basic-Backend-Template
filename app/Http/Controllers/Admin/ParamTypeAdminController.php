<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helper\_RuleHelper;
use App\Http\Resources\Admin\ParamTypeResource;
use App\Http\Resources\BaseResource;
use App\Services\Interfaces\IType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ParamTypeAdminController extends Controller
{

    private IType $type;

    /**
     * @param IType $type
     */
    public function __construct(IType $type)
    {
        $this->type = $type;
    }


    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->type->index($request);
            return ParamTypeResource::collection($res);
        } catch (Exception $exception) {
            return BaseResource::return();

        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return ParamTypeResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $rules = _RuleHelper::getRule('param_type');
            $request->validate($rules);
            $res = $this->type->store($request);
            if ($res) {
                return ParamTypeResource::create($res);
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
     * @return ParamTypeResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->type->getById($id);
            if ($res) {
                return ParamTypeResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return ParamTypeResource|JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $rules = _RuleHelper::getRule('param_type');
            $request->validate($rules);
            $res = $this->type->update($request, $id);
            if ($res) {
                return ParamTypeResource::create($res);
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
            $res = $this->type->delete($id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::return();
        }
    }
}
