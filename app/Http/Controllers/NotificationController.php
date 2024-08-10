<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NotificationRequest;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

            $notifications = DB::table('notification as a')
                            ->select('a.id','a.title','a.detail','a.user_id','b.is_read','a.create_at')
                            ->leftJoin('notification_read as b','b.notification_id','=','a.id')
                            ->where('a.user_id', $user_id)
                            ->orderBy('a.create_at','desc')
                            ->get();

            // $res_notifications = [];
            // foreach ($notifications as $key => $value) {

            //     $createdAt = Carbon::parse($value->create_at);
            //     $now = Carbon::now();
            //     $minutesDiff = $createdAt->diffInMinutes($now);

            //     $res_notifications[] = [
            //         'id' => $value->id,
            //         'title' => $value->title,
            //         'detail' => $value->detail,
            //         'user_id' => $value->user_id,
            //         'is_read' => $value->is_read,
            //         'minutesDiff' => $minutesDiff
            //     ];
            // }

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

    public function detailShowNotificationAlumni($notification_id)
    {
        

        DB::beginTransaction();
        try {

           // get almuni prospek materil by user_id
            // get data alumin yang remember = 'Y'
            $user_id = Auth::user()->id;


            // cek apakah notifikasi tersebut sudah di read, jika sudah jangan input
            $cek = DB::table('notification_read')->where('notification_id', $notification_id)->where('user_id', $user_id)->count();
            if ($cek == 0) {
                //  insert is read notification
                DB::table('notification_read')->insert([
                    'is_read' => 1,
                    'notification_id' => $notification_id,
                    'user_id' => $user_id
                ]);

            }else{
                DB::table('notification_read')->where('notification_id', $notification_id)->where('user_id', $user_id)->update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

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

            DB::commit();

            return view('marketings.list-remember-prospect-alumni',$data);

        } catch (\Exceprion $e) {
           DB::rollback();
           Log::channel('daily')->error($e->getMessage());
           return ResponseFormatter::error([
                'message' =>  $e->getMessage()
            ]);

        }
    }
}
