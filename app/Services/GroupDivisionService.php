<?php 
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\GroupDivision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\LogHelper;

date_default_timezone_set('Asia/Jakarta');

class GroupDivisionService
{
    public static function ListGroupDivision($cari)
    {
        $data_where     = [
            ['id','LIKE','%'.$cari.'%']
        ];
        $get_data   = DB::table('group_divisions')
                        ->where($data_where)
                        ->orderBy('created_at', 'DESC')
                        ->get();
        
        return $get_data;
    }

    public static function doSimpanGroupDivisions($jenis, $data, $ip)
    {
        DB::beginTransaction();

        // var_dump($jenis, $data);die();

        if($jenis == 'add') {
            $data_simpan    = array(
                "id"    => Str::random(30),
                "name"  => $data['groupDivisionName'],
                "roles_id"      => $data['groupDivisionRole'],
                "created_by"    => Auth::user()->id,
                "updated_by"    => Auth::user()->id,
                "created_at"    => date('Y-m-d H:i:s'),
                "updated_at"    => date('Y-m-d H:i:s'),
            );

            try {
                DB::table('group_divisions')->insert($data_simpan);
                DB::commit();
                $output     = array(
                    "status"    => "berhasil",
                    "errMsg"    => null,
                );
                // CREATE LOG
                LogHelper::create("add", "Berhasil Menambahkan Grup Divisi Baru (".$data_simpan['name'].")", $ip);
            } catch (\Exception $e) {
                DB::rollback();
                $output  = array(
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                );
                // CREATE LOG
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create("add", "Gagal Menambahkan Grup Divsii Baru", $ip);
            }
        } else if($jenis == 'edit') {
            $data_where     = [
                "id"    => $data['groupDivisionID'],
            ];

            $data_update    = [
                "name"          => $data['groupDivisionName'],
                "roles_id"      => $data['groupDivisionRole'],
                "updated_by"    => Auth::user()->id,
                "updated_at"    => date('Y-m-d H:i:s'),
            ];

            try {
                DB::table('group_divisions')
                    ->where($data_where)
                    ->update($data_update);
                DB::commit();
                $output     = array(
                    "status"    => "berhasil",
                    "message"   => null,
                );
                // CREATE LOG
                LogHelper::create("edit", "Berhasil Mengubah Grup Divisi (".$data_update['name'].")", $ip);
            } catch(\Exception $e) {
                DB::rollback();
                $output     = array(
                    "status"    => "gagal",
                    "errMsg"    => $e->getMessage(),
                );
                // CREATE LOG
                Log::channel('daily')->error($e->getMessage());
                LogHelper::create("add", "Gagal Mengubah Grup Divisi (error : ".$e->getMessage().")", $ip);
            } 
        }

        return $output;

        // $data_simpan = array(
        //     "id"    => Str::random(30),
        //     "name"  => $data['group_division_name'],
        //     "created_by"    => Auth::user()->id,
        //     "updated_by"    => Auth::user()->id,
        //     "created_at"    => date('Y-m-d H:i:s'),
        //     "updated_at"    => date('Y-m-d H:i:s'),
        // );

        // try {
        //     GroupDivision::create($data_simpan);
        //     DB::commit();
        //     return 'berhasil';
        // } catch(\Exection $e) {
        //     DB::rollback();
        //     return 'gagal';
        // }
    }

    public static function doUpdateGroupDivisions($data)
    {
        DB::beginTransaction();
        $data_where     = array(
            "id"        => $data['gdID']
        );
        $data_update    = array(
            "name"      => $data['gdName'],
        );

        try {
            GroupDivision::where($data_where)->update($data_update);
            DB::commit();
            return 'berhasil';
        } catch(\Exception $e) {
            DB::rollback();
            return 'gagal';
        }
    }

    public static function doDeleteGroupDivisions($data)
    {
        DB::beginTransaction();

        $data_where     = array(
            "id"        => $data['gdID'],
        );

        $data_update    = array(
            "is_active" => "0",
        );

        try {
            GroupDivision::where($data_where)->update($data_update);
            DB::commit();
            return 'berhasil';
        } catch(\Exception $e) {
            DB::rollback();
            return 'gagal';
        }
    }
}