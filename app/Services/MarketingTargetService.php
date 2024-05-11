<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\DetailMarketingTarget;
use App\Models\MarketingTarget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MarketingTargetService 
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
        sleep(1);
        $orderBy = 'year';
        switch ($request->input('order.0.column')) {
            case '1':
                $orderBy = 'year';
                break;
        }

        $data = MarketingTarget::select('id','year','total_target','total_realization','total_difference');

        if($request->input('search.value')!=null){
            $data = $data->where(function($q)use($request){
                $q->whereRaw('LOWER(year) like ? ',['%'.strtolower($request->input('search.value')).'%']);
                ;
            });
        }

        // if($request->input('month') != '' AND $request->input('year') != ''){
        //                     $data->whereMonth('start_date', $request->month);
        //                     $data->whereYear('end_date', $request->year);
        //                 }

        $recordsFiltered = $data->get()->count();
        if($request->input('length')!=-1) $data = $data->skip($request->input('start'))->take($request->input('length'));
        $data = $data->orderBy($orderBy,$request->input('order.0.dir'))->get();

        $recordsTotal = $data->count();

        return response()->json([
                'draw'=>$request->input('draw'),
                'recordsTotal'=>$recordsTotal,
                'recordsFiltered'=>$recordsFiltered,
                'data'=> $data
            ]);
    }

    public static function detailMarketingTarget($marketingTargetId)
    {
        $detailMarketingTargets = DetailMarketingTarget::where('marketing_target_id', $marketingTargetId)->get();
        return $detailMarketingTargets;
    }

    public static function detailMarketingTargetStore($requestDetailMarketingtarget)
	{
		DB::beginTransaction();
        try {
            
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

        } catch (\Exception $e) {
			DB::rollback();
            return ResponseFormatter::error([
                'error' => $e->getMessage()
            ]);
        }
		
	}
	
}