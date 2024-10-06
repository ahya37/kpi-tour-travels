<?php 
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use App\Models\SubDivision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

date_default_timezone_set('Asia/Jakarta');

class SysUmhajService
{
    public static function test_koneksi()
    {
        $query      = DB::connection('umhaj_percik')->select(
            "
            SELECT 	*
            FROM 		(
            SELECT 	a.CS_BY,
                            count(b.ID) AS TOTAL_BERANGKAT_UMRAH,
                            b.JENIS_UMRAH
            FROM 		member a
            JOIN 		umrah b ON a.ID = b.ID_MEMBER
            WHERE 	b.TGL_DAFTAR BETWEEN '2024-09-01' AND '2024-09-30'
            GROUP BY a.CS_BY, b.JENIS_UMRAH
            ) AS TOTAL_UMRAH
            ORDER BY TOTAL_UMRAH.TOTAL_BERANGKAT_UMRAH ASC
            "
        );

        return $query;
    }

    public static function get_list_program_umrah()
    {
        $query  = DB::table('programs')
                    ->select('name as prog_name')
                    ->orderBy('name', 'asc')
                    ->get();
        return $query;
    }

    public static function get_data_umhaj_umrah($data)
    {
        $type_umrah     = $data['type_umrah'];
        $tahun_cari     = $data['tahun_cari'];
        $bulan_cari     = $data['bulan_cari'];

        if(empty($bulan_cari)) {
            $query  = DB::connection('umhaj_percik')
                        ->select("
                            SELECT 	UMRAH_REPORT.BULAN_DAFTAR,
                                    UMRAH_REPORT.KODE_UMRAH,
                                    UMRAH_REPORT.JENIS_UMRAH,
                                    UMRAH_REPORT.TOTAL_DATA
                            FROM 	(
                                    SELECT 	COUNT(a.ID) AS TOTAL_DATA,
                                            b.TIPE AS JENIS_UMRAH,
                                            a.JENIS_UMRAH AS KODE_UMRAH,
                                            EXTRACT(MONTH FROM a.TGL_DAFTAR) AS BULAN_DAFTAR
                                    FROM    umrah a
                                    JOIN 	jadwal_umrah b ON a.JENIS_UMRAH = b.KODE
                                    WHERE 	EXTRACT(YEAR FROM a.TGL_DAFTAR) = '$tahun_cari'
                                    AND 	b.TIPE LIKE '%$type_umrah%'
				                    GROUP BY a.JENIS_UMRAH, b.TIPE, EXTRACT(MONTH FROM a.TGL_DAFTAR)
                            ) AS UMRAH_REPORT
                            ORDER BY UMRAH_REPORT.BULAN_DAFTAR, UMRAH_REPORT.TOTAL_DATA ASC                        
                        ");
        } else {
            $query  = DB::connection('umhaj_percik')
                        ->select("
                            SELECT 	UMRAH_REPORT.BULAN_DAFTAR,
                                    UMRAH_REPORT.KODE_UMRAH,
                                    UMRAH_REPORT.JENIS_UMRAH,
                                    UMRAH_REPORT.TOTAL_DATA
                            FROM 	(
                                    SELECT 	COUNT(a.ID) AS TOTAL_DATA,
                                            b.TIPE AS JENIS_UMRAH,
                                            a.JENIS_UMRAH AS KODE_UMRAH,
                                            EXTRACT(MONTH FROM a.TGL_DAFTAR) AS BULAN_DAFTAR
                                    FROM    umrah a
                                    JOIN 	jadwal_umrah b ON a.JENIS_UMRAH = b.KODE
                                    WHERE 	EXTRACT(YEAR FROM a.TGL_DAFTAR) = '$tahun_cari'
                                    AND     EXTRACT(MONTH FROM a.TGL_DAFTAR) = '$bulan_cari'
                                    AND 	b.TIPE LIKE '%$type_umrah%'
				                    GROUP BY a.JENIS_UMRAH, b.TIPE, EXTRACT(MONTH FROM a.TGL_DAFTAR)
                            ) AS UMRAH_REPORT
                            ORDER BY UMRAH_REPORT.BULAN_DAFTAR, UMRAH_REPORT.TOTAL_DATA ASC                        
                        ");
        }

        return $query;
    }

    // UPDATE 02 OKTOBER 2024
    // NOTE : PENGAMBILAN DATA MEMBER
    public static function get_data_umhaj_member($data)
    {
        $cs_name    = $data['cs'];
        $tahun      = $data['tahun_cari'];
        $bulan      = $data['bulan_cari'];

        $query      = DB::connection('umhaj_percik')
                        ->table('member')
                        ->select(DB::raw('EXTRACT(MONTH FROM CREATED_DATE) AS month'), DB::raw('COUNT(ID) AS total_data_member'))
                        ->where(DB::raw('EXTRACT(YEAR FROM CREATED_DATE)'), '=', $tahun)
                        ->where('CS_BY', 'LIKE', '%'.$cs_name.'%')
                        ->groupBy(DB::RAW('EXTRACT(MONTH FROM CREATED_DATE)'))
                        ->get();
        return $query;
    }

    // NOTE : AMBIL DATA CS
    public static function get_data_umhaj_cs()
    {
        $query      = DB::connection('umhaj_percik')
                        ->table('__t_users')
                        ->select('nama_lengkap as cs_name')
                        ->where('is_cs', '=', 'Y')
                        ->where('blokir', '=', 'N')
                        ->orderBy('nama_lengkap', 'asc')
                        ->get();
        return $query;
    }

    // NOTE : AMBIL DATA MEMBER DETAIL PER CS
    public static function get_data_umhaj_member_detail($data)
    {
        $cs_name    = $data['cs_name'];
        $tahun      = $data['tahun_cari'];
        $bulan      = $data['bulan_cari'];

        $query      = DB::connection('umhaj_percik')
                        ->select(
                            "
                            SELECT 	*
                            FROM    (
                                    SELECT 	CS_BY AS PIC_NAME,
                                            DATE_FORMAT(CREATED_DATE, '%Y-%m-%d') AS CREATED_DATE,
                                            COUNT(ID) as TOTAL_DATA
                                    FROM 	member
                                    WHERE 	EXTRACT(YEAR FROM CREATED_DATE) = '$tahun'
                                    AND 	EXTRACT(MONTH FROM CREATED_DATE) = '$bulan'
                                    AND     CS_BY LIKE '%".$cs_name."%'
                                    GROUP BY CS_BY, DATE_FORMAT(CREATED_DATE, '%Y-%m-%d')
                            ) AS DETAIL_MEMBER
                            ORDER BY DETAIL_MEMBER.CREATED_DATE ASC, DETAIL_MEMBER.TOTAL_DATA DESC
                            "
                        );
        return $query;
    }

    // 03 OKTOBER 2024
    // NOTE : AMBIL DATA UMRAH DETAIL
    public static function get_data_umhaj_umrah_detail($data)
    {
        $type_umrah     = $data['type_umrah'];
        $bulan          = $data['bulan_ke'];
        $tahun          = $data['tahun_ke'];

        $query      = DB::connection('umhaj_percik')
                        ->table('umrah as a')
                        ->join('jadwal_umrah as b', 'a.JENIS_UMRAH', '=', 'b.KODE')
                        ->select('a.TGL_DAFTAR', 'b.TIPE as JENIS_UMRAH', 'b.KODE as KODE_UMRAH', DB::raw('COUNT(a.ID) as TOTAL_DATA'))
                        ->where(DB::raw('EXTRACT(YEAR FROM TGL_DAFTAR)'), '=', $tahun)
                        ->where(DB::raw('EXTRACT(MONTH FROM TGL_DAFTAR)'), '=', $bulan)
                        ->where('b.TIPE', 'like', '%'.$type_umrah.'%')
                        ->groupBy('a.TGL_DAFTAR', 'b.TIPE', 'b.KODE')
                        ->orderBy('a.TGL_DAFTAR', 'asc')
                        ->get();
        return $query;
    }

    // 04 OKTOBER 2024
    // NOTE : AMBIL LIST UMRAH
    public static function get_data_umhaj_umrah_list($tahun)
    {
        // RAW QUERY
        // $query  = DB::connection('umhaj_percik')
        //             ->select(
        //                 "
        //                 SELECT 	a.KODE AS UMRAH_TOUR_CODE,
        //                         a.TIPE AS UMRAH_TYPE,
        //                         a.RUTE AS UMRAH_ROUTE,
        //                         a.LAMA AS UMRAH_DAY,
        //                         a.BERANGKAT AS UMRAH_DEPATURE,
        //                         a.PULANG AS UMRAH_ARRIVAL,
        //                         a.KAPASITAS AS UMRAH_TARGET,
        //                         COUNT(b.ID) AS UMRAH_TARGET_REALIZATION,
        //                         a.PEMBIMBING AS UMRAH_TOUR_MENTOR,
        //                         a.TOURLEADER AS UMRAH_TOUR_LEADER
        //                 FROM 	jadwal_umrah a
        //                 JOIN 	umrah b ON a.KODE = b.JENIS_UMRAH
        //                 WHERE 	EXTRACT(YEAR FROM a.BERANGKAT) = '2024'
        //                 GROUP BY a.KODE, a.TIPE, a.RUTE, a.LAMA, a.BERANGKAT, a.PULANG, a.KAPASITAS, a.PEMBIMBING, a.TOURLEADER
        //                 ORDER BY a.BERANGKAT ASC
        //                 "
        //             );

        $query  = DB::connection('umhaj_percik')
                    ->table('jadwal_umrah as a')
                    ->join('umrah as b', 'a.KODE', '=', 'b.JENIS_UMRAH')
                    ->select(
                        'a.KODE as UMRAH_TOUR_CODE',
                        'a.TIPE as UMRAH_TYPE',
                        'a.RUTE as UMRAH_ROUTE',
                        'a.LAMA as UMRAH_DAY',
                        'a.BERANGKAT as UMRAH_DEPATURE',
                        'a.PULANG as UMRAH_ARRIVAL',
                        'a.KAPASITAS as UMRAH_TARGET',
                        DB::raw('COUNT(b.ID) as UMRAH_TARGET_REALIZATION'),
                        'a.PEMBIMBING as UMRAH_TOUR_MENTOR',
                        'a.TOURLEADER as UMRAH_TOUR_LEADER',
                    )
                    ->where(DB::raw('EXTRACT(YEAR FROM a.BERANGKAT)'), '=', $tahun)
                    ->groupBy('a.KODE', 'a.TIPE', 'a.RUTE', 'a.LAMA', 'a.BERANGKAT', 'a.PULANG', 'a.KAPASITAS', 'a.PEMBIMBING', 'a.TOURLEADER')
                    ->orderBy('a.BERANGKAT', 'asc')
                    ->get();
        return $query;
    }

    // 05 OKTOBER 2024
    public static function get_data_umhaj_umrah_detail_byTourCode($tourCode)
    {
        // GET DATA HEADER
        $query_header   = DB::connection('umhaj_percik')
                            ->table('jadwal_umrah as a')
                            ->select('a.KODE as umrah_tour_code', 'a.RUTE as umrah_route', 'a.BERANGKAT as umrah_depature', 'a.PULANG as umrah_arrival', 'a.PEMBIMBING as umrah_mentor', 'a.TOURLEADER as umrah_tour_leader')
                            ->where('a.KODE', '=', $tourCode)
                            ->groupBy('a.KODE', 'a.RUTE', 'a.BERANGKAT', 'a.PULANG', 'a.PEMBIMBING', 'a.TOURLEADER')
                            ->get();
        // GET DATA DETAIL
        $query_detail   = DB::connection('umhaj_percik')
                            ->table('umrah as a')
                            ->select('a.TGL_DAFTAR as detail_umrah_registry_date', DB::raw('COUNT(a.id) as detail_umrah_total_data'))
                            ->where('a.JENIS_UMRAH', '=', $tourCode)
                            ->groupBy('a.TGL_DAFTAR')
                            ->get();
        
        $output         = [
            "header"    => $query_header,
            "detail"    => $query_detail,
        ];

        return $output;
    }
}