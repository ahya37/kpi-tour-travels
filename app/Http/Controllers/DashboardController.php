<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\EmployeeService;
use Database\Seeders\EmployeeSeeder;
use Date;
use File;
use Illuminate\Support\Facades\Auth;
use Response;
use Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function Laravel\Prompts\alert;

// use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $title    = "ERP Percik Tours";
    public function index()
    {
        // CHECK APAKAH SUDAH ABSEN ATAU BELUM
        if(Auth::user()->getRoleNames()[0] != 'admin') 
        {
            $absen  = BaseService::doGetPresenceToday();
            if(count($absen) > 0) {
                $data = [
                    'title'         => $this->title . " | Dashboard",
                    'sub_title'     => 'Selamat Datang '.Auth::user()->name,
                    'user_id'       => Auth::user()->id,
                    'user_name'     => Auth::user()->name,
                    'user_role'     => Auth::user()->getRoleNames()[0],
                ];
        
                return view('dashboard/index', $data);
            } else {
                $data   = [
                    "title"         => $this->title." | Absen",
                    "user_id"       => Auth::user()->id,
                    "user_name"     => Auth::user()->name,
                ];
                return view('dashboard/absen', $data);
            }
        } else {
            $data = [
                'title'         => $this->title . " | Dashboard",
                'sub_title'     => 'Selamat Datang '.Auth::user()->name,
                'user_id'       => '%',
                'user_name'     => Auth::user()->name,
                'user_role'     => AUth::user()->getRoleNames()[0],
            ];
    
            return view('dashboard/index', $data);
        }
    }

    public function index_pulang()
    {
        if(Auth::user()->getRoleNames()[0] != 'admin') {
            $data   = [
                "title"     => $this->title . " | Absen",
                "user_id"   => Auth::user()->id,
                "user_name" => Auth::user()->name,
            ];

            return view('dashboard/absen', $data);
        }
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

    public function absensi_ambil_data_user(Request $request)
    {
        $send_data  = [
            "user_id"   => $request->all()['data']['user_id'],
            "month"     => $request->all()['data']['selected_month'],
            "year"      => $request->all()['data']['selected_year'],
        ];

        $get_data   = EmployeeService::get_absensi_ambil_data_user($send_data);        
        if(count($get_data) > 0) {
            $data_absen     = [];
            if(count($get_data['absensi']) > 0) {
                for($i = 0; $i < count($get_data['absensi']); $i++) {
                    $employee_name      = $get_data['absensi'][$i]->prs_name;
                    $presence_date      = $get_data['absensi'][$i]->prs_date;
                    $day_in_week        = date('N', strtotime($presence_date));
                    $jam_masuk          = EmployeeService::get_data_jam_kerja($presence_date, $day_in_week)[0]->clock_in;
                    $jam_keluar         = EmployeeService::get_data_jam_kerja($presence_date, $day_in_week)[0]->clock_out;

                    $presence_time_in   = !empty($get_data['absensi'][$i]->prs_in_time) ? date('H:i:s', strtotime($get_data['absensi'][$i]->prs_in_time)) : $jam_masuk;
                    $presence_time_out  = !empty($get_data['absensi'][$i]->prs_out_time) ? date('H:i:s', strtotime($get_data['absensi'][$i]->prs_out_time)) : ($presence_date == date('Y-m-d') ? '00:00:00' : $jam_keluar);
                    $hitung_kurang_jam  = strtotime($presence_time_in) - strtotime($jam_masuk);
                    $hitung_lebih_jam   = strtotime($presence_time_out) - strtotime($jam_keluar);
                    $hitung_jam_kerja   = strtotime($presence_time_out) - strtotime($presence_time_in);
                    $kurang_jam         = $hitung_kurang_jam < 0 ? '00:00:00' : gmdate('H:i:s', $hitung_kurang_jam);
                    $lebih_jam          = $hitung_lebih_jam < 0 ? '00:00:00' : gmdate('H:i:s', $hitung_lebih_jam);
                    $total_jam_kerja    = $hitung_jam_kerja < 0 ? '00:00:00' : gmdate('H:i:s', $hitung_jam_kerja);

                    $data_absen[]   = [
                        'prs_emp_name'      => $employee_name,
                        'prs_date'          => $presence_date,
                        'prs_in'            => $presence_time_in,
                        'prs_out'           => $presence_time_out,
                        'prs_late_time'     => $kurang_jam,
                        'prs_over_time'     => $lebih_jam,
                        'prs_total_time'    => $total_jam_kerja,
                        'prs_day_in_week'   => $day_in_week,
                        'prs_system_in'     => $jam_masuk,
                        'prs_system_out'    => $jam_keluar,
                    ];
                }
            }
            
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data Absensi",
                "data"      => $data_absen,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Mengambil Data Absensi",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}
