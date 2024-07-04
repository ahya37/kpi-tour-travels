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

			$year  = request()->year;
			$month = request()->month;
			
			#Get divisi id marketing 
			$groupDivisionID = env('APP_GROUPDIV_MARKETING');
			
			// get kegiatan berdasarkan bulanan dan divisinya
			$prokerBulanan = ProkerBulanan::getProkerBulananMarkering($groupDivisionID, $month, $year);

			// group per tanggal
			$grouped = $prokerBulanan->groupBy('pkb_start_date');

			$res_grouped = [];
			foreach ($grouped as $key => $value) {

				// get jenis pekerjaan 
				$res_jenis_pekerjaan = [];
				foreach ($value as $item) {

					$prokerBulananDetail = ProkerBulanan::getProkerBulananDetail($item->id);

					// GET HARIAN BERDASARKAN BULANAN DAN CREATED_BY NYA 
					$aktivitas_harian = ProkerHarian::getProkerHarianByBulananAndUser($item->uuid,$item->created_by);

					$res_jenis_pekerjaan[] = [
						'id' => $item->id,
						'pkb_start_date' => $item->pkb_start_date,
						'pkb_title' => $item->pkb_title,
						'created_by_name' => $item->created_by_name,
						'created_by' => $item->created_by,
						'jenis_pekerjaan' => $prokerBulananDetail,
						'aktivitas_harian' => $aktivitas_harian
					];
				}
				$res_grouped[] = [
					'tanggal' => $key,
					'uraian_pekerjaan' => $res_jenis_pekerjaan
				];
			}
			
            return ResponseFormatter::success([
				'bulan' =>  Months::monthName($month),  
				'rencanakerja'  => $res_grouped,
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
