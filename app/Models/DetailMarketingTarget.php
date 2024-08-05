<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailMarketingTarget extends Model
{
    use HasFactory;

    protected $table = 'detailed_marketing_targets';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];

    public static function getDetailMarketingTargetWhereMarketingId($detailMarketingTargetId)
    {
        return DB::table('detailed_marketing_targets as a')
                ->select('a.id','a.month_name','b.name as program','a.target','a.realization','a.difference')
                ->join('programs as b','a.program_id','=','b.id')
                ->where('marketing_target_id',$detailMarketingTargetId);  
    }
    public static function getProgramByYearAndMonth($year, $month = 0, $tipe)
    {
        return DB::table('detailed_marketing_targets as a')
                ->select('c.year','a.id', 'b.name as tipe_name','a.month_number','a.target', 'a.realization','a.difference')
                ->join('programs as b','a.program_id','=','b.id')
                ->join('marketing_targets as c','a.marketing_target_id','=','c.id')
                ->where('c.year', $year)
                ->where('a.month_number', $month)
                ->where('b.name', $tipe)
                ->first();
    }

}
