<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ParamTypeAdminController;
use App\Http\Controllers\Admin\CartAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\DiscountAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ParamAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CategoryController;
use App\Http\Controllers\User\GlobalController;
use App\Http\Controllers\User\OrderUserController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\UserAddressController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::get('countries', [GlobalController::class, 'countries']);
    Route::group(['prefix' => 'user', 'middleware' => []], function () {
        Route::get('sitemap', [GlobalController::class, 'sitemap']);
        Route::post('/profile', [UserController::class, 'profile'])->name('profile');
        Route::post('/login', [UserController::class, 'login'])->name('login');
        Route::post('/loginByEmail', [UserController::class, 'loginByEmail'])->name('loginByEmail');
        Route::post('/register', [UserController::class, 'register'])->name('register');
        Route::group(['prefix' => 'otp'], function () {
            Route::post('/send', [UserController::class, 'sendOTP'])->name('sendOTP');
            Route::post('/check', [UserController::class, 'checkOTP'])->name('checkOTP');
        });

        Route::group(['prefix' => 'category'], function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category.list');
            Route::get('/{slug}', [CategoryController::class, 'getBySlug'])->name('category.show_by_slug');
        });
        Route::group(['prefix' => 'product'], function () {
            Route::get('/', [ProductController::class, 'index'])->name('product.list');
            Route::get('/blank', [ProductController::class, 'blankList'])->name('product.list.without.filter');
            Route::get('/{slug}', [ProductController::class, 'getBySlug'])->name('product.show_by_slug');
        });

        Route::post('/contact-us', [UserController::class, 'contact'])->name('contact');
        Route::get('/order/status/{id}/{token}', [OrderUserController::class, 'orderSubmitted'])->name('orderSubmitted');

        Route::group(['middleware' => ['auth:api']], function () {
            Route::apiResources([
                "cart" => CartController::class,
                "wishlist" => WishlistController::class,
                'order' => OrderUserController::class,
                'user-address' => UserAddressController::class,
            ], [
                'except' => [
                    'edit', 'create'
                ]
            ]);
            Route::group(['prefix' => 'user-address'], function () {
                Route::post('/default/{id}', [UserAddressController::class, 'setDefaultAddress']);
            });

            Route::group(['prefix' => 'update-info'], function () {
                Route::post('/', [UserController::class, 'updateInfo']);
                Route::post('/security', [UserController::class, 'updateSecurityInfo']);
            });

            Route::group(['prefix' => 'order'], function () {
                Route::get('/invoice/{id}', [OrderUserController::class, 'invoice']);
            });
            Route::get('/default-address', [UserAddressController::class, 'getDefaultAddress']);

            Route::post('/cart/update-quantity/{id}', [CartController::class, 'changeQuantity']);
            Route::post('update-cart/increment', [CartController::class, 'removeAll']);
            Route::delete('clear-cart', [CartController::class, 'removeAll']);
            Route::delete('clear-wishlist', [WishlistController::class, 'removeAll']);
            Route::get('default-user-address', [UserAddressController::class, 'getDefaultAddress']);
            Route::post('default-user-address', [UserAddressController::class, 'changeDefaultAddress']);
        });
    });


    Route::group(['prefix' => 'admin', 'middleware' => []], function () {
        Route::post('/login', [UserAdminController::class, 'login'])->name('login');
        Route::post('/profile', [UserAdminController::class, 'profile'])->name('profile');

    });
    Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'admin']], function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::apiResources([
            "category" => CategoryAdminController::class,
            "product" => ProductAdminController::class,
            "discount" => DiscountAdminController::class,
            "user" => UserAdminController::class,
            "params" => ParamAdminController::class,
            "paramType" => ParamTypeAdminController::class,
        ], [
            'except' => [
                'edit', 'create'
            ]
        ]);
        Route::apiResources([
            "order" => OrderAdminController::class,
            "cart" => CartAdminController::class,
        ], [
            'except' => [
                'store', 'update'
            ]
        ]);
        Route::group(['prefix' => 'order'], function () {
            Route::get('/invoice/{id}', [OrderAdminController::class, 'invoice']);
            Route::get('/{id}', [OrderAdminController::class, 'invoice']);
        });
        Route::group(['prefix' => 'user'], function () {
            Route::get('/{id}/orders', [UserAdminController::class, 'orders']);
            Route::get('/{id}/address', [UserAdminController::class, 'addresses']);
            Route::post('/{id}/permission', [UserAdminController::class, 'changePermission']);

        });
        Route::group(['prefix' => 'products'], function () {
            Route::post('/param/{product_id}', [ProductAdminController::class, 'productParams']);
            Route::post('/media/{id}', [ProductAdminController::class, 'uploadMedia']);
            Route::delete('/media/{product_id}/{id}', [ProductAdminController::class, 'deleteMedia']);

        });
        Route::group(['prefix' => 'category'], function () {
            Route::post('/media/{id}', [CategoryAdminController::class, 'uploadMedia']);
            Route::delete('/media/{product_id}/{id}', [ProductAdminController::class, 'deleteMedia']);

        });
    });
});
