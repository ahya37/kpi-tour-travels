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
        if(count($getData) > 1) {
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
}
