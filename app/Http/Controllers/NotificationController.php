<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NotificationRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

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
           Log::chanel('daily')->error( $e->getMessage());
           return ResponseFormatter::error([
                'message' =>  $e->getMessage()
            ]);

        }
    }

    public function showNotificationByUserLogin($user_id)
    {

        DB::beginTransaction();
        try {

            $notifications = DB::table('notification')->select('title','detail','user_id')->where('user_id', $user_id)->get();

            return ResponseFormatter::success([
                'notifications' =>  $notifications,
                'count_notification' => count($notifications)
            ]);

        } catch (\Exceprion $e) {
           return ResponseFormatter::error([
                'message' =>  $e->getMessage()
            ]);

        }
    }

    public function detailShowNotificationAlumni()
    {
        // get almuni prospek materil by user_id
         // get data alumin yang remember = 'Y'
         $user_id = Auth::user()->id;

         $alumni   = DB::table('alumni_prospect_material as a')
                    ->select('a.id','a.label')
                    ->join('job_employees as b','a.job_employee_id','=','b.id')
                    ->join('employees as c','b.employee_id','=','c.id')
                    ->where('c.user_id',$user_id)
                    ->get();

        $results = [];
        foreach ($alumni as $key => $value) {
            $list_alumni = DB::table('detail_alumni_prospect_material as a')
                            ->select('a.name','a.telp','notes')->where('remember','Y')
                            ->where('alumni_prospect_material_id', $value->id)
                            ->get();
            $results[] = [
                'label' => $value->label,
                'list_alumni' => $list_alumni
            ];
        }

        $data = [
            'title' => 'Daftar Alumni',
            'subtitle' => 'Daftar alumni untuk dihubungi kembali',
            'alumni' => $results
        ];

        return view('marketings.list-remember-prospect-alumni',$data);
    }
}
