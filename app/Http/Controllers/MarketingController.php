<?php

namespace App\Http\Controllers;

use App\Helpers\Months;
use App\Helpers\NumberFormat;
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
use App\Models\MarketingTarget;
use App\Models\Program;
use App\Models\Reason;
use App\Models\DetailMarketingTarget;
use App\Models\PicDetailMarketingTarget;
use App\Models\PicDetailMarketingTargetListJamaah;
use App\Services\MarketingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class MarketingController extends Controller
{
    public function target()
    {
        
        return view('marketings.targets',[
            'title' => 'Laporan Umrah'
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
       
        // $detailMatketingTargets = MarketingService::detailMarketingTarget($marketingTargetId);

        return view('marketings.detail-marketing-target', [
            'title' => 'Detail Target Marketing',
            'marketingTargetId' => $marketingTargetId,
            // 'detailMatketingTargets' => $detailMatketingTargets
        ]);

    }


    public function singkronRealisasi(Request $request)
    {
        DB::beginTransaction();
        try {

            $id =  $request->id;

            #get data taregt by id 
            $target = MarketingTarget::select('id','year','total_target','total_realization','total_difference')->where('id', $id)->first();

            #get data realiasai dari API umhaj
            $formData['year'] = $target->year;
            $res_realiasi = MarketingService::getRelisasiUmrah($formData);

            $res_umrah    = $res_realiasi['data'];


            #pecah data realisasi dari API
            $res_data_umrah = [];
            foreach ($res_umrah as $key =>  $value) {
                #update total realisasi target marketing
                foreach ($value['umrah'] as $umrah) {
                    $res_data_umrah[] = $umrah;
                }
            }

            // return $res_data_umrah;

            $res = [];
            foreach($res_data_umrah as $value){
                $detailtarget     = DetailMarketingTarget::getProgramByYearAndMonth($target->year, $value['bulan'], $value['tipe']);

                $data1            = new Collection($value);
                $data2            = new Collection($detailtarget);
                
                #merge dan filter, yang hanya memiliki atribut id saja
                $mergedData = $data1->merge($data2);
                $res[] = $mergedData;
            }

           // Menghapus entri dengan id kosong
            $res_data = array_filter($res, function($entry) {
                return !empty($entry['id']);
            });

           #update detail target marketing
           foreach($res_data as $value){
            $DetailMarketingTarget =  DetailMarketingTarget::where('id', $value['id'])->first();

            $DetailMarketingTarget->update([
                    'realization' => $value['realiasi'],
                    'difference'  => $value['realiasi'] - $DetailMarketingTarget->target
                ]);

                #simpan pic per program dari API
                foreach ($value['pic'] as $pic) {
                    
                    #jika data pic detail where detailed_marketing_id && employee_id sudah ada, maka update saja
                    $PicDetailMarketingTarget = PicDetailMarketingTarget::where('detailed_marketing_target_id', $DetailMarketingTarget->id)->where('employee_id', $pic['kpi_percik_employee_id'])->first();
                    if ($PicDetailMarketingTarget) {
                        $PicDetailMarketingTarget->update([
                            'realization' => $pic['realisasi']
                        ]);

                    }else{

                        #jika belum ada maka buat baru
                        PicDetailMarketingTarget::create([
                            'detailed_marketing_target_id' => $DetailMarketingTarget->id,
                            'employee_id' => $pic['kpi_percik_employee_id'],
                            'realization' => $pic['realisasi']
                        ]);

                    }

                    PicDetailMarketingTargetListJamaah::where('pic_detailed_marketing_target_id', $PicDetailMarketingTarget->id)->delete();
                    foreach($pic['list_jamaah'] as $jamaah){
                        $this->savePicDetailMarketingTargetListJamaah($jamaah, $PicDetailMarketingTarget->id, $DetailMarketingTarget);
                    }

                }

           }


            DB::commit();
            return ResponseFormatter::success([
                'message' => 'Sukses singkornisasi'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' =>  'Terjadi kesalahan !'
            ]);
        }
    }

    public function savePicDetailMarketingTargetListJamaah($jamaah, $PicDetailMarketingTargetId,$DetailMarketingTarget)
    {
        
        PicDetailMarketingTargetListJamaah::create([
            'pic_detailed_marketing_target_id' => $PicDetailMarketingTargetId,
            'id_member' => $jamaah['ID_MEMBER'],
            'name' => $jamaah['NAMA'],
            'is_alumni' => $jamaah['is_alumni'],
            'sumber' => $jamaah['sumber'],
            'month_number' => $DetailMarketingTarget->month_number,
            'program_id' => $DetailMarketingTarget->program_id,
            'marketing_target_id' => $DetailMarketingTarget->marketing_target_id,
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

    public function reportUmrahBulanan($marketingTargetId)
    {
        // $marketing_target_id = request()->id;
        $marketing = MarketingTarget::select('year')->where('id', $marketingTargetId)->first();
        

        $data   = [
            'title'     => 'Laporan Umrah Bulanan Tahun '.$marketing->year,
            'sub_title' => 'Laporan Umrah Bulanan',
        ];


        return view('marketings/laporan/report-umrah-bulanan', $data);

    }

    

    public function pencapaianBulanan()
    {
        try {
            
            $id  = request()->id;

            $startDate  = request()->start;
            $endDate    = request()->end;

            // format number 
            $fn  = new NumberFormat();

            $list_programs = MarketingTarget::getProgramBytargetBulanan($id);
            $umrah_prbulan = MarketingTarget::getPencapaianUmrahPerBulanByTahun($id);
            $umrah_program = MarketingTarget::getPencapaianUmrahPerProgramByTahun($id);
            $umrah_per_pic = MarketingTarget::getPencapaianUmrahPerPicByTahun($id);
            $umrah_per_sumber = MarketingTarget::getPencapaianUmrahPerSumber($id);
            $umrah_per_alumni = MarketingTarget::getPencapaianUmrahAlumni($id);
            
            if ($startDate != '' AND $endDate != '') {

                $startDate      = Carbon::parse($startDate)->format('Y-m-d');
                $endDate        = Carbon::parse($endDate)->format('Y-m-d');

                $carbonStartDate = Carbon::parse($startDate);
                $startDate       = $carbonStartDate->month;

                $carbonEndDate = Carbon::parse($endDate);
                $endDate       = $carbonEndDate->month;

                $umrah_per_pic = $umrah_per_pic->whereBetWeen('b.month_number',[$startDate, $endDate]);
                $umrah_program = $umrah_program->whereBetWeen('a.month_number',[$startDate, $endDate]);
                $umrah_prbulan = $umrah_prbulan->whereBetWeen('a.month_number',[$startDate, $endDate]);
                $list_programs = $list_programs->whereBetWeen('a.month_number',[$startDate, $endDate]);
                $umrah_per_sumber = $umrah_per_sumber->whereBetWeen('month_number',[$startDate, $endDate]);
                $umrah_per_alumni = $umrah_per_alumni->whereBetWeen('month_number',[$startDate, $endDate]);

            }

            $umrah_program =  $umrah_program->groupBy('b.name')->orderBy('realisasi','desc')->get();
            $res_umrah_program = [];
            foreach ($umrah_program as  $value) {

                $persentage_per_program = $fn->persentage($value->realisasi,$value->target);

				if ($persentage_per_program !== null) {
						$persentage_per_program  = $fn->persen($persentage_per_program);  
				}

                $res_umrah_program['label'][]    = $value->name;
				// $res_umrah_program['target'][]   = $value->target;
				$res_umrah_program['target'][]   = $value->target - $value->realisasi;
				$res_umrah_program['realisasi'][]   = $value->realisasi;
				$res_umrah_program['persentage_per_program'][]   = $persentage_per_program;
				$res_umrah_program['color'][] = '#d3d3d3';
            }

            $chart_umrah_program = array(
                "labels" => $res_umrah_program['label'],
                "datasets" => array(
                    array(
                        "label" => 'Realisasi',
                        "data"  => $res_umrah_program['realisasi'],
                        "color" => $res_umrah_program['color'],
                        "backgroundColor" => "#a3e1d4",
                        
                    ),
                        array(
                            "label" => 'Target',
                            "data"  => $res_umrah_program['target'],
                            "color" => $res_umrah_program['color'],
                            "backgroundColor" => "#DF9E0F",
                            
                        ),
                        array(
                            "label" => 'Persentase',
                            "data"  => $res_umrah_program['persentage_per_program'],
                            "type" => 'line',
                            "borderColor" => "#C00000",
                            "backgroundColor" => 'rgba(0, 0, 0, 0.0)',
                            
                        )
                    )
            );
           
            $umrah_prbulan =  $umrah_prbulan->groupBy('a.month_number', 'a.month_name')->orderBy('a.month_number','asc')->get();
            $res_umrah_bulan = [];
            foreach ($umrah_prbulan as  $value) {
                $persentage_per_bulan = $fn->persentage($value->realisasi,$value->target);

				if ($persentage_per_bulan !== null) {
						$persentage_per_bulan  = $fn->persen($persentage_per_bulan);  
				}

                $res_umrah_bulan['label'][]    = $value->month_name;
				// $res_umrah_bulan['target'][]   = $value->target;
				$res_umrah_bulan['target'][]   = $value->target - $value->realisasi;
				$res_umrah_bulan['realisasi'][]   = $value->realisasi;
				$res_umrah_bulan['persentage_per_bulan'][]   = $persentage_per_bulan;
				$res_umrah_bulan['color'][] = '#d3d3d3';
            }

            $chart_umrah_bulan = array(
                "labels" => $res_umrah_bulan['label'],
                "datasets" => array(
                    array(
                        "label" => 'Realisasi',
                        "data"  => $res_umrah_bulan['realisasi'],
                        "color" => $res_umrah_bulan['color'],
                        "borderColor" => "#a3e1d4",
                        "backgroundColor" => "#a3e1d4",
                        
                    ),
                        array(
                            "label" => 'Target',
                            "data"  => $res_umrah_bulan['target'],
                            "color" => $res_umrah_bulan['color'],
                            "backgroundColor" => "#DF9E0F",
                            
                        ),
                        array(
                            "label" => 'Persentase',
                            "data"  => $res_umrah_bulan['persentage_per_bulan'],
                            "type" => 'line',
                            "borderColor" => "#C00000",
                            "backgroundColor" => 'rgba(0, 0, 0, 0.0)',
                            
                        )
                    )
            );

            $umrah_per_pic = $umrah_per_pic->groupBy('c.name')->orderBy('realisasi','desc')->get();

            $res_umrah_per_pic = [];
            foreach ($umrah_per_pic as  $value) {

                $res_umrah_per_pic['label'][]    = $value->name;
				$res_umrah_per_pic['realisasi'][]   = $value->realisasi;
				// $res_umrah_per_pic['persentage_per_bulan'][]   = $persentage_per_bulan;
				$res_umrah_per_pic['color'][] = '#d3d3d3';
            }
            $chart_umrah_per_pic = array(
                "labels" => $res_umrah_per_pic['label'],
                "datasets" => array(
                        array(
                            "label" => 'Realisasi',
                            "data"  => $res_umrah_per_pic['realisasi'],
                            "color" => $res_umrah_per_pic['color'],
                            "backgroundColor" => "#a3e1d4",
                            
                        )
                    )
            );

            // Umrah per sumber 
            $umrah_per_sumber = $umrah_per_sumber->groupBy('sumber')->orderBy('realisasi','desc')->get();

            $res_umrah_per_sumber = [];
            foreach ($umrah_per_sumber as  $value) {

                $res_umrah_per_sumber['label'][]    = $value->sumber;
				$res_umrah_per_sumber['realisasi'][]   = $value->realisasi;
				// $res_umrah_per_sumber['persentage_per_bulan'][]   = $persentage_per_bulan;
				$res_umrah_per_sumber['color'][] = '#d3d3d3';
            }
            $chart_umrah_per_sumber = array(
                "labels" => $res_umrah_per_sumber['label'],
                "datasets" => array(
                        array(
                            "label" => 'Realisasi',
                            "data"  => $res_umrah_per_sumber['realisasi'],
                            "color" => $res_umrah_per_sumber['color'],
                            "backgroundColor" => "#a3e1d4",
                        )
                    )
            );


            $list_programs =$list_programs->orderBy('b.sequence','asc')->get();

           // all total 
            $total_target = collect($list_programs)->sum(function($q){
                return $q->target;
            });
            $total_realisasi = collect($list_programs)->sum(function($q){
                return $q->realisasi;
            });
            $total_selisih = $total_realisasi - $total_target;

            $persentage_total_pencapaian = $fn->persentage($total_realisasi,$total_target);
            if ($persentage_total_pencapaian !== null) {
                    $persentage_total_pencapaian  = $fn->persen($persentage_total_pencapaian);  
            }

            $umrah_per_alumni = $umrah_per_alumni->groupBy('is_alumni')->get();

            $baru           = $umrah_per_alumni->filter(function($item){
                return $item->is_alumni == 1;
            });

            $sum_baru      = collect($baru)->sum(function($q){
                return $q->jamaah;
            });

            $alumni           = $umrah_per_alumni->filter(function($item){
                return $item->is_alumni > 1;
            });

            $sum_alumni      = collect($alumni)->sum(function($q){
                return $q->jamaah;
            });

            $res_alumni[] = [
                'jamaah_baru' => $sum_baru,
                'jamaah_alumni' => $sum_alumni
            ];

            $res_umrah_per_kelompok_jamaah = [];
            foreach ($res_alumni as $key =>   $value) {

                // $res_umrah_per_kelompok_jamaah['label'][]    = 'Kelompok';
				$res_umrah_per_kelompok_jamaah['jamaah_baru'][]   = $value['jamaah_baru'];
				$res_umrah_per_kelompok_jamaah['jamaah_alumni'][]   = $value['jamaah_alumni'];
				// $res_umrah_per_kelompok_jamaah['persentage_per_bulan'][]   = $persentage_per_bulan;
				$res_umrah_per_kelompok_jamaah['color'][] = '#d3d3d3';
            }
            $chart_umrah_per_alumni = array(
                "labels" => ['Jamaah Baru', 'Alumni'],
                "datasets" => array(
                        array(
                            "label" => 'Jamaah baru',
                            "data"  => $res_umrah_per_kelompok_jamaah['jamaah_baru'],
                            "color" => $res_umrah_per_kelompok_jamaah['color'],
                            "backgroundColor" => "#a3e1d4",
                        ),
                        array(
                            "label" => 'Alumni',
                            "data"  => $res_umrah_per_kelompok_jamaah['jamaah_alumni'],
                            "color" => $res_umrah_per_kelompok_jamaah['color'],
                            "backgroundColor" => "#DF9E0F",
                        )
                    )
            );


            // get data rincian table 
            $rincian_tables = $this->getReportUmrahBulanan($id, $startDate, $endDate, $fn);

            return ResponseFormatter::success([
                'chart_umrah_program' => $chart_umrah_program,
                'chart_umrah_bulan' => $chart_umrah_bulan,
                'chart_umrah_per_pic' => $chart_umrah_per_pic,
                'total_target' => $fn->decimalFormat($total_target),
                'total_realisasi' => $fn->decimalFormat($total_realisasi),
                'total_selisih' => $fn->decimalFormat($total_selisih),
                'persentage_total_pencapaian' => $persentage_total_pencapaian,
                'chart_umrah_per_sumber' => $chart_umrah_per_sumber,
                // 'umrah_baru' => $sum_baru,
                // 'umrah_alumni' => $sum_alumni,
                'chart_umrah_per_alumni' => $chart_umrah_per_alumni,
                'rincian_tables' => $rincian_tables
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Terjadi kesalahan!'
            ]);
        }
    }

    public function getReportUmrahBulanan($marketingTargetId, $startDate, $endDate, $fn)
    {
        // $marketing_target_id = request()->id;
        $marketing = MarketingTarget::select('year')->where('id', $marketingTargetId)->first();
        // $year      = $marketing->year;
        $targetMarketing = MarketingTarget::getReportUmrahBulanan($marketingTargetId);

        if ($startDate != '' AND $endDate != '') {

            // $startDate      = Carbon::parse($startDate)->format('Y-m-d');
            // $endDate        = Carbon::parse($endDate)->format('Y-m-d');

            // $carbonStartDate = Carbon::parse($startDate);
            // $startDate       = $carbonStartDate->month;

            // $carbonEndDate = Carbon::parse($endDate);
            // $endDate       = $carbonEndDate->month;

            $targetMarketing = $targetMarketing->whereBetWeen('a.month_number',[$startDate, $endDate]);

        }

        $targetMarketing = $targetMarketing->groupBy('a.month_name','a.month_number')->orderBy('a.month_number','asc')->get();
        
        #get jumlah program yang ada
        $programs = Program::select('id','name','color')->where('is_active','Y')->orderBy('sequence','asc')->get();
        $countProgram = count($programs);

        $res_target = [];
        
        foreach ($targetMarketing as $key => $value) {
            
            $list_programs = MarketingTarget::getProgramBytargetBulanan($marketingTargetId);
            $list_programs = $list_programs->where('a.month_number', $value->month_number)->orderBy('b.sequence','asc')->get();
            $res = [];

            foreach ($list_programs as  $list) {
                $persentage_jml_res = $fn->persentage($list->realisasi,$list->target);
                if ($persentage_jml_res !== null) {
                    $persentage_jml_res  = $fn->persen($persentage_jml_res);  
                }

                $res[] = [
                    'program' => $list->program,
                    'target' => $list->target,
                    'realisasi' => $list->realisasi,
                    'selisih' => $list->selisih,
                    'persentage_jml_res' => $persentage_jml_res,
                    'color' => $list->color,
                ];
            }


            // jumlah target
            $jml_target = collect($res)->sum(function($q){
                return $q['target'];
            });
            // jumlah realisasi
            $jml_realisasi = collect($res)->sum(function($q){
                return $q['realisasi'];
            });

            // jumlah realisasi
            $jml_selisih = collect($res)->sum(function($q){
                return  $q['realisasi'] - $q['target'];
            });

            // jml per bulan nya
            $persentage_jml_pencapaian = $fn->persentage($jml_realisasi,$jml_target);
            if ($persentage_jml_pencapaian !== null) {
				$persentage_jml_pencapaian  = $fn->persen($persentage_jml_pencapaian);  
			}


            $res_target[] = [
                'color' => Months::monthColor($value->month_number),
                'nomor_bulan' => $value->month_number,
                'bulan' => $value->month_name,
                'target' => $value->terget,
                'realisasi' => $value->realisasi,
                'selisih' => $value->selisih,
                'persentage_jml_pencapaian' => $persentage_jml_pencapaian,
                'list_program' => $res,
                'jml_target' => $jml_target,
                'jml_realisasi' => $jml_realisasi,
                'jml_selisih' => $jml_selisih,
                'count_list_program' => count($res)
            ];
        }


        // all total 
        $total_target = collect($res_target)->sum(function($q){
            return $q['jml_target'];
        });
        $total_realisasi = collect($res_target)->sum(function($q){
            return $q['jml_realisasi'];
        });
        $total_selisih = collect($res_target)->sum(function($q){
            return $q['jml_selisih'];
        });

        $persentage_total_pencapaian = $fn->persentage($total_realisasi,$total_target);
            if ($persentage_total_pencapaian !== null) {
				$persentage_total_pencapaian  = $fn->persen($persentage_total_pencapaian);  
			}

        $html = "";
        foreach ($res_target as  $value) {
            $rowspanBulan = $value['count_list_program'] + 1;

            $html .= '<tr style="background-color: #FFFFFF">';
                $html .= '<td rowspan='.$rowspanBulan.' style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px">'.$value['nomor_bulan'].'</td>';
                $html .= '<td rowspan='.$rowspanBulan.' style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px">'.$value['bulan'].'</td>';
            $html .= '</tr>';

            $no = 1;
            foreach ($value['list_program'] as $program) {
                $html .= '<tr style="background-color: '.$program['color'].'">';
                    $html .= '<td >'.$no++.'.'.$program['program'].'</td>';
                    $html .= '<td style="text-align: right">'.$program['target'].'</td>';
                    $html .= '<td style="text-align: right">'.$program['realisasi'].'</td>';
                    $html .= '<td style="text-align: right">'.$program['selisih'].'</td>';
                    $html .= '<td style="text-align: right">'.$program['persentage_jml_res'].' %</td>';
                $html .= '</tr>';
            }

            $html = $html.'<tr>';
            $html = $html.'<th colspan="3" style="text-align: right">Jumlah</th>';
            $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($value['jml_target']).'</th>';
            $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($value['jml_realisasi']).'</th>';
            $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($value['jml_selisih']).'</th>';
            $html = $html.'<th style="text-align: right">'.$value['persentage_jml_pencapaian'].' %</th>';
            $html = $html.'</tr>';
        }

        $html = $html.'<tr>';
        $html = $html.'<th colspan="3" style="text-align: right">Total</th>';
        $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($total_target).'</th>';
        $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($total_realisasi).'</th>';
        $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($total_selisih).'</th>';
        $html = $html.'<th style="text-align: right">'.$fn->decimalFormat($persentage_total_pencapaian).' %</th>';
        $html = $html.'</tr>';

        return $html;

    }

    public function reportHaji()
    {
    
        return view('marketings.laporan.report-haji',[
            'title' => 'Laporan Haji',
        ]);
    }

    public function settingTargetHaji()
    {
    
        return view('marketings.setting-target-haji',[
            'title' => 'Setting Target Haji',
        ]);
    }

    
    public function loadModalTargetHaji()
    {
        // sleep(1);

        #get data bulan 
        $months = Months::Months();
        
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"><b>Tahun</b></label>
                                <div class="col-sm-9">
                                    <input type="hidden" name="_token" value="'.csrf_token().'">
                                    <input type="number" class="form-control form-control-sm" name="year" id="year">
                                </div>
                            </div>';
                                foreach ($months as $key => $value) {
                                    $modalContent = $modalContent.'<div class="form-group row">
                                        <label class="col-sm-3 col-form-label">'.$value['month'].'</label>
                                        <div class="col-sm-9">';
        
                                    $modalContent = $modalContent.'<input type="number" class="form-control form-control-sm" name="month[]" id="month">';
                                    $modalContent = $modalContent.'
                                                                </div>
                                                            </div>';
                                    }

        $modalContent = $modalContent.'
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json([
            'modalContent' => $modalContent,
        ]);

    }

    public function saveTargetHaji(Request $request)
    {
        try {

           // save target haji
           $months['month'] = $request->months;

           array_unshift($months['month'], '');
           unset($months['month'][0]);

           $response = MarketingService::saveTargethajiToUmhaj($request->year, $months['month']);

           return $response;

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            return ResponseFormatter::error([
                'message' => 'Gagal menyimpan target haji'
            ]);

        }
    }


    // AREA PROGRAM KERJA
    // 10 JULI 2024
    // NOTE : PEMBUATAN DASHBOARD PROGRAM KERJA U/ MARKETING
    public function marketing_programKerja_dashboard()
    {
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('marketing')) {
            $data   = array(
                "title"         => "Marketing - Program Kerja - Dashboard",
                "sub_title"     => "Dashboard Program Kerja Marketing",
            );

            return view('marketings.programKerja.dashboard', $data);
        }
    }

    public function marketing_programKerja_sasaran()
    {
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole(('marketing'))) {
            $data   = array(
                "title"         => "Marketing - Program Kerja - Sasaran",
                "sub_title"     => "Program Kerja - Sasaran",
                "current_role"  => Auth::user()->getRoleNames()[0],
                "current_id"    => Auth::user()->id,
            );

            return view('marketings.programKerja.sasaran.index', $data);
        }
    }

    public function marketing_programKerja_listSasaran(Request $request)
    {
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole(('marketing'))) {
            $getData    = MarketingService::doGetListSasaran();

            if(!empty($getData)) {
                $header     = $getData['header'];
                $detail     = $getData['detail'];

                if(count($header) > 0) {
                    for($i = 0; $i < count($header); $i++) {
                        $data[]     = array(
                            $i + 1,
                            $header[$i]->pkt_title,
                            $header[$i]->pkt_total_job,
                            $header[$i]->pkt_year,
                            "<button class='btn btn-sm btn-primary' title='Preview' value='" . $header[$i]->pkt_uuid . "' onclick='show_modal(`modalForm`, `edit`, this.value)'><i class='fa fa-eye'></i></button>",
                        );
                    }
                } else {
                    $data   = [];
                }
            } else {
                $data   = [];
            }

            $output     = array(
                "draw"  => "1",
                "data"  => $data,
            );
        }

        return Response::json($output, 200);
    }

    public function marketing_programKerja_listGroupDivision(Request $request)
    {
        $getData    = MarketingService::getListGroupDivision($request->all()['sendData']);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Berhasil Diambil",
                "data"      => $getData,
            );
        } else {
            $output    = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Tidak Ditemukan",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function marketing_programKerja_simpanSasaran($jenis, Request $request)
    {
        $data   = [
            "sendData"  => $request->all()['sendData'],
            "jenis"     => $jenis,
            "user_id"   => Auth::user()->id,
            "ip"        => $request->ip()
        ];

        $doSimpan   = MarketingService::doSimpanSasaran($data, $jenis);

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Data Sasaran Baru Telah Ditambahkan" : "Berhasil Mengubah Data Sasarn",
                    ],
                ],
            );
        } else if($doSimpan['status'] == 'gagal'){
            $output     = array(
                "status"    => 500,
                "message"   => false,
                "alert"     => [
                    "icon"  => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $jenis == 'add' ? "Data Sasaran Gagal Ditambahkan" : "Data Sasaran Gagal Diubah",
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    public function marketing_programKerjas_dataSasaran($id, Request $request)
    {
        $data       = array(
            "sasaranID" => $id,
            "ip"        => $request->ip(),
        );
        $getData    = MarketingService::getDataSasaran($data);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "message"   => "Gagal Ambil Data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : AREA PROGRAM
    public function marketing_programKerja_program()
    {
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole(('marketing'))) {
            $data   = array(
                "title"         => "Marketing - Program Kerja - Program",
                "sub_title"     => "Program Kerja - Program",
                "current_role"  => Auth::user()->getRoleNames()[0],
                "current_id"    => Auth::user()->id,
            );

            return view('marketings.programKerja.program.index', $data);
        }
    }

    // 11 JULI 2024
    // NOTE : AMBIL LIST SELECT SASARAM UNTUK FUNGSI TAMBAH PROGRAM BARU
    public function marketing_programKerja_listSelectSasaranMarketing(Request $request)
    {
        $getData    = MarketingService::doGetListSasaran();
        if(!empty($getData))
        {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "message"   => "Terjadi Kesalahan",
                "data"      => []
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN PROGRAM BARU
    public function marketing_programKerja_simpanProgram($jenis, Request $request)
    {
        $data   = [
            "jenis"     => $jenis,
            "sendData"  => $request->all()['sendData'],
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0],
            "ip"        => $request->ip(),
        ];

        $doSimpan   = MarketingService::doSimpanProgram($data);

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => $jenis == 'add' ? "Berhasil Menambahkan Data Program Baru" : "Berhasil Mengubah Data Program",
                    ],
                ],
            );
        } else if($doSimpan['status'] == 'transaction') {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Tidak Bisa mengubah data ini, dikarenakan ada relasi dengan jenis pekerjaan yang telah dibuat",
                    ],
                ],
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $jenis == 'add' ? "Gagal Menambahkan Data Program Baru" : "Gagal Mengubah Data Program",
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : UNTUK DATATABLE LIST PROGRAM MARKETING
    public function marketing_programKerja_listProgramMarketing(Request $request)
    {
        $getData    = MarketingService::getListProgramMarketing($request->all()['sendData']);

        if(count($getData['header']) > 0 || count($getData['detail'])) {
            for($i = 0; $i < count($getData['header']); $i++) {
                $ke         = $i + 1;
                $prog_id    = $getData['header'][$i]->pkb_id;
                $prog_title = $getData['header'][$i]->pkb_title;
                $prog_sasaran_group_divisi  = $getData['header'][$i]->group_division_name;
                $prog_total_target  = !empty($getData['header'][$i]->total_target) ? $getData['header'][$i]->total_target : 0;
                $prog_bulan         = date('F', strtotime($getData['header'][$i]->program_date));

                $data[]     = array(
                    $ke,
                    $prog_title,
                    $prog_bulan,
                    $prog_sasaran_group_divisi,
                    $prog_total_target,
                    
                    "<button class='btn btn-sm btn-primary' value='".$prog_id."' title='Edit Data' onclick='show_modal(`modalProgram`, `edit`, this.value)'><i class='fa fa-edit'></i></button> 
                        
                    <button type='button' class='btn btn-sm btn-success' value='" . $prog_id . "' title='Lihat Data' onclick='show_modal(`modalDetailProgram`, `view`, this.value)'><i class='fa fa-eye'></i></button>",
                );
            }
        } else {
            $data    = [];
        }
        
        $output     = array(
            "draw"  => 1,
            "data"  => $data,
        );

        return Response::json($output, 200);
    }

    public function marketing_programKerja_listSelectedProgramMarketing(Request $request)
    {
        $getData    = MarketingService::getListProgramMarketing($request->all()['sendData']);

        if(!empty($getData)) {
            $data       = [
                "programID"                 => $getData['header'][0]->pkb_id,
                "programTitle"              => $getData['header'][0]->pkb_title,
                "program_masterProgramID"   => $getData['header'][0]->pkb_master_program_id,
                "program_sasaranID"         => $getData['header'][0]->sasaran_id,
                "program_sasaranSequence"   => $getData['header'][0]->sasaran_sequence,
                "program_bulan"             => $getData['header'][0]->program_date,
                "program_detail"            => $getData['detail'],
            ];
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil",
                "data"      => $data,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Tidak ada data",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 12 JULI 2024
    // NOTE : AMBIL DATA PROGRAM UNTUK JENIS PEKERJAAN
    public function marketing_programKerja_dataProgram(Request $request)
    {
        $getData    = MarketingService::doGetDataProgram($request->all()['sendData']);
        
        if(!empty($getData)) {
            $output     = array(
                "status"        => 200,
                "success"       => true,
                "message"       => "Data Berhasil Diambil",
                "data"          => $getData,
            );
        } else {
            $output     = array(
                "status"        => 500,
                "success"       => false,
                "message"       => "Data Gagal Diambil",
                "data"          => $getData,
            );
        }

        return Response::json($output, $output['status']);
    }

    // 13 JULI 2024
    // NOTE : PEMBUATAN DATA PROGRAM DETAIL
    public function marketing_programKerja_dataProgramDetail($programID, Request $request)
    {
        $getData    = MarketingService::doGetDataProgramDetail($programID);

        if(!empty($getData)) {
            $output     = array(
                "status"        => 200,
                "success"       => true,
                "message"       => "Data Berhasil Diambil",
                "data"          => $getData,
            );
        } else {
            $output     = array(
                "status"        => 500,
                "success"       => false,
                "message"       => "Data Gagal Diambil",
                "data"          => $getData,
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : SIMPAN DATA JENIS PEKERJAAN
    public function marketing_programKerja_doSimpanJenisPekerjaan(Request $request)
    {
        $data_simpan    = [
            "sendData"  => $request->all()['sendData'],
            "ip"        => $request->ip(),
            "user_id"   => Auth::user()->id,
            "user_role" => Auth::user()->getRoleNames()[0],
        ];
        
        $doSimpan   = MarketingService::doSimpanJenisPekerjaan($data_simpan);
        // $doSimpan    = [
        //     'status'    => 'berhasil',
        //     'errMsg'    => ''
        // ];

        if($doSimpan['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Data Berhasil Disimpan",
                        "errMsg"    => "",
                    ],
                ],
            );
        } else if($doSimpan['status'] == 'gagal') {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Data Gagal Disimpan",
                        "errMsg"    => $doSimpan['errMsg'],
                    ],
                ],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function marketing_programKerja_jpkDataEventsCalendar(Request $request)
    {
        $data_kirim     = array(
            "start_date"    => $request->all()['sendData']['start_date'],
            "end_date"      => $request->all()['sendData']['end_date'],
        );

        $getData        = MarketingService::getDataEventsCalendarJpk($data_kirim);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Ambil Data",
                "data"      => $getData,
            );
        }
    
        return Response::json($output, $output['status']);
    }

    public function marketing_programKerja_jpkDataDetailEventsCalendar($id)
    {
        $getData    = MarketingService::getDataDetaiLEventsCalendar($id);

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Berhasil Diambil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data yang dicari tidak ada",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    
    // 15 JULI 2024
    public function marketing_programKerja_deleteProgram($id, Request $request)
    {
        $data_kirim     = [
            "programID" => $id,
            "ip"        => $request->ip(),
        ];

        $doDelete   = MarketingService::doDeleteProgram($data_kirim);

        if($doDelete['status'] == 'berhasil')
        {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"      => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "text"      => "Berhasil Menghapus Data Program",
                        "errMsg"    => [],
                    ],
                ],
            );
        } else if($doDelete['status'] == 'gagal') {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => "Gagal Menghapus Data Program",
                        "errMsg"    => $doDelete['errMsg'],
                    ],
                ],
            );
        } else if($doDelete['status'] == 'data_ada') {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"      => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "text"      => $doDelete['errMsg'],
                        "errMsg"    => $doDelete['errMsg'],
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    // NOTE : UNTUK DASHBOARD
    public function marketing_programKerja_dashboardSasaran()
    {
        $getData    = MarketingService::doGetDashboardSasaran();

        if(count($getData) > 0) {
            $output     = [
                "status"    => 200,
                "success"   => true,
                "message"   => "Data Berhasil Dimuat",
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "status"    => 500,
                "success"   => false,
                "message"   => "Data Gagal Dimuat",
                "data"      => [],
            ];
        }

        return Response::json($output, $output['status']);
    }

    public function marketing_programKerja_masterProgram()
    {
        $getData    = MarketingService::getMasterProgram();

        if(!empty($getData)) {
            $output     = [
                "success"   => true,
                "status"    => 200,
                "message"   => "Master Program Berhasil Dimuat",
                "data"      => $getData,
            ];
        } else {
            $output     = [
                "success"   => false,
                "status"    => 404,
                "message"   => "Tidak Ada Data Yang Bisa Ditampilkan",
                "data"      => $getData,
            ];
        }

        return Response::json($output, $output['status']);
    }

    // 16 JULI 2024
    // NOTE : DELETE JENIS PEKERJAAN
    public function marketing_programKerja_deleteJenisPekerjaan($id, Request $request)
    {
        $sendData   = [
            "jpk_ID"        => $id,
            "ip"            => $request->ip(),
            "current_user"  => Auth::user()->id,
            "current_role"  => Auth::user()->getRoleNames()[0],
        ];

        $doDelete   = MarketingService::doDeleteJenisPekerjaan($sendData);

        if($doDelete['status'] == 'berhasil') {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "alert"     => [
                    "icon"  => "success",
                    "message"   => [
                        "title"     => "Berhasil",
                        "message"   => "Data Berhasil Dihapus",
                        "errMsg"    => [],
                    ],
                ],
            );
        } else if($doDelete['status'] == 'gagal') {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "alert"     => [
                    "icon"  => "error",
                    "message"   => [
                        "title"     => "Terjadi Kesalahan",
                        "message"   => "Gagal Menghapus Data",
                        "errMsg"    => $doDelete['errMsg'],
                    ],
                ],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 19 JULY 2024
    // NOTE : DASHBOARD JENIS PEKERJAAN
    public function marketing_programKerja_jenisPekerjaanDahsboard()
    {
        $data   = [
            "title"         => "Dashboard - Jenis Pekerjaan",
            "sub_title"     => "Dashboard - Jenis Pekerjaan",
            "current_role"  => Auth::user()->getRoleNames()[0]
        ];

        return view('marketings.programKerja.jenisPekerjaan.index', $data);
    }

    public function importTargetUmrahByTahun()
    {
        try {

            DB::beginTransaction();
            
            // save target haji
            $marketingTargetId = request()->id;

            $marketingTarget = MarketingTarget::select('year','id')->where('id', $marketingTargetId)->first();

            $formData['year'] = $marketingTarget->year; 

            $res       = MarketingService::getTargetBulanFromUmhajByTahun($formData);
            $res_umrah = $res['data']['umrah'];

            // save to table detailed_marketing_target
            foreach ($res_umrah as  $bulan) {
                foreach ($bulan['programs'] as  $program) {

                    // $cek_program = DB::table('programs')->where('id','!=', $program['erp_program_id'])->count();
                    // if ($cek_program > 0) {
                    //     return ResponseFormatter::error([
                    //         'message' => $cek_program
                    //     ]);
                    // }else{

                    // }

                    $DetailMarketingTarget      =  new DetailMarketingTarget();
                    $DetailMarketingTarget->id = Str::random(30);
                    $DetailMarketingTarget->marketing_target_id = $marketingTarget->id;
                    $DetailMarketingTarget->program_id = $program['erp_program_id'] ?? '';
                    $DetailMarketingTarget->month_number = (int) $program['bulan'];
                    $DetailMarketingTarget->month_name   = Months::monthName($program['bulan']);
                    $DetailMarketingTarget->target       = (int) $program['kapasitas'];
                    $DetailMarketingTarget->realization = 0;
                    $DetailMarketingTarget->difference  = 0;
                    $DetailMarketingTarget->jamaah_alumni = 0;
                    $DetailMarketingTarget->jamaah_baru = 0;
                    $DetailMarketingTarget->created_by   = Auth::user()->id;
                    $DetailMarketingTarget->updated_by   = Auth::user()->id;
                    $DetailMarketingTarget->save();
                    
                }
            }

            DB::commit();

            return ResponseFormatter::success([
                'message' => 'Berhasil menyimpan target umrah !'
            ]);

         } catch (\Exception $e) {
             DB::rollback();
             Log::channel('daily')->error($e->getMessage());
             return ResponseFormatter::error([
                 'message' => 'Gagal menyimpan target umrah'
             ]);
 
         }

    }

    public function marketing_programKerja_listDetailProgram($id)
    {
        $getData    = MarketingService::getListDetailProgram($id);

        if( !empty($getData['header']) && count($getData['detail']) > 0) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Berhasil Ambil Data Program Kerja Detail",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Gagal Ambil Data Program Kerja Detail",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }
}
