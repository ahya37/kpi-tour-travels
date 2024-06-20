<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProkerBulanan extends Model
{
    use HasFactory;

    protected $table = 'proker_bulanan';
	protected $guarded = [];

    public static function getProkerBulananByDivisiUser($user)
    {
        return DB::select("SELECT d.id , d.pkt_title from employees as a  
                join job_employees as b on b.employee_id = a.id
                join group_divisions as c on b.group_division_id = c.id
                join proker_tahunan as d on d.division_group_id = c.id
                WHERE a.user_id = $user and d.parent_id is NOT NULL  order by d.pkt_title asc");
    }
}
