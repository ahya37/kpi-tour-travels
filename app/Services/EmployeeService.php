<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\Employee;
use App\Models\User;
use App\Models\JobEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\LogHelper;
date_default_timezone_set('Asia/Jakarta');

class EmployeeService
{

    public static function getDataEmployee($cari)
    {
        $rawQuery   = DB::select(
            "
            SELECT 	b.id as employee_id,
                    b.name as employee_name,
                    c.id as group_division_id,
                    c.name as group_division_name,
                    d.id as sub_division_id,
                    d.name as sub_division_name,
                    e.email as employee_email,
                    f.role_id,
				    g.name as role_name,
                    e.is_active as user_active
            FROM 	job_employees a
            INNER JOIN employees b ON a.employee_id = b.id
            INNER JOIN group_divisions c ON a.group_division_id = c.id
            INNER JOIN sub_divisions d ON a.sub_division_id = d.id
            INNER JOIN users e ON b.user_id = e.id
            INNER JOIN model_has_roles f ON b.user_id = f.model_id
            INNER JOIN roles g ON f.role_id = g.id
            WHERE 	(a.id LIKE '%$cari%' OR b.id LIKE '%$cari%' OR c.id LIKE '%$cari%' OR d.id LIKE '%$cari%')
            ORDER BY b.name ASC
            "
        );
        return $rawQuery;
    }

    public static function ambilDataDivisionGlobal($keyword)
    {
        $query  = DB::select(
            "
            SELECT 	a.id as group_division_id,
                    a.name as group_division_name,
                    b.id as sub_division_id,
                    b.name as sub_division_name
            FROM 	group_divisions a
            INNER JOIN sub_divisions b ON a.id = b.division_group_id
            ORDER BY a.name ASC
            "
        );
            
        return $query;
    }
    
    public static function doSaveDataEmployee($data, $ip)
    {
        DB::beginTransaction();

        // CHECK
        $queryGetEmployee   = DB::table('employees')
                                ->select('name', 'user_id')
                                ->where('name','=', $data['empNama'])
                                ->get()->toArray();
        if((count($queryGetEmployee) > 0) && ($data['transJenis'] == 'add')) {
            $output = [
                "status"    => "ada_akun",
                "errMsg"    => $queryGetEmployee,
            ];
        } else {
            if($data['transJenis'] == 'add') {
                try {
                    // INSERT AND GET USERS ID
                    $dataUsers  = array(
                        "name"          => $data['empNama'],
                        "email"         => $data['empUserName'],
                        "password"      => Hash::make('rahasia'),
                        "created_at"    => date('Y-m-d H:i:s'),
                    );
                    $insertUser         = User::create($dataUsers);
                    $insertUser->assignRole($data['empRole']);
                    
                    // INSERT TO EMPLOYEE
                    $dataEmployees  = array(
                        "id"        => Str::random(30),
                        "user_id"   => $insertUser->id,
                        "name"      => $data['empNama'],
                        "created_by"=> Auth::user()->id,
                        "updated_by"=> Auth::user()->id,
                        "created_at"=> date('Y-m-d H:i:s'),
                        "updated_at"=> date('Y-m-d H:i:s'),
                    );
                    $insertEmployees    = Employee::create($dataEmployees);
        
                    // INSERT TO JOB EMPLOYEES
                    $dataJobEmployees   = array(
                        "id"            => Str::random(30),
                        "employee_id"   => $insertEmployees->id,
                        "sub_division_id"   => explode(' | ', $data['empGDID'])[1],
                        "group_division_id" => explode(' | ', $data['empGDID'])[0],
                        "created_by"=> Auth::user()->id,
                        "updated_by"=> Auth::user()->id,
                        "created_at"=> date('Y-m-d H:i:s'),
                        "updated_at"=> date('Y-m-d H:i:s'),
                    );
                    $insertJobEmployee  = JobEmployee::create($dataJobEmployees);

                    $employeeID     = DB::getPdo()->lastInsertId();
                    DB::commit();
                    $output     = array(
                        "status"    => "berhasil",
                        "errMsg"    => "",
                    );
                    LogHelper::create('add', 'Berhasil Menambahkan Data Employee Baru : '.$employeeID, $ip);
                } catch(\Exception $e) {
                    DB::rollback();
                    Log::channel('daily')->error($e->getMessage());
                    $output     = array(
                        "status"    => "gagal",
                        "errMsg"    => $e->getMessage(),
                    );

                    LogHelper::create('error_system', 'Gagal Menambahkan Data Employee Baru', $ip);
                }
            } else if($data['transJenis'] == 'edit') {
                // GET USER ID
                $employeeID     = $data['empID'];
                $roleName       = $data['empRole'];
                $query_user_id  = DB::select(
                    "
                    SELECT  *
                    FROM    employees 
                    WHERE   id = '".$employeeID."'
                    "
                );
                $userID     = $query_user_id[0]->user_id;

                // GET USER ID
                $role_id    = DB::table('roles')->select('id')->where([ 'name' => $data['empRole'] ])->get();
                $user       = User::find($userID);
                $user->roles()->detach();
                $user->roles()->attach($role_id[0]->id);

                // UBAH DATA USERS
                $data_where     = array(
                    "employee_id"   => $data['empID'],
                );

                $data_update    = array(
                    "group_division_id"     => explode(" | ", $data['empGDID'])[0],
                    "sub_division_id"       => explode(" | ", $data['empGDID'])[1],
                );

                DB::table('job_employees')->where($data_where)->update($data_update);
                try {
                    // UBAH ROLES
                    DB::commit();
                    $output     = array(
                        "status"    => "berhasil",
                        "errMsg"    => "",
                    );
                    LogHelper::create('edit', 'Berhasil Mengubah Data Employee : '. $data['empID'], $ip);
                } catch(\Exception $e) {
                    DB::rollback();
                    $output     = array(
                        "status"    => "gagal",
                        "errMsg"    => $e->getMessage(),
                    );
                    LogHelper::create('error_system', 'Gagal Merubah Data Employee', $ip);
                }
            }
        }
        return $output;
    }

    // GET DATA
    public static function getData($type, $value)
    {
        if($type == 'roles') {
            $query  = DB::select(
                "
                SELECT  id as role_id,
                        name as role_name
                FROM    roles
                ORDER BY id ASC
                "
            );

            return $query;
        }
    }

    // 27 AGUSTUS 2024
    // NOTE : AMBIL DATA EMPLOYEE UNTUK KEBUTUHAN GLOBAL
    public static function do_get_data_employee_global()
    {
        return DB::select(
            "
            SELECT  user_id as emp_id,
                    name as emp_name
            FROM    employees
            ORDER BY user_id ASC
            "
        );
    }

    // 06 SEPTEMBER 2024
    // NOTE : MELAKUKAN PERUBAHAN STATUS EMPLOYEE DARI ACTIVE => NON ACTIVE ATAU SEBALIKNYA
    public static function do_ubah_status_employee($data)
    {
        $emp_id     = $data['emp_id'];
        $emp_status = $data['emp_status'];
        $ip         = $data['ip'];

        DB::beginTransaction();

        // DAPETIN ID USER
        $query_get_id_user  = DB::table('employees')->select('user_id')->where(['id' => $emp_id])->get();
        $id_selected_user   = $query_get_id_user[0]->user_id;

        // UPDATE TABLE USERS
        $data_where_update_user     = [
            "id"        => $id_selected_user,
        ];
        
        $data_for_update_user       = [
            "is_active" => $emp_status,
            "updated_at"=> date('Y-m-d H:i:s'),
        ];
        
        DB::table('users')->where($data_where_update_user)->update($data_for_update_user);

        try {
            DB::commit();
            LogHelper::create('edit', 'Berhasil Mengubah Status User : '.$id_selected_user, $ip);

            $output     = [
                "status"    => "berhasil",
                "errMsg"    => "",
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->error($e->getMessage());
            LogHelper::create('error_system', 'Gagal Mengubah Status User', $ip);

            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }

    // UPDATE UNTUK AMBIL DATA ABSENSI
    public static function get_absensi_ambil_data_user($data)
    {
        $user_id    = $data['user_id'];
        $year       = $data['year'];
        $month      = $data['month'];

        $query      = DB::table('tm_presence as a')
                        ->join('users as b', 'a.prs_user_id', '=', 'b.id')
                        ->select('b.name as prs_name', 'a.prs_date', 'a.prs_in_time', 'a.prs_out_time')
                        ->where(DB::raw('EXTRACT(YEAR FROM a.prs_date)'),'=', $year)
                        ->where(DB::raw('EXTRACT(MONTH FROM a.prs_date)'),'=', $month)
                        ->where('a.prs_user_id', '=', $user_id)
                        ->orderBy('a.prs_date', 'asc')
                        ->get();
        
        return $query;
    }
}