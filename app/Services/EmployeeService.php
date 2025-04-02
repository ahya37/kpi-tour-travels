<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\Employee;
use App\Models\User;
use App\Models\JobEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Helpers\LogHelper;
date_default_timezone_set('Asia/Jakarta');

class EmployeeService
{

    public static function getDataEmployee($cari)
    {
        $rawQuery   = DB::select(
            "
            SELECT 	b.id as employee_id,
                    b.name as employee_name,
                    c.id as group_division_id,
                    c.name as group_division_name,
                    d.id as sub_division_id,
                    d.name as sub_division_name,
                    e.email as employee_email,
                    f.role_id,
				    g.name as role_name,
                    e.is_active as user_active
            FROM 	job_employees a
            INNER JOIN employees b ON a.employee_id = b.id
            INNER JOIN group_divisions c ON a.group_division_id = c.id
            INNER JOIN sub_divisions d ON a.sub_division_id = d.id
            INNER JOIN users e ON b.user_id = e.id
            INNER JOIN model_has_roles f ON b.user_id = f.model_id
            INNER JOIN roles g ON f.role_id = g.id
            WHERE 	(a.id LIKE '%$cari%' OR b.id LIKE '%$cari%' OR c.id LIKE '%$cari%' OR d.id LIKE '%$cari%')
            ORDER BY e.is_active, b.name ASC
            "
        );
        return $rawQuery;
    }

    public static function ambilDataDivisionGlobal($keyword)
    {
        $query  = DB::select(
            "
            SELECT 	a.id as group_division_id,
                    a.name as group_division_name,
                    b.id as sub_division_id,
                    b.name as sub_division_name
            FROM 	group_divisions a
            INNER JOIN sub_divisions b ON a.id = b.division_group_id
            ORDER BY a.name ASC
            "
        );
            
        return $query;
    }
    
    public static function doSaveDataEmployee($data, $jenis)
    {
        DB::beginTransaction();

        $employee_id    = $data['data']['employee_id'];
        $employee_name  = $data['data']['employee_name'];
        $employee_group_division    = $data['data']['employee_group_division'];
        $employee_username  = $data['data']['employee_username'];
        $employee_role_id   = $data['data']['employee_role'];

        $user_id        = $data['user_id'];
        $ip             = $data['ip_address'];

        if($jenis == 'add') {
            // CHECK NAME
            $query_get_employee     = DB::table('employees')
                                        ->select('name', 'user_id')
                                        ->where('name', '=', $employee_name)
                                        ->get();
            if(count($query_get_employee) > 0) {
                $output     = [
                    'is_success'    => false,
                    'status_code'   => 401,
                    'data'          => [],
                    'message'       => 'Duplikat Data',
                ];
            } else {
                // INSERT TO TABLE USERS
                $data_insert_users  = [
                    'name'      => $employee_name,
                    'email'     => $employee_username,
                    'password'  => Hash::make('rahasia'),
                    'is_active' => "1",
                    'created_at'=> date('Y-m-d H:i:s'),
                ];

                DB::table('users')->insert($data_insert_users);

                // GET LAST INSERT ID
                $new_employee_id    = DB::getPdo()->lastInsertId();

                // INSERT TO ROLES
                $data_insert_role_user  = [
                    'role_id'       => $employee_role_id,
                    'model_type'    => 'App\Model\User',
                    'model_id'      => $new_employee_id
                ];

                DB::table('model_has_roles')->insert($data_insert_role_user);

                // INSERT TO TABLE EMPLOYEES
                $data_insert_employees  = [
                    'id'            => Str::random(30),
                    'user_id'       => $new_employee_id,
                    'name'          => $employee_name,
                    'created_by'    => $user_id,
                    'updated_by'    => $user_id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];

                DB::table('employees')->insert($data_insert_employees);

                // INSERT TO TABLE JOB EMPLOYEES
                $data_insert_job_employees  = [
                    'id'                => Str::random(30),
                    'employee_id'       => $data_insert_employees['id'],
                    'sub_division_id'   => explode(' | ', $employee_group_division)[1],
                    'group_division_id' => explode(' | ', $employee_group_division)[0],
                    'created_by'        => $user_id,
                    'updated_by'        => $user_id,
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i;s'),
                ];

                DB::table('job_employees')->insert($data_insert_job_employees);

                try {
                    DB::commit();
                    
                    $output     = [
                        'is_success'    => true,
                        'status_code'   => 201,
                        'message'       => 'Berhasil Menambahkan Data Akun Pegawai',
                        'data'          => [],
                    ];

                    LogHelper::create('add', $output['message'] . " ID : " . $data_insert_job_employees['employee_id'], $ip);
                } catch (\Exception $e) {
                    DB::rollback();

                    $output     = [
                        'is_success'    => false,
                        'status_code'   => 500,
                        'message'       => 'Internal Server Error',
                        'data'          => []
                    ];

                    Log::channel('daily')->error($e->getMessage());
                    LogHelper::create('error_system', $output['message'], $ip);
                }
            }
        } else if ($jenis == 'edit') {
            // UPDATE JOB EMPLOYEES
            $data_where_job_employees   = [
                'employee_id'   => $employee_id,
            ];

            $data_update_job_employees  = [
                'sub_division_id'      => explode(' | ', $employee_group_division)[1],
                'group_division_id'    => explode(' | ', $employee_group_division)[0],
            ];

            DB::table('job_employees')->where($data_where_job_employees)->update($data_update_job_employees);

            // UPDATE ROLES
            // GET USER ID
            $query_get_user_id  = DB::table('employees')->select('user_id')->where('id', '=', $employee_id)->get();
            
            $data_where_update_roles    = [
                'model_id'  => $query_get_user_id[0]->user_id,
            ];

            $data_update_role           = [
                'role_id'   => $employee_role_id,
            ];

            DB::table('model_has_roles')->where($data_where_update_roles)->update($data_update_role);

            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Update Data Akun Pegawai',
                    'data'          => []
                ];

                LogHelper::create('edit', $output['message'] . "id : " . $employee_id, $ip);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('daily')->error($e->getMessage());

                $output = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Internal Server Error',
                    'data'          => []
                ];

