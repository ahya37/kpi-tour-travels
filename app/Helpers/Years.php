<?php 
namespace App\Helpers;

class Years {

    public static function list()
    {
        $MaxTahun=date('Y')+5;
        $MinTahun=1900;
        $i=1;
        while($MaxTahun>=$MinTahun){
            $ArrTahun[$i-1]=$MaxTahun;
            $i++;
            $MaxTahun--;
        }

        return $ArrTahun;
    }
}