<?php

namespace App\Providers;

use App\Http\Composers\LayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
class ComposerServiceProvider extends ServiceProvider
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
        View::share('period', LayoutComposer::morningPeriod());

        view()->composer(
            ['web.layout','mobile.layout','web.layout.layout'],     //模板名
            'App\Http\Composers\LayoutComposer@all'    //方法名或者类中的方法
        );

    }
}
