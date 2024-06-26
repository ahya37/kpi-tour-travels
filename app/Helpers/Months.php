<?php 
namespace App\Helpers;

class Months {

    public static function months()
    {
        $months = [
            ['key' => 1, 'month' => 'Januari'],
            ['key' => 2, 'month' => 'Februari'],
            ['key' => 3, 'month' => 'Maret'],
            ['key' => 4, 'month' => 'April'],
            ['key' => 5, 'month' => 'Mei'],
            ['key' => 6, 'month' => 'Juni'],
            ['key' => 7, 'month' => 'Juli'],
            ['key' => 8, 'month' => 'Agustus'],
            ['key' => 9, 'month' => 'September'],
            ['key' => 10, 'month' => 'Oktober'],
            ['key' => 11, 'month' => 'November'],
            ['key' => 12, 'month' => 'Desember'],
        ];

        return $months;
    }

    public static function monthColor($month)
    {
        // menentukan warna berdasarkan ganjil genap
        $color = '';
        if ($month % 2 == 0 ) {
            // genap
            $color = '#F2F2F2';

        }else{

            $color = '#FFFFFF';
        }

        return $color;
    }

    public static function monthName($month)
    {
        switch ($month) {
            case '1':
                return 'JANUARI';
                break;
            case '2':
                return 'FEBRUARI';
                break;
             case '3':
                return 'MARET';
                break;
             case '4':
                return 'APRIL';
                break;
             case '5':
                return 'MEI';
                break;
             case '6':
                return 'JUNI';
                break;
             case '7':
                return 'JULI';
                break;
             case '8':
                return 'AGUSTUS';
                break;
             case '9':
                return 'SEPTEMBER';
                break;
             case '10':
                return 'OKTOBER';
                break;
             case '11':
                return 'NOVEMBER';
                break;
             case '12':
                return 'DESEMBER';
                break;
        }
    }
}