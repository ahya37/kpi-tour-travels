<?php 
namespace App\Helpers;
use App\Models\Notification;

class Notifications{

    public static function prospekAlumniByAccount($user_id)
    {
        $notification = Notification::where('user_id', $user_id)->get();
        
        return $notification;
    }
}