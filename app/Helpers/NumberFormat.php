<?php
namespace App\Helpers;

class NumberFormat {

    public  function persentage($numerator, $denominator)
    {
        // try {
		// 	// Attempt the division
		// 	$result = ($numerator / $denominator) * 100;
		// 	return $result;
		// } catch (\Exception $e) {
		// 	// Handle division by zero error
		// 	return $numerator*100; 
		// }
        $result = 0;
        if ($denominator != 0) {
            $result = ($numerator / $denominator) * 100;

        }else{

            $result = $numerator*100; 
        }

        return $result;
    }

    public function persen($data)
    {
        $show = number_format($data,0);
        return $show;
    }

    public function decimalFormat($data) {
        $show = number_format((float)$data,0,',','.');
        return $show;
    }

}

?>