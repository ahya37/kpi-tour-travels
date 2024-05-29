<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupDivision;
use App\Models\ProkerBulanan;

class ActivityController extends Controller
{
    public function daily()
    {

       return view('activities.daily', [
        'title' => 'Aktivitas Harian'
       ]);
    }

    public function loadModalFormDailyActivities()
    {
        $groupDivisions = GroupDivision::select('id', 'name')->get();

        #get data proker bulanan berdasarkan divisi
        #divisi yg di get berdasarkan user login di sebagai divisi
        $proker_bulanan = ProkerBulanan::select('id','pkb_title')->get();

        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <label class="col-sm-2 col-form-label">Divisi</label>
                                <div class="col-sm-10">
                                <select class="form-control select2" name="month" id="month">';

        foreach ($groupDivisions as $key => $value) {
            $modalContent = $modalContent . '<option value="' . $value->id . '">' . $value->name . '</option>';
        }

        $modalContent = $modalContent . '</select>
                        </div>
                        </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Tanggal</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>';

        return response()->json([
            'modalContent' => $modalContent,
            'proker_bulanan' => $proker_bulanan

        ]);
    }
}
