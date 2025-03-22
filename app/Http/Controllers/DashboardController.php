<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\EmployeeService;
use Database\Seeders\EmployeeSeeder;
use File;
use Illuminate\Support\Facades\Auth;
use Response;

date_default_timezone_set('Asia/Jakarta');

// use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    protected $title    = "ERP Percik Tours";
    public function index()
    {
        // GET USER SUB-DIVISION
        $user_id    = Auth::user()->id;
        // CHECK APAKAH SUDAH ABSEN ATAU BELUM
        if(Auth::user()->getRoleNames()[0] != 'admin') 
        {
            $get_user_sub_division  = BaseService::doGetCurrentSubDivision('', $user_id);
            $absen                  = BaseService::doGetPresenceToday();

            if($get_user_sub_division[0]->sub_division_name == 'manager finance' || count($absen) > 0) {
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

    // 22 MARET 2025
    // NOTE : ABSENSI V2
    public function absensi_user($jenis, Request $request)
    {
        $ip_address     = $request->ip();
        $user_id        = Auth::user()->id;
        $today          = date('Y-m-d H:i:s');
        $data_absen     = $request->all();

        $data_simpan    = [
            'ip_address'    => $ip_address,
            'user_id'       => $user_id,
            'today'         => $today,
            'data_absen'    => $data_absen,
        ];

        $do_simpan      = BaseService::do_simpan_absensi($jenis, $data_simpan);

        $output      = [
            'success'   => $do_simpan['is_success'],
            'status'    => $do_simpan['status_code'],
            'message'   => $do_simpan['message'],
            'data'      => $do_simpan['data'],
        ];

        return Response::json($output, $output['status']);
    }
}
