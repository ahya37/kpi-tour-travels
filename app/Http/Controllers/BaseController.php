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

    // 25 JUNI 2024
    // NOTE : GET SUB DIVISION BY CURRENT ROLE
    public function getCurrentSubDivision(Request $request)
    {
        $role   = $request->all()['sendData']['role_name'];
        $userID = $request->all()['sendData']['user_id'];
        $getData    = BaseService::doGetCurrentSubDivision($role, $userID);

        if(!empty($getData)) {
            $output     = array(
                "status"    => 200,
                "success"   => true,
                "message"   => "Berhasil",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "status"    => 500,
                "success"   => false,
                "message"   => "Gagal",
                "data"      => [],
            );
        }

        return Response::json($output, $output['status']);
    }

    // 09 juli 2024
    // note : fungsi pengambilan data master program untuk proker bulanan
    public function getMasterProgram()
    {
        $getData    = BaseService::doGetMasterProgram();

        if(!empty($getData)) {
            $output     = array(
                "success"   => true,
                "status"    => 200,
                "message"   => "Data Ditemukan",
                "data"      => $getData,
            );
        } else {
            $output     = array(
                "success"   => false,
                "status"    => 404,
                "message"   => "Data Tidak Ditemukan"
            );
        }

        return Response::json($output, $output['status']);
    }
}
