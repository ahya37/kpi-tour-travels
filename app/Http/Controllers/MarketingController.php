<?php

namespace App\Http\Controllers;

use App\Helpers\Months;
use App\Http\Requests\MarketingTargetRequest;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Helpers\Years;
use App\Http\Requests\DetailMarketingTargetRequests;
use App\Http\Requests\ManageAlumniProspectMaterialRequest;
use App\Models\AlumniProspekMaterial;
use App\Models\DetailAlumniProspekMaterial;
use App\Models\Employee;
use App\Models\JobEmployee;
use App\Models\Program;
use App\Models\Reason;
use App\Services\MarketingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\select;

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
                                    <select class="form-control select2" name="month" id="month">';

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

            $formData['start_year'] = $request->start_year;
            $formData['end_year'] = $request->end_year;

            $res = MarketingService::prospectMaterialStore($formData);
            $res_total   = $res['data']['total'];
            $res_members = $res['data']['members'];

            $req_total_data = $request->jml_data; // total data yg di request dikali dengan jumlah cs yg di request

            #jika data kurang dari seribu beri notif dan lakukan select range tahun lagi
            if ($res_total < $req_total_data) {
                return ResponseFormatter::success([
                    'status'  => 2,
                    'message' => 'Data kurang dari '.$req_total_data.' silahkan tentukan range tahun kembali!'
                ]);
            }

            #jika jumlah data dari sumber > jumlah yang di request, maka hapus sebanyak selisihnya
            if ($res_total > $req_total_data) {
                
                $selisih = $res_total - $req_total_data;
                $res_members = array_slice($res_members, 0, - $selisih, true);
            }

            #get data cs by request cs
            $csId       = explode(",", $request->cs); 
            $res_cs     = [];
            foreach ($csId as $key => $employeeId) {
                $res_cs[] = [
                    'cs' => JobEmployee::getCsByEmployeeId($employeeId)
                ];
            }

            $jml_bagi = $req_total_data / count($csId);

            #membagi data jamaah dengan  jumlah cs yg di request
            $groups = array_chunk($res_members, $jml_bagi);

            $results = [];
            foreach ($groups as $key => $member) {
                $group_cs_key = $res_cs[$key %  count($csId)];

                #kelompokan jamaah yang sudah dibagi dengan customer service nya
                $results[] = [
                    'cs' => $group_cs_key,
                    'members' => $member
                ];
            }

            #simpan ke table bahan prospek 
            $label = 'BAHAN PROSPEK ALUMNI ('.date('m').'-'.date('Y').')';
            foreach ($results as $value) {
                foreach ($value['cs'] as $cs) {
                    $asveAlumniProspectMaterial = AlumniProspekMaterial::create([
                            'id' => Str::random(30),
                            'periode' => 0,
                            'label' => $label,
                            'job_employee_id' => $cs->id,
                            'members' => count($value['members']),
                            'notes' => '',
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                    ]);

                    foreach ($value['members'] as $member) {
                          $address   = $member['ALAMAT'] ?? $member['ALAMAT_UMRAH'];
                          $kelurahan = $member['KELURAHAN'] ?? $member['KELURAHAN_UMRAH'];
                          $kecamatan = $member['KECAMATAN'] ?? $member['KECAMATAN_UMRAH'];
                          $kota      = $member['KOTA'] ?? $member['KOTA_UMRAH'];
                          $provinsi  = $member['PROPINSI'] ?? $member['PROPINSI_UMRAH'];

                          $addressFull = $address.', '.$kelurahan.', '.$kecamatan.', '.$kota.', '.$provinsi;

                          DetailAlumniProspekMaterial::create([
                               'id' => Str::random(30),
                               'alumni_prospect_material_id' => $asveAlumniProspectMaterial->id,
                               'id_members' =>  $member['ID_MEMBER'],
                               'name' =>  $member['NAMA'],
                               'telp' =>  $member['TELEPON'] ??  $member['HP'],
                               'provinsi' => $provinsi,
                               'kota' => $kota,
                               'kecamatan' => $kecamatan,
                               'kelurahan' => $kelurahan,
                               'alamat' => $address,
                               'address' => $addressFull,
                               'created_by' => Auth::user()->id,
                               'updated_by' => Auth::user()->id,
                          ]);
                    }
                    
                }
            }

            DB::commit();

            return ResponseFormatter::success([
                'message' => 'Berhasil generate alumni',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal generate alumni'
            ]);
        }
    }

    public function singkronisasiDataAlumniUmrah($id)
    {
        DB::beginTransaction();
        try {

            # get data detail_alumni_prospect_material where alumni_prospect_material where id
            $detailProspectMaterials = DetailAlumniProspekMaterial::select('id_members','id')->where('alumni_prospect_material_id',$id)->get();
            $id_members = [];
            foreach ($detailProspectMaterials as  $value) {
                $id_members[] =  [
                    'id_members' => $value->id_members
                ];
                // if ($updateMember) {
                //     DB::table('detail_alumni_prospect_material')->where('id_members', $updateMember)->update(['is_sinkronisasi' => '1']);
                // }
            }

            #Update is_bahan_prospek_di API / db utama
            $res_update_data=  MarketingService::updateApiIsBahanProspek($id_members);
            foreach ($res_update_data as $key => $value) {
                    DB::table('detail_alumni_prospect_material')->where('id_members', $value)->update(['is_sinkronisasi' => '1']);

            }

            AlumniProspekMaterial::where('id', $id)->update(['is_sinkronisasi' => 1]);

            DB::commit();
            return ResponseFormatter::success([
                'message' => 'Berhasil Singkronkan data'
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal Singkronkan data!'
            ]);
        }

        // API umhaj update member jika member tersebut sudah menjadi bahan prospek alumni
        //  $formDataUpdate['id_member']    = $member['ID_MEMBER'];
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

    public function loadModalManageAlumniProspectMaterial($detailId)
    {
        // get data programs
        $reasons = Reason::select('id','name')->get();
        $years   = Years::list();
        
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Respon</label>
                                <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="hidden" name="idDetail" id="idDetail" value="'.$detailId.'">
                                    <select class="form-control select2" name="response" id="response" required>
                                        <option value="">-Pilih respon-</option>
                                        <option value="Y">Ya</option>
                                        <option value="N">Tidak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row d-none div-year">
                                <label class="col-sm-2 col-form-label">Program</label>
                                <div class="col-sm-5">
                                <label class="col-form-label">Tahun</label>
                                    <select class="form-control select2" name="year" id="year">
                                        <option value="">-Pilih Tahun-</option>';

                                        foreach ($years as $key => $year) {
                                            $modalContent = $modalContent.'<option value="'.$year.'">'.$year.'</option>';
                                        }
                                        
        $modalContent = $modalContent.'</select>
                                </div>
                                <div class="col-sm-5">
                                <label class="col-form-label"></label>
                                </div>
                            </div>

                            <div class="form-group row d-none div-year">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-5">
                                <label class="col-form-label">Umrah</label>
                                <select class="select2 form-control" name="tourcode" id="tourcode">
                                </select>
                                </div>
                            </div>
                            

                            <div class="form-group row d-none div-year">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-5">
                                <label class="col-form-label">Haji</label>
                                <select class="select2 form-control" name="tourcodeHaji" id="tourcodeHaji"></select>
                                </div>
                            </div>

                            <div class="form-group row d-none div-year">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-5">
                            <label class="col-form-label">Tour Muslim</label>
                            <select class="select2 form-control" name="tourcodeMuslim" id="tourcodeMuslim"></select>
                            </div>
                        </div>

                            <div class="form-group row d-none" id="div-reason">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                <label class="col-sm-2 col-form-label">Alasan</label>
                                <div class="col-sm-10">
                                    <select class="form-control select2" name="reason" id="reason">
                                    <option value="">-Pilih alasan-</option>';

                                    foreach ($reasons as $key => $value) {
                                        $modalContent = $modalContent.'<option value="'.$value->id.'">'.$value->name.'</option>';
                                    }
                                        
        $modalContent = $modalContent.'</select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Keterangan</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control form-control-sm" name="notes" id="notes"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Ingatkan saya kembali</label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                               <input type="radio" value="Y" id="remember1" name="remember"> Ya
                               <input type="radio" value="N" id="remember2" name="remember"> Tidak
                            </div>
                        </div>
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json([
            'modalContent' => $modalContent,
        ]);
    }

    public function listAlumniProspectMaterial(Request $request, $alumniprospectmaterialId)
    {
        $requestDataTableMarketingtarget = $request;
        return MarketingService::listAlumniProspectMaterial($requestDataTableMarketingtarget, $alumniprospectmaterialId);
        
    }

    public function manageAlumniProspectMaterialStore(ManageAlumniProspectMaterialRequest $request)
    {
        try {

            $user = Auth::user()->id;
            $requestAlumniProspectMaterial = $request->validated();
            $requestAlumniProspectMaterial['user'] = $user;

    
            $results = MarketingService::manageAlumniProspectMaterialStore($requestAlumniProspectMaterial);
            return ResponseFormatter::success($results);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal kelola jamaah'
            ]);
        }
    }

    // REPORT
    public function laporanPelaksanaanIklan()
    {
        $data   = [
            'title'     => 'Report Marketing',
            'sub_title' => 'Laporan Pelaksanaan Iklan',
        ];

        return view('marketings/laporan/pelaksanaan_iklan/index', $data);
    }

    public function simpanLaporanIklan(Request $request)
    {
        $doSimpan   = MarketingService::doSimpanLaporanIklan($request->all());
        
    }

    public function loadModalGenerateAlumni()
    {
        // sleep(1);

        #get data cs
        $cs = Employee::getCustomerServices();
        
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                <label class="col-sm-3 col-form-label">CS</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" name="cs[]" id="cs" multiple="multiple" required>
                                    ';

                                    foreach ($cs as $key => $value) {
                                        $modalContent = $modalContent.'<option value="'.$value->id.'">'.$value->cs.'</option>';
                                    }

        $modalContent = $modalContent.'</select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Jumlah Data</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control form-control-sm" name="jmlData" id="jmlData">
                                </div>
                            </div>

                            <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tahun</label> 
                                <div class="col-sm-9">
                                    <div class="form-group" id="data_5">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="form-control-sm form-control" name="start" placeholder="Awal" id="startYear">
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="form-control-sm form-control" name="end"  placeholder="Akhir" id="endYear"/>
                                    </div>
                                 </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json([
            'modalContent' => $modalContent,
        ]);

    }
}
