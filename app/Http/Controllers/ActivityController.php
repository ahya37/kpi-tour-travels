<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupDivision;
use App\Models\ProkerBulanan;
use Illuminate\Support\Facades\Auth;

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
        #get data proker bulanan berdasarkan divisi
        #divisi yg di get berdasarkan user login di sebagai divisi
        $user = Auth::user()->id;
        $proker_bulanan = ProkerBulanan::getProkerBulananByDivisiUser($user);

        #get data uraian tugas bulanan berdasarkan divisi user login
        

        $modalContent = '<form id="form" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <label class="col-sm-2 col-form-label">Klasifikasi</label>
                                <div class="col-sm-10">
                                <select class="form-control select2" name="month" id="month"> 
                                <option value="">-Pilih Klasifikasi-</option>
                                ';

        foreach ($proker_bulanan as $key => $value) {
            $modalContent = $modalContent . '<option value="' . $value->id . '">' . $value->pkt_title . '</option>';
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
