<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailAlumniProspekMaterial extends Model
{
    use HasFactory;

    protected $table = 'detail_alumni_prospect_material';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function getDetailAlumniProspekMaterialByAlumniProspekMaterial($alumniProspectMaterialId)
    {
        return DB::table('detail_alumni_prospect_material as a')
                ->select('a.id','a.id_members','a.name','a.telp','a.address','a.is_respone','b.name as reason','a.notes')
                ->leftJoin('reasons as b','a.reason_id','=','b.id')
                ->where('a.alumni_prospect_material_id', $alumniProspectMaterialId);

    }
}
