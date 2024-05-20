<?php

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

        //modal 
        Route::get('modal/target','loadModalMarketingTarget');
        Route::get('modal/target/detail','loadModalDetailMarketingTarget');

        // Rencana Kerja
        Route::prefix('workplans')->controller(WorkPlanController::class)->group(function(){
            Route::get('','index')->name('marketing.workplans.index');
        });

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
            Route::get('/trans/get/dataGroupDivisions/{cari}', [GroupDivisionController::class, 'tableGroupDivision'])->name('groupDivision.data.tableGroupDivisions');
    
            // TRANS ON MODAL
            // MODAL TRANS ADD
            Route::post('/trans/store/dataGroupDivisions', [GroupDivisionController::class , 'storeDataGroupDivision'])->name('groupDivision.trans.storeDataGroupDivision');
            // MODAL TRANS EDIT
            Route::get('/trans/get/modalDataGroupDivisions/{cari}', [GroupDivisionController::class, 'modalGetDataGroupDivisions'])->name('groupDivision.data.modalGroupDivisions');
            Route::post('/trans/store/modalDataGroupDivisions/{cari}', [GroupDivisionController::class, 'storeDataEditGroupDivisions'])->name('groupDivision.trans.storeDataGroupDivision');
            Route::post('/trans/delete/modalDataGroupDivisions/{cari}', [GroupDivisionController::class, 'deleteDataGroupDivisions'])->name('groupDivision.trans.deleteDataGroupDivisions');
        });

        // SUB DIVISION
        Route::prefix('subDivisions')->group(function(){
            Route::get('/', [SubDivisionController::class, 'index'])->name('subDivisions.index');
            Route::get('/trans/get/tableDataGroupDivision', [SubDivisionController::class, 'getDataTableSubDivision'])->name('subDivision.trans.getTableMaster');
            Route::post('/trans/get/selectDataGroupDivision', [SubDivisionController::class , 'getDataGroupDivision'])->name('subDivision.trans.getDataGroupDivision');
            Route::post('/trans/store/modalDataSubDivision', [SubDivisionController::class, 'saveDataSubDivision'])->name('subDivision.trans.storeDataSubDivision');
            // MODAL
            Route::get('/trans/get/modalDataSubDivision', [SubDivisionController::class, 'getDataSubDivision'])->name('subDivision.trans.getDataSubDivision');
            Route::post('/trans/store/editDataSubDivision', [SubDivisionController::class, 'saveEditDataSubDivision'])->name('subDivision.trans.storeDataSubDivisionEdit');
        });

        // EMPLOYEES
        Route::prefix('employees')->group(function(){
            Route::get('/', [EmployeesController::class, 'index'])->name('Employees.index');
            Route::get('/home', [EmployeesController::class,'index'])->name('Employees.index');
            Route::get('/trans/get/dataGroupDivision/{cari}', [EmployeesController::class, 'getDataDivisionGlobal'])->name('employee.trans.getDataDivisionGlobal');
            Route::post('/trans/post/dataEmployeeNew', [EmployeesController::class, 'saveDataEmployee'])->name('employee.trans.postDataEmployee');
            Route::get('/trans/get/dataTableEmployee', [EmployeesController::class, 'getDataTableEmployee'])->name('employee.trans.getDataTableEmployee');
        });
    });
});