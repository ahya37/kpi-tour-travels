<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebsiteService;
use Dotenv\Repository\RepositoryInterface;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{
    protected $title    = "ERP Percik Tours | ";

    private function link_api()
    {
        return env('API_PERCIK_V2');
    }

    public function index()
    {
        $data   = [
            "title"     => $this->title . " Perciktours.com",
            "sub_title" => "Tarik Data untuk Perciktours.com",
        ];

        return view('perciktours.dashboard', $data);
    }

    public function perciktourscom_summary_data()
    {
        $get_data   = WebsiteService::percikTours_data_summary();

        $output     = [
            "success"   => true,
            "status"    => 200,
            "message"   => "Berhasil ambil data summary",
            "data"      => $get_data,
        ];
        
        return Response::json($output, $output['status']);
    }

    public function perciktourscom_get_data_summary()
    {
        $get_data   = Http::get($this->link_api() . "/api/perciktours/tarik_data_summary");
        
        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];
        
        return Response::json($output, $output['status']);
    }

    // 17 JANUARI 2025
    // NOTE : GET MASTER UMRAH
    public function perciktourscom_master_jadwal(Request $request)
    {
        $tahun  = $request->all()['tahun'];

        $get_data   = WebsiteService::get_master_jadwal($tahun);

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Jadwal Umrah Tahun " . $tahun,
                "data"      => $get_data
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Jadwal Umrah Tahun " . $tahun,
                "data"      => []
            ];
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : SYNC DATA MASTER UMRAH
    public function perciktourscom_master_jadwal_tarik(Request $request)
    {
        $tahun  = $request->all()['tahun'];

        $data_api   = Http::get($this->link_api() . "/api/umhaj/master/jadwal_umrah?tahun=" . $tahun);

        if($data_api->status() >= 200 || $data_api->status() < 300) {
            $send_data  = [
                "tahun"     => $tahun,
                "data"      => $data_api->json()['data'],
                "user_id"   => Auth::user()->id,
                "ip"        => $request->ip(),
            ];

            $get_data   = WebsiteService::sync_master_jadwal($send_data);

            if($get_data['status']  == 'berhasil') {
                $data       = WebsiteService::get_master_jadwal($tahun);
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Memperbarui Data Jadwal Umrah",
                    "data"      => $data,
                ];
            } else {
                $output     = [
                    "success"   => false,
                    "status"    => 500,
                    "message"   => "Berhasil Memperbarui Data Jadwal Umrah",
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                "success"   => false,
                "status"    => $data_api->status(),
                "message"   => $data_api->json()['message'],
                "data"      => $data_api->json()['data'],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 18 JANUARI 2025
    // NOTE : AMBIL MASTER JADWAL DETAIL
    public function perciktourscom_master_jadwal_detail(Request $request)
    {
        $tour_code  = $request->all()['tour_code'];

        $get_data   = WebsiteService::get_jadwal_detail($tour_code);

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Tour Code " . $tour_code,
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "message"   => "Tidak Ada Data Tour Code " . $tour_code,
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 21 JANUARI 2025
    // UPLOAD FLYER
    public function perciktourscom_upload_flyer(Request $request)
    {
        $file       = $request->file('detail_flyer');
        $tour_code  = $request->tour_code;

        $validator  = Validator::make($request->all(), [
            'detail_flyer'  => 'required|file|max:2048|mimes:jpg,png,jpeg,pdf',
        ]);

        if($validator->fails() === true) {
            $output     = [
                "success"       => false,
                "status"        => 400,
                "alert"         => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $validator->messages()->first(), 
                    ],
                ],
            ];
        } else {
            $file_info  = [
                "file_name"     => $file->getClientOriginalName(),
                "file_extension"=> $file->getClientOriginalExtension(),
                "file_real_path"=> $file->getRealPath(),
                "file_size"     => $file->getSize(),
                "custom_name"   => "Flyer_" . str_replace('/', '_', $tour_code) . "." . $file->getClientOriginalExtension(),
                "user_id"       => Auth::user()->id,
                "storage_path"  => "storage/data-files/flyer/" . str_replace('/', '_', $tour_code),
                "ip"            => $request->ip(),
                "tour_code"     => $tour_code,
            ];

            $do_upload  = WebsiteService::do_upload_flyer($file_info);

            if($do_upload['status'] == 'berhasil') {
                $tujuan_upload  = public_path($file_info['storage_path']);

                if(File::exists($tujuan_upload) && File::isDirectory($tujuan_upload)) {
                    $files  = File::files($tujuan_upload);

                    foreach($files as $currFile) {
                        File::delete($currFile);
                    }
                }

                if(file_exists($tujuan_upload)) {
                    File::delete($tujuan_upload);
                }

                $file->move($tujuan_upload, $file_info['custom_name']);

                $output     = [
                    "success"       => true,
                    "status"        => 200,
                    "alert"         => [
                        "icon"      => "success",
                        "message"   => [
                            "title"     => "Berhasil",
                            "text"      => "Berhasil Mengunggah File Flyer", 
                        ],
                    ],
                ];
            } else {
                $output     = [
                    "success"       => false,
                    "status"        => 500,
                    "alert"         => [
                        "icon"      => "error",
                        "message"   => [
                            "title"     => "Terjadi Kesalahan",
                            "text"      => "Gagal Upload Flyer", 
                        ],
                    ],
                ];
            }
        }

        return Response::json($output, $output['status']);
    }

    // 4 FEBRUARI 2025
    // NOTE : GET LIST ARTICLE
    public function perciktourscom_article_list(Request $request)
    {
        $send_data   = [
            'username'      => Auth::user()->id,
            "ip_address"    => $request->ip(), 
        ];

        $get_data   = WebsiteService::get_article_list($send_data);

        if($get_data['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Artikel Umrah",
                "data"      => $get_data['data'],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "message"   => "Internal Server Error",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 05 FEBRUARI 2025
    // NOTE : AMBIL TOUR CODE UNTUK KEBUTUHAN ARTIKEL
    public function perciktourscom_article_tour_detail(Request $request)
    {
        $send_data  = [
            "ip_address"=> $request->ip(),
            "tour_code" => $request->all()['tour_code'],
        ];

        $get_data   = WebsiteService::get_article_tour_detail($send_data);

        if($get_data['status'] == 'berhasil') {
            // var_dump(count($get_d))
            if(count($get_data['data']) > 0) {
                $output = [
                    "successs"   => true,
                    "status"    => 201,
                    "message"   => "Berhasil Mengambil Data Tour Code " . $send_data['tour_code'],
                    "data"      => $get_data['data'],
                ];
            } else {
                $output     = [
                    "success"   => true,
                    "status"    => 204,
                    "message"   => "Gagal Mengambil Data Tour Code " . $send_data['tour_code'],
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "message"   => "Internal Server Error",
                "data"      => []
            ];
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN ARTIKEL TOUR CODE
    public function perciktouscom_article_save($type, Request $request)
    {
        $validator_rules    = [
            'act_prog_title'        => 'required|string',
            'act_prog_destination'  => 'required|string',
            'act_prog_duration'     => 'required',
            'act_prog_airlines'     => 'required',
            'act_prog_hotel_mekkah' => 'required|string',
            'act_prog_hotel_madinah'=> 'required|string',
            'act_prog_cost_quad'    => 'required|numeric|min:1',
            'act_prog_cost_triple'  => 'required|numeric|min:1',
            'act_prog_cost_double'  => 'required|numeric|min:1',
        ];

        $validator  = Validator::make($request->all(), $validator_rules, []);

        if($validator->fails()) {
            $output     = [
                "success"   => false,
                "status"    => 400,
                "message"   => $validator->errors(),
                "data"      => [],
            ];
        } else {
            $article_data   = [
                'jdw_article_uuid'  => $request->act_prog_uuid,
                'jdw_article_title' => $request->act_prog_title,
                'jdw_tour_code'     => $request->act_prog_tour_code,
                'jdw_airline'       => $request->act_prog_airlines,
                'jdw_cost_double'   => $request->act_prog_cost_double,
                'jdw_cost_triple'   => $request->act_prog_cost_triple,
                'jdw_cost_quad'     => $request->act_prog_cost_quad,
                'jdw_destination'   => $request->act_prog_destination,
                'jdw_duration'      => $request->act_prog_duration,
                'jdw_hotel'         => $request->act_prog_hotel_mekkah . " | " . $request->act_prog_hotel_madinah,
            ];

            $send_data  = [
                "user_id"   => Auth::user()->id,
                "ip_address"=> $request->ip(),
                "data"      => $article_data,
                "type"      => $type
            ];

            $do_simpan  = WebsiteService::do_save_article_umrah($send_data);

            if($do_simpan['status']  == 'berhasil') {
                $output     = [
                    'status'    => 200,
                    'success'   => true,
                    'message'   => $do_simpan['message'],
                    'data'      => [],
                ];
            } else {
                $output     = [
                    'status'    => 500,
                    'success'   => false,
                    'message'   => $do_simpan['message'],
                    'data'      => [],
                ];
            }
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : 07 FEBRUARI 2025
    // NOTE : AMBIL ARTIKEL DETAIL
    public function perciktourscom_article_detail($uuid, Request $request)
    {
        $send_data  = [
            'ip_address'    => $request->ip(),
            'article_uuid'  => $uuid,
        ];

        $get_data   = WebsiteService::get_article_detail($send_data);

        if($get_data['status'] == 'berhasil') {
            $output     = [
                "success"   => true,
                "status"    => 201,
                "message"   => "Berhasil Mengambil Data Artikel",
                "data"      => $get_data['data'],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 500,
                "message"   => $get_data['message'],
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }
}