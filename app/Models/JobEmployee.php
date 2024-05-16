<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JobEmployee extends Model
{
    use HasFactory;

    protected $table = 'job_employees';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];

    public static function getJobDivisionCustomerServices()
    {
        return DB::table('job_employees as a')
                ->select('a.id','c.name')
                ->join('sub_divisions as b','a.sub_division_id','=','b.id')
                ->join('employees as c','a.employee_id','=','c.id')
                ->where('b.name','Customer Service')
                ->get();
    }
}
