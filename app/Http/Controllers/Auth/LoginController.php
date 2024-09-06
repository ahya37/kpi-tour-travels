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

        $sendData   = [
            "email"         => $request->all()['email'],
            "password"      => $request->all()['password'],
        ];

        //get email and password from request
        //  $credentials = $request->only('email', 'password');
        $credentials    = $sendData;

         //attempt to login
         if (auth()->attempt($credentials)) {
 
            // CHECK USER IS ACTIVE OR NOT?
            $data_user_is_active    = [
                "email"     => $sendData['email'],
                "password"  => $sendData['password'],
                "is_active" => "1",
            ];

            if(auth()->attempt($data_user_is_active) === true) {
                //regenerate session
                $request->session()->regenerate();
    
                //redirect route dashboard
                return redirect()->route('dashboard');                   
            } else {
                // REMOVE CURRENT SESSION
                $request->session()->invalidate();
                return back()->with([
                    'error' => 'Akun Sudah Tidak Aktif',
                ]);
            }
         } else {
            return back()->with([
                'error' => 'Email / Password Salah',
            ]);
         }
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
