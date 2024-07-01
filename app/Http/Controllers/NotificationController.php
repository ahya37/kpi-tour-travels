<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NotificationRequest;
use App\Helpers\ResponseFormatter;

class NotificationController extends Controller
{
    public function createNotificationAlumniJamaah(NotificationRequest $request)
    {
        DB::beginTransaction();
        try {

            $requests = $request->validated();

            // get data alumin yang remember = 'Y'
            $alumni   = DB::table('alumni_prospect_material as a')
                        ->select('c.name','c.user_id','a.id',
                            DB::raw("
                                (select count(a1.id) from detail_alumni_prospect_material as a1 where a1.remember = 'Y' and a1.alumni_prospect_material_id = a.id) as remember
                            ")
                        )
                        ->join('job_employees as b','a.job_employee_id','=','b.id')
                        ->join('employees as c','b.employee_id','=','c.id')
                        ->having('remember','!=',0)
                        ->get();

            #get data detail alumninya 
            $res_almuni = [];
            foreach ($alumni as $key => $value) {

                #title
                $title = '('.$value->remember.') Alumni Perlu dihubungi kembali !';

                // $detail_almuni = DB::table('detail_alumni_prospect_material as a')->select('a.*')->where('remember','Y')->where('alumni_prospect_material_id', $value->id)->get();

                $save = DB::table('notification')->insert([
                    'title' => $title,
                    'detail' => $requests['detail'],
                    'create_at' => date('Y-m-d H:i:s'),
                    'user_id' => $value->user_id,
                    'category_notification_id' => $requests['category_notification_id']
                ]);

                $res_almuni[] = [
                    'save' => $save
                ];
            }

            DB::commit();

            return ResponseFormatter::success([
                'message' =>  $res_almuni
            ]);

        } catch (\Exceprion $e) {
           DB::rollback();
           return ResponseFormatter::error([
                'message' =>  $e->getMessage()
            ]);

        }
    }
}
