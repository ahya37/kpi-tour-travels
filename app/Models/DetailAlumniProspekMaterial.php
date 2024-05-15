<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAlumniProspekMaterial extends Model
{
    use HasFactory;

    protected $table = 'detail_alumni_prospect_material';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
