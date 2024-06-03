<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BaseService 
{
	public static function getDataGroupDivision()
    {
        $role   = Auth::user()->getRoleNames()[0] == 'admin' ? '%' : Auth::user()->getRoleNames()[0];
        
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
}