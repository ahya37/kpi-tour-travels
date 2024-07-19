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
use App\Services\ProgramKerjaService;
use Illuminate\Support\Facades\Route;


Route::get('/test', function () {
    return 'test';
});

Route::get('/', function () {
    return view('auth.login');  
});

//route login index
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');

//route login store
Route::post('/login', [LoginController::class, 'store'])->name('login.store')->middleware('guest');
//route logout


Route::group(['middleware' => ['auth']], function () {

    //route dashboard
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
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
            Route::get('/programKerja/', [MarketingController::class, 'marketing_programKerja_dashboard'])->name('marketing.programkerja.dashboard');
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
            });
            // ADDITIONAL
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
            Route::get('/userProfiles', 'userProfiles')->name('accounts.user.profile');
            Route::get('/userProfiles/ChangePasswordUser', 'ChangePasswordUser');
            Route::get('/userProfiles/CheckPasswordCurrentUser', 'CheckPasswordCurrentUser');
            Route::prefix('userLog')->group(function(){
                Route::get('/', 'userLog')->name('accounts.user.log');
                Route::get('/dataTableUserLog', 'dataTableUserLog');
            });
            Route::get('/userLog', 'userLog')->name('accounts.user.log');
        });

        Route::controller(RoleController::class)->group(function(){
            Route::get('/roles', 'index')->name('roles.index')->middleware('permission:roles.index');
        });

        // Route::get('/userProfiles', UserController::class, 'userProfiles')->name('accounts.user.profile');
    });


    Route::prefix('master')->group(function(){
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
            Route::get('/trans/get/dataGroupDivision', [EmployeesController::class, 'getDataDivisionGlobal'])->name('employee.trans.getDataDivisionGlobal');
            Route::post('/trans/post/dataEmployeeNew', [EmployeesController::class, 'saveDataEmployee'])->name('employee.trans.postDataEmployee');
            Route::get('/trans/get/dataTableEmployee', [EmployeesController::class, 'getDataTableEmployee'])->name('employee.trans.getDataTableEmployee');
            Route::get('/getDataEmployeesDetail', [EmployeesController::class, 'getDataEmployeesDetail'])->name('employee.trans.getDataEmployeesDetail');
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
            });
            Route::prefix('rules')->group(function(){
                Route::get('/', [DivisiController::class, 'indexRuleProkerBulanan'])->name('index.operasional.rulesprokerbulanan');
                Route::get('/listRules', [DivisiController::class, 'listRules']);
                Route::post('/simpanDataRules/{tipe}', [DivisiController::class, 'simpanDataRules']);
                Route::get('/getRulesDetail/{rulesID}', [DivisiController::class, 'getRulesDetail']);
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
        });

        Route::prefix('master')->group(function(){
            Route::get('/getDataProkerTahunan', [DivisiController::class, 'getDataProkerTahunan']);
            Route::get('/getDataSubDivision', [DivisiController::class, 'getDataSubDivision']);
        });
    });

    Route::prefix('aktivitas')->group(function(){
        // Route::get('/daily','daily')->name('aktivitas.daily.index');
        // Route::get('modal/create','loadModalFormDailyActivities');
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
});