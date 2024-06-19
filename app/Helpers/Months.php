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
}