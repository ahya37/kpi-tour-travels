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
	
	public static function getProkerBulananMarkering($groupDivisionID, $month, $year)
	{
		$proker_bulanan = DB::table('proker_bulanan as a')
						  ->select('a.id','a.uuid','a.pkb_start_date','a.pkb_title','d.name as created_by_name','a.created_by')
						  ->join('proker_tahunan as b', function($join1){
							  $join1->on(DB::raw('SUBSTRING_INDEX(a.pkb_pkt_id, "|",1)'), '=', 'b.uid');
						  })
						  ->join('users as d','a.created_by','=','d.id')
						  ->where('b.division_group_id', $groupDivisionID)
						  ->whereYear('a.pkb_start_date', $year)
						  ->whereMonth('a.pkb_start_date', $month)
						  ->orderBy('a.pkb_start_date','asc')
						  ->get();
						  
		return $proker_bulanan;
	}
	
	public static function getProkerBulananDetail($pkb_id)
	{
		$proker_bulanan_detail = DB::table('proker_bulanan_detail')->where('pkb_id', $pkb_id)->get();
						
		return $proker_bulanan_detail;
	}

    public static function getProgramByDivisi($groupDivisionID, $year, $month, $week = null)
    {
        $programs = DB::table('proker_bulanan as a')
						  ->select('a.id','a.uuid','a.pkb_start_date','a.pkb_title','a.pkb_hasil')
						  ->join('proker_tahunan as b', function($join1){
							  $join1->on(DB::raw('SUBSTRING_INDEX(a.pkb_pkt_id, "|",1)'), '=', 'b.uid');
						  })
                          ->join('master_program as e','a.master_program_id','=','e.id')
                          ->where('b.division_group_id', $groupDivisionID)
                          ->whereYear('a.pkb_start_date', $year)
                          ->whereMonth('a.pkb_start_date', $month)
                          ->orderBy('a.pkb_start_date','asc')
                          ->get();
						  
		return $programs;
    }

    public static function getProgramByDivisiPerMinggu($groupDivisionID, $year, $month, $week = null)
    {
        $programs = DB::select("
                                select b.id,b.uuid, b.pkb_title, b.pkb_start_date,
                                    (
                                        select sum(b1.pkbd_num_target) from proker_bulanan_detail as b1 where b1.pkb_id = b.id
                                    ) as target,
                                    (
                                        select count(a1.id) from proker_harian as a1 where substring_index(a1.pkh_pkb_id,'|',1) = b.uuid  
                                        and year(a1.pkh_date) = $year
                                        and month(a1.pkh_date) = $month
                                        and FLOOR((DAY(a1.pkh_date) - 1) / 7) + 1 = $week
                                    ) as pkb_hasil 
                                    FROM proker_harian AS a
                                    JOIN proker_bulanan AS b ON SUBSTRING_INDEX(a.pkh_pkb_id,'|', 1) = b.uuid 
                                    JOIN proker_bulanan_detail AS c ON SUBSTRING_INDEX(a.pkh_pkb_id,'|', -1) = c.id
                                    join proker_tahunan as  d on substring_index(b.pkb_pkt_id,'|',1) = d.uid 
                                    WHERE d.division_group_id = '$groupDivisionID'
                                    AND YEAR(a.pkh_date) = $year
                                    AND MONTH(a.pkh_date) = $month
                                    and  FLOOR((DAY(a.pkh_date) - 1) / 7) + 1 = $week
                                    group by b.id, b.pkb_title
                                "
                            );
		return $programs;
    }

    public static function getJenisPekerjaan($pkb_id)
	{
		$proker_bulanan_detail = DB::table('proker_bulanan_detail as a')
                                    ->select('a.pkbd_type' , 'a.pkbd_num_target' , 'a.pkbd_num_result')
                                    ->join('proker_bulanan as b','a.pkb_id','=','b.id')
                                    ->where('a.pkb_id', $pkb_id)
                                    ->orderBy('a.created_at','asc')
                                    ->get();
						  
		return $proker_bulanan_detail;
	}

    public static function getTotalTargetUmrahBulanBytahun($year, $month)
    {
        $target = DB::table('detailed_marketing_targets as a')
                    ->select(DB::raw('sum(a.target) as target'), DB::raw('sum(a.realization) as realisasi'))
                    ->join('marketing_targets as b','a.marketing_target_id','=','b.id')
                    ->where('a.month_number', $month)
                    ->where('b.year', $year)
                    ->first();

        return $target;
    }

    public static function getSasaranMarketing($groupDivisionID)
    {
        $sasaran = DB::table('proker_tahunan_detail as a')
                    ->select('b.uid','a.pkt_id','a.pktd_seq','a.pktd_title')
                    ->join('proker_tahunan as b','a.pkt_id','=','b.id')
                    ->where('b.division_group_id', $groupDivisionID)
                    ->orderBy('a.pktd_title','desc')->get();

        return $sasaran;
    }

    public static function getSasaranSearchMarketing($groupDivisionID, $search)
    {
        $sasaran = DB::table('proker_tahunan_detail as a')
                    ->select('b.uid','a.pkt_id','a.pktd_seq','a.pktd_title')
                    ->join('proker_tahunan as b','a.pkt_id','=','b.id')
                    ->where('b.division_group_id', $groupDivisionID)
                    ->where('a.pktd_title', 'LIKE',"%$search%")
                    ->orderBy('a.pktd_title','desc')->get();

        return $sasaran;
    }

    public static function getSasaranUmum($year, $pkb_pkt_id)
    {
        $sasaran_umum = DB::table('proker_bulanan')
                        ->select(DB::raw('MONTH(pkb_start_date) as bulan'))
                        ->where('pkb_pkt_id', $pkb_pkt_id)
                        ->whereYear('pkb_start_date', $year)
                        ->groupBy('bulan')
                        ->orderBy('bulan','asc')
                        ->get();

        return $sasaran_umum;
    }

}
