<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;

class BaseService 
{
	public static function getDataGroupDivision()
    {
        $query  = DB::table('group_divisions')
                    ->get()
                    ->toArray();
        return $query;
    }

    public static function getDataEmployeeByGroupDivision($groupDivisionID)
    {
        $query  = DB::table('job_employees as a')
                    ->select('a.group_division_id','c.name as group_division_name','a.employee_id','b.name as employee_name')
                    ->join('employees as b','a.employee_id','=', 'b.id')
                    ->join('group_divisions as c','a.group_division_id','=','c.id')
                    ->where('group_division_id','=', $groupDivisionID)
                    ->orderBy('c.name','ASC')
                    ->get()
                    ->toArray();
        return $query;
    }
}