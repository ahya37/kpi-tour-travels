<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\AlumniProspekMaterial;
use App\Models\DetailAlumniProspekMaterial;
use App\Models\DetailMarketingTarget;
use App\Models\MarketingTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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

    public static function getRelisasiUmrah($formData)
    {
       
        $response =Http::withHeaders([
                    'x-api-key' => env('API_PERCIK_KEY')
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
        // sleep(1);
        $orderBy = 'name';
        switch ($request->input('order.0.column')) {
            case '1':
                $orderBy = 'name';
                break;
        }

       $marketingTargets =  DetailAlumniProspekMaterial::getDetailAlumniProspekMaterialByAlumniProspekMaterial($alumniprospectmaterialId);

        if($request->input('search.value')!=null){
           $marketingTargets =$marketingTargets->where(function($q)use($request){
                $q->whereRaw('LOWER(name) like ? ',['%'.strtolower($request->input('search.value')).'%']);
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

        $results = [];
        $no = 1;
        foreach ($marketingTargets as $value) {
            $is_response = '';
            if ($value->is_respone == 'Y') {
                $is_response = 'Ya';
            }elseif ($value->is_respone == 'N') {
                $is_response = 'Tidak';
            }

            $results[] = [
                'id' => $value->id,
                'no' => $no++,
                'name' => $value->name,
                'telp' => $value->telp,
                'address' => $value->address,
                'is_respone' => $is_response,
                'reason' => $value->reason,
                'notes' => $value->notes
            ];
        }

        return response()->json([
                'draw'=>$request->input('draw'),
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'data'=>$results
            ]);
    }

    public static function doSimpanLaporanIklan($data)
    {
        var_dump($data);die();
    }
	
}