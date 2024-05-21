<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BaseService;
use Response;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Home'
        ];
        return view('home', $data);
    }
}
