<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProkerBulanan;

class ProkerBulananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['pkb_start_date' => '2024-06-01','pkb_title' => '1. PELAKSANAAN TES KESEHATAN TERKAHIR JEMAAH HAJI', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-01','pkb_title' => '2. PENGUMPULAN KOPER JEMAAH HAJI KHUSUS', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-01','pkb_title' => '3. LIPUTAN PENGUMPULAN KOPER JEMAAH HAJI', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-03','pkb_title' => 'KEBERANGKATAN HAJI', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-04','pkb_title' => '1. KONSOLIDASI DENGAN BANK DANAMON', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-04','pkb_title' => '2. UPLOAD VIDEO KEBERANGKATAN HAJI KHUSUS', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-05','pkb_title' => '1. Maintenance Perwakilan CIAMIS', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-05','pkb_title' => '2. Pertemuan Dengan Calon Agen/Perwakilan Tasikmalaya', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-05','pkb_title' => '3. EDIT VIDEO PELAKSANAAN UMRAH GROUP HAJI KHUSUS 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-06','pkb_title' => '1.Maintenance Agen Majalaya', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-06','pkb_title' => '2. Maintenance Agen Cicalengka', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-07','pkb_title' => '1. EDIT PHOTOBOOK HAJI KHUSUS 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-07','pkb_title' => '2. UPLOAD KONTEN VIDEO HIBURAN - PENDAFTARAN UMRAH', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-08','pkb_title' => '1. EDIT PHOTOBOOK HAJI KHUSUS 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-08','pkb_title' => '2. PEMBUATAN FLYER LIVE IG - PERSIAPAN PELAKSANAAN HAJI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-10','pkb_title' => '1. EDIT PHOTOBOOK HAJI KHUSUS 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-10','pkb_title' => '2. KONFIRMASI PELUNASAN GROUP UMRAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-10','pkb_title' => '3. KONFIRMASI PENGAMBILAN KOPER JEMAAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-11','pkb_title' => '1. PERTEMUAN DENGAN CALON AGEN/PERWAKILAN MAJALENGKA', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-11','pkb_title' => '2. KONFIRMASI PELUNASAN GROUP UMRAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-11','pkb_title' => '3. KONFIRMASI PENGAMBILAN KOPER JEMAAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-12','pkb_title' => '1. EDIT PHOTOBOOK HAJI KHUSUS 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-12','pkb_title' => '2. KONSOLIDASI DENGAN BANK DANAMON', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-12','pkb_title' => '3. LIVE IG (PROGRAM PERSIAPAN PELAKSANAAN HAJI) - HOTEL TRANSIT', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-13','pkb_title' => 'Kunjungan ke KANWIL Pegadaian Jakarta', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-14','pkb_title' => '1. UPLOAD KONTEN VIDEO FLYER UMRAH', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-15','pkb_title' => '1. PEMBUATAN UCAPAN SELAMAT HARI RAYA IDUL ADHA', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-15','pkb_title' => '2. KONFIRMASI PELUNASAN GROUP UMRAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-15','pkb_title' => '3. KONFIRMASI PENGAMBILAN KOPER JEMAAH 17 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-17','pkb_title' => '1. UPLOAD KONTEN UCAPAN SELAMAT HARI RAYA IDUL ADHA', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-18','pkb_title' => '1. MAINTENANCE AGEN BANJARAN', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-19','pkb_title' => 'Maintenance Perwakilan Cianjur', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-20','pkb_title' => '1. KONSOLIDASI DENGAN BANK DANAMON', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-21','pkb_title' => '1. KONFIRMASI PELUNASAN GROUP UMRAH 24 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-21','pkb_title' => '2. KONFIRMASI PENGAMBILAN KOPER JEMAAH 24 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-22','pkb_title' => '1. KONFIRMASI PELUNASAN GROUP UMRAH 24 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-24','pkb_title' => '1. KONFIRMASI PELUNASAN GROUP UMRAH 24 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-24','pkb_title' => '2. KONFIRMASI PENGAMBILAN KOPER JEMAAH 24 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-25','pkb_title' => '1. LIVE IG', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-26','pkb_title' => '1. KONFIRMASI PELUNASAN GROUP UMRAH 31 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-26','pkb_title' => '2. KONFIRMASI PENGAMBILAN KOPER JEMAAH 31 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-27','pkb_title' => 'PERSIAPAN KEPULANGAN HAJI KHUSUS', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-28','pkb_title' => 'KEPULANGAN HAJI KHUSUS', 'pkb_pkt_id' => 0, 'created_by' => '15'],

            ['pkb_start_date' => '2024-06-29','pkb_title' => '1. KONFIRMASI PELUNASAN GROUP UMRAH 31 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
            ['pkb_start_date' => '2024-06-29','pkb_title' => '2. KONFIRMASI PENGAMBILAN KOPER JEMAAH 31 JULI 2024', 'pkb_pkt_id' => 0, 'created_by' => '15'],
        ];

        foreach ($data as $key => $value) {
            ProkerBulanan::create($value);
        }
    }
}
