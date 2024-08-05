<?php

namespace App\Http\Controllers;

use App\Models\GroupDivision;
use Illuminate\Http\Request;

class WorkPlanController extends Controller
{
    public function index()
    {
        return view('workplans.index', [
            'title' => 'Rencana Kerja',
        ]);
    }

    public function loadModalWorkPlans()
    {
        $groupDivisions = GroupDivision::select('id', 'name')->get();


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

        return response()->json(['modalContent' => $modalContent]);
    }
}
