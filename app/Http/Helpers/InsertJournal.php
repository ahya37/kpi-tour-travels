<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;
use Log;

class InsertJournal {
    public static function insert_finance_journal($data = [])
    {
        // GENERATE JOURNAL ID
        $tanggal_cari   = date('Y-m-d', strtotime($data['journal_date']));
        $query  = DB::table('fin_trans_journal')
                    ->select(DB::raw("SUBSTRING_INDEX(journal_id, '-', -1) as last_number_id"))
                    ->where('journal_date', '=', $tanggal_cari)
                    ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(journal_id, '-', -1) AS UNSIGNED)"), "desc")
                    ->get();

        if(count($query) > 0) {
            $get_last_number    = (int)$query[0]->last_number_id;
            $new_last_number    = $get_last_number + 1;
            $journal_id         = "J-" . date('dmy', strtotime($tanggal_cari)) . "-" . str_pad($new_last_number, 4, 0, STR_PAD_LEFT);
        } else {
            $journal_id         = "J-" . date('dmy', strtotime($tanggal_cari)) . "-0001";
        }

        // INSERT JOURNAL DATA
        DB::beginTransaction();
        
        $journal_insert_data    = [
            'journal_id'            => $journal_id,
            'journal_date'          => $data['journal_date'],
            'journal_description'   => $data['journal_description'],
            'journal_coa_id'        => $data['journal_coa_id'],
            'journal_currency'      => $data['journal_currency'],
            'journal_total_amount'  => $data['journal_total_amount'],
            'journal_type'          => $data['journal_type'],
            'journal_reff_code'     => $data['journal_reff_code'],
            'created_by'            => $data['created_by'],
            'created_date'          => $data['created_date'],
            'updated_by'            => $data['updated_by'],
            'updated_date'          => $data['updated_date'],
        ];

        DB::table('fin_trans_journal')->insert($journal_insert_data);

        try {
            DB::commit();

            LogHelper::create('add', 'Berhasil Menambahkan Jurnal Harian id : ' . $journal_id, $data['ip_address']);

            return 'berhasil';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('daiy')->error($e->getMessage());
            LogHelper::create('error_system', 'Internal Server Error', $data['ip_address']);
            
            return 'gagal';
        }
    }
}

?>