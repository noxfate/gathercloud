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
        view()->composer('layouts.master-index', function($view) {
            $conn = User::find(auth()->user()->id)->tokens->all();
            $link = User::find(auth()->user()->id)->links
                ->groupBy('link_name')
                ->all();
            $view->with([
                'conn'=> $conn,
                'link' => $link
                ]);
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
