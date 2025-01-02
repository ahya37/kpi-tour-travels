<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SysUmhajService;
use Http;
use Illuminate\Support\Facades\Auth;
use Response;
use Symfony\Component\VarDumper\VarDumper;

use function PHPSTORM_META\map;

class SysUmhajController extends Controller
{
    //
    protected $title    = "ERP Percik Tours | ";
    protected $mkt_view = "/marketings/umhaj";

    private function link_api()
    {
        return env('API_PERCIK_V2');
    }

    public function index_umhaj()
    {
        $data   = [
            "title"     => $this->title."Umhaj",
            "sub_title" => "Umhaj Dashboard"
        ];

        return view($this->mkt_view."/dashboard/index", $data);
    }

    // MASTER
    public function umhaj_umrah_get_list_program()
    {
        $get_data   = SysUmhajService::get_list_program_umrah();
        
        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Memuat Data",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Memuat Data",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_umrah_get_data(Request $request)
    {
        if(Auth::user()->getRoleNames()[0] == 'admin' || Auth::user()->getRoleNames()[0] == 'marketing') {
            $send_data  = [
                "type_umrah"    => $request->all()['data']['jenis'] == "semua" ? "%" : $request->all()['data']['jenis'],
                "tahun_cari"    => $request->all()['data']['tahun_cari'],
                "bulan_cari"    => $request->all()['data']['bulan_cari']
            ];
    
            $get_data   = SysUmhajService::get_data_umhaj_umrah($send_data);

            if(count($get_data) > 0) {
                // MERGING DATA
                $res    = [];
                foreach($get_data as $item) :
                    $bulan_ke   = $item->BULAN_DAFTAR;
                    $total_data = $item->TOTAL_DATA;

                    if(isset($res[$bulan_ke])) {
                        $res[$bulan_ke]['total_data'] += $total_data;
                    } else {
                        $res[$bulan_ke] = [
                            "bulan_ke"      => $bulan_ke,
                            "total_data"    => $total_data
                        ];
                    }
                endforeach;
                
                for($i = 0; $i < 12; $i++)
                {
                    if(!empty($res[$i + 1]['bulan_ke'])) {
                        $data[]     = [
                            "month"     => $res[$i + 1]['bulan_ke'] < 10 ? "0".$res[$i + 1]['bulan_ke'] : $res[$i + 1]['bulan_ke'],
                            "total_data"=> $res[$i + 1]['total_data'],
                        ];
                    } else {
                        $data[]     = [
                            "month"     => $i + 1 < 10 ? "0".$i + 1 : $i + 1,
                            "total_data"=> 0,
                        ];
                    }
                }
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Ambil Data",
                    "data"      => $data,
                ];
            } else {
                $output     = [
                    "success"   => false,
                    "status"    => 404,
                    "message"   => "Gagal Mengambil Data",
                    "data"      => [],
                ];
            }

            return Response::json($output, $output['status']);
        } else {
            return redirect('/');
        }
    }

