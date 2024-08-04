<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Auth;
use File;
use Hash;
use Illuminate\Support\Facades\Response;
use Storage;
use Validator;

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
        } else {
            $status     = 404;
        }

        $output     = array(
            "draw"  => 1,
            "data"  => $data,
        );

        return Response::json($output, $status);
    }

    // USER PROFILE
    public function userProfiles()
    {
        $data   = [
            "title"     => "User Account Control",
            "sub_title" => "Lihat Profile User"
        ];

        return view('users/userProfile/index', $data);
    }

    public function ChangePasswordUser(Request $request) {
        $data    = $request->all()['sendData'];
        $data_update    = [
            'password'  => Hash::make($data['userNewPassword']),
        ];

        $doChange       = User::whereId($data['userId'])->update($data_update);
        if($doChange) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengubah Password"
            );
            LogHelper::create('edit', 'Berhasil Mengubah Password User : '.Auth::user()->name, $request->ip());
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "message"   => "Bad Request"
            );
            LogHelper::create('error_system', 'Gagal Mengubah Password User : '.Auth::user()->name, $request->ip());
        }

        return Response::json($output, $output['status']);
    }

    public function CheckPasswordCurrentUser(Request $request)
    {
        $data   = $request->all()['sendData'];
        // DO CHECK PASSWORD    
        $user   = Auth::user($data['currentUserId']);
        
        if(Hash::check($data['currentPassword'], $user->password)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Ok"
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "message"   => "Bad Request"
            );
        }

        return Response::json($output, $output['status']);
    }

    public function getUserData()
    {
        $data   = [
            "user_id"   => Auth::user()->id,
            "user_roles"=> Auth::user()->getRoleNames()[0],
        ];

        $getData    = UserService::doGetUserData($data);

        $output     = [
            "status"    => 200,
            "message"   => "Berhasil Mengambil Data",
            "data"      => $getData,
        ];

        return Response::json($output, $output['status']);
    }

    public function updateProfilePicture(Request $request)
    {
        $file   = $request->file('photo');

        // VALIDATE
        // $request->validate(
        //     [
        //         'photo' => 'required|file|max:5000|mimes:jpg,png,jpeg',
        //     ],
        //     [
        //         'photo.max' => "Max. Size Foto adalah 5 MB (Mega Byte)",
        //         'photo.mimes'   => 'File yang diterima hanya yang berformat .jpg, .png, dan .jpeg',
        //     ]
        // );
        $validator  = Validator::make($request->all(), [
            'photo' => 'required|file|max:2500|mimes:jpg,png,jpeg',
        ]);

        if($validator->fails() === true) {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $validator->messages()->first(),
                    ],
                ],
            );
        } else {
            $file_info  = [
                "file_name"     => $file->getClientOriginalName(),
                "file_extension"=> $file->getClientOriginalExtension(),
                "file_real_path"=> $file->getRealPath(),
                "file_size"     => $file->getSize(),
                "custom_name"   => date('YmdHis')."-".Auth::user()->id.".".$file->getClientOriginalExtension(),
                "user_id"       => Auth::user()->id,
                "storage_path"  => "storage/data-files/profile_pictures/".Auth::user()->id,
                "ip"            => $request->ip()
            ];
    
            $doUpload   = UserService::doUploadProfilePicture($file_info);
            if($doUpload['status'] == 'berhasil') {
                $tujuan_upload  = public_path('/storage/data-files/profile_pictures/'.$file_info['user_id']);
    
                if(File::exists($tujuan_upload) && File::isDirectory($tujuan_upload)) {
                    $files  = File::files($tujuan_upload);
    
                    foreach($files as $currFile) {
                        File::delete($currFile);
                    }
                }
                
                // if(!is_dir($tujuan_upload)) {
                //     mkdir($tujuan_upload, 0755, true);
                // }
    
                if(file_exists($tujuan_upload)) {
                    File::delete($tujuan_upload);
                }
                
                $file->move($tujuan_upload, $file_info['custom_name']);
    
                $output     = array(
                    'success'   => true,
                    'status'    => 200,
                    'alert'     => [
                        'icon'      => 'success',
                        'message'   => [
                            'title'     => 'Berhasil',
                            'text'      => 'Berhasil Merubah Foto Profil Akun',
                        ],
                    ],
                );
            } else {
                $output     = array(
                    'success'   => false,
                    'status'    => 500,
                    'alert'     => [
                        'icon'      => 'error',
                        'message'   => [
                            'title'     => 'Terjadi Kesalahan',
                            'text'      => 'Gagal Merubah Foto Profil Akun',
                        ],
                    ],
                );
            }
        }

        return Response::json($output, $output['status']);
    }

    public function getDataUser()
    {
        $id     = Auth::user()->id;

        $getData    = UserService::doGetDataUser($id);

        return Response::json($getData, 200);
    }
}
