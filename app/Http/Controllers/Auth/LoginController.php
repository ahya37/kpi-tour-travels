<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = '/home';

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    public function index()
    {
        //return inertia
        return view('auth.login');
    }

    public function store(Request $request)
    {
        //set validation
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

         //get email and password from request
         $credentials = $request->only('email', 'password');

         //attempt to login
         if (auth()->attempt($credentials)) {
 
             //regenerate session
             $request->session()->regenerate();
 
             //redirect route dashboard
             return redirect()->route('dashboard');
         }
 
         //if login fails
         return back()->with([
             'error' => 'Email atau password salah',
         ]);
    }

    public function logout(Request $request)
    {
        //logout user
        auth()->logout();
        
        //invalidate session
        $request->session()->invalidate();
        
        //session regenerate
        $request->session()->regenerateToken();
        
        //redirect login page
        return redirect('/login');
    }
}
