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
        require_once app_path('Helpers/GetAuthUserId.php');
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
        // $user_id = GetAuthUserId();
        // dd($user_id);
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
        // $userId = app('user_id');
        // dd($userId);
        // if (Auth::check()) {
        //     $userId = Auth::id();
        //     // Lakukan sesuatu dengan $userId, seperti mendaftarkan view composer
        //     view()->composer('*', function ($view) use ($userId) {
        //         $view->with('userId', $userId);
        //     });
        // }

    }
}
