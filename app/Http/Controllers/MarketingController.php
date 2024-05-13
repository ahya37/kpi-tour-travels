<?php

namespace App\Http\Controllers;

use App\Helpers\Months;
use App\Http\Requests\MarketingTargetRequest;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\DetailMarketingTargetRequests;
use App\Models\Program;
use App\Services\MarketingTargetService;
use Illuminate\Support\Facades\Http;

class MarketingController extends Controller
{
    public function target()
    {
        
        return view('marketings.targets',[
            'title' => 'Target Marketing'
        ]);
    }

    public function storeTarget(MarketingTargetRequest $request)
    {
            $requestMarketingtarget = $request->validated();
            MarketingTargetService::store($requestMarketingtarget);
            return ResponseFormatter::success([
                'message' =>  'Save marketing target is successfuly!' 
            ]);
    }

    public function listTarget(Request $request)
    {
        $requestDataTableMarketingtarget = $request;
        return MarketingTargetService::listTarget($requestDataTableMarketingtarget);
        
    }

    public function detailMarketingTarget($marketingTargetId)
    {
        $detailMatketingTargets = MarketingTargetService::detailMarketingTarget($marketingTargetId);

        return view('marketings.detail-marketing-target', [
            'title' => 'Detail Target Marketing',
            'detailMatketingTargets' => $detailMatketingTargets
        ]);

    }

    public function loadModalMarketingTarget()
    {
        sleep(1);
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Tahun</label>
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                <div class="col-sm-10">
                                <input id="year" type="text" name="year"
                                        class="form-control" required></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json(['modalContent' => $modalContent]);
    }

    public function loadModalDetailMarketingTarget()
    {
        sleep(1);

        // get data programs
        $programs = Program::select('id','name')->get();
        // get list bulan dalam 1 tahun
        $months  = Months::months();
        
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                <label class="col-sm-2 col-form-label">Bulan</label>
                                <div class="col-sm-10">
                                    <select class="form-control select2_demo_2" name="month" id="month">';

                                    foreach ($months as $key => $value) {
                                        $modalContent = $modalContent.'<option value="'.$value['key'].'-'.$value['month'].'">'.$value['month'].'</option>';
                                    }
                                        
        $modalContent = $modalContent.'</select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Program</label>
                                <div class="col-sm-10">
                                <select class="form-control select2_demo_1" name="program_id" id="program_id">';

                                foreach ($programs as $key => $value) {
                                    $modalContent = $modalContent.'<option value="'.$value->id.'">'.$value->name.'</option>';
                                }
                                    
        $modalContent  = $modalContent.'</select>
                            </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Target</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control form-control-sm" name="target" id="target">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json([
            'modalContent' => $modalContent,
        ]);
    }

    public function detailMarketingTargetStore(DetailMarketingTargetRequests $request, $marketingTargetId)
    {
            $requestDetailMarketingtarget = $request->validated();

            $requestDetailMarketingtarget['month_number'] = (int) explode("-",$requestDetailMarketingtarget['month'])[0];
            $requestDetailMarketingtarget['month_name']   =  explode("-",$requestDetailMarketingtarget['month'])[1];
            $requestDetailMarketingtarget['marketing_target_id']   =  $marketingTargetId;

            $results = MarketingTargetService::detailMarketingTargetStore($requestDetailMarketingtarget);
            return ResponseFormatter::success($results);
    }

    public function detailListTarget(Request $request, $detailMarketingTargetId)
    {
        $requestDataTableMarketingtarget = $request;
        return MarketingTargetService::detailListTarget($requestDataTableMarketingtarget,$detailMarketingTargetId);
        
    }

    // Bahan Prospek
    public function prospectMaterial()
    {
        return view('marketings.prospect-material',[
            'title' => 'Bahan Prospek'
        ]);
    }

    public function prospectMaterialStore(Request $request)
    {
        $formData = $request->only(['year']);
        $response = MarketingTargetService::prospectMaterialStore($formData);
        return $response;
    }

    public function prospectMaterialList(Request $request)
    {
        $response = MarketingTargetService::prospectMaterialList($request);
        return $response;
    }
}
