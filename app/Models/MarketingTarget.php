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
}
