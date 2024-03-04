<?php

namespace App\Http\Middleware;

use App\Http\Resources\BaseResource;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();
        if ($user->role == 'admin') {
            return $next($request);
        }
        return BaseResource::return('Unauthorized!', 401);
    }
}
