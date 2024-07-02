<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Log;
use App\Models\ProkerBulanan;
use App\Models\ProkerHarian;
use App\Helpers\Months;

class TestController extends Controller
{
    public function reportRencanaKerjaMarekting()
	{
        try {
			
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			
			$year  = date('Y');

			$results = [];

			// get data employee
			// $employees = Employee::getEmployees();

			// get data pekerjaan harian
			#Get divisi id marketing 
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			$proker_tahunan_group_bulan   = ProkerBulanan::prokerGroupBulananByTahunan($groupDivisionID);

			foreach ($proker_tahunan_group_bulan as $annual) {

				// $res_proker_harian = ProkerHarian::getAktivitasHarianByBulanTahunAndDivisiByTest($groupDivisionID, $annual->month, $year);
				// $res_proker_harian = $res_proker_harian->orderBy('a.pkh_date','asc')->get();
				
				// get kegiatan berdasarkan bulanan dan divisinya
				$prokerBulanan = ProkerBulanan::getProkerBulananMarkering($groupDivisionID, $annual->month, $year);
				
				$res_proker_bulanan = [];
				foreach($prokerBulanan as $item){
					// GET HARIAN BERDASARKAN BULANAN DAN CREATED_BY NYA 
					$aktivitas_harian = ProkerHarian::getProkerHarianByBulananAndUser($item->uuid,$item->created_by);
				
					// get jenis pekerjaan nya di table bulanan detail berdasarkan id bulan nya
					$prokerBulananDetail = ProkerBulanan::getProkerBulananDetail($item->id);
					$res_proker_bulanan[] = [
						'pkb_start_date' => $item->pkb_start_date,
						'pkb_title' => $item->pkb_title,
						'created_by_name' => $item->created_by_name,
						'created_by' => $item->created_by,
						'janis_pekerjaan' => $prokerBulananDetail,
						'aktivitas_harian' => $aktivitas_harian 
					];
				}
				
				
				$results[] = [
						// 'annual' => $annual->pkt_title,
						'month_number' => $annual->month,
						'month_name' => Months::monthName($annual->month),
						'rencana_kerja_bulanan' => $res_proker_bulanan
					];

			}
			
            return ResponseFormatter::success([  
				'rencanakerja'  => $results,
                'message' => 'Laporan percenaan kerja marketing'
            ]);

        } catch (\Exception $e) {
            Log::channel('daily')->error($e->getMessage());
			return $e->getMessage();
            return ResponseFormatter::error([
                'message' => 'Gagal Singkronkan data!'
            ]);
        }
	}
}
