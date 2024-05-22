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
use Hash;

date_default_timezone_set('Asia/Jakarta');

class EmployeeService
{

    public static function getDataEmployee($cari)
    {
        $rawQuery   = DB::select(
            "
            SELECT 	b.id as employee_id,
                    b.name as employee_name,
                    c.name as group_division_name,
                    d.name as sub_division_name
            FROM 	job_employees a
            INNER JOIN employees b ON a.employee_id = b.id
            INNER JOIN group_divisions c ON a.group_division_id = c.id
            INNER JOIN sub_divisions d ON a.sub_division_id = d.id
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
    
    public static function doSaveDataEmployee($data)
    {
        DB::beginTransaction();
        // CHECK
        $queryGetEmployee   = DB::table('employees')
                                ->select('name', 'user_id')
                                ->where('name','=', $data['empNama'])
                                ->get()->toArray();
        if(count($queryGetEmployee) > 0) {
            return 'akun_ada';
        } else {
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
                DB::commit();
                return 'berhasil';
            } catch(\Exception $e) {
                DB::rollback();
                return 'gagal';
            }
        }
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