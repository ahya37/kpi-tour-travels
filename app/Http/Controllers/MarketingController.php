<?php

namespace App\Http\Controllers;

use App\Helpers\Months;
use App\Http\Requests\MarketingTargetRequest;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\DetailMarketingTargetRequests;
use App\Models\AlumniProspekMaterial;
use App\Models\DetailAlumniProspekMaterial;
use App\Models\JobEmployee;
use App\Models\Program;
use App\Models\SubDivision;
use App\Services\AlumniProspectMaterialService;
use App\Services\AlumniProspekMaterialService;
use App\Services\MarketingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            MarketingService::store($requestMarketingtarget);
            return ResponseFormatter::success([
                'message' =>  'Save marketing target is successfuly!' 
            ]);
    }

    public function listTarget(Request $request)
    {
        $requestDataTableMarketingtarget = $request;
        return MarketingService::listTarget($requestDataTableMarketingtarget);
        
    }

    public function detailMarketingTarget($marketingTargetId)
    {
        $detailMatketingTargets = MarketingService::detailMarketingTarget($marketingTargetId);

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

            $results = MarketingService::detailMarketingTargetStore($requestDetailMarketingtarget);
            return ResponseFormatter::success($results);
    }

    public function detailListTarget(Request $request, $detailMarketingTargetId)
    {
        $requestDataTableMarketingtarget = $request;
        return MarketingService::detailListTarget($requestDataTableMarketingtarget,$detailMarketingTargetId);
        
    }

    // Bahan Prospek
    public function prospectMaterial()
    {
        $prospectMaterials = MarketingService::prospectMaterialList();
        $no = 1;
        return view('marketings.prospect-material',[
            'title' => 'Bahan Prospek',
            'prospectMaterial' => $prospectMaterials,
            'no' => $no
        ]);
    }

    public function prospectMaterialStore(Request $request)
    {
        DB::beginTransaction();
        try {

            $formData = $request->only(['year']);
            $response = MarketingService::prospectMaterialStore($formData);
            $response = $response['data']['members'];

            // get data job divisi customer service
            $customerServices = JobEmployee::getJobDivisionCustomerServices();
            $results  = [];

            foreach($response as $no => $member){
			   
                $job_employee_id = $customerServices[$no]->id;
                
                $list_id_members = [];
                $count_members   = 0; 
                foreach($member as $no_member => $id_member){
                    
                    $list_id_members[] = ['id_member' => $id_member];
                    $count_members     = count($id_member);
                }
                
                $results[] = [
                     'no' => $no,
                     'job_employee_id' => $job_employee_id,
                     'count_members' => $count_members,
                     'list_members' => $list_id_members,
                ];
                
            }

            // simpan ke table bahan prospek 
		    $label = 'BAHAN PROSPEK ALUMNI ('.date('m').'-'.date('Y').')';
            // $bahanProspekAlumni = $response['data']['listBahanProspekAlumni'];
            foreach ($results as  $value) {
               $asveAlumniProspectMaterial = AlumniProspekMaterial::create([
                    'id' => Str::random(30),
                    'periode' => 0,
                    'label' => $label,
                    'job_employee_id' => $value['job_employee_id'],
                    'members' => $value['count_members'],
                    'notes' => '',
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                foreach ($value['list_members'] as $item) {
                    // dd($item['id_member']);
                    foreach ($item['id_member'] as $t) {
                          $address   = $t['members']['ALAMAT'] ?? $t['members']['ALAMAT_UMRAH'];
                          $kelurahan = $t['members']['KELURAHAN'] ?? $t['members']['KELURAHAN_UMRAH'];
                          $kecamatan = $t['members']['KECAMATAN'] ?? $t['members']['KECAMATAN_UMRAH'];
                          $kota      = $t['members']['KOTA'] ?? $t['members']['KOTA_UMRAH'];
                          $provinsi  = $t['members']['PROPINSI'] ?? $t['members']['PROPINSI_UMRAH'];
       
                          $addressFull = $address.', '.$kelurahan.', '.$kecamatan.', '.$kota.', '.$provinsi;

                          DetailAlumniProspekMaterial::create([
                               'id' => Str::random(30),
                               'alumni_prospect_material_id' => $asveAlumniProspectMaterial->id,
                               'id_members' =>  $t['members']['ID'],
                               'name' =>  $t['members']['NAMA'],
                               'telp' =>  $t['members']['TELEPON'] ??  $t['members']['HP'],
                               'address' => $addressFull,
                               'created_by' => Auth::user()->id,
                               'updated_by' => Auth::user()->id,
                          ]);

                         
                        }

                        // API umhaj update member jika member tersebut sudah menjadi bahan prospek alumni
                        // $formData['id_member'] = $t['members']['ID'];
                       $formDataUpdate['data']    = $item['id_member'];
                       MarketingService::updateApiIsBahanProspek($formDataUpdate);
                    //    return $update;
                }


            }

            DB::commit();
            return redirect()->back()->with(['success' => 'Sukses generate alumni jamaah umrah']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(['file' => get_class(), 'errors' => $e->getMessage()]);
            return redirect()->back()->with(['error' => 'Gagal generate alumni jamaah umrah']);
        }
    }

    public function alumniProspectMaterialByAccountCS()
    {
        // Get data prospek alumni berdasarkan login cs
       $auth = Auth::user()->id;
       $prospectMaterials = MarketingService::alumniProspectMaterialByAccountCS($auth);
       $no = 1;

       return view('marketings.cs.prospect-material', [
            'title' => 'Bahan Prospek Alumni',
            'prospectMaterials' => $prospectMaterials,
            'no' => $no
        ]);
    }

    public function detailAlumniProspectMaterialByAccountCS($id)
    {
        // Get data prospek alumni berdasarkan login cs
       $detailProspectMaterials = MarketingService::detailAlumniProspectMaterialByAccountCS($id);

       $no = 1;

       return view('marketings.cs.detail-prospect-material', [
            'title' => 'Daftar Jamaah',
            'detailProspectMaterials' => $detailProspectMaterials,
            'no' => $no
        ]);
    }
}
