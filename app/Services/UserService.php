<?php 

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService {

    public static function store($request)
    {
        $user = User::create([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'password' => bcrypt($request['password'])
        ]);

        //assign roles to user
        return $user->assignRole($request['roles']);
    }
}