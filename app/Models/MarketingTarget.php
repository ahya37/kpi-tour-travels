<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarketingTarget extends Model
{
    use HasFactory;

    protected $table = 'marketing_targets';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];

    public static function getMarketingtargets()
    {
        return DB::table('marketing_targets')->select('id','year','total_target','total_realization','total_difference');
    }

    public static function getReportUmrahBulanan($marketing_target_id)
    {
        return DB::table('detailed_marketing_targets as a')
               ->select('a.month_number','a.month_name',
                    DB::raw('
                        (select sum(a1.target) from detailed_marketing_targets as a1 where a1.month_number = a.month_number) as terget
                    '),
                    DB::raw('
                        (select sum(a2.realization) from detailed_marketing_targets as a2 where a2.month_number = a.month_number) as realisasi
                    '),
                    DB::raw('
                        (select sum(a3.difference) from detailed_marketing_targets as a3 where a3.month_number = a.month_number) as selisih
                    ')
               )
               ->join('programs as b','a.program_id','=','b.id')
               ->where('a.marketing_target_id', $marketing_target_id);
    }

    public static function getProgramBytargetBulanan($marketing_target_id)
    {
        return DB::table('detailed_marketing_targets as a')
               ->select('b.name as program', 'a.target' , 'a.realization as realisasi' , 'a.difference as selisih','b.color')
               ->join('programs as b','a.program_id','=','b.id')
               ->where('b.is_active', 'Y')
               ->where('a.marketing_target_id', $marketing_target_id);
            //    ->where('a.month_number', $bulan)
            //    ->orderBy('b.sequence','asc')
            //    ->get();
    }

    public static function getProgramBytargetBulananAndProgramId($bulan, $marketing_target_id, $program_id)
    {
        return DB::table('detailed_marketing_targets as a')
               ->select('b.name as program', 'a.target' , 'a.realization as realisasi' , 'a.difference as selisih')
               ->join('programs as b','a.program_id','=','b.id')
               ->where('a.marketing_target_id', $marketing_target_id)
               ->where('a.month_number', $bulan)
               ->where('b.is_active', 'Y')
               ->where('a.program_id', $program_id)
               ->orderBy('b.sequence','asc')
               ->first();
    }

    public static function getPencapaianUmrahPerProgramByTahun($marketing_target_id)
    {
        $umrah =  DB::table('detailed_marketing_targets as a')
                ->select('b.name',
                    DB::raw('(sum(a.target)) as target'),
                    DB::raw('(sum(a.realization)) as realisasi'),
                    DB::raw('(sum(a.difference)) as selisih'),
                )
                ->join('programs as b','a.program_id','=','b.id')
                ->where('a.marketing_target_id', $marketing_target_id);

        return $umrah;
    }

    public static function getPencapaianUmrahPerBulanByTahun($marketing_target_id)
    {
        return DB::table('detailed_marketing_targets as a')
                ->select('a.month_number', 'a.month_name',
                    DB::raw('(sum(a.target)) as target'),
                    DB::raw('(sum(a.realization)) as realisasi'),
                    DB::raw('(sum(a.difference)) as selisih'),
                )
                ->join('programs as b','a.program_id','=','b.id')
                ->where('a.marketing_target_id', $marketing_target_id);
    }
    
    public static function getPencapaianUmrahPerPicByTahun($marketing_target_id)
    {
        $umrah = DB::table('pic_detailed_marketing_target as a')
                ->select('c.name',
                    DB::raw('(sum(a.realization)) as realisasi') 
                )->join('detailed_marketing_targets as b','a.detailed_marketing_target_id','=','b.id')
                ->join('employees as c','a.employee_id','=','c.id')
                ->where('b.marketing_target_id', $marketing_target_id);

        return $umrah;
    }

    public static function getPencapaianUmrahPerSumber($marketing_target_id)
    {
        $umrah = DB::table('pic_detailed_marketing_target_list_jamaah')
                ->select('sumber',
                    DB::raw('count(id) as realisasi')
                )
                ->where('marketing_target_id', $marketing_target_id);

        return $umrah;
    }

    public static function getPencapaianUmrahAlumni($marketing_target_id)
    {
        $umrah = DB::table('pic_detailed_marketing_target_list_jamaah')
                ->select('is_alumni',
                    DB::raw('count(id) as jamaah')
                )
                ->where('marketing_target_id', $marketing_target_id);

        return $umrah;
    }

    public static function getRealisasiUmrahPerBulanByTahun($year, $month)
    {
        $umrah = DB::table('detailed_marketing_targets as a')
                ->select(DB::raw('SUM(a.realization) as pencapaian'),DB::raw('SUM(a.target) as target'),DB::raw('SUM(a.difference) as selisih'))
                ->join('marketing_targets as b','a.marketing_target_id','=','b.id')
                ->where('b.year', $year)
                ->where('a.month_number', $month)
                ->first();

        return $umrah;
    }
}
