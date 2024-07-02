<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';
	protected $primaryKey = 'id'; 
    protected $keyType = 'string';
	public $incrementing = false; 	
	protected $guarded = [];
}
