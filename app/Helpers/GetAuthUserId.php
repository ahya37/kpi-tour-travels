<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('GetAuthUserId')) {
    function GetAuthUserId() {
        return Auth::check() ? Auth::user()->id : null;
    }
}