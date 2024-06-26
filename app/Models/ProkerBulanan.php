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

    public static function getProkerBulananByProkerTahunan($pkb_pkt_id)
    {
        return DB::table('proker_bulanan')
                ->select('uuid','pkb_start_date','pkb_title')
                ->whereRaw(DB::raw("SUBSTRING_INDEX(pkb_pkt_id, '|', 1) = '$pkb_pkt_id'"))
                ->orderBy('pkb_start_date','asc')
                ->get();
    }

    public static function prokerGroupBulananByTahunan($groupDivisionID)
    {
        // return DB::table('proker_bulanan')
        //         ->select(DB::raw('MONTH(pkb_start_date) as month'))
        //         ->whereRaw(DB::raw("SUBSTRING_INDEX(pkb_pkt_id, '|', 1) = '$pkb_pkt_id'"))
        //         ->groupBy('month')
        //         ->orderBy('month','asc')
        //         ->get();
        $proker_harian = DB::select(
            "
                SELECT month(pkb_start_date) as month FROM proker_bulanan as a
                        join proker_tahunan as b on substring_index(a.pkb_pkt_id, '|', 1) = b.uid
                        where b.division_group_id = '$groupDivisionID'
                        group by  month(a.pkb_start_date)
                        order by month(a.pkb_start_date) asc
            "
        );

        return $proker_harian;
    }
}
