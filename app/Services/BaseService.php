<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BaseService 
{
	public static function getDataGroupDivision()
    {
        $role   = Auth::user()->getRoleNames()[0] == ('admin' || 'umum') ? '%' : Auth::user()->getRoleNames()[0];
        
        $query  = DB::select(
            "
            SELECT  gd.*
            FROM    group_divisions gd
            JOIN    roles r ON gd.roles_id = r.id
            WHERE   r.name LIKE '$role'
            "
        );

        return $query;
    }

    public static function getDataEmployeeByGroupDivision($groupDivisionID)
    {
        $query  = DB::table('job_employees as a')
                    ->select('a.group_division_id','c.name as group_division_name','a.employee_id','b.name as employee_name')
                    ->join('employees as b','a.employee_id','=', 'b.id')
                    ->join('group_divisions as c','a.group_division_id','=','c.id')
                    ->join('roles as d', 'c.roles_id','=','d.id')
                    ->where('group_division_id','=', $groupDivisionID)
                    ->orderBy('c.name','ASC')
                    ->get()
                    ->toArray();
        return $query;
    }

    public static function doGetGroupDivisionWRole()
    {
        $query = DB::select(
            "
            SELECT 	a.id as gd_id,
                    a.name as gd_name,
                    b.id as role_id,
                    b.name as role_name
            FROM 	group_divisions a
            JOIN 	roles b ON a.roles_id = b.id
            ORDER BY a.name ASC
            "
        );
        
        return $query;
    }

    public static function doGetProgramUmrah($data, $program_name)
    {
        $valueCari  = $data->all()['sendData']['cari'];

        $query      = DB::select(
            "
            SELECT 	a.id as program_id,
                    a.name as program_name,
                    a.product_id as product_id,
                    b.name as product_name
            FROM 	programs a
            JOIN 	products b ON a.product_id = b.id
            WHERE 	a.id LIKE '$valueCari'
            AND 	(a.product_id LIKE '$program_name' or lower(b.name) LIKE '$program_name')

            UNION

            SELECT 	a.id as program_id,
                    a.name as program_name,
                    a.product_id as product_id,
                    b.name as product_name
            FROM 	programs a
            JOIN 	products b ON a.product_id = b.id
            WHERE 	a.id LIKE '$valueCari'
            AND 	(a.product_id LIKE 'Haji' or lower(b.name) LIKE 'Haji')
            "
        );

        return $query;
    }

    // 25 JUNI 2024
    // NOTE : GET DATA FOR doGetCurrentSubDivision
    public static function doGetCurrentSubDivision($role, $userID)
    {
        $query  = DB::select(
            "
            SELECT 	LOWER(c.name) AS sub_division_name
            FROM 	employees a
            JOIN 	job_employees b ON a.id = b.employee_id
            JOIN	sub_divisions c ON b.sub_division_id = c.id
            WHERE 	a.user_id = '$userID'
            "
        );

        return $query;
    }
}