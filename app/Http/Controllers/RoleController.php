<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        //get roles
        $roles = Role::when(request()->q, function($roles) {
            $roles = $roles->where('name', 'like', '%'. request()->q . '%');
        })->with('permissions')->latest()->paginate(5);

        //append query string to pagination links
        $roles->appends(['q' => request()->q]);

        $no = 1;

        return view('roles.index',[
            'title' => 'Roles',
            'roles' => $roles,
            'no' => $no
        ]);
    }
}
