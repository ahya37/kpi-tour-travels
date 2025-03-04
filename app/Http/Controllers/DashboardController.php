<?php

namespace App\Http\Controllers;

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
            // KONTROL
            if(count($get_data['jam_kerja']) > 0) {
                $jam_senin      = $get_data['jam_kerja'][0];
                $jam_selasa     = $get_data['jam_kerja'][1];
                $jam_rabu       = $get_data['jam_kerja'][2];
                $jam_kamis      = $get_data['jam_kerja'][3];
                $jam_jumat      = $get_data['jam_kerja'][4];
                $jam_sabtu      = $get_data['jam_kerja'][5];
                $jam_minggu     = $get_data['jam_kerja'][6];
            } else {
                $jam_senin      = ['clock_in' => '08:00:00', 'clock_out' => '16:00:00'];
                $jam_selasa     = ['clock_in' => '08:00:00', 'clock_out' => '16:00:00'];
                $jam_rabu       = ['clock_in' => '08:00:00', 'clock_out' => '16:00:00'];
                $jam_kamis      = ['clock_in' => '08:00:00', 'clock_out' => '16:00:00'];
                $jam_jumat      = ['clock_in' => '08:00:00', 'clock_out' => '16:00:00'];
                $jam_sabtu      = ['clock_in' => '08:00:00', 'clock_out' => '13:30:00'];
                $jam_minggu     = ['clock_in' => '00:00:00', 'clock_out' => '00:00:00'];
            }

            $data_absen     = [];
            if(count($get_data['absensi']) > 0) {
                for($i = 0; $i < count($get_data['absensi']); $i++) {
                    $employee_name = $get_data['absensi'][$i]->prs_name;
                    $presence_date = $get_data['absensi'][$i]->prs_date;
                    // MENDAPATKAN JAM KERJA, JAM PULANG, LEBIH JAM, DAN KURANG JAM
                    $day_in_week    = date('N', strtotime($presence_date));
                    switch ($day_in_week) {
                        case '1' :
                            $jam_masuk  = !$jam_senin->clock_in ? $jam_senin['clock_in'] : $jam_senin->clock_in;
                            $jam_keluar = !$jam_senin->clock_out ? $jam_senin['clock_out'] : $jam_senin->clock_out;
                            $day_name   = 'Senin';
                        break;
                        case '2' :
                            $jam_masuk  = !$jam_selasa->clock_in ? $jam_selasa['clock_in'] : $jam_selasa->clock_in;
                            $jam_keluar = !$jam_selasa->clock_out ? $jam_selasa['clock_out'] : $jam_selasa->clock_out;
                            $day_name   = 'Selasa';
                        break;
                        case '3' :
                            $jam_masuk  = !$jam_rabu->clock_in ? $jam_rabu['clock_in'] : $jam_rabu->clock_in;
                            $jam_keluar = !$jam_rabu->clock_out ? $jam_rabu['clock_out'] : $jam_rabu->clock_out;
                            $day_name   = 'Rabu';
                        break;
                        case '4' :
                            $jam_masuk  = !$jam_kamis->clock_in ? $jam_kamis['clock_in'] : $jam_kamis->clock_in;
                            $jam_keluar = !$jam_kamis->clock_out ? $jam_kamis['clock_out'] : $jam_kamis->clock_out;
                            $day_name   = 'Kamis';
                        break;
                        case '5' :
                            $jam_masuk  = !$jam_jumat->clock_in ? $jam_jumat['clock_in'] : $jam_jumat->clock_in;
                            $jam_keluar = !$jam_jumat->clock_out ? $jam_jumat['clock_out'] : $jam_jumat->clock_out;
                            $day_name   = 'Jumat';
                        break;
                        case '6' :
                            $jam_masuk  = !$jam_sabtu->clock_in ? $jam_sabtu['clock_in'] : $jam_sabtu->clock_in;
                            $jam_keluar = !$jam_sabtu->clock_out ? $jam_sabtu['clock_out'] : $jam_sabtu->clock_out;
                            $day_name   = 'Sabtu';
                        break;
                        case '7' :
                            $jam_masuk  = !$jam_minggu->clock_in ? $jam_minggu['clock_in'] : $jam_minggu->clock_in;
                            $jam_keluar = !$jam_minggu->clock_out ? $jam_minggu['clock_out'] : $jam_minggu->clock_out;
                            $day_name   = 'Minggu';
                        break;
                    }

                    $presence_time_in   = !empty($get_data['absensi'][$i]->prs_in_time) ? date('H:i:s', strtotime($get_data['absensi'][$i]->prs_in_time)) : $jam_masuk;
                    $presence_time_out  = !empty($get_data['absensi'][$i]->prs_out_time) ? date('H:i:s', strtotime($get_data['absensi'][$i]->prs_out_time)) : $jam_keluar;
                    $hitung_kurang_jam  = strtotime($presence_time_in) - strtotime($jam_masuk);
                    $hitung_lebih_jam   = strtotime($presence_time_out) - strtotime($jam_keluar);
                    $kurang_jam         = $hitung_kurang_jam < 0 ? '00:00:00' : gmdate('H:i:s', $hitung_kurang_jam);
                    $lebih_jam          = $hitung_lebih_jam < 0 ? '00:00:00' : gmdate('H:i:s', $hitung_lebih_jam);

                    $data_absen[]   = [
                        'prs_emp_name'      => $employee_name,
                        'prs_date'          => $presence_date,
                        'prs_in'            => $presence_time_in,
                        'prs_out'           => $presence_time_out,
                        'prs_late_time'     => $kurang_jam,
                        'prs_over_time'     => $lebih_jam,
                        'prs_day_in_week'   => $day_in_week,
                        'prs_day_name'      => $day_name,
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
