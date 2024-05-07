<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthService 
{
	public static function login($request)
	{
		DB::beginTransaction();
        try {
           
			$auth = $request->only('email','password');
			
			$user = User::where('email', $request->email);
			$checkAdmin = $user->count(); 
			
			if($checkAdmin == 0) return redirect()->back()->with(['error' => 'Email / Password Salah']); 
			
			#proses authentication
			if (auth()->guard('admin')->attempt($auth)) {

				$userAuth = auth()->guard('admin')->user();
				$token = $userAuth->createToken('app-percik')->plainTextToken;

				// update token from token sanctum
				$user->update(['remember_token' => $token]);
				DB::commit();

				return redirect()->intended(route('dashboard'));
				
			}else{

				return redirect()->back()->with(['error' => 'Email / Password Salah']);
			}
		   
        } catch (\Exception $e) {
			DB::rollback();
            return $e->getMessage();
        }
		
	}
	
	public static function logout()
	{
		$auth = auth()->guard('admin');

		// delete APi Token
		$auth->user()->tokens()->delete(); 

        #logout
        $auth->logout();
        return redirect('/');
	}
	
}