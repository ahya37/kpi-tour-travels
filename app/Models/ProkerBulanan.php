<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProkerBulanan extends Model
{
    use HasFactory;

    protected $table = 'proker_bulanan';
	protected $guarded = [];

    public static function getProkerBulananByDivisiUser()
    {
        
    }
}
