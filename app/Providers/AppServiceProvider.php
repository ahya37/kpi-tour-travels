<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Notifications;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Providers\GlobalProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // get notification by user login
        // $gF       = new Globalprovider();
        // $userMenus = $gF->getUserId();

        // dd($userMenus);
        // $user_id = Auth::user();
        // $notifications = Notifications::prospekAlumniByAccount($user_id);

        // view()->composer('*', function($view){
        //     if (Auth::check()) {
                
        //         View::share([
        //             'notifications' => $notifications,
        //         ]);
        //     }
        // });
        // Ensure this code runs after the authentication system is initialized
        // $this->app->booted(function () {
        //     if (Auth::check()) {
        //         $userId = Auth::user()->id;
        //         // Use the $userId as needed
        //         dd($userId);
        //     }
        // });

    }
}
