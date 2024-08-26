<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BaseService;
use File;
use Illuminate\Support\Facades\Auth;
use Response;
use Storage;

// use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title'         => 'Home',
            'sub_title'     => 'Selamat Datang '.Auth::user()->name,
            'user_id'       => Auth::user()->id
        ];

        return view('dashboard/index', $data);
        // if(Auth::user()->getRoleNames()[0] == 'admin') {
        //     return view('dashboard/index', $data);
        // } else {
        //     return view('home', $data);
        // }
    }

    public function dashboard_presence(Request $request, $jenis)
    {
        date_default_timezone_set('Asia/Jakarta');
        $today      = date('Y-m-d');
        // MOVE DATA TO FOLDER
        $imageData  = $request->all()['sendData']['prs_image'];
        $imageData  = str_replace('data:image/png;base64,','', $imageData);
        $imageData  = str_replace(' ', '+', $imageData);

        $imageName  = Auth::user()->id.'_'.time().'.png';
        $imagePath  = 'storage/data-files/absen/'.$today.'/';

        $sendData   = [
            "data"      => $request->all()['sendData'],
            "data_url"  => $imagePath.$imageName,
            "ip"        => $request->ip(),
        ];

        // Storage::put($imagePath, base64_decode($imageData));

        $doSimpan   = BaseService::doAbsen($sendData);

        if($doSimpan['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'masuk' ? "Kamu Berhasil Absen Masuk" : "Kamu Berhasil Absen Keluar",
                    ],
                ],
            ];
            // PINDAHKAN FILE
            $tujuan_upload  = public_path($imagePath);

            if(!File::exists($tujuan_upload)) {
                File::makeDirectory($tujuan_upload, 0755, true);
            }

            File::put($tujuan_upload.$imageName, base64_decode($imageData));
            

        } else if($doSimpan['status'] == 'gagal') {
            $output     = [
                "success"   => true,
                "status"    => 500,
                "alert"     => [
                    "icon"  => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $jenis == 'masuk' ? "Kamu Gagal Absen Masuk" : "Kamu Gagal Absen Keluar"
                    ],
                ],
            ];
        } else if($doSimpan['status'] == 'duplikat') {
            $output     = [
                "success"   => false,
                "status"    => 409,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $doSimpan['errMsg'],
                    ],
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function dashboard_getPresenceToday()
    {
        $getData    = BaseService::doGetPresenceToday();
        
        if(count($getData) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Absen Berhasil Dimuat",
                "data"      => $getData[0],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Absen Gagal Dimuat",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}
