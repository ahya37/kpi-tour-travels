<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupDivision extends Model
{
    use HasFactory;

    protected $table = 'group_divisions';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];

    public static function get_data_list_group_division()
    {
        return DB::select('SELECT * FROM group_divisions ORDER BY created_at DESC');
    }
}
