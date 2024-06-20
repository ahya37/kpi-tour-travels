<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BaseService;
use Response;

class BaseController extends Controller
{
    public function getGroupDivision()
    {
        $getData    = BaseService::getDataGroupDivision();
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 500,
                "data"      => [],
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function getGroupDivisionWRole()
    {
        $getData    = BaseService::doGetGroupDivisionWRole();
        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "data"      => $getData,
            );
        }
        
        return Response::json($output, $output['status']);
    }

    public function getProgramUmrah(Request $request, $program)
    {
        $getData    = BaseService::doGetProgramUmrah($request, $program);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil Mengambil data Program ".$program,
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 404,
                "success"   => false,
                "message"   => "Gagal Mengambil Data Program ".$program,
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }
}
