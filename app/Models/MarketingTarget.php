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
               ->where('a.marketing_target_id', $marketing_target_id)
               ->groupBy('a.month_name','a.month_number')
               ->orderBy('a.month_number','asc')
               ->get();
    }

    public static function getProgramBytargetBulanan($bulan, $marketing_target_id)
    {
        return DB::table('detailed_marketing_targets as a')
               ->select('b.name as program', 'a.target' , 'a.realization as realisasi' , 'a.difference as selisih','b.color')
               ->join('programs as b','a.program_id','=','b.id')
               ->where('a.month_number', $bulan)
               ->where('b.is_active', 'Y')
               ->where('a.marketing_target_id', $marketing_target_id)
               ->orderBy('b.sequence','asc')
               ->get();
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
        return DB::table('detailed_marketing_targets as a')
                ->select('b.name',
                    DB::raw('(sum(a.target)) as target'),
                    DB::raw('(sum(a.realization)) as realisasi'),
                    DB::raw('(sum(a.difference)) as selisih'),
                )
                ->join('programs as b','a.program_id','=','b.id')
                ->where('a.marketing_target_id', $marketing_target_id)
                ->groupBy('b.name')
                ->orderBy('realisasi','desc')
                ->get();
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
                ->where('a.marketing_target_id', $marketing_target_id)
                ->groupBy('a.month_number', 'a.month_name')
                ->orderBy('a.month_number','asc')
                ->get();
    }
}
