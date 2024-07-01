<?php 
namespace App\Helpers;
use App\Models\Notification;
use Auth;

class Notifications{

    public static function prospekAlumniByAccount()
    {
        $user_id = Auth::user();
        $notification = Notification::where('user_id', $user_id)->get();
        
        return $notification;
    }
}