<?php

namespace App\Providers;

use App\Services\Facades\FBase;
use App\Services\Facades\FCart;
use App\Services\Facades\FCategory;
use App\Services\Facades\FCurrency;
use App\Services\Facades\FDiscounts;
use App\Services\Facades\FOrder;
use App\Services\Facades\FProduct;
use App\Services\Facades\FProductParam;
use App\Services\Facades\FType;
use App\Services\Facades\FUser;
use App\Services\Facades\FWishlist;
use App\Services\Interfaces\IBase;
use App\Services\Interfaces\ICart;
use App\Services\Interfaces\ICategory;
use App\Services\Interfaces\ICurrency;
use App\Services\Interfaces\IDiscounts;
use App\Services\Interfaces\IOrder;
use App\Services\Interfaces\IProduct;
use App\Services\Interfaces\IProductParam;
use App\Services\Interfaces\IType;
use App\Services\Interfaces\IUser;
use App\Services\Interfaces\IWishlist;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(IBase::class, FBase::class);
        $this->app->singleton(ICategory::class, FCategory::class);
        $this->app->singleton(ICurrency::class, FCurrency::class);
        $this->app->singleton(IProduct::class, FProduct::class);
        $this->app->singleton(IOrder::class, FOrder::class);
        $this->app->singleton(ICart::class, FCart::class);
        $this->app->singleton(IWishlist::class, FWishlist::class);
        $this->app->singleton(IDiscounts::class, FDiscounts::class);
        $this->app->singleton(IUser::class, FUser::class);
        $this->app->singleton(IProductParam::class, FProductParam::class);
        $this->app->singleton(IType::class, FType::class);
    }
}
