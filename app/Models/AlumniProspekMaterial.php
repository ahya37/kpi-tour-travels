<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AlumniProspekMaterial extends Model
{
    use HasFactory;

    protected $table = 'alumni_prospect_material';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function getProspectMaterial()
    {
        return DB::table('alumni_prospect_material as a')
               ->select('a.id','a.members','a.label','a.created_at','c.name as cs')
               ->join('job_employees as b','a.job_employee_id','=','b.id')
               ->join('employees as c','b.employee_id','=','c.id')
               ->get();
    }

    public static function alumniProspectMaterialByAccountCS($auth)
    {
        return  DB::table('employees as a')
                 ->select('c.id','c.label','c.members','a.name as cs','c.created_at')
                 ->join('job_employees as b','a.id','=','b.employee_id')
                 ->join('alumni_prospect_material as c','c.job_employee_id','=','b.id')
                 ->where('user_id', $auth)
                 ->get();
    }

}
