<?php 
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\SubDivision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

date_default_timezone_set('Asia/Jakarta');

class SubDivisionService
{
    public static function dataSubDivision($data)
    {
        $keyword    = $data['keyword'];

        $query      = DB::select(
            "
            SELECT 	a.id as sub_division_id,
                    a.name as sub_division_name,
                    b.id as group_division_id,
                    b.name as group_division_name,
                    a.created_at
            FROM 	sub_divisions a
            JOIN 	group_divisions b ON a.division_group_id = b.id
            WHERE   (a.id LIKE '%".$keyword."%' OR a.name LIKE '%".$keyword."%')
            ORDER BY created_at DESC
            "
        );
        return $query;
    }

    public static function doSimpanDataSubDivision($data, $jenis)
    {
        DB::beginTransaction();
        if($jenis == 'add') {
            $data_simpan    = array(
                "id"                => Str::random(30),
                "name"              => $data['subDivisionName'],
                "division_group_id" => $data['groupDivisionID'],
                "created_by"        => Auth::user()->id,
                "updated_by"        => Auth::user()->id,
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),
            );
            // SubDivision::create($data_simpan);
            DB::table('sub_divisions')->insert($data_simpan);
        } else if($jenis == 'edit') {
            $data_where     = array(
                "id"    => $data['subDivisionID'],
            );
            
            $data_update    = array(
                "name"              => $data['subDivisionName'],
                "division_group_id" => $data['groupDivisionID'],
            );

            DB::table('sub_divisions')->where($data_where)->update($data_update);
        }
        
        try {
            DB::commit();
            $output     = [
                "status"    => "berhasil",
                "errMsg"    => null,
            ];
        } catch(\Exection $e) {
            DB::rollback();
            $output     = [
                "status"    => "gagal",
                "errMsg"    => $e->getMessage(),
            ];
        }

        return $output;
    }
    
    public static function postDataSubDivisionNew($data)
    {
        DB::beginTransaction();

        $data_simpan    = array(
            "id"                => Str::random(30),
            "name"              => $data['sdName'],
            "division_group_id" => $data['gdID'],
            "created_by"        => Auth::user()->id,
            "updated_by"        => Auth::user()->id,
            "created_at"        => date('Y-m-d H:i:s'),
            "updated_at"        => date('Y-m-d H:i:s'),
        );

        try {
            SubDivision::create($data_simpan);
            DB::commit();
            return 'berhasil';
        } catch(\Exection $e) {
            DB::rollback();
            return 'gagal';
        }
    }

    public static function postDataSubDivisionEdit($data)
    {
        DB::beginTransaction();

        $data_update    = array(
            "name"              => $data['sdName'],
            "division_group_id" => $data['gdID'],
        );

        $data_where     = array(
            'id'        => $data['sdID'],
        );

        try {
            SubDivision::where($data_where)->update($data_update);
            DB::commit();
            return 'berhasil';
        } catch(\Exception $e) {
            DB::rollback();
            return 'gagal';
        }
    }
}