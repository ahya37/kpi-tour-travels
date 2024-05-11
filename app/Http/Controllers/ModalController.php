<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModalController extends Controller
{
    public function loadModalMarketingTarget()
    {
        sleep(1);
        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Tahun</label>
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                <div class="col-sm-10">
                                <input id="year" type="text" name="year"
                                        class="form-control" required></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json(['modalContent' => $modalContent]);
    }
}