                LogHelper::create('error_system', $output['message'], $ip);
            }
        }

        return $output;
    }

    // GET DATA
    public static function getData($type, $value)
    {
        if($type == 'roles') {
            $query  = DB::select(
                "
                SELECT  id as role_id,
                        name as role_name
                FROM    roles
                ORDER BY id ASC
                "
            );

            return $query;
        }
    }

    // 27 AGUSTUS 2024
    // NOTE : AMBIL DATA EMPLOYEE UNTUK KEBUTUHAN GLOBAL
    public static function do_get_data_employee_global()
    {
        return DB::select(
            "
            SELECT  user_id as emp_id,
                    name as emp_name
            FROM    employees
            ORDER BY user_id ASC
            "
        );
    }

    // 06 SEPTEMBER 2024
    // NOTE : MELAKUKAN PERUBAHAN STATUS EMPLOYEE DARI ACTIVE => NON ACTIVE ATAU SEBALIKNYA
    public static function do_ubah_status_employee($data)
    {
        $emp_id     = $data['emp_id'];
        $emp_status = $data['emp_status'];
        $ip         = $data['ip'];

        DB::beginTransaction();

        // DAPETIN ID USER
        $query_get_id_user  = DB::table('employees')->select('user_id')->where(['id' => $emp_id])->get();
        $id_selected_user   = $query_get_id_user[0]->user_id;

        // UPDATE TABLE USERS
        $data_where_update_user     = [
            "id"        => $id_selected_user,
        ];
        
        $data_for_update_user       = [
            "is_active" => $emp_status,
            "updated_at"=> date('Y-m-d H:i:s'),
        ];
        
        DB::table('users')->where($data_where_update_user)->update($data_for_update_user);

        try {
            DB::commit();
            
            $output     = [
                'is_success'    => true,
                'status_code'   => 201,
                'message'       => $emp_status == "1" ? 'Berhasil Mengaktifkan Akun' : 'Berhasil Menonaktifkan Akun',
                'data'          => []
            ];

            LogHelper::create('edit', $output['message'] . ' user id : '. $id_selected_user, $ip);
        } catch(\Exception $e) {
            DB::rollBack();
            Log::channel('daily')->error($e->getMessage());

            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => $emp_status == "1" ? 'Gagal Mengaktifkan Akun' : 'Gagal Menonaktifkan Akun',
                'data'          => []
            ];
            
            LogHelper::create('error_system', $output['message'], $ip);
        }

        return $output;
    }

    // UPDATE UNTUK AMBIL DATA ABSENSI
    public static function get_absensi_ambil_data_user($data)
    {
        $user_id    = $data['user_id'];
        $year       = $data['year'];
        $month      = $data['month'];

        $query_absensi  = DB::table('tm_presence as a')
                        ->join('users as b', 'a.prs_user_id', '=', 'b.id')
                        ->select('b.name as prs_name', 'a.prs_date', 'a.prs_in_time', 'a.prs_out_time')
                        ->where(DB::raw('EXTRACT(YEAR FROM a.prs_date)'),'=', $year)
                        ->where(DB::raw('EXTRACT(MONTH FROM a.prs_date)'),'=', $month)
                        ->where('a.prs_user_id', 'LIKE', '%'.$user_id.'%')
                        ->orderBy('a.prs_date', 'asc')
                        ->get();

        $output     = [
            'absensi'   => $query_absensi,
        ];

        return $output;
    }

    // NOTE : AMBIL DATA EMPLOYEES ALL
    public static function do_get_data_employee()
    {
        $query  = DB::table('employees as a')
                        ->join('users as b', 'a.user_id', '=', 'b.id')
                        ->select('b.id as employee_id', 'b.name as employee_name', 'b.email as employee_email', 'b.is_active as active')
                        ->orderBy('b.name', 'asc')
                        ->get();
        try {
            if(count($query) > 0 ) {
                $output     = [
                    'success'   => true,
                    'status'    => 200,
                    'message'   => 'Berhasil Mengambil Data Karyawan',
                    'data'      => $query,
                ];
            } else {
                $output     = [
                    'success'   => false,
                    'status'    => 404,
                    'message'   => 'Tidak Ada Data Karyawan',
                    'data'      => []
                ];
            }
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            $output     = [
                'success'   => false,
                'status'    => 500,
                'message'   => 'Internal Server Error',
                'data'      => []
            ];
        }
        
        return $output;
    }

    // NOTE : AMBIL DATA JAM KERJA
    public static function get_data_jam_kerja($date, $day_ke)
    {
        $query  = DB::table('v_master_hour')
                    ->where('date_start', '<=', $date)
                    ->where('date_end', '>=', $date)
                    ->where('day_num', '=', $day_ke)
                    ->orderBy('day_num', 'asc')
                    ->get();

        return $query;
    }

    // 02 APRIL 2025
    // NOTE : SIMPAN ROLE BARU
    public static function do_simpan_role($jenis, $data)
    {
        DB::beginTransaction();

        $user_id    = $data['user_id'];
        $ip_address = $data['ip_address'];
        $role_name  = str_replace(' ', '_', $data['data']['fr_name']);
        $role_id    = $data['data']['fr_id'];

        if($jenis == 'add') {
            $data_simpan    = [
                'name'      => $role_name,
                'guard_name'=> 'web',
                'created_at'=> date('Y-m-d H:i:s'), 
                'updated_at'=> date('Y-m-d H:i:s'),
            ];
            
            DB::table('roles')->insert($data_simpan);
            $new_role_id    = DB::getPdo()->lastInsertId();

            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Menambahkan Role Baru',
                    'data'          => [],
                ];

                LogHelper::create('add', $output['message'] . ' id : ' . $new_role_id, $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::channel('daily')->error($e->getMessage());

                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Gagal Menambahkan Role Baru',
                    'data'          => []
                ];

                LogHelper::create('error_system', $output['message'], $ip_address);
            }
        } else if($jenis == 'edit') {
            $data_where     = [
                'id'        => $role_id,
            ];
            $data_update    = [
                'name'      => $role_name,
                'updated_at'=> date('Y-m-d H:i:s'),
            ];

            DB::table('roles')->where($data_where)->update($data_update);

            try {
                DB::commit();

                $output     = [
                    'is_success'    => true,
                    'status_code'   => 201,
                    'message'       => 'Berhasil Mengubah Data Roles',
                    'data'          => []
                ];

                LogHelper::create('edit', $output['message'] . ' id : ' . $role_id, $ip_address);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::channel('daily')->error($e->getMessage());

                $output     = [
                    'is_success'    => false,
                    'status_code'   => 500,
                    'message'       => 'Gagal Mengubah Data Roles',
                    'data'          => []
                ];

                LogHelper::create('error_system', $output['message'] . ' id : ' . $role_id, $ip_address);
            }
        }

        return $output;
    }

    // NOTE : GET ROLE DATA BY ID
    public static function get_data_role_by_id($id)
    {
        $query  = DB::table('roles')
                    ->select('id as role_id', 'name as role_name')
                    ->where('id', '=', $id)
                    ->get();
        
        try {
            if(count($query) > 0) {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 200,
                    'message'       => 'Berhasil Mengambil Data Role',
                    'data'          => $query,
                ];
            } else {
                $output     = [
                    'is_success'    => true,
                    'status_code'   => 404,
                    'message'       => 'Data Tidak Ditemukan',
                    'data'          => [],
                ];
            }
            
        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
            $output     = [
                'is_success'    => false,
                'status_code'   => 500,
                'message'       => 'Internal Server Error',
                'data'          => []
            ];
        }

        return $output;
    }
}