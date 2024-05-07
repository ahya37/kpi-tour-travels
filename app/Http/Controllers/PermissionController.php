<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
         //get permissions
         $permissions = Permission::when(request()->q, function($permissions) {
            $permissions = $permissions->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $permissions->appends(['q' => request()->q]);

        return $permissions;
    }
}
