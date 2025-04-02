<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\GroupDivisionController;
use App\Http\Controllers\SubDivisionController;
use App\Http\Controllers\WorkPlanController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TarikDataController;
use App\Http\Controllers\SysUmhajController;
use App\Http\Controllers\WebsiteController as percikToursController;
use App\Http\Controllers\FinanceController as finance;
use App\Http\Controllers\noLoginController as noLogin;
use App\Models\Division;
use App\Services\ProgramKerjaService;
use App\Services\SysUmhajService;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    if(!empty(Auth::user()->id)) {
        return redirect('/dashboard');
    } else {
        return view('auth.login');
    }  
});

//route login index
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');

//route login store
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
//route logout


Route::group(['middleware' => ['auth']], function () {

    //route dashboard
    Route::prefix('dashboard')->group(function() {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getDataPresenceToday', [DashboardController::class, 'dashboard_getPresenceToday']);
        Route::get('/absensi_pulang', [DashboardController::class, 'index_pulang'])->name('absen.pulang');
        Route::get('/tarik_data', [DashboardController::class, 'index_tarik_data_presensi']);
        Route::prefix('absensi')->group(function(){
            Route::post('/{jenis}', [DashboardController::class, 'absensi_user']);
            Route::get('/get_user_presence', [DashboardController::class, 'absensi_ambil_data_user']);
        });
    });
    // Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class,'logout'])->name('logout.store');
    
    // NOTIFICATIONS
    Route::get('/notifications/show/user/{userId}',[NotificationController::class, 'showNotificationByUserLogin']);
     // Notification
     Route::get('/marketings/notifications/show/detail/user/notification/{notificationId}',[NotificationController::class, 'detailShowNotificationAlumni']);
	 
	 Route::get('/marketings/rencanakerja/report',[ProgramKerjaController::class, 'reportRencanaKerjaMarekting'])->name('marketings.rencancakerja.report');
	 Route::post('/marketings/rencanakerja/report/data',[ProgramKerjaController::class, 'getReportRencanaKerjaMarekting']);
	 Route::post('/marketings/rencanakerja/report/rinciankegiatan',[ProgramKerjaController::class, 'getRincianKegiatanByJenisPekerjaan']);

	 Route::post('/marketings/report/evaluasi',[ProgramKerjaController::class, 'getReportEvaluasiMarketing']);
	 Route::post('/marketings/report/evaluasi/kegiatan/rincian',[ProgramKerjaController::class, 'getRincianKegiatanByProgramBulanan']);
	 Route::post('/marketings/report/evaluasi/perbulan/perminggu',[ProgramKerjaController::class, 'getReportProgramPerMingguByBulan']);
	 Route::post('/marketings/report/evaluasi/perbulan/perminggu/list',[ProgramKerjaController::class, 'getDaftarKegitanHarianPerMinggu']);
	 
    //  GET EVALUASI MARKETING
     Route::get('/marketings/sasaran',[ProgramKerjaController::class, 'getSasaran']);
	 Route::post('/marketings/sasaran/programs',[ProgramKerjaController::class, 'getProgramKerjaBulananBySasaran']);
	 Route::post('/marketings/sasaran/programs/jenis/aktivitas/list',[ProgramKerjaController::class, 'getAktivitasHarianByJenisPekerjaan']);

    //marketing
    Route::prefix('marketings')->controller(MarketingController::class)->group(function(){
        Route::get('/target','target')->name('marketing.target');
        Route::post('/target','storeTarget')->name('marketing.target.store');

        Route::post('/target/singkronrealisasi','singkronRealisasi');
        
        // datatable
        Route::post('/target/list','listTarget');
        Route::get('/target/report/perbulan/pertahun/marketingtarget/{id}','reportUmrahBulanan');
        
        // detail target marketing
        Route::get('/target/detail/{marketingTargetId}','detailMarketingTarget');
        Route::post('/target/detail/{marketingTargetId}/store','detailMarketingTargetStore');
        Route::post('/target/detail/list/{detailMarketingTargetId}','detailListTarget');
        
        
        // bahan prospek
        Route::get('/prospectmaterial','prospectMaterial')->name('marketing.prospectmaterial');
        Route::post('/prospectmaterial/store','prospectMaterialStore')->name('marketing.prospectmaterial.store');
        Route::get('/prospectmaterial/modal/create','loadModalGenerateAlumni');

        // For CS
        Route::get('/alumniprospectmaterial','alumniProspectMaterialByAccountCS')->name('marketing.alumniprospectmaterial');
        Route::get('/alumniprospectmaterial/singkronisasi/{id}','singkronisasiDataAlumniUmrah')->name('marketing.singkronisasi');
        Route::get('/alumniprospectmaterial/{id}','detailAlumniProspectMaterialByAccountCS')->name('marketing.alumniprospectmaterial.detail');
        Route::get('/alumniprospectmaterial/detail/manage/modal/{detailId}','loadModalManageAlumniProspectMaterial');
        Route::post('/alumniprospectmaterial/detail/manage/store','manageAlumniProspectMaterialStore')->name('marketing.alumniprospectmaterial.store');
        Route::post('/alumniprospectmaterial/detail/list/{alumniprospectmaterialId}','listAlumniProspectMaterial');
        // laporan
        Route::get('/laporan/pelaksanaan_iklan','laporanPelaksanaanIklan')->name('marketing.laporan.iklan');
        Route::post('/laporan/trans/store/reportAds','simpanLaporanIklan')->name('marketing.trans.storeDataLaporanIklan');


        //modal 
        Route::get('modal/target','loadModalMarketingTarget');
        Route::get('modal/target/detail','loadModalDetailMarketingTarget');

        // Grafik Laporan Umrah Bulanan
        Route::post('/pencapaian/bulanan','pencapaianBulanan');
        Route::post('/pencapaian/bulanan/table/{marketingTargetId}','getReportUmrahBulanan');

        // HAJI
        Route::get('/haji/report','reportHaji')->name('marketings.haji.report');
        Route::get('/haji/target/create','settingTargetHaji')->name('marketings.haji.target');
        Route::post('/haji/target/save','saveTargetHaji');
        Route::get('modal/target/haji','loadModalTargetHaji');
		
        // PROGRAM KERJA
        Route::prefix('programKerja')->group(function(){
            Route::get('/', function(){
                return redirect('/marketings/programKerja/Dashboard');
            });
            Route::get('/Dashboard', [MarketingController::class, 'marketing_programKerja_dashboard'])->name('marketing.programkerja.dashboard');
            Route::get('/getListSasaran', [MarketingController::class, 'marketing_programKerja_dashboardSasaran']);
            Route::get('/getListDashboard', [MarketingController::class, 'marketing_programKerja_dashboardList']);

            // SASARAN
            Route::prefix('sasaran')->group(function(){
                Route::get('/', [MarketingController::class, 'marketing_programKerja_sasaran'])->name('marketing.programkerja.sasaran');
                Route::get('/listSasaranMarketing', [MarketingController::class, 'marketing_programKerja_listSasaran']);
                Route::get('/listGroupDivision', [MarketingController::class, 'marketing_programKerja_listGroupDivision']);
                Route::post('/simpanSasaran/{jenis}', [MarketingController::class, 'marketing_programKerja_simpanSasaran']);
                Route::get('/dataSasaran/{id}', [MarketingController::class, 'marketing_programKerjas_dataSasaran']);
            });
            // PROGRAM
            Route::prefix('program')->group(function(){
                Route::get('/', [MarketingController::class, 'marketing_programKerja_program'])->name('marketing.programkerja.program');
                Route::get('/listProgramMarketing', [MarketingController::class, 'marketing_programKerja_listProgramMarketing']);
                Route::get('/listSelectedProgramMarketing', [MarketingController::class, 'marketing_programKerja_listSelectedProgramMarketing']);
                Route::get('/listSelectSasaranMarketing', [MarketingController::class, 'marketing_programKerja_listSelectSasaranMarketing']);
                Route::post('/simpanProgram/{jenis}', [MarketingController::class, 'marketing_programKerja_simpanProgram']);
                Route::post('/deleteProgram/{id}', [MarketingController::class, 'marketing_programKerja_deleteProgram']);
                Route::get('/listMasterProgram', [MarketingController::class, 'marketing_programKerja_masterProgram']);
                Route::get('/listDetailProgram/{id}', [MarketingController::class, 'marketing_programKerja_listDetailProgram']);
                // FOR DASHBOARD
                Route::get('/listProgramMarketingByYear', [MarketingController::class, 'marketing_programKerja_listProgramMarketing_yearly']);
                Route::get('/listProgramMarketingByMonth', [MarketingController::class, 'marketing_programKerja_listProgramMarketing_monthly']);
                Route::get('/listProgramMarketingByWeek', [MarketingController::class, 'marketing_programKerja_listProgramMarketing_weekly']);
                Route::get('/listProgramMarketingByDay', [MarketingController::class, 'marketing_programKerja_listProgramMarketing_daily']);
            });
            // JENIS PEKERJAAN
            Route::prefix('jenisPekerjaan')->group(function(){
                Route::get('/', [MarketingController::class, 'marketing_programKerja_jenisPekerjaanDahsboard'])->name('marketing.jenisPekerjaan.index');
                Route::get('/dataProgram', [MarketingController::class, 'marketing_programKerja_dataProgram']);
                Route::get('/dataProgramDetail/{programID}', [MarketingController::class, 'marketing_programKerja_dataProgramDetail']);
                Route::post('/doSimpan', [MarketingController::class, 'marketing_programKerja_doSimpanJenisPekerjaan']);
                Route::get('/dataEventsCalendar', [MarketingController::class, 'marketing_programKerja_jpkDataEventsCalendar']);
                Route::get('/dataDetailEventsCalendar/{id}', [MarketingController::class, 'marketing_programKerja_jpkDataDetailEventsCalendar']);
                Route::post('/deleteJeniPekerjaan/{id}', [MarketingController::class, 'marketing_programKerja_deleteJenisPekerjaan']);
                Route::get('/listActUser', [MarketingController::class, 'marketing_programKerja_listActUser']);
            });
            // ADDITIONAL
            Route::prefix('master')->group(function(){
                Route::get('/getListPIC', [MarketingController::class, 'marketing_programKerja_dataPIC']);
            });
        });
		
        // AGENT
        Route::prefix('agent')->group(function(){
            Route::get('/', [MarketingController::class, 'marketing_agent_dashboard'])->name('marketing.agent');
            Route::get('/tarik_data_agent', [MarketingController::class, 'marketing_agent_tarik_data_agent']);
            Route::get('/tarik_data_agent_local', [MarketingController::class, 'marketing_agent_tarik_data_agent_local']);
            Route::post('/simpan_data/{jenis}', [MarketingController::class, 'marketing_agent_simpan_data']);
            Route::get('/ambil_data/{idAgent}', [MarketingController::class, 'marketing_agent_ambil_data']);
            Route::get('/ambil_data_tour_code/{tahun}', [MarketingController::class, 'marketing_agent_ambil_data_tour_code_by_tahun']);
            Route::post('/simpan_data/type_agent/{jenis}', [MarketingController::class, 'marketing_agent_simpan_data_type_agent']);
            Route::get('/ambil_data_act_agent/{idAgent}', [MarketingController::class, 'marketing_agent_ambil_data_act_agent']);
            Route::get('/tour_code', [MarketingController::class, 'marketing_agent_cari_tour_code']);
            Route::prefix('member')->group(function(){
                Route::get('/ambil_member_umhaj', [MarketingController::class, 'marketing_agent_ambil_data_member']);
                Route::get('/ambil_agent_act_jemaah', [MarketingController::class, 'marketing_agent_ambil_data_agent_act_jemaah']);
                Route::post('/simpan_member_umhaj/{jenis}', [MarketingController::class, 'marketing_agent_simpan_data_member']);
            });

            Route::prefix('master')->group(function(){
                Route::get('/periode', [MarketingController::class, 'marketing_master_agent_periode'])->name('marketing.masterAgent.periode');
                Route::post('/simpan_periode/{jenis}', [MarketingController::class, 'marketing_master_agent_periode_trans']);
            });
        });
        
    });

    Route::prefix('umhaj')->group(function(){
        Route::get('/', function(){
            return redirect('/umhaj/dashboard');
        });
        Route::get('/dashboard', [SysUmhajController::class, 'index_umhaj'])->name('umhaj.dashboard');
        Route::prefix('umrah')->group(function(){
            Route::get("/get_data", [SysUmhajController::class, 'umhaj_umrah_get_data'])->name('umhaj.umrah.get_data');
            Route::post("/get_data_chart_umrah", [SysUmhajController::class, 'umhaj_chart_umrah_data']);
            Route::get("/get_data_detail", [SysUmhajController::class, 'umhaj_umrah_get_data_detail'])->name('umhaj.umrah.get_data');
            Route::get("/list_program", [SysUmhajController::class, 'umhaj_umrah_get_list_program'])->name('umhaj.umrah.get_list_program');
            Route::get("/get_data_umrah_list/tahun/{tahun}", [SysUmhajController::class, 'umhaj_umrah_list'])->name('umhaj.umrah.list_data');
            Route::get("/get_data_umrah/tour_code", [SysUmhajController::class, 'umhaj_umrah_detail'])->name('umhaj.umhra.get_data_detail');
        });
        
        Route::prefix('member')->group(function(){
            Route::get('/get_data', [SysUmhajController::class, 'umhaj_member_get_data'])->name('umhaj.member.get_data');
            Route::post("/get_data_chart_member", [SysUmhajController::class, 'umhaj_chart_member_data']);
            Route::post('/get_data_detail', [SysUmhajController::class, 'umhaj_member_get_data_detail'])->name('umhaj.member.get_data.detail');
            Route::post('/get_data_member_v2', [SysUmhajController::class, 'umhaj_member_get_data_v2'])->name('umhaj.member.get_data.v2');
            Route::get('/get_jemaah_2_detail', [SysUmhajController::class, 'umhaj_member_get_data_detail_v2']);
            Route::post('/simpan_data_jemaah/{jenis}', [SysUmhajController::class, 'umhaj_member_simpan_data']);
        });

        Route::prefix('agent')->group(function() {
            Route::post('/get_data_agent', [SysUmhajController::class, 'umhaj_agent_get_data']);
        });

        Route::prefix('cs')->group(function(){
            Route::get('/get_data', [SysUmhajController::class, 'umhaj_cs_get_data'])->name('umhaj.cs.get_data');
        });

        Route::prefix('master')->group(function(){
            Route::get('/master_data_sumber', [SysUmhajController::class, 'umhaj_master_data_sumber']);
            Route::get('/data_wilayah/provinsi', [SysUmhajController::class, 'erp_master_wilayah_provinsi']);
            Route::get('/data_wilayah/kota', [SysUmhajController::class, 'erp_master_wilayah_kota']);
            Route::get('/data_wilayah/kecamatan', [SysUmhajController::class, 'erp_master_wilayah_kecamatan']);
            Route::get('/data_wilayah/kelurahan', [SysUmhajController::class, 'erp_master_wilayah_kelurahan']);
            Route::get('/master_data_program', [SysUmhajController::class, 'umhaj_master_data_program_umrah']);
            Route::get('/master_data_user_cs', [SysUmhajController::class, 'umhaj_master_data_cs']);
            Route::post("/master_data_jadwal_umrah", [SysUmhajController::class, 'umhaj_data_jadwal_umrah']);
        });
    });

     // Rencana Kerja
     Route::prefix('workplans')->controller(WorkPlanController::class)->group(function(){
        Route::get('','index')->name('marketing.workplans.index');
        Route::get('modal/create','loadModalWorkPlans');

    });

    Route::prefix('accounts')->group(function(){
        Route::get('/permissions',[PermissionController::class,'index'])->name('permissions.index')
            ->middleware('permission:permissions.index');

        Route::controller(UserController::class)->group(function(){
            Route::get('/users', 'index')->name('users.index')->middleware('permission:users.index');
            Route::get('/users/create', 'create')->name('users.create')->middleware('permission:users.create');
            Route::post('/users/store', 'store')->name('users.store');
            Route::prefix('userLog')->group(function(){
                Route::get('/', 'userLog')->name('accounts.user.log');
                Route::get('/dataTableUserLog', 'dataTableUserLog');
            });
            Route::get('/userLog', 'userLog')->name('accounts.user.log');
            Route::prefix('userProfiles')->group(function(){
                Route::get('/', 'userProfiles')->name('accounts.user.profile');
                Route::get('/ChangePasswordUser', 'ChangePasswordUser');
                Route::get('/CheckPasswordCurrentUser', 'CheckPasswordCurrentUser');
                Route::get('/getUserData', 'getUserData');
                Route::post('/updatePicture', 'updateProfilePicture')->name('accounts.changePicture');
                Route::get('/getDataUser', 'getDataUser');
                Route::get('/getDataLastActUser', 'getDataLastActUser');
            });
            // Route::get('/userProfiles/updateProfilePict', 'updateProfilePicture')->name('accounts.do.upload');
        });

        Route::controller(RoleController::class)->group(function(){
            Route::get('/roles', 'index')->name('roles.index')->middleware('permission:roles.index');
        });

        // Route::get('/userProfiles', UserController::class, 'userProfiles')->name('accounts.user.profile');
    });


    Route::prefix('master')->group(function(){
        // JABATAN
        Route::get('/dashboard', [EmployeesController::class, 'dashboard_master_jabatan'])->name('master.jabatan.index');
        // GROUP DIVISIONS
        Route::prefix('groupDivisions')->group(function(){
            Route::get('/', [GroupDivisionController::class, 'index'])->name('groupDivision.index');
            Route::get('/trans/get/dataGroupDivisions', [GroupDivisionController::class, 'tableGroupDivision'])->name('groupDivision.data.tableGroupDivisions');
    
            // TRANS ON MODAL
            // MODAL TRANS ADD
            Route::post('/trans/store/dataGroupDivisions/{jenis}', [GroupDivisionController::class , 'storeDataGroupDivision'])->name('groupDivision.trans.storeDataGroupDivision');
            // MODAL TRANS EDIT
            Route::get('/trans/get/modalDataGroupDivisions/{cari}', [GroupDivisionController::class, 'modalGetDataGroupDivisions'])->name('groupDivision.data.modalGroupDivisions');
            Route::post('/trans/store/modalDataGroupDivisions', [GroupDivisionController::class, 'storeDataEditGroupDivisions'])->name('groupDivision.trans.storeDataGroupDivision');
            Route::post('/trans/delete/modalDataGroupDivisions/{cari}', [GroupDivisionController::class, 'deleteDataGroupDivisions'])->name('groupDivision.trans.deleteDataGroupDivisions');
        });

        // SUB DIVISION
        Route::prefix('subDivisions')->group(function(){
            Route::get('/', [SubDivisionController::class, 'index'])->name('subDivisions.index');
            Route::get('/trans/get/tableDataGroupDivision', [SubDivisionController::class, 'getDataTableSubDivision'])->name('subDivision.trans.getTableMaster');
            Route::get('/trans/get/selectDataGroupDivision', [SubDivisionController::class , 'getDataGroupDivision'])->name('subDivision.trans.getDataGroupDivision');
            Route::post('/trans/store/modalDataSubDivision', [SubDivisionController::class, 'saveDataSubDivision'])->name('subDivision.trans.storeDataSubDivision');
            Route::post('/simpanDataSubDivision/{jenis}', [SubDivisionController::class,'simpanDataSubDivision'])->name('subDivision.simpanDataSubDivision');
            Route::get('/getDataSubDivision', [SubDivisionController::class, 'getDataSubDivision']);
        });

        // EMPLOYEES
        Route::prefix('employees')->group(function(){
            Route::get('/', [EmployeesController::class, 'index'])->name('Employees.index');
            Route::prefix('trans')->group(function(){
                Route::get('/get/dataGroupDivision', [EmployeesController::class, 'getDataDivisionGlobal'])->name('employee.trans.getDataDivisionGlobal');
                Route::get('/get/dataTableEmployee', [EmployeesController::class, 'getDataTableEmployee'])->name('employee.trans.getDataTableEmployee');
                Route::get('/getDataEmployeesDetail', [EmployeesController::class, 'getDataEmployeesDetail'])->name('employee.trans.getDataEmployeeDetail');
                Route::prefix('post')->group(function(){
                    Route::post('dataEmployeeNew/{jenis}', [EmployeesController::class, 'saveDataEmployee']);
                });
            });
            Route::get('/data_employees', [EmployeesController::class, 'getDataEmployee']);
        });

        Route::prefix('programkerja')->group(function(){
            Route::get('/', [ProgramKerjaController::class,'index'])->name('programKerja.index');
            Route::get('/getDataTotalProgramKerja', [ProgramKerjaController::class,'getDataTotalProgramKerja'])->name('programKerja.getDataTotalProgramKerja');
            Route::get('/getDataTableDashboard', [ProgramKerjaController::class, 'getDataTableDashboard']);
            Route::get('/getDatatableDashboardListUser', [ProgramKerjaController::class, 'getDatatableDashboardListUser']);
            Route::prefix('tahunan')->group(function(){
                Route::get('/', [ProgramKerjaController::class,'indexTahunan'])->name('programKerja.tahunan.index');
                Route::post('/trans/store/dataProkerTahunan/{jenis}', [ProgramKerjaController::class, 'simpanDataProkerTahunan'])->name('programKerja.tahunan.simpan');
                Route::get('/trans/get/listDataProkerTahunan', [ProgramKerjaController::class, 'ambilListDataProkerTahunan'])->name('programKerja.tahunan.listDataProkerTahunan');
                Route::get('/trans/get/getDataProkerTahunanDetail/{uid}', [ProgramKerjaController::class, 'ambilDataProkerTahunanDetail'])->name('programKerja.tahunan.getDataProkerTahunanDetail');

                // REPORT PERKERJAAN MARKETING
                Route::get('/marketing/report',[ProgramKerjaController::class,'reportPekerjaanMarketing'])->name('marketing.pekerjaan.report');
                Route::post('/marketing/report/list',[ProgramKerjaController::class,'getReportPekerjaanMarketing']);

            });
            Route::prefix('bulanan')->group(function(){
                Route::get('/', [ProgramKerjaController::class, 'indexBulanan'])->name('programKerja.bulanan.index');
                Route::get('/getDataAllProkerBulanan', [ProgramKerjaController::class, 'getProkerBulananAll'])->name('programKerja.bulanan.datapProkerBulananAll');
                Route::get('/getDataProkerTahunan', [ProgramKerjaController::class, 'getProkerTahunan'])->name('programKerja.bulanan.dataProkerTahunan');
                Route::get('/getDataSubProkerTahunan', [ProgramKerjaController::class, 'getSubProkerTahunan'])->name('programKerja.bulanan.dataSubProkerTahunan');
                Route::get('/getDataPICByGroupDivisionID',[ProgramKerjaController::class,'getDataPICbyGroupDivisionID'])->name('programKerja.bulanan.dataPIC');
                Route::post('/postDataProkerBulanan', [ProgramKerjaController::class,'simpanProkerBulanan'])->name('programKerja.bulanan.simpanData');
                Route::get('/getListDataHarian', [ProgramKerjaController::class, 'getListDataHarian'])->name('programKerja.bulanan.getListDataHarian');
                Route::post('/fileUpload', [ProgramKerjaController::class, 'testUpload'])->name('programKerja.harian.upload');
                Route::post('/deleteUpload', [ProgramKerjaController::class, 'deleteUpload'])->name('programKerja.harian.deleteUpload');
                Route::get('/listProkerTahunan', [ProgramKerjaController::class, 'listProkerTahunan']);
                Route::get('/cellProkerBulanan', [ProgramKerjaController::class, 'cellProkerBulanan']);
                Route::get('/listSelectJadwalUmrah', [ProgramKerjaController::class, 'listSelectJadwalUmrah']);
                Route::get('/listSelectJadwalUmrahForm', [ProgramKerjaController::class, 'listSelectJadwalUmrahForm']);
                Route::get('/listSelectedJadwalUmrahForm', [ProgramKerjaController::class, 'listSelectedJadwalUmrahForm']);
                Route::post('/hapusProgramKerjaBulanan', [ProgramKerjaController::class, 'hapusProgramKerja']);
            });
            Route::prefix('harian')->group(function(){
                Route::get('/', [ProgramKerjaController::class, 'indexHarian'])->name('programKerja.harian.index');
                Route::get('/listTableProkerHarian', [ProgramKerjaController::class,'listTableProkerHarian'])->name('programKerja.harian.listTable');
                Route::get('/detailDataProkerHarian', [ProgramKerjaController::class,'detailDataProkerHarian'])->name('programKerja.harian.detailprokerharian');
                Route::post('/fileUpload', [ProgramKerjaController::class, 'testUpload'])->name('programKerja.harian.upload');
                Route::post('/deleteUpload', [ProgramKerjaController::class, 'deleteUpload'])->name('programKerja.harian.deleteUpload');
                Route::get('/cariDataProkerBulanan', [ProgramKerjaController::class,'dataProkerBulanan'])->name('programKerja.harian.dataProkerBulanan');
                Route::post('/doSimpanTransHarian', [ProgramKerjaController::class,'simpanDataHarian'])->name('programKerja.harian.postDataProkerHarian');
                Route::get('/downloadFile/{path}', [ProgramKerjaController::class, 'ProkerHarianDownloadFile']);
                Route::get('/getProgramKerjaTahunan/{groupDivisionID}', [ProgramKerjaController::class, 'getProgramKerjaTahunan']);
                Route::get('/getProgramKerjaBulanan/{programKerjaTahunanID}', [ProgramKerjaController::class, 'getProgramKerjaBulanan']);
                Route::post('/hapusDataHarian/{id}', [ProgramKerjaController::class, 'hapusDataHarian'])->name('harian.delete');
            });
            Route::prefix('master_program')->group(function(){
                Route::get('/', [ProgramKerjaController::class, 'index_masterProgram'])->name('programKerja.masterProgram.index');
                Route::get('/list_master_program', [ProgramKerjaController::class, 'get_list_master_program']);
                Route::post('/simpan_master_program/{jenis}', [ProgramKerjaController::class, 'simpan_master_program']);
                Route::post('/hapus_master_program', [ProgramKerjaController::class, 'hapus_master_program']);
            });
            // GLOBAL
            Route::get('/get/data/PIC', [ProgramKerjaController::class, 'getDataPIC'])->name('programKerja.get.data.pic');
        });

        Route::prefix('data')->group(function(){
            Route::get('/trans/get/dataRoles', [EmployeesController::class, 'getDataRoles'])->name('master.data.roles');
            Route::get('/trans/get/groupDivision', [BaseController::class, 'getGroupDivision'])->name('master.data.groupDivision');
            Route::get('/getGroupDivisionWRole', [BaseController::class,'getGroupDivisionWRole'])->name('master.data.groupDivisionWRole');
            Route::get('/getProgramUmrah/{program}', [BaseController::class, 'getProgramUmrah'])->name('master.data.getProgramUmrah');
            Route::get('/getCurrentSubDivision/{current_role}', [BaseController::class, 'getCurrentSubDivision']);
            Route::get('/getMasterProgram', [BaseController::class, 'getMasterProgram']);
        });
    });
    
    Route::prefix('operasional')->group(function(){
        Route::get('/', [DivisiController::class, 'indexOperasional'])->name('index.operasional');
            Route::get('/dataTableGenerateJadwalUmrah', [DivisiController::class, 'dataTableGenerateJadwalUmrah']);
            Route::get('/generateRules', [DivisiController::class, 'generateRules']);
            Route::get('/getDataDashboard/{year}', [DivisiController::class, 'getDataDashboard']);
            Route::get('/getDataRulesJadwal/{idJadwalProgram}', [DivisiController::class, 'getDataRulesJadwal']);
            Route::get('/getDataRulesJadwalDetail', [DivisiController::class, 'getDataRulesJadwalDetail']);
            Route::get('/getJobUser', [DivisiController::class, 'getDataJobUser']);

            Route::prefix('program')->group(function(){
                Route::get('/', [DivisiController::class, 'indexProgram'])->name('index.operasional.program');
                Route::get('/listJadwalumrah', [DivisiController::class, 'listJadwalUmrah']);
                Route::post('/simpanJadwalUmrah', [DivisiController::class, 'simpanJadwalUmrah']);
                Route::get('/getDataJadwalUmrah', [DivisiController::class, 'getDataJadwalUmrah']);
                Route::post('/hapusProgram/{id}', [DivisiController::class, 'hapusProgram']);
                // V2
                Route::post('/simpanJadwalUmrahV2', [DivisiController::class, 'simpanJadwalUmrahV2']);
            });
            Route::prefix('rules')->group(function(){
                Route::get('/', [DivisiController::class, 'indexRuleProkerBulanan'])->name('index.operasional.rulesprokerbulanan');
                Route::get('/listRules', [DivisiController::class, 'listRules']);
                Route::post('/simpanDataRules/{tipe}', [DivisiController::class, 'simpanDataRules']);
                Route::get('/getRulesDetail/{rulesID}', [DivisiController::class, 'getRulesDetail']);
            });
            Route::prefix('daily')->group(function(){
                Route::get('/listFilterDaily', [DivisiController::class, 'operasional_programKerja_listFilter']);
                Route::get('/listEventsCalendarOperasional', [DivisiController::class, 'operasional_programKerja_listDaily']);
                Route::get('/listProkerOperasional', [DivisiController::class, 'operasional_programKerja_listProkerAll']);
                Route::get('/listPIC', [DivisiController::class, 'operasional_programKerja_listPIC']);
                Route::get('/detailEventsCalendarOperasional', [DivisiController::class, 'operasional_programKerja_detailCalendarOperasional']);
                Route::post('/simpan', [DivisiController::class, 'operasional_programKerja_simpanJenisPekerjaan']);
                Route::post('/hapus', [DivisiController::class, 'operasional_programKerja_hapusJenisPekerjaan']);
                Route::get('/listAktivitasProgram', [DivisiController::class, 'operasional_programKerja_listAktivitasProgram']);
                Route::get('/generateProgramWithAPI', [DivisiController::class, 'generate_with_api']);
            });
    });

    Route::prefix('divisi')->group(function(){
        Route::prefix('operasional')->group(function(){
            Route::get('/', [DivisiController::class, 'indexOperasional'])->name('index.operasional');
            Route::get('/dataTableGenerateJadwalUmrah', [DivisiController::class, 'dataTableGenerateJadwalUmrah']);
            Route::get('/generateRules', [DivisiController::class, 'generateRules']);
            Route::get('/getDataDashboard/{year}', [DivisiController::class, 'getDataDashboard']);
            Route::get('/getDataRulesJadwal/{idJadwalProgram}', [DivisiController::class, 'getDataRulesJadwal']);
            Route::get('/getDataRulesJadwalDetail', [DivisiController::class, 'getDataRulesJadwalDetail']);
            Route::get('/getJobUser', [DivisiController::class, 'getDataJobUser']);
            Route::get('/getRKAP', [DivisiController::class, 'divisi_operasional_getRKAP']);
            Route::get('/getListAktivitasUserChart', [DivisiController::class, 'operasional_dahsboard_actDetailUserChart']);

            Route::prefix('program')->group(function(){
                Route::get('/', [DivisiController::class, 'indexProgram'])->name('index.operasional.program');
                Route::get('/listJadwalumrah', [DivisiController::class, 'listJadwalUmrah']);
                Route::post('/simpanJadwalUmrah', [DivisiController::class, 'simpanJadwalUmrah']);
                Route::get('/getDataJadwalUmrah', [DivisiController::class, 'getDataJadwalUmrah']);
                Route::post('/hapusProgram/{id}', [DivisiController::class, 'hapusProgram']);
            });
            Route::prefix('rules')->group(function(){
                Route::get('/', [DivisiController::class, 'indexRuleProkerBulanan'])->name('index.operasional.rulesprokerbulanan');
                Route::get('/listRules', [DivisiController::class, 'listRules']);
                Route::post('/simpanDataRules/{tipe}', [DivisiController::class, 'simpanDataRules']);
                Route::get('/getRulesDetail/{rulesID}', [DivisiController::class, 'getRulesDetail']);
            });

            // UMHAJ
            Route::prefix('umhaj')->group(function(){
                Route::get('/umrah_getData_tourCode/{tahun}', [DivisiController::class, 'umh_get_data_tour_code']);
                Route::get('/umrah_getData_tourCode_detail', [DivisiController::class, 'umh_get_data_tour_code_detail']);
            });

            Route::prefix('aktivitas')->group(function() {
                Route::get('/data_aktivitas_tahunan', [DivisiController::class, 'opr_get_data_tahunan']);
                Route::post('/aktivitas_tahunan_simpan/{jenis}', [DivisiController::class, 'opr_act_simpan']);
            });
        });

        Route::prefix('finance')->group(function(){
            Route::get('/', [DivisiController::class, 'indexFinance'])->name('index.finance');
            Route::get('/eventsFinance', [DivisiController::class, 'eventsFinance']);
            Route::get('/getTourCode/{tourcode}', [DivisiController::class, 'finance_programKerja_tourCode']);
            Route::get('/getEventsFinanceDetail/{id}', [DivisiController::class, 'finance_programKerja_eventsDetail']);
            Route::post('/simpanAktivitas/{jenis}', [DivisiController::class, 'finance_programKerja_simpanAktivitas']);
            Route::prefix('rkap')->group(function(){
                Route::get('/listRKAP', [DivisiController::class, 'finance_rkap_list']);
                Route::post('/simpanRKAP/{jenis}', [DivisiController::class, 'finance_rkap_simpan']);
                // GET RKAP DATA
                Route::get('/getRKAPData', [DivisiController::class, 'finance_rkap_getData']);
            });
            
            Route::prefix('pembayaran')->group(function(){
                Route::get('/', [finance::class, 'finance_pembayaran_dashboard'])->name('finance.pembayaran.index');

                Route::prefix('haji')->group(function(){
                    Route::get('/list_pembayaran_haji', [finance::class, 'finance_pembayaran_haji_list']);
                    Route::get('/detail_jemaah', [finance::class, 'finance_detail_jemaah_haji']);
                    Route::post('/simpan_haji/{type}', [finance::class, 'finance_pembayaran_haji_simpan_haji']);
                    Route::get('/pembayaran_detail_jemaah', [finance::class, 'finance_pembayaran_detail_haji_jemaah']);
                    Route::get('/report_pembayaran_detail_jemaah_demo/{jenis}', [finance::class, 'finance_report_pembayaran_detail_jemaah_demo']);
                    Route::get('/report_pembayaran_detail_jemaah_excel/{tahun}', [finance::class, 'finance_report_pembayaran_detail_jemaah_excel']);
                });
            });

            Route::prefix('master')->group(function(){
                Route::get('/', [finance::class, 'finance_master_dashboard'])->name('finance.master.index');

                // GAJI POKOK
                Route::get('/gaji_pokok_employee', [DivisiController::class, 'finance_master_employees_fee']);
                Route::put('/gaji_pokok_employee/{emp_id}', [DivisiController::class, 'finance_master_employees_fee_update']);

                // MASTER COA
                Route::prefix('coa')->group(function(){
                    Route::get('/', [finance::class, 'finance_master_coa'])->name('finance.master.coa.index');
                    Route::get('/list', [finance::class, 'finance_list_coa']);
                    Route::post('/save/{jenis}', [finance::class, 'finance_save_coa']);
                });

                // MASTER BANK
                Route::prefix('bank')->group(function(){
                    Route::get('/', [finance::class, 'finance_master_bank'])->name('finance.master.bank.index');
                    Route::get('/list_bank', [finance::class, 'finance_list_bank']);
                    Route::get('/account', [finance::class, 'finance_bank_account'])->name('finance.master.bank_account.index');
                    Route::get('/list_bank_account', [finance::class, 'finance_list_bank_account']);
                    Route::get('/selected_bank_account', [finance::class, 'finance_selected_bank_account']);
                    Route::post('/save_bank_account/{type}', [finance::class, 'finane_save_bank_account']);
                });

                // MASTER CURRENCY
                Route::prefix('currency')->group(function(){
                    Route::get('/list', [finance::class, 'finance_master_currency']);
                    Route::post('/currency_update/{trans_type}', [finance::class, 'finance_master_currency_trans']);
                    Route::get('/list_detail', [finance::class, 'finance_mater_currency_detail']);
                });

                // MASTER MEMBER HAJI
                Route::prefix('member')->group(function(){
                    Route::get('/list', [finance::class, 'master_get_data_member']);
                });

                // MASTER TOUR CODE
                Route::prefix('tour_code')->group(function(){
                    Route::get('/list_tour_code/{kode}', [finance::class, 'master_tour_code']);
                });
            });
            Route::prefix('simulasi')->group(function(){
                Route::get('/employees_fee', [DivisiController::class, 'finance_sim_employees_fee']);
                Route::get('/employees_fee_download', [DivisiController::class, 'finance_sim_employees_fee_download']);
            });
            Route::prefix('pengajuan')->group(function() {
                // LEMBUR
                Route::get('/lembur', [DivisiController::class, 'finance_pgj_lembur_karyawan_list']);
                Route::post('/trans_lembur/{jenis}', [DivisiController::class, 'finance_pgj_lembur_karyawan_trans']);

                // PENGAJUAN KEUANGAN
                Route::get('/keuangan', [finance::class, 'finance_umhaj_pengajuan_keuangan']);
                Route::get('/keuangan_detail', [finance::class, 'finance_umhaj_pengajuan_keuangan_detail']);

                // SIMPAN TRANSAKSI PENGAJUAN KEUANGAN
                Route::post('/simpan_keuangan', [finance::class, 'finance_simpan_pengajuan_keuangan']);
            });

            Route::prefix('hpp')->group(function(){
                Route::get('/data_hpp', [finance::class, 'finance_hpp_get_data']);
                Route::post('/simpan_data_hpp', [finance::class, 'finance_hpp_save_data']);

                Route::post('/download_report_hpp', [finance::class, 'finance_hpp_report']);
                Route::post('/delete_report_hpp', [finance::class, 'finance_delete_hpp_report']);
            });

            Route::prefix('aktivitas')->group(function(){
                Route::get('/', [ProgramKerjaController::class, 'indexHarian'])->name('finance.index.aktivitas.harian');
            });

            Route::prefix('report')->group(function(){
                Route::post('/pembayaran_haji/{typefile}', [finance::class, 'finance_report_pembayaran_haji']);
                Route::post('/delete_pembayaran_haji', [finance::class, 'finance_delete_report_pembayaran_haji']);
            });
        });

        Route::prefix('digital')->group(function(){
            Route::get('/aktivitasHarian', [DivisiController::class, 'digital_programKerja_index'])->name('index.programKerja.digital');
            Route::get('/listEventsCalendarDigital', [DivisiController::class, 'digital_programKerja_listEvents']);
            Route::get('/listEventsCalendarDigitalDetail', [DivisiController::class, 'digital_programKerja_listEventDetail']);
            Route::get('/getDataProgramDigital', [DivisiController::class, 'digital_programKerja_listProgram']);
            Route::post('/simpanAktivitasHarian/{jenis}', [DivisiController::class, 'digital_programKerja_simpanAktivitasHarian']);
            Route::get('/listAktivitasHarian', [DivisiController::class, 'digital_programKerja_listAktivitasHarian']);
            Route::prefix('umrah')->group(function(){
                Route::get('/jadwal', [DivisiController:: class, 'digital_index_umrah'])->name('index.digital.jadwal_umrah');
                Route::get('/data_jadwal_umrah', [DivisiController::class, 'digital_jadwal_umrah'])->name('digital.data.jadwal_umrah');
                Route::prefix('trans')->group(function(){
                    Route::post('/simpan_detail/{jenis}', [DivisiController::class, 'digital_jadwal_umrah_simpan_detail']);
                });
            });
        });

        Route::prefix('human_resource')->group(function(){
            // Route::get('/', [DivisiController::class, 'indexHR'])->name('index.human_resouce');
            Route::get('/', function(){
                return redirect()->route('index.human_resource.dashboard');
            });
            Route::get('/dashboard', [DivisiController::class, 'indexHR'])->name('index.human_resource.dashboard');
            Route::prefix('/employee')->group(function(){
                Route::get('/list', [DivisiController::class, 'hr_list_employee']);
                Route::post('/simpan_status', [DivisiController::class, 'hr_ubah_status_employee']);

                Route::get('/employee_detail', [DivisiController::class, 'hr_detail_employee']);
                Route::post('/simpan_data/{jenis}', [DivisiController::class, 'hr_simpan_employee']);
            });
            Route::prefix('absensi')->group(function(){
                Route::get('/list', [DivisiController::class, 'absensi_list']);
                Route::get('/list_v2', [DivisiController::class, 'hr_absensi_list_v2']);
                Route::get('/excelDownload', [DivisiController::class, 'absensi_download_excel']);
                Route::post('/excelDelete', [DivisiController::class, 'absensi_delete_excel']);
                Route::post('/simpan_edit', [DivisiController::class, 'absensi_simpan_edit']);
            });
            Route::prefix('jam_kerja')->group(function(){
                Route::get('/', [DivisiController::class, 'HR_indexJamKerja'])->name('index.human_resource.jam_kerja');
                Route::get('/data_jam_kerja', [DivisiController::class, 'HR_getDataJamKerja']);
                Route::post('/simpan_jam_kerja/{type}', [DivisiController::class, 'HR_simpanDataJamKerja']);
            });

            Route::prefix('master')->group(function(){
                Route::get('/list_group_division', [DivisiController::class, 'hr_list_group_division']);
                Route::get('/list_sub_division', [DivisiController::class, 'hr_list_sub_division']);
            });
        });

        Route::prefix('marketing')->group(function(){
            Route::prefix('pembayaran')->group(function(){
                Route::get('/haji', [MarketingController::class, 'marketing_pembayaran_haji'])->name('index.marketing.pembayaranHaji');
                Route::get('/pembayaran_haji_detail', [MarketingController::class, 'marketing_pembayaran_haji_detail']);
                Route::post('/simpan_pembayaran_haji/{type}', [MarketingController::class, 'marketing_simpan_pembayaran_haji']);

                Route::get('/umrah', [MarketingController::class, 'marketing_pembayaran_umrah'])->name('index.marketing.pembayaranUmrah');
            });
        });

        Route::prefix('master')->group(function(){
            Route::get('/getDataProkerTahunan', [DivisiController::class, 'getDataProkerTahunan']);
            Route::get('/getDataSubDivision', [DivisiController::class, 'getDataSubDivision']);
            Route::get('/getDataEmployees', [EmployeesController::class, 'data_employee_global']);
        });
    });

    Route::prefix('aktivitas')->group(function(){
        Route::get('/', [ProgramKerjaController::class, 'indexHarian'])->name('aktivitas.harian.index');
        Route::get('/listTableProkerHarian', [ProgramKerjaController::class,'listTableProkerHarian'])->name('programKerja.harian.listTable');
        Route::get('/detailDataProkerHarian', [ProgramKerjaController::class,'detailDataProkerHarian'])->name('programKerja.harian.detailprokerharian');
        Route::post('/fileUpload', [ProgramKerjaController::class, 'testUpload'])->name('programKerja.harian.upload');
        Route::post('/deleteUpload', [ProgramKerjaController::class, 'deleteUpload'])->name('programKerja.harian.deleteUpload');
        Route::get('/cariDataProkerBulanan', [ProgramKerjaController::class,'dataProkerBulanan'])->name('programKerja.harian.dataProkerBulanan');
        Route::post('/doSimpanTransHarian', [ProgramKerjaController::class,'simpanDataHarian'])->name('programKerja.harian.postDataProkerHarian');
        Route::get('/downloadFile/{path}', [ProgramKerjaController::class, 'ProkerHarianDownloadFile'])->name('programKerja.harian.downloadFile');
        Route::post('/fileUpload', [ProgramKerjaController::class, 'testUpload'])->name('programKerja.harian.upload');
        Route::post('/deleteUpload', [ProgramKerjaController::class, 'deleteUpload'])->name('programKerja.harian.deleteUpload');
    });

    Route::prefix('presensi')->group(function(){
        Route::get('report',[PresensiController::class,'report'])->name('presensi.report');
    });

    Route::prefix('pengajuan')->group(function(){
        Route::prefix('cuti')->group(function(){
            Route::get('/', [DivisiController::class, 'pengajuan_cuti'])->name('index.pengajuan.cuti');
        });
        Route::get('/listCuti', [DivisiController::class, 'pengajuan_list_cuti']);
        Route::post('/simpanCuti', [DivisiController::class, 'pengajuan_simpan_cuti']);
        Route::prefix('lembur')->group(function(){
            Route::get('/', [DivisiController::class, 'pengajuan_lembur'])->name('index.pengajuan.lembur');
            Route::get('/list_lembur', [DivisiController::class, 'list_lembur']);
            Route::post('/simpan/{jenis}', [DivisiController::class, 'simpan_pengajuan_lembur']);
            Route::get('/get_data', [DivisiController::class, 'get_data_lembur']);
            Route::put('/konfirmasi', [DivisiController::class, 'konfirmasi_data_lembur']);

            Route::get('/list_lembur_v2', [DivisiController::class, 'list_lembur_v2']);
        });
    });

    Route::prefix('tarik_data')->group(function() {
        Route::get('/absensi', [TarikDataController::class, 'tarik_data_index'])->name('index.tarik_data.absensi');
        Route::post('/absensi', [TarikDataController::class, 'tarik_data_absensi']);
        Route::get('/get_list_absensi', [TarikDataController::class, 'tarik_data_get_absensi']);

        Route::prefix('umhaj')->group(function(){
            Route::get('jadwal_umrah', [TarikDataController::class, 'umhaj_jadwal_umrah_index'])->name('index.tarik_data.umhaj.jadwal_umrah');
            Route::get('data_jadwal_umrah', [TarikDataController::class, 'umhaj_jadwal_umrah_get']);
            Route::post('sync_data_local', [TarikDataController::class, 'umhaj_jadwal_umrah_sync']);
        });
    });

    Route::prefix('simulasi')->group(function(){
        Route::prefix('perhitungan_lembur')->group(function(){
            Route::get('/', [DivisiController::class, 'index_simulasi_perhitungan_lembur']);
        });
    });

    Route::prefix('website')->group(function(){
        Route::get('/', [percikToursController::class, 'index'])->name('index.perciktours.com');
        Route::get('/summary_data', [percikToursController::class, 'perciktourscom_summary_data']);
        Route::get('/get_summary_data', [percikToursController::class, 'perciktourscom_get_data_summary']);

        Route::prefix('master')->group(function(){
            Route::get('/product', [percikToursController::class, 'perciktourscom_master_product']);
            Route::get('/jadwal', [percikToursController::class, 'perciktourscom_master_jadwal']);
            Route::get('/jadwal_tarik', [percikToursController::class, 'perciktourscom_master_jadwal_tarik']);
            Route::get('/jadwal_detail', [percikToursController::class, 'perciktourscom_master_jadwal_detail']);
            Route::get('/asset_umrah', [percikToursController::class, 'perciktourscom_master_asset_umrah']);
        });

        Route::prefix('transaction')->group(function(){
            Route::post('/flyer', [percikToursController::class, 'perciktourscom_upload_flyer']);
        });

        Route::prefix('article')->group(function(){
            Route::get('/list', [percikToursController::class, 'perciktourscom_article_list']);
            Route::get('/tour_detail', [percikToursController::class, 'perciktourscom_article_tour_detail']);
            Route::post('/save/{type}', [percikToursController::class, 'perciktouscom_article_save']);
            Route::get('/detail/{uuid}', [percikToursController::class, 'perciktourscom_article_detail']);
        });
        
        Route::prefix('assets')->group(function(){
            Route::get('/dashboard', [percikToursController::class, 'perciktourscom_assets'])->name('index.management.assets');
            Route::post('/simpan_data_asset', [percikToursController::class, 'perciktourscom_simpan_data_asset']);
        });
    });
});