<?php

namespace App\Listeners;

use App\Events\UserLogin;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;

class UserLoginListener
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     */
    public function handle(UserLogin $event)
    {
        //simpan session user_id 
        session(['user_id' => $event->userId]);
    }
}
