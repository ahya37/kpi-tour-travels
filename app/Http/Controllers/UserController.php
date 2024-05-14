<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
         //get users
         $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q . '%');
        })->with('roles')->latest()->paginate(5);
        
        //append query string to pagination links
        $users->appends(['q' => request()->q]);

        $no = 1;

        return view('users.index',[
            'title' => 'Users',
            'users' => $users,
            'no' => $no
        ]);
    }

    public function create()
    {
        $roles = Role::all();

        return view('users.create',[
            'title' => 'Tambah User',
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            
            $requestUser = $request->validated(); 
            $results = UserService::store($requestUser);
            
            DB::commit();
            return $results;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
