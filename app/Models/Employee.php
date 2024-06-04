<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];

    public static function getCustomerServices()
    {
        $cs = DB::table('employees as a')
              ->select('a.id','a.name as cs','c.name as sub_div')
              ->join('job_employees as b','b.employee_id','=','a.id')
              ->join('sub_divisions as c','b.sub_division_id','=','c.id')
              ->where('c.name','Customer Service')
              ->get();

        return $cs;
    }
}
