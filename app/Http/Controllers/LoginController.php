<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function loginpage()
    {
        return view('auth.login');
    }

    public function login(AuthRequest $request)
    {
      try{
        
        return AuthService::login($request);
        
      }catch(ValidationException $e){
        // Log error $e->getMessage();
        return redirect()->back()->with(['error' => 'Email / Password Salah']); 
      }
          
    }

    public function logout()
    {
       return AuthService::logout();
    }
}
