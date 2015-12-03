<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        $conn = User::find(auth()->user()->id)->tokens->all();
        view()->composer('layout.layout-of-index', function($view) {
            $conn = User::find(auth()->user()->id)->tokens->all();
            $view->with('conn', $conn);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
