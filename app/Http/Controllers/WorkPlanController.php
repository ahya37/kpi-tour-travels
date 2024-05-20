<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkPlanController extends Controller
{
    public function index()
    {
        return view('workplans.index', [
            'title' => 'Rencana Kerja',
        ]);
    }
}
