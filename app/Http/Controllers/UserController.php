<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Auth;
use Illuminate\Support\Facades\Response;

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
            UserService::store($requestUser);
            
            DB::commit();

            return redirect()->route('users.index')->with(['success' => 'Sukses menyimpan users!']);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // ADDITIONAL
    public function userLog()
    {
        $data   = [
            'title'     => 'Log Activity User',
            'sub_title' => 'Aktivitas User'
        ];
        
        return view('users/userLog/index', $data);
    }
    
    public function dataTableUserLog()
    {
        $current_user   = Auth::user()->id;

        $getData    = UserService::DoGetDataTableUserLog($current_user);
        $data       = [];

        if(!empty($getData)) {
            $i  = 1;
            foreach($getData as $tarik) {
                $data[]     = array(
                    $i++,
                    $tarik->log_desc,
                    "<span class='label label-sm ".$tarik->type_trans_color."'>".$tarik->type_trans."</span>",
                    $tarik->log_date_time,
                );
            }
            $status     = 200;
        }

        $output     = array(
            "draw"  => 1,
            "data"  => $data,
        );

        return Response::json($output, 200);
    }
}