    public function umhaj_member_get_data(Request $request)
    {
        $send_data  = [
            "cs"        => $request->all()['data']['cs_name'] == 'semua' ? '%' : $request->all()['data']['cs_name'],
            "tahun_cari"=> $request->all()['data']['tahun_cari'],
            "bulan_cari"=> $request->all()['data']['bulan_cari'],
        ];

        $data       = [];

        $get_data   = SysUmhajService::get_data_umhaj_member($send_data);

        if(count($get_data) > 0) {
            for($i = 0; $i < 12; $i++) {
                $bulan_ke   = $i + 1;
                if(!empty($get_data[$i]->month)) {
                    $data[]     = [
                        "month"     => $bulan_ke < 10 ? "0".$bulan_ke : $bulan_ke,
                        "total_data"=> $get_data[$i]->total_data_member
                    ];
                } else {
                    $data[]     = [
                        "month"     => $bulan_ke < 10 ? "0".$bulan_ke : $bulan_ke,
                        "total_data"=> 0,
                    ];
                }
            }

            $output     = [
                "status"        => 200,
                "success"       => true,
                "message"       => "Berhasil Mengambil Data Member",
                "data"          => $data,
            ];
        } else {
            $output     = [
                "status"        => 404,
                "success"       => false,
                "message"       => "Gagal Memuat Data",
                "data"          => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_cs_get_data()
    {
        $get_data   = SysUmhajService::get_data_umhaj_cs();

        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Memuat Data CS",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Memuat Data CS",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function umhaj_member_get_data_detail(Request $request)
    {
        $send_data  = [
            "cs_name"       => $request->all()['data']['cs_name'] == 'semua' ? '%' : $request->all()['data']['cs_name'],
            "tahun_cari"    => $request->all()['data']['tahun_cari'],
            "bulan_cari"    => $request->all()['data']['bulan_cari']
        ];

        $get_data   = SysUmhajService::get_data_umhaj_member_detail($send_data);

        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Bulan ".date('F', strtotime($send_data['bulan_cari'])),
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Mengambil Data Bulan ".date('F', strtotime($send_data['bulan_cari'])),
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 03 OKTOBER 2024
    // NOTE : AMBIL DATA UMRAH DETAIL
    public function umhaj_umrah_get_data_detail(Request $req)
    {
        $send_data  = [
            "type_umrah"    => $req->all()['data']['jenis'] == 'semua' ? '%' : $req->all()['data']['jenis'],
            "bulan_ke"      => $req->all()['data']['bulan_cari'],
            "tahun_ke"      => $req->all()['data']['tahun_cari']
        ]; 

        $get_data   = SysUmhajService::get_data_umhaj_umrah_detail($send_data);

        if(count($get_data) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Umrah Bulan ".$send_data['bulan_ke'],
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "sucess"    => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data Umrah Bulan ".$send_data['bulan_ke'],
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 04 OKTOBER 2024
    // NOTE : AMBIL LIST DATA UMRAH
    public function umhaj_umrah_list($tahun)
    {
        $get_data   = SysUmhajService::get_data_umhaj_umrah_list($tahun);
        
        if(count($get_data) > 0) {
            $output  = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Berhasil Dimuat",
                "data"      => [
                    "total_data"    => count($get_data),
                    "data"          => $get_data,
                ],
            ];
        } else {
            $output  = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Gagal Dimuat",
                "data"      => [
                    "total_data"    => 0,
                    "data"          => []
                ],
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 05 OKTOBER 2024
    // NOTE : AMBIL DATA TOUR CODE DETAIL
    public function umhaj_umrah_detail(Request $request)
    {
        $tourCode   = $request->all()['tourCode'];
        $get_data   = SysUmhajService::get_data_umhaj_umrah_detail_byTourCode($tourCode);

        if(count($get_data['header']) > 0) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Mengambil Data Umrah Tour Code : ".$tourCode,
                "data"      => [
                    "header"    => $get_data['header'][0],
                    "detail"    => $get_data['detail'],
                ],
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Mengambil Data Umrah Tour Code : ".$tourCode,
                "data"      => $get_data,
            ];
        }
        
        return Response::json($output, $output['status']);
    }

    // 23 DESEMBER 2024
    // NOTE : AMBIL DATA UMHAJ DINAMIS BY TABLE
    public function umhaj_member_get_data_v2(Request $request)
    {
        $member     = [];

        $limit      = $request->all()['length'];
        $offset     = $request->all()['start'];
        $search     = !empty($request->all()['search']['value']) ? $request->all()['search']['value'] : "%";

        // GET DATA FROM API
        $get_data   = Http::withHeaders([
            'Content-Type'  => 'application/json',
        ])->post($this->link_api()."/api/umhaj/member/list_v2", [
            "limit"     => $limit,
            "offset"    => $offset,
            "search"    => $search,
        ]);
        if($get_data->status() >= 200 && $get_data->status() < 300) {
            
            $data_member    = $get_data->json()['data']['data_member'];
            $total_data_member  = $get_data->json()['data']['total_data_member'][0]['TOTAL_DATA'];
            
            if(count($data_member) > 0) {
                $nomor_urut     = $offset + 1;
                for($i = 0; $i < count($data_member); $i++) {
                    $no             = $nomor_urut++;
                    $nama_jemaah    = $data_member[$i]['NAMA_DEPAN']." ".$data_member[$i]['NAMA_TENGAH']." ".$data_member[$i]['NAMA_BELAKANG'];
                    $kota_jemaah    = $data_member[$i]['KOTA'];
                    $alamat_jemaah  = $data_member[$i]['ALAMAT'];
                    $id_jemaah      = $data_member[$i]['ID_JEMAAH'];
                    $button_edit    = "<button class='btn btn-sm btn-primary' title='Ubah Data' value='$id_jemaah' onclick='showModal(`modal_form_member`, this.value)' disabled><i class='fa fa-pencil'></i></button>";
                    $button_delete  = "<button class='btn btn-sm btn-danger' title='Hapus Data' value='$id_jemaah' onclick='showModal(`modal_delete_member`, this.value)' disabled><i class='fa fa-trash'></i></button>";
                    $member[]   = [
                        "<label class='font-weight-normal no-margins'>$no</label>",
                        "<label class='font-weight-normal no-margins'>".$nama_jemaah."</label>",
                        "<label class='font-weight-normal no-margins'>" . $kota_jemaah . "</label>",
                        "<label class='font-weight-normal no-margins'>" . $alamat_jemaah . "</label>",
                        $button_edit."&nbsp;".$button_delete,
                    ];
                }
            }
        } else {
            $member     = [];
            $total_data_member  = 0;
        }

        $output     = [
            "draw"              => $request->all()['draw'],
            "data"              => $member,
            "recordsTotal"      => $total_data_member,
            "recordsFiltered"   => $total_data_member
        ];

        return Response::json($output, 200);
        // dd($request->all());
    }

    // 24 DESEMBER 2024
    // NOTE : GET MASTER SUMBER
    public function umhaj_master_data_sumber()
    {
        $get_data   = Http::get($this->link_api().'/api/umhaj/master/sumber');
        
        return Response::json($get_data->json(), $get_data->status());
    }

    // 27 DESEMBER 2024
    // NOTE : AMBIL WILAYAH - PROVINSI
    public function erp_master_wilayah_provinsi()
    {
        $get_data   = SysUmhajService::get_data_wilayah_provinsi();

        if(count($get_data) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil Data Wilayah (Provinsi)",
                "data"      => $get_data,
            ];
        } else {
            $output     = [
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak Ada Data Wilayah (Provinsi)",
                "data"      => [],   
            ];
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL WILAYAH - KOTA BY PROVINSI ID
    public function erp_master_wilayah_kota(Request $request)
    {
        $provinces_id   = $request->all()['province_id'];

        if(!empty($provinces_id)) {
            $get_data   = SysUmhajService::get_data_wilayah_kota($provinces_id);
            
            if(count($get_data) > 0) {
                $output     = [
                    "status"    => 200,
                    "success"   => false,
                    "message"   => "Berhasil Mengambil Data Wilayah Kota",
                    "data"      => $get_data,
                ];
            } else {
                $output     = [
                    "status"    => 404,
                    "success"   => false,
                    "message"   => "Data Wilayah Kota Tidak Ditemukan",
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                'status'    => 404,
                'success'   => false,
                'message'   => '`pronvices_id` Tidak Boleh Kosong',
                'data'      => []
            ];
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL WILAYAH - KECAMATAN BY KOTA ID
    public function erp_master_wilayah_kecamatan(Request $request)
    {
        $city_id    = $request->all()['city_id'];

        if(!empty($city_id)) {
            $get_data   = SysUmhajService::get_data_wilayah_kecamatan($city_id);

            if(count($get_data) > 0) {
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Mengambil Data Wilayah Kecamatan",
                    "data"      => $get_data,
                ];
            } else {
                $output      = [
                    "success"   => false,
                    "status"    => 404,
                    "message"   => "Data Wilayah Kelurahan Tidak Ditemukan",
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "`city_id` Tidak Boleh Kosong",
                "data"      => [],
            ];
        }
        
        return Response::json($output, $output['status']);
    }
    
    // NOTE : AMBIL WILAYAH - KELURAHAN BY KECAMATAN ID
    public function erp_master_wilayah_kelurahan(Request $request)
    {
        $district_id    = $request->all()['district_id'];

        if(!empty($district_id)) {
            $get_data   = SysUmhajService::get_data_wilayah_kelurahan($district_id);

            if(count($get_data) > 0) {
                $output     = [
                    "success"   => true,
                    "status"    => 200,
                    "message"   => "Berhasil Mengambil Data Wilayah Kelurahan",
                    "data"      => $get_data,
                ];
            } else {
                $output      = [
                    "success"   => false,
                    "status"    => 404,
                    "message"   => "Data Wilayah Kelurahan Tidak Ditemukan",
                    "data"      => [],
                ];
            }
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "`district_id` Tidak Boleh Kosong",
                "data"      => [],
            ];
        }
        
        return Response::json($output, $output['status']);
    }

    // NOTE : AMBIL DATA MEMBER / JEMAAH BY ID
    public function umhaj_member_get_data_detail_v2(Request $request)
    {
        $jemaahID   = $request->all()['jemaahID'];

        $get_data   = Http::get($this->link_api() . "/api/umhaj/member/detail?jemaah=".$jemaahID);

        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // 31 DESEMBER 2024
    // NOTE : SIMPAN DATA JEMAAH KE UMHAJ DAN ERP
    public function umhaj_member_simpan_data(Request $request, $jenis)
    {
        var_dump($jenis);die();
    }

    // 2 JANUARI 2025
    // NOTE : GET CHART DATA MEMBER
    public function umhaj_chart_member_data(Request $request)
    {
        $cs_name    = $request->all()['cs_name'];
        $tahun_cari = $request->all()['tahun_cari'];
        $bulan_cari = $request->all()['bulan_cari'];

        $get_data   = Http::withHeaders([
            'Content-Type'  => 'application/json',
        ])->post($this->link_api()."/api/umhaj/member/chart_data", [
            "cs_name"   => $cs_name,
            "tahun_cari"=> $tahun_cari,
            "bulan_cari"=> $bulan_cari,
        ]);

        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);
    }

    public function umhaj_agent_get_data()
    {
        $get_data   = Http::get($this->link_api()."/api/umhaj/agent/list");

        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);
    }

    public function umhaj_master_data_program_umrah()
    {
        $get_data   = Http::get($this->link_api() . "/api/umhaj/master/program");

        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);
    }

    // NOTE : GET DATA CS FROM API
    public function umhaj_master_data_cs()
    {
        $get_data   = Http::get($this->link_api() . "/api/umhaj/master/user/cs");
        
        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);    
    }

    // NOTE : GET DATA UMRAH CHART
    public function umhaj_chart_umrah_data(Request $request)
    {
        $jenis      = $request->all()['jenis'];
        $tahun_cari = $request->all()['tahun_cari'];
        $bulan_cari = $request->all()['bulan_cari'];

        $get_data   = Http::withHeaders([
            'Content-Type'  => 'application/json'
        ])->post($this->link_api() . "/api/umhaj/umrah/get_data_umrah", [
            "jenis"     => $jenis,
            "tahun_cari"=> $tahun_cari,
            "bulan_cari"=> $bulan_cari,
        ]);
         
        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);   
    }

    // GET DATA JADWAL UMRAH BY TAHUN
    public function umhaj_data_jadwal_umrah(Request $request)
    {
        $tahun  = $request->all()['tahun'];

        $get_data   = Http::get($this->link_api() . "/api/umhaj/master/jadwal_umrah?tahun=" . $tahun);

        $output     = [
            "success"   => $get_data->json()['success'],
            "status"    => $get_data->status(),
            "message"   => $get_data->json()['message'],
            "data"      => $get_data->json()['data'],
        ];

        return Response::json($output, $output['status']);   
    }
}
