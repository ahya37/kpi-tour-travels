<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProkerHarian extends Model
{
    use HasFactory;

    protected $table = 'proker_harian';
	protected $guarded = [];

    public static function getProkerHarianByProkerBulanan($pkh_pkb_id)
    {
        return DB::table('proker_harian as a')
                ->select('a.pkh_date','a.pkh_start_time','a.pkh_end_time','a.pkh_title','b.name')
                ->join('users as b','a.created_by','=','b.id')
                ->whereRaw(DB::raw("SUBSTRING_INDEX(a.pkh_pkb_id, '|', 1) = '$pkh_pkb_id'"))
                ->orderBy('a.pkh_date','asc')
                ->get();
    }

    public static function getAktivitasHarianByBulanTahunAndDivisi($groupDivisionID, $month, $year = 2024)
    {
        $aktivitas = DB::select(
            "
                SELECT a.pkh_date, a.pkh_start_time , a.pkh_title, d.name as pic  from proker_harian as a
                    join proker_bulanan as b on substring_index(a.pkh_pkb_id, '|', 1) = b.uuid
                    join proker_tahunan as c on substring_index(b.pkb_pkt_id, '|', 1) = c.uid
                    join users as d on a.created_by = d.id 
                    where c.division_group_id = '$groupDivisionID'
                    and month(a.pkh_date) = $month
                    and year(a.pkh_date)  = $year
                    order by a.pkh_date asc
            "
        );

        return $aktivitas;
    }

    public static function getAktivitasHarianByBulanTahunAndDivisiByPic($groupDivisionID, $month,$pic,$year = 2024)
    {
        $aktivitas = DB::select(
            "
                SELECT a.pkh_date, a.pkh_start_time , a.pkh_title, d.name as pic  from proker_harian as a
                    join proker_bulanan as b on substring_index(a.pkh_pkb_id, '|', 1) = b.uuid
                    join proker_tahunan as c on substring_index(b.pkb_pkt_id, '|', 1) = c.uid
                    join users as d on a.created_by = d.id 
                    where c.division_group_id = '$groupDivisionID'
                    and month(a.pkh_date) = $month
                    and year(a.pkh_date)  = $year
                    and d.id = $pic
                    order by a.pkh_date asc
            "
        );

        return $aktivitas;
    }

    public static function getAktivitasHarianByBulanTahunAndDivisiByTest($groupDivisionID,$month,$year)
    {
        $aktivitas = DB::table('proker_harian as a')
                    ->select('a.pkh_date', 'a.pkh_start_time','a.pkh_title', 'd.name as pic')
                    ->join('proker_bulanan as b', function($a){
                        $a->on(DB::raw("SUBSTRING_INDEX(a.pkh_pkb_id, '|', 1)"),'=', 'b.uuid');
                    })
                    ->join('proker_tahunan as c', function($b){
                        $b->on(DB::raw("SUBSTRING_INDEX(b.pkb_pkt_id, '|', 1)"),'=','c.uid');
                    })
                    ->join('users as d','a.created_by','=','d.id')
                    ->where('c.division_group_id',$groupDivisionID)
                    // ->where('d.id', $pic)
                    ->whereMonth('a.pkh_date',$month)
                    ->whereYear('a.pkh_date',$year);

        return $aktivitas;
    }
	
	public static function getProkerHarianByBulananAndUser($pkh_pkb_id,$created_by)
	{
		$proker_harian = DB::table('proker_harian as a')
						->select('a.pkh_date','a.pkh_start_time', 'a.pkh_end_time', 'a.pkh_title', 'b.name')
						->join('users as b','a.created_by','=','b.id')
						->where(DB::raw("SUBSTRING_INDEX(a.pkh_pkb_id, '|', 1)"),'=', $pkh_pkb_id)
						->where('a.created_by', $created_by)
						->get();
						
		return $proker_harian;
					
	}
}
