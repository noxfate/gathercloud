<?php

namespace App\Providers;

use App\Providers;
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
            $conn = User::find(auth()->user()->id)->token->all();
            $logo = array();
            foreach($conn as $c){
                array_push($logo,Providers::where('id',$c->provider_id)->first());
            }
            $link = User::find(auth()->user()->id)->link
                ->groupBy('link_name')
                ->all();
            $view->with([
                'conn'=> $conn,
                'logo'=>$logo,
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
