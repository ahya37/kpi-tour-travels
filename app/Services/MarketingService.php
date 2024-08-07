<?php 

namespace App\Services;

use App\Helpers\LogHelper;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\AlumniProspekMaterial;
use App\Models\DetailAlumniProspekMaterial;
use App\Models\DetailMarketingTarget;
use App\Models\MarketingTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Log;
use Request;

date_default_timezone_set('Asia/Jakarta');

class MarketingService 
{
	public static function store($requestMarketingtarget)
	{
		DB::beginTransaction();
        try {
            
            MarketingTarget::create([
                'id' => Str::random(30),
                'year' => $requestMarketingtarget['year'],
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);
            
            DB::commit(); 

        } catch (\Exception $e) {
			DB::rollback();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ]);
        }
		
	}

    public static function listTarget($request)
    {
        // sleep(1);
        $orderBy = 'year';
        switch ($request->input('order.0.column')) {
            case '1':
                $orderBy = 'year';
                break;
        }

       $marketingTargets = MarketingTarget::getMarketingtargets();

        if($request->input('search.value')!=null){
           $marketingTargets =$marketingTargets->where(function($q)use($request){
                $q->whereRaw('LOWER(year) like ? ',['%'.strtolower($request->input('search.value')).'%']);
            });
        }

        // if($request->input('month') != '' AND $request->input('year') != ''){
        //                    $marketingTargets->whereMonth('start_date', $request->month);
        //                    $marketingTargets->whereYear('end_date', $request->year);
        //                 }

        $recordsFiltered =$marketingTargets->get()->count();
        if($request->input('length')!=-1)$marketingTargets =$marketingTargets->skip($request->input('start'))->take($request->input('length'));
       $marketingTargets =$marketingTargets->orderBy($orderBy,$request->input('order.0.dir'))->get();

        $recordsTotal =$marketingTargets->count();

        return response()->json([
                'draw'=>$request->input('draw'),
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'data'=>$marketingTargets
            ]);
    }

    public static function detailMarketingTarget($marketingTargetId)
    {
        $detailMarketingTargets = DetailMarketingTarget::where('marketing_target_id', $marketingTargetId)
                                  ->orderBy('month_number','asc')
                                  ->get();
        return $detailMarketingTargets;
    }

    public static function detailMarketingTargetStore($requestDetailMarketingtarget)
	{
		DB::beginTransaction();
        try {

            // cek jika program sudah ada di bulan dan tahun yang sama
            $countDetailMarketingTargets = DetailMarketingTarget::getDetailMarketingTargetWhereMarketingId($requestDetailMarketingtarget['marketing_target_id'])
                                           ->where('program_id',$requestDetailMarketingtarget['program_id'])
                                           ->where('month_number',$requestDetailMarketingtarget['month_number'])
                                           ->count();

            if ($countDetailMarketingTargets != 0) {
                
                $results = [
                    'success'  => 0,
                    'message' => "Program di bulan tersebut sudah tersedia !"
                ];

                return $results;

            }else{

                DetailMarketingTarget::create([
                    'id' => Str::random(30),
                    'marketing_target_id' => $requestDetailMarketingtarget['marketing_target_id'],
                    'program_id' => $requestDetailMarketingtarget['program_id'],
                    'month_number' => $requestDetailMarketingtarget['month_number'],
                    'month_name' => $requestDetailMarketingtarget['month_name'],
                    'target' => $requestDetailMarketingtarget['target'],
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                DB::commit(); 
                $results = [
                    'success'  => 1,
                    'message' => 'Program dengan target sukses tersimpan!'
                ];

                return $results;
            }

            

        } catch (\Exception $e) {
			DB::rollback();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ]);
        }
		
	}

    public static function detailListTarget($request, $detailMarketingTargetId)
    {
        // sleep(1);
        $orderBy = 'month_name';
        switch ($request->input('order.0.column')) {
            case '1':
                $orderBy = 'month_name';
                break;
        }

        $detailTargetMarketing = DetailMarketingTarget::getDetailMarketingTargetWhereMarketingId($detailMarketingTargetId);

        if($request->input('search.value')!=null){
            $detailTargetMarketing = $detailTargetMarketing->where(function($q)use($request){
                $q->whereRaw('LOWER(month_name) like ? ',['%'.strtolower($request->input('search.value')).'%']);
            });
        }

        $recordsFiltered = $detailTargetMarketing->get()->count();
        if($request->input('length')!=-1) $detailTargetMarketing = $detailTargetMarketing->skip($request->input('start'))->take($request->input('length'));
        $detailTargetMarketing = $detailTargetMarketing->orderBy($orderBy,$request->input('order.0.dir'))->get();

        $recordsTotal = $detailTargetMarketing->count();


        return response()->json([
                'draw'=>$request->input('draw'),
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'data'=> $detailTargetMarketing
            ]);
    }

    public static function prospectMaterialStore($formData)
    {
       
        $response = Http::post(env('API_PERCIK').'/member/umrah/alumni',$formData);
        
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    public static function getTargetBulanFromUmhajByTahun($formData)
    {
       
        $response =Http::withHeaders([
            'x-api-key' => env('API_PERCIK_KEY')
        ])->post(env('API_PERCIK').'/umrah/getTargetUmrahByTahun',$formData);
        
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    public static function getRelisasiUmrah($formData)
    {
       
        $response =Http::withHeaders([
                    'x-api-key' => env('API_PERCIK_KEY')
                ])->withOptions([
                    'verify' => false,
                    'timeout' => 60, // set timeout ke 60 detik
                ])->post(env('API_PERCIK').'/umrah/realisasi',$formData);

        
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    public static function updateApiIsBahanProspek($id_member)
    {
        $formData['id_member'] = $id_member;
        $response = Http::post(env('API_PERCIK').'/member/bahanprospek/update',$formData);
        
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    public static function prospectMaterialList()
    {
        $prospectMaterials = AlumniProspekMaterial::getProspectMaterial();
        return $prospectMaterials;
    }

    public static function alumniProspectMaterialByAccountCS($auth)
    {
       $data = AlumniProspekMaterial::alumniProspectMaterialByAccountCS($auth);
       return $data;

    }

    public static function detailAlumniProspectMaterialByAccountCS($id)
    {
       $data = DetailAlumniProspekMaterial::where('alumni_prospect_material_id', $id)->get();
       return $data;

    }

    public static function manageAlumniProspectMaterialStore($request)
    {
        $update = DetailAlumniProspekMaterial::where('id', $request['idDetail'])->first();
        $update->update([
            'is_respone' => $request['response'],
            'reason_id' => $request['reason'],
            'tourcode' => $request['tourcode'],
            'tourcode_haji' => $request['tourcodeHaji'],
            'tourcode_tourmuslim' => $request['tourcodeMuslim'],
            'notes' => $request['notes'],
            'remember' => $request['remember'],
            'created_by' => $request['user'],
            'updated_by' => '',
        ]);

        $data = [
            'message' => 'Berhasil kelola jamaah'
        ];

        return $data;
    }

    public static function listAlumniProspectMaterial($request, $alumniprospectmaterialId)
    {
    
        $marketingTargets =  DetailAlumniProspekMaterial::getDetailAlumniProspekMaterialByAlumniProspekMaterial($alumniprospectmaterialId)->get();

        if(!empty($marketingTargets)) {
            
            for($i = 0; $i < count($marketingTargets); $i++) {
                $is_respone = '';

                if ($marketingTargets[$i]->is_respone == 'Y') {
                    $is_respone = 'Ya';
                }elseif($marketingTargets[$i]->is_respone == 'N'){
                    $is_respone = 'Tidak';
                }
                
                $data[]     = array(
                    $i + 1,
                    $marketingTargets[$i]->name,
                    $marketingTargets[$i]->telp,
                    $marketingTargets[$i]->address,
                    $is_respone,
                    $marketingTargets[$i]->reason,
                    $marketingTargets[$i]->notes,
                    "<button type='button' data-id='".$marketingTargets[$i]->id."' class='btn btn-sm btn-primary' data-toggle='modal' data-target='#myModal5'>Kelola</button>"
                );
            }
            
            $output     = array(
                "draw"  => 1,
                "data"  => $data,
            );
        } else {
            $output     = array(
                "draw"  => 1,
                "data"  => [],
            );
        }

        return response()->json($output);
    }

    public static function doSimpanLaporanIklan($data)
    {
        var_dump($data);die();
    }

    public static function getReportHajiPerTahun($year)
    {

        $response =Http::withHeaders([
            'x-api-key' => env('API_PERCIK_KEY')
        ])->post(env('API_PERCIK').'/haji/realisasi/report',[
            'year' => $year
        ]);
               
        // Check if the request was successful
        if ($response->successful()) {

            $data = $response;

            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    public static function saveTargethajiToUmhaj($year, $months)
    {
        $response = Http::withHeaders([
            'x-api-key' => env('API_PERCIK_KEY')
        ])->post(env('API_PERCIK').'/haji/target/save',[
            'year' => $year,
            'user_id' => '1',
            'month' => $months
        ]);
        
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            return $data;

        } else {

            $errorCode    = $response->status();
            $errorMessage = $response->body();

            $data = [
                'error' => $errorCode,
                'message' => $errorMessage
            ];

            return  response()->json($data);
        }
    }

    // PROGRAM KERJA
    // 10 JULI 2024
    // NOTE : AMBIL DATA UNTUK LIST SASARAN
    public static function doGetListSasaran()
    {
        $curr_year  = date('Y');
        $curr_role  = Auth::user()->hasRole('admin') ? 'marketing' : Auth::user()->getRoleNames()[0];

        $query_header   = DB::select(
            "
            SELECT 	a.uid as pkt_uuid,
                    a.pkt_title,
                    a.pkt_description,
                    a.pkt_year
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	roles c ON b.roles_id = c.id
            WHERE 	a.pkt_year = '$curr_year'
            AND 	c.name LIKE '$curr_role'
            ORDER BY a.created_by ASC
            "
        );

        $query_detail   = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    a.pkt_title,
                    d.pktd_seq,
                    d.pktd_title,
                    d.pktd_target
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            JOIN 	roles c ON b.roles_id = c.id
            JOIN 	proker_tahunan_detail d ON a.id = d.pkt_id
            WHERE 	c.name LIKE '$curr_role'
            AND 	a.pkt_year = '$curr_year'
            "
        );

        $output     = array(
            "header"    => $query_header,
            "detail"    => $query_detail,
        );

        return $output;
    }

    public static function getListGroupDivision($valueCari)
    {
        $query  = DB::select(
            "
            SELECT 	a.id as group_division_id,
                    a.name as group_division_name,
                    b.name as role_name
            FROM 	group_divisions a
            JOIN 	roles b ON a.roles_id = b.id
            "
        );

        return $query;

    }

    public static function doSimpanSasaran($data)
    {
        DB::beginTransaction();
        
        $userID     = $data['user_id'];
        $data_table = $data['sendData'];
        $ip         = $data['ip'];

        // GET GROUP DIVISION & EMPLOYEE ID
        $query_get_employee     = DB::select(
            "
            SELECT 	a.id as employee_id,
                    a.name as employee_name,
                    c.id as employee_group_division_id,
                    c.name as employee_group_division_name
            FROM 	employees a
            JOIN 	job_employees b ON b.employee_id = a.id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            WHERE 	a.user_id LIKE '$userID'
            "
        );

        $query_get_group_divsision_mkt  = DB::select(
            "
            SELECT 	a.id
            FROM 	group_divisions a
            JOIN 		roles b ON a.roles_id = b.id
            WHERE 	b.name LIKE '%marketing%'
            "
        )[0]->id;

        if($data['jenis'] == 'add') {
            // UPDATE HEADER
            $data_add   = array(
                "uid"                       => Str::random(30),
                "pkt_title"                 => $data_table['prtTitle'],
                "pkt_description"           => $data_table['prtDescription'],
                "pkt_year"                  => $data_table['prtPeriode'],
                "pkt_pic_job_employee_id"   => Auth::user()->getRoleNames()[0] == 'admin' ? '' : $query_get_employee[0]->employee_id,
                "division_group_id"         => Auth::user()->getRoleNames()[0] == 'admin' ? $query_get_group_divsision_mkt : $query_get_employee[0]->employee_group_division_id,
                "created_by"                => $userID,
                "updated_by"                => $userID,
                "created_at"                => date('Y-m-d H:i:s'),
                "updated_at"                => date('Y-m-d H:i:s'),
            );

            DB::table('proker_tahunan')->insert($data_add);
            $pkt_id     = DB::getPdo()->lastInsertId();

            // UPDATE DETAIL
            // REMOVE EMPTY DATA IN DETAIL
            $detail     = [];
            for($i = 0; $i < count($data_table['prtSub']); $i++) {
                $data_detail    = $data_table['prtSub'][$i];

                if(!empty($data_detail['subProkTitle'])) {
                    $detail[]   = array(
                        "subProkSeq"        => $i + 1,
                        "subProkTitle"      => $data_detail['subProkTitle'],
                        "subProkTarget"     => $data_detail['subProkTarget'],
                    );
                } else {
                    // DO NOTHING
                }
            }

            // INSERT TO TABLE DETAIL
            for($j = 0; $j < count($detail); $j++) {
                $data_insert_detail     = array(
                    "pkt_id"        => $pkt_id,
                    "pktd_seq"      => $detail[$j]['subProkSeq'],
                    "pktd_title"    => $detail[$j]['subProkTitle'],
                    "pktd_target"   => $detail[$j]['subProkTarget'], 
                );
                
                DB::table('proker_tahunan_detail')->insert($data_insert_detail);
            }
        } else if($data['jenis'] == 'edit') {
            $data_where     = array(
                "uid"       => $data_table['prtID'],
            );

            // UPDATE HEADER
            $data_update    = array(
                "pkt_title"                 => $data_table['prtTitle'],
                "pkt_description"           => $data_table['prtDescription'],
                "pkt_year"                  => $data_table['prtPeriode'],
                "updated_by"                => $userID,
                "updated_at"                => date('Y-m-d H:i:s'),
            );

            DB::table('proker_tahunan')->where($data_where)->update($data_update);

            $pktID      = DB::table('proker_tahunan')->select('id')->where($data_where)->get()[0]->id;
            // DELETE ALL DATA FROM DETAIL
            DB::table('proker_tahunan_detail')->where('pkt_id', $pktID)->delete();
            // INSERT NEW DATA
            $detail     = [];
            for($i = 0; $i < count($data_table['prtSub']); $i++) {
                $data_detail    = $data_table['prtSub'][$i];

                if(!empty($data_detail['subProkTitle'])) {
                    $detail[]   = array(
                        "subProkSeq"        => $i + 1,
                        "subProkTitle"      => $data_detail['subProkTitle'],
                        "subProkTarget"     => $data_detail['subProkTarget'],
                    );
                } else {
                    // DO NOTHING
                }
            }

            // INSERT TO TABLE DETAIL
            for($j = 0; $j < count($detail); $j++) {
                $data_insert_detail     = array(
                    "pkt_id"        => $pktID,
                    "pktd_seq"      => $detail[$j]['subProkSeq'],
                    "pktd_title"    => $detail[$j]['subProkTitle'],
                    "pktd_target"   => $detail[$j]['subProkTarget'], 
                );
                
                DB::table('proker_tahunan_detail')->insert($data_insert_detail);
            }
        }

        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                'errMsg'    => []
            );
            if($data['jenis'] == 'add') {
                LogHelper::create('add', 'Berhasil Menambahkan Sasaran Baru Untuk Div. Marketing ID : '.$data_add['uid'], $ip);
            } else if($data['jenis'] == 'edit') { 
                LogHelper::create('edit', 'Berhasil Mengubah Data Sasaran Untuk Div. Marketing ID : '.$data_table['prtID'], $ip);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create('error_system', 'Gagal Menambahkan Sasaran Baru Untuk Div. Marketing', $ip);
        }

        return $output;
    }
	
    public static function getDataSasaran($data)
    {
        $sasaranID  = $data['sasaranID'];

        $query_header   = DB::select(
            "
            SELECT 	a.uid as pkt_uuid,
                    a.pkt_title,
                    a.pkt_description,
                    a.pkt_year
            FROM 	proker_tahunan a
            WHERE 	a.uid = '$sasaranID'
            "
        );

        $query_detail   = DB::select(
            "
            SELECT 	a.uid as pkt_id,
                    b.pktd_seq,
                    b.pktd_title,
                    b.pktd_target
            FROM 	proker_tahunan a
            JOIN 	proker_tahunan_detail b ON a.id = b.pkt_id
            WHERE 	a.uid = '$sasaranID'
            ORDER BY CAST(b.pktd_seq AS UNSIGNED) ASC
            "
        );
        
        $output     = array(
            "header"    => $query_header,
            "detail"    => $query_detail,
        );

        return $output;
    }

    public static function doSimpanProgram($data)
    {
        DB::beginTransaction();
        $sendData   = $data['sendData'];
        $jenis      = $data['jenis'];
        $ip         = $data['ip'];
        $user_id    = $data['user_id'];
        $user_role  = $data['user_role'];

        if($jenis == 'add')
        {   
            if($user_role != 'admin') {
                $get_employee       = DB::table('employees')
                                        ->select('id')
                                        ->where(['user_id'  => $user_id])
                                        ->get();
                $employee_id        = $get_employee[0]->id;   
            } else {
                $employee_id        = "";
            }

            // SIMPAN DATA TO PROKER BULANAN
            $data_simpan_bulanan    = array(
                "uuid"                  => Str::random(30),
                "pkb_title"             => strtoupper($sendData['program_uraian']),
                "pkb_start_date"        => "2024-".$sendData['program_bulan']."-01",
                "pkb_pkt_id"            => $sendData['program_sasaranID']." | ".$sendData['program_detailSasaranID'],
                "master_program_id"     => $sendData['program_masterID'],
                "pkb_employee_id"       => $employee_id,
                "pkb_is_active"         => "t",
                "created_by"            => $user_id,
                "created_at"            => date('Y-m-d H:i:s'),
                "updated_by"            => $user_id,
                "updated_at"            => date('Y-m-d H:i:s'),
            );
            DB::table('proker_bulanan')->insert($data_simpan_bulanan);
            
            $pkb_id     = DB::getPdo()->lastInsertId();
            $pkb_uuid   = DB::table('proker_bulanan')->select('uuid')->where(['id'=>$pkb_id])->get()[0]->uuid;
            
            for($i = 0; $i < count($sendData['program_detail']); $i++) {
                $data_simpan_bulanan_detail     = array(
                    "pkb_id"            => $pkb_id,
                    "pkbd_type"         => $sendData['program_detail'][$i]['detail_title'],
                    "pkbd_num_target"   => $sendData['program_detail'][$i]['detail_target'],
                    "pkbd_num_result"   => "0",
                    "pkbd_pic"          => $sendData['program_detail'][$i]['detail_pic'],
                    "created_by"        => $user_id,
                    "created_at"        => date('Y-m-d H:i:s'),
                    "updated_by"        => $user_id,
                    "updated_at"        => date('Y-m-d H:i:s'),
                );
                DB::table('proker_bulanan_detail')->insert($data_simpan_bulanan_detail);
            }
        } else if($jenis == 'edit') {
            // GET ID
            $pkb_id     = DB::table('proker_bulanan')->select('id')->where(['uuid'  => $sendData['program_ID']])->get()[0]->id;
            // UPDATE PROKER_BULANAN TABLE
            $data_where = array(
                "id"    => $pkb_id,
                "uuid"  => $sendData['program_ID'],
            );

            // CHECK APAKAH SUDAH MASUK HARIAN / BELUM
            $uuid_pkb     = $data_where['uuid'];
            $check_first    = DB::select(
                "
                SELECT 	*
                FROM 	proker_harian a
                WHERE 	SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = '$uuid_pkb' 
                AND 	a.pkh_is_active = 't'
                LIMIT 1
                "
            );
            if(!empty($check_first)) {
                DB::rollBack();
                $output     = array(
                    "status"    => "transaction",
                    "errMsg"    => [],
                );
                return $output;
            } else {
                $data_update = array(
                    "pkb_title"     => $data['sendData']['program_uraian'],
                    "pkb_pkt_id"    => $data['sendData']['program_sasaranID']." | ".$sendData['program_detailSasaranID'],
                    "updated_by"    => $user_id,
                    "updated_at"    => date('Y-m-d H:i:s'),
                    "master_program_id" => $data['sendData']['program_masterID'],
                );
    
                DB::table('proker_bulanan')->where($data_where)->update($data_update);
    
                // CHECK APAKAH DETAIL ADA KOSONG / TIDAK
                $temp_detail    = [];
                for($i = 0; $i < count($sendData['program_detail']); $i++) {
                    if($sendData['program_detail'][$i]['detail_title'] != '') {
                        array_push($temp_detail, $sendData['program_detail'][$i]);
                    }
                }
                
                // DELETE DATA LAMA DI PROKER BULANAN
                DB::table('proker_bulanan_detail')->where(['pkb_id' => $pkb_id])->delete();
    
                // INSERT DATA BARU
                for($j = 0; $j < count($temp_detail); $j++) {
                    $data_insert_detail     = array(
                        "pkb_id"            => $pkb_id,
                        "pkbd_type"         => $temp_detail[$j]['detail_title'],
                        "pkbd_num_target"   => $temp_detail[$j]['detail_target'],
                        "pkbd_num_result"   => 0,
                        "pkbd_pic"          => $temp_detail[$j]['detail_pic'],
                        "created_by"        => $user_id,
                        "created_at"        => date('Y-m-d H:i:s'),
                        "updated_by"        => $user_id,
                        "updated_at"        => date('Y-m-d H:i:s'),
                    );
                    DB::table('proker_bulanan_detail')->insert($data_insert_detail);
                }
            }
        }

        try {
            DB::commit();
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
            if($jenis == 'add') {
                LogHelper::create("add", "Berhasil Menambahkan Data Master Program Baru ID : ".$pkb_uuid, $ip);
            } else if($jenis == 'edit') {
                LogHelper::create('edit', 'Berhasi Mengubah Data Master Program ID : '.$sendData['program_ID'], $ip);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
            LogHelper::create("error_system", "Gagal Membuat Data Master Program", $ip);
            Log::channel('daily')->error($e->getMessage());
        }

        return $output;
    }
    
    public static function getListProgramMarketing($id)
    {
        $role   = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        $query  = DB::select(
            "
            SELECT 	a.uuid as pkb_id,
                    UPPER(CONCAT(a.pkb_title, ' - ', c.pktd_title)) as pkb_title,
                    e.name as group_division_name,
                    a.pkb_start_date as program_date,
                    b.uid as sasaran_id,
                    c.pktd_seq as sasaran_sequence,
                    SUM(f.pkbd_num_target) as total_target,
                    SUM(f.pkbd_num_result) as total_result,
                    a.master_program_id as pkb_master_program_id
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
            JOIN 	proker_tahunan_detail c ON (SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) = c.pktd_seq AND c.pkt_id = b.id)
            JOIN 	master_program d ON a.master_program_id = d.id
            JOIN 	group_divisions e ON d.division_group_id = e.id
            JOIN 	proker_bulanan_detail f ON a.id = f.pkb_id
            WHERE  	a.master_program_id IS NOT NULL
            AND 	a.pkb_is_active = 't'
            AND 	e.name LIKE '%$role%'
            AND 	a.uuid LIKE '$id'
            GROUP BY a.uuid, UPPER(CONCAT(a.pkb_title, ' - ', c.pktd_title)), e.name, a.pkb_start_date, b.uid, c.pktd_seq, a.master_program_id
            ORDER BY a.pkb_start_date DESC
            "
        );
        
        $query_detail   = DB::select(
            "
            SELECT 	b.pkb_id,
                    b.pkbd_type as pkbd_title,
                    CASE
                        WHEN b.pkbd_num_target IS NULL THEN 0
                        ELSE b.pkbd_num_target
                    END AS pkbd_num_target,
                    b.pkbd_num_result,
                    b.pkbd_pic
            FROM 	proker_bulanan a
            JOIN 	proker_bulanan_detail b ON b.pkb_id = a.id
            WHERE 	a.uuid = '$id'
            ORDER BY b.pkb_id ASC
            "
        );

        $output     = array(
            "header"    => $query,
            "detail"    => $query_detail,
        );

        return $output;
    }

    // UNTUK JENIS PEKERJAAN
    public static function doGetDataProgram($data)
    {
        $selectedMonth  = date('m', strtotime($data));
        $selectedYear   = date('Y', strtotime($data));
        // var_dump($selectedMonth, $selectedYear);die();
        return DB::select(
            "
            SELECT 	a.id,
                    b.uuid as pkb_id,
                    a.name,
                    d.pktd_title,
                    b.pkb_is_active
            FROM 	master_program a
            JOIN 	proker_bulanan b ON a.id = b.master_program_id
            JOIN 	proker_tahunan c ON SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', 1) = c.uid
            JOIN 	proker_tahunan_detail d ON (d.pkt_id = c.id AND SUBSTRING_INDEX(b.pkb_pkt_id, ' | ', -1) = d.pktd_seq)
            WHERE 	EXTRACT(YEAR FROM b.pkb_start_date) = '$selectedYear'
            AND 	EXTRACT(MONTH FROM b.pkb_start_date) = '$selectedMonth'
            AND 	b.pkb_is_active = 't'
            "
        );
    }

    public static function doGetDataProgramDetail($id)
    {
        $user_role  = Auth::user()->getRoleNames()[0];
        $user_id    = Auth::user()->id;

        if($user_role == 'admin') {
            $query  = DB::select(
                "
                SELECT 	a.uuid as pkb_id,
                        b.id as pkbd_id,
                        b.pkbd_type as pkbd_title,
                        b.pkbd_num_target,
                        b.pkbd_num_result
                FROM 	proker_bulanan a
                JOIN 	proker_bulanan_detail b ON b.pkb_id = a.id
                WHERE 	a.master_program_id IS NOT NULL
                AND 	a.uuid = '$id'
                ORDER BY b.id ASC
                "
            );
        } else {
            $query  = DB::select(
                "
                SELECT 	a.uuid as pkb_id,
                        b.id as pkbd_id,
                        b.pkbd_type as pkbd_title,
                        b.pkbd_num_target,
                        b.pkbd_num_result
                FROM 	proker_bulanan a
                JOIN 	proker_bulanan_detail b ON b.pkb_id = a.id
                WHERE 	a.master_program_id IS NOT NULL
                AND 	a.uuid = '$id'
                AND     b.pkbd_pic IN ('0', '$user_id')
                ORDER BY b.id ASC
                "
            );
        }

        return $query;
    }

    public static function doSimpanJenisPekerjaan($data)
    {
        DB::beginTransaction();
        if($data['sendData']['jenis_pekerjaan_type_trans'] == 'add') {
            $data_simpan_harian     = array(
                "uuid"              => Str::random(30),
                "pkh_title"         => $data['sendData']['jenis_pekerjaan_title'],
                "pkh_date"          => $data['sendData']['jenis_pekerjaan_date'],
                "pkh_start_time"    => $data['sendData']['jenis_pekerjaan_date']." ".$data['sendData']['jenis_pekerjaan_start_time'],
                "pkh_end_time"      => $data['sendData']['jenis_pekerjaan_date']." ".$data['sendData']['jenis_pekerjaan_end_time'],
                "pkh_pkb_id"        => $data['sendData']['jenis_pekerjaan_programIDDetail'],
                "pkh_is_active"     => "t",
                "created_by"        => $data['user_id'],
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_by"        => $data['user_id'],
                "updated_at"        => date('Y-m-d H:i:s'),
            );

            DB::table('proker_harian')->insert($data_simpan_harian);
            $harian_id      = DB::getPdo()->lastInsertId();
            $harian_uuid    = DB::table('proker_harian')->select('uuid')->where(['id'=>$harian_id ])->get()[0]->uuid;
            
            // UPDATE BULANAN 
            $pkb_bulanan_id    = explode(' | ', $data['sendData']['jenis_pekerjaan_programIDDetail'])[0];
            $pkb_bulanan_detail= explode(' | ', $data['sendData']['jenis_pekerjaan_programIDDetail'])[1];

            $query_get_proker_bulanan   = DB::select(
                "
                SELECT 	a.id as pkb_id,
                        b.*
                FROM 	proker_bulanan a
                JOIN 	proker_bulanan_detail b ON a.id = b.pkb_id
                WHERE 	a.uuid = '$pkb_bulanan_id'
                AND 	b.id = '$pkb_bulanan_detail'
                "
            );

            $pkb_hasil  = $query_get_proker_bulanan[0]->pkbd_num_result;
            $pkb_id     = $query_get_proker_bulanan[0]->pkb_id;
            $pkb_hasil_baru     = $pkb_hasil + 1;

            $data_where_bulanan = array(
                "pkb_id"        => $pkb_id,
                "id"            => $pkb_bulanan_detail,
            );
            
            $data_update_bulanan = array(
                "pkbd_num_result"   => $pkb_hasil_baru,
            );
            
            DB::table('proker_bulanan_detail')->where($data_where_bulanan)->update($data_update_bulanan);
        } else if($data['sendData']['jenis_pekerjaan_type_trans'] == 'edit') {
            $data_harian_where  = array(
                "uuid"      => $data['sendData']['jenis_pekerjaan_ID'],
            );

            $get_data_harian    = DB::table('proker_harian')->where($data_harian_where)->get()[0];

            $data_harian_update = array(
                "pkh_title"         => $data['sendData']['jenis_pekerjaan_title'],
                "pkh_start_time"    => $data['sendData']['jenis_pekerjaan_date']." ".$data['sendData']['jenis_pekerjaan_start_time'],
                "pkh_end_time"      => $data['sendData']['jenis_pekerjaan_date']." ".$data['sendData']['jenis_pekerjaan_end_time'],
                "pkh_pkb_id"        => $data['sendData']['jenis_pekerjaan_programIDDetail'],
                "pkh_is_active"     => "t",
                "updated_by"        => $data['user_id'],
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            
            DB::table('proker_harian')->where($data_harian_where)->update($data_harian_update);
            
            $pkh_pkb_id         = $get_data_harian->pkh_pkb_id;

            if($pkh_pkb_id != $data['sendData']['jenis_pekerjaan_programIDDetail']) {
                $pkb_id_old     = explode(' | ', $pkh_pkb_id)[0];
                $pkb_id_seq_old = explode(' | ', $pkh_pkb_id)[1];

                $pkb_id_new     = explode(' | ', $data['sendData']['jenis_pekerjaan_programIDDetail'])[0];
                $pkb_id_seq_new = explode(' | ', $data['sendData']['jenis_pekerjaan_programIDDetail'])[1];

                // BALIKIN VALUE LAMA
                $get_data_bulanan_old       = DB::table('proker_bulanan')->where(['uuid' => $pkb_id_old])->get()[0];
                $get_data_bulanan_detail_old= DB::table('proker_bulanan_detail')->where(['id' => $pkb_id_seq_old, 'pkb_id'=> $get_data_bulanan_old->id])->get()[0];
                
                $result_old                 = $get_data_bulanan_detail_old->pkbd_num_result;
                $new_result_old         = $result_old - 1;
                DB::table('proker_bulanan_detail')->where(['id' => $pkb_id_seq_old])->update(['pkbd_num_result' => $new_result_old]);

                // INSERT VALUE BARU
                $get_data_bulanan_new       = DB::table('proker_bulanan')->where(['uuid' => $pkb_id_new])->get()[0];
                $get_data_bulanan_detail_new= DB::table('proker_bulanan_detail')->where(['id' => $pkb_id_seq_new, 'pkb_id' => $get_data_bulanan_new->id])->get()[0];
                $new_result                 = $get_data_bulanan_detail_new->pkbd_num_result + 1;
                DB::table('proker_bulanan_detail')->where(['id' => $pkb_id_seq_new])->update(['pkbd_num_result' => $new_result]);
            }
        }

        try {
            DB::commit();
            if($data['sendData']['jenis_pekerjaan_type_trans'] == 'add') {
                LogHelper::create('add', 'Berhasil Menambahkan Data Jenis Pekerjaan : '.$harian_uuid, $data['ip']);
            } else if($data['sendData']['jenis_pekerjaan_type_trans'] == 'edit') {
                LogHelper::create('edit', 'Berhasil Merubah Data Jenis Pekerjaan : '.$data_harian_where['uuid'], $data['ip']);
            }
            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
        } catch(\Exception $e) {
            DB::rollBack();
            LogHelper::create('error_system', 'Gagal Membuat Jenis Pekerjaan', $data['ip']);
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }

    public static function getDataEventsCalendarJpk($data)
    {
        $start_date     = $data['start_date'];
        $end_date       = $data['end_date'];

        $query          = DB::select(
            "
            SELECT 	b.uuid,
                    UPPER(CONCAT(a.pkb_title,' ', c.pkbd_type)) as pkh_title,
                    b.pkh_date,
                    b.pkh_start_time,
                    b.pkh_end_time,
                    b.pkh_is_active
            FROM 	proker_bulanan a
            JOIN	proker_harian b ON SUBSTRING_INDEX(b.pkh_pkb_id, ' | ', 1) = a.uuid
            JOIN 	proker_bulanan_detail c ON (SUBSTRING_INDEX(b.pkh_pkb_id, ' | ', -1) =  c.id AND c.pkb_id = a.id)
            WHERE 	a.master_program_id IS NOT NULL
            AND 	b.pkh_date BETWEEN '$start_date' AND '$end_date'
            AND     b.pkh_is_active = 't'
            ORDER BY a.created_at ASC
            "
        );

        return $query;
    }

    public static function getDataDetaiLEventsCalendar($id)
    {
        return DB::select(
            "
            SELECT 	a.uuid as pkh_uuid,
                    a.pkh_title,
                    a.pkh_date,
                    a.pkh_start_time,
                    a.pkh_end_time,
                    a.pkh_pkb_id,
                    b.master_program_id,
                    b.uuid as pkb_id
            FROM 	proker_harian a
            JOIN 	proker_bulanan b ON SUBSTRING_INDEX(a.pkh_pkb_id, ' | ', 1) = b.uuid
            WHERE 	a.uuid = '$id'
            "
        );
    }

    public static function doDeleteProgram($data)
    {
        DB::beginTransaction();

        // CHECK DULU
        $programID  = $data['programID'];
        $ip         = $data['ip'];

        $query_check_harian     = DB::select(
            "
            SELECT 	b.*
            FROM 	proker_bulanan a
            JOIN 	proker_harian b ON SUBSTRING_INDEX(b.pkh_pkb_id, ' | ', 1) = a.uuid
            WHERE 	a.uuid = '$programID'
            AND     b.pkh_is_active = 't'
            ORDER BY b.id ASC
            "
        );

        if(!empty($query_check_harian)) {
            DB::rollBack();
            $output     = array(
                "status"    => "data_ada",
                "errMsg"    => "Tidak Bisa menghapus data ini, dikarenakan ada relasi dengan jenis pekerjaan yang telah dibuat"
            );
        } else {
            $data_where     = array(
                "uuid"      => $programID,
            );
            $data_update    = array(
                "pkb_is_active" => "f",
            );
            
            DB::table('proker_bulanan')->where($data_where)->update($data_update);
    
            try {
                DB::commit();
                LogHelper::create('delete', 'Berhasil Menghapus Program : '.$programID, $ip);
    
                $output     = array(
                    'status'    => 'berhasil',
                    'errMsg'    => null,
                );
            } catch(\Exception $e) {
                DB::rollBack();
                LogHelper::create('error_system', 'Gagal Menghapus Program', $ip);
                
                $output     = array(
                    'status'    => 'gagal',
                    'errMsg'    => $e->getMessage()
                );
            }
        }

        return $output;
    }

    public static function doGetDashboardSasaran()
    {
        $currYear   = date('Y');
        
        $header     = DB::select(
            "
            SELECT 	a.uid,
                    a.pkt_title
            FROM 	proker_tahunan a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            WHERE 	b.name LIKE '%marketing%'
            AND     a.pkt_year = '$currYear'
            "
        );
        
        $detail     = DB::select(
            "
            SELECT 	a.uid as pkt_uuid,
                    a.pkt_year,
                    b.pktd_seq,
                    b.pktd_title,
                    b.pktd_target
            FROM 	proker_tahunan a
            JOIN 	proker_tahunan_detail b ON b.pkt_id = a.id
            JOIN 	group_divisions c ON a.division_group_id = c.id
            WHERE 	a.pkt_year = EXTRACT(YEAR FROM CURRENT_DATE)
            AND 	c.name LIKE '%marketing%'
            ORDER BY a.uid, CAST(b.pktd_seq AS UNSIGNED) ASC
            "
        );

        if(!empty($header) && !empty($detail)) {
            $output     = [
                "header"    => $header,
                "detail"    => $detail,
            ];
        } else {
            $output     = [];
        }

        return $output;
    }

    public static function getMasterProgram()
    {
        $role   = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        return DB::select(
            "
            SELECT 	a.*
            FROM    master_program a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            AND 	b.name LIKE '%$role%'
            ORDER BY a.name, a.id ASC
            "
        );
    }

    public static function doDeleteJenisPekerjaan($data)
    {
        DB::beginTransaction();

        $data_where     = array(
            "uuid"      => $data['jpk_ID'],
        );

        $data_update    = array(
            "pkh_is_active" => "f",
        );

        DB::table('proker_harian')->where($data_where)->update($data_update);

        // BALIKIN NILAI DETAIL
        $get_ref    = DB::table('proker_harian')->where($data_where)->get()[0];
        $pkb_id     = explode(' | ', $get_ref->pkh_pkb_id)[0];
        $pkbd_id    = explode(' | ', $get_ref->pkh_pkb_id)[1];

        $get_result_detail  = DB::table('proker_bulanan_detail')->where(['id' => $pkbd_id])->get()[0];
        $new_results_detail = $get_result_detail->pkbd_num_result - 1;
        DB::table('proker_bulanan_detail')->where(['id' => $pkbd_id])->update(['pkbd_num_result' => $new_results_detail]);

        try {
            DB::commit();
            
            LogHelper::create('delete', 'Berhasil Menghapus Data Jenis Pekerjaan : '.$data_where['uuid'], $data['ip']);

            $output     = array(
                "status"    => "berhasil",
                "errMsg"    => [],
            );
        } catch (\Exception $e) {
            DB::rollBack();

            LogHelper::create('error_system', 'Gagal Menghapus Data Jenis Pekerjaan', $data['ip']);
            
            $output     = array(
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            );
        }

        return $output;
    }

    // 19 JULY 2024
    // NOTE : GET LIST PROGRAM DETAIL
    public static function getListDetailProgram($id)
    {
        $header     = DB::select(
            "
            SELECT 	a.uuid as pkb_id,
                    a.pkb_title as pkb_title,
                    UPPER(c.pktd_title) as pkb_sub_title,
                    EXTRACT(YEAR FROM a.pkb_start_date) as pkb_year_periode,
                    CASE
                        WHEN LENGTH(EXTRACT(MONTH FROM a.pkb_start_date)) < 2 THEN CONCAT('0',EXTRACT(MONTH FROM a.pkb_start_date))
                        ELSE EXTRACT(MONTH FROM a.pkb_start_date)
                    END as pkb_month_periode,
                    UPPER(e.name) as group_division_name
            FROM 	proker_bulanan a
            JOIN 	proker_tahunan b ON SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', 1) = b.uid
            JOIN 	proker_tahunan_detail c ON (SUBSTRING_INDEX(a.pkb_pkt_id, ' | ', -1) = c.pktd_seq AND b.id = c.pkt_id)
            JOIN 	master_program d ON a.master_program_id = d.id
            JOIN 	group_divisions e ON d.division_group_id = e.id
            WHERE 	uuid = '$id'
            AND     a.pkb_is_active = 't'
            "
        );

        $detail     = DB::select(
            "
            SELECT 	b.id as pkbd_id,
                    b.pkbd_type as pkbd_title,
                    b.pkbd_num_target as pkbd_target,
                    b.pkbd_num_result as pkbd_result
            FROM 	proker_bulanan a
            JOIN 	proker_bulanan_detail b ON b.pkb_id = a.id
            WHERE 	a.uuid = '$id'
            AND 	a.pkb_is_active = 't'
            "
        );

        $output     = array(
            "header"    => $header[0],
            "detail"    => $detail,
        );

        return $output;
    }

    public static function getDataPICMarketing()
    {
        $roles   = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        return DB::select(
            "
            SELECT 	a.user_id,
                    a.name
            FROM 	employees a
            JOIN 	job_employees b ON b.employee_id = a.id
            JOIN 	group_divisions c ON b.group_division_id = c.id
            JOIN 	roles d ON c.roles_id = d.id
            WHERE 	d.name LIKE '%$roles%'
            ORDER BY a.user_id ASC
            "
        );
    }
}