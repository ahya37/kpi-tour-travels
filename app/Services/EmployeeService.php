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
				    g.name as role_name
            FROM 	job_employees a
            INNER JOIN employees b ON a.employee_id = b.id
            INNER JOIN group_divisions c ON a.group_division_id = c.id
            INNER JOIN sub_divisions d ON a.sub_division_id = d.id
            INNER JOIN users e ON b.user_id = e.id
            INNER JOIN model_has_roles f ON b.user_id = f.model_id
            INNER JOIN roles g ON f.role_id = g.id
            WHERE 	(a.id LIKE '%$cari%' OR b.id LIKE '%$cari%' OR c.id LIKE '%$cari%' OR d.id LIKE '%$cari%')
            ORDER BY a.created_at DESC
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

                    var_dump($e->getMessage());die();

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
                $roleID     = $data['empRole'];
                $user       = User::find($userID);
                $user->roles()->detach();
                $user->roles()->attach($roleID);

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
}