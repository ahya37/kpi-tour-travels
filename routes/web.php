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
use Illuminate\Support\Facades\Route;

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

    //marketing
    Route::prefix('marketings')->controller(MarketingController::class)->group(function(){
        Route::get('/target','target')->name('marketing.target');
        Route::post('/target','storeTarget')->name('marketing.target.store');
        
        // datatable
        Route::post('/target/list','listTarget');
        
        // detail target marketing
        Route::get('/target/detail/{marketingTargetId}','detailMarketingTarget');
        Route::post('/target/detail/{marketingTargetId}/store','detailMarketingTargetStore');
        Route::post('/target/detail/list/{detailMarketingTargetId}','detailListTarget');
        
        // bahan prospek
        Route::get('/prospectmaterial','prospectMaterial')->name('marketing.prospectmaterial');
        Route::post('/prospectmaterial/store','prospectMaterialStore')->name('marketing.prospectmaterial.store');

        // For CS
        Route::get('/alumniprospectmaterial','alumniProspectMaterialByAccountCS')->name('marketing.alumniprospectmaterial');
        Route::get('/alumniprospectmaterial/{id}','detailAlumniProspectMaterialByAccountCS')->name('marketing.alumniprospectmaterial.detail');
        Route::get('/alumniprospectmaterial/detail/manage/modal/{detailId}','loadModalManageAlumniProspectMaterial');
        Route::post('/alumniprospectmaterial/detail/manage/store','manageAlumniProspectMaterialStore')->name('marketing.alumniprospectmaterial.store');
        Route::post('/alumniprospectmaterial/detail/list/{alumniprospectmaterialId}','listAlumniProspectMaterial');
        // laporan
        Route::prefix('laporan')->controller(MarketingController::class)->group(function(){
            Route::get('/pelaksanaan_iklan',[MarketingController::class,'laporanPelaksanaanIklan'])->name('marketing.laporan.iklan');
            Route::post('/trans/store/reportAds', [MarketingController::class, 'simpanLaporanIklan'])->name('marketing.trans.storeDataLaporanIklan');
        });

        //modal 
        Route::get('modal/target','loadModalMarketingTarget');
        Route::get('modal/target/detail','loadModalDetailMarketingTarget');


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
        });

        Route::controller(RoleController::class)->group(function(){
            Route::get('/roles', 'index')->name('roles.index')->middleware('permission:roles.index');
        });
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
            Route::prefix('tahunan')->group(function(){
                Route::get('/', [ProgramKerjaController::class,'indexTahunan'])->name('programKerja.tahunan.index');
                Route::post('/trans/store/dataProkerTahunan/{jenis}', [ProgramKerjaController::class, 'simpanDataProkerTahunan'])->name('programKerja.tahunan.simpan');
                Route::get('/trans/get/listDataProkerTahunan', [ProgramKerjaController::class, 'ambilListDataProkerTahunan'])->name('programKerja.tahunan.listDataProkerTahunan');
                Route::get('/trans/get/getDataProkerTahunanDetail/{uid}', [ProgramKerjaController::class, 'ambilDataProkerTahunanDetail'])->name('programKerja.tahunan.getDataProkerTahunanDetail');
            });
            Route::prefix('bulanan')->group(function(){
                Route::get('/', [ProgramKerjaController::class, 'indexBulanan'])->name('programKerja.bulanan.index');
                Route::get('/getDataAllProkerBulanan', [ProgramKerjaController::class, 'getProkerBulananAll'])->name('programKerja.bulanan.datapProkerBulananAll');
                Route::get('/getDataProkerTahunan', [ProgramKerjaController::class, 'getProkerTahunan'])->name('programKerja.bulanan.dataProkerTahunan');
                Route::get('/getDataPICByGroupDivisionID',[ProgramKerjaController::class,'getDataPICbyGroupDivisionID'])->name('programKerja.bulanan.dataPIC');
                Route::post('/postDataProkerBulanan', [ProgramKerjaController::class,'simpanProkerBulanan'])->name('programKerja.bulanan.simpanData');
            });
            Route::get('/harian', [ProgramKerjaController::class,'indexHarian'])->name('programKerja.harian.index');
            // GLOBAL
            Route::get('/get/data/PIC', [ProgramKerjaController::class, 'getDataPIC'])->name('programKerja.get.data.pic');
        });

        Route::prefix('data')->group(function(){
            Route::get('/trans/get/dataRoles', [EmployeesController::class, 'getDataRoles'])->name('master.data.roles');
            Route::get('/trans/get/groupDivision', [BaseController::class, 'getGroupDivision'])->name('master.data.groupDivision');
        });
    });

    Route::prefix('activities')->controller(ActivityController::class)->group(function(){
        Route::get('/daily','daily')->name('aktivitas.daily.index');
        Route::get('modal/create','loadModalFormDailyActivities');
    });
});