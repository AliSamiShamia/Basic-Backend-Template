<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\WishlistResource;
use App\Services\Interfaces\IWishlist;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    private $wishlist;


    /**
     * @param IWishlist $wishlist
     */
    public function __construct(IWishlist $wishlist)
    {
        $this->wishlist = $wishlist;
    }


    /**
     * Display a listing of the resource.
     *
     * @return BaseResource|JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $res = $this->wishlist->index($request);
            return WishlistResource::paginable($res);
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return BaseResource|JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $res = $this->wishlist->addItem($request);
            if ($res) {
                return BaseResource::ok();
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
     * @return WishlistResource|JsonResponse
     */
    public function show($id)
    {
        try {
            $res = $this->wishlist->getById($id);
            if ($res) {
                return WishlistResource::create($res);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::exception($exception);
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
            $res = $this->wishlist->removeItem($id);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            Log::error($exception);
            return BaseResource::exception($exception);
        }
    }

    public function removeAll()
    {
        try {
            $res = $this->wishlist->removeAll();
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (\Exception $exception) {
            return BaseResource::exception($exception);

        }
    }
}
