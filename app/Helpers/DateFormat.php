<?php 
namespace App\Helpers;
use DateTime;
use DateInterval;

class DateFormat {

    public static function getWeekStartEndDates($year, $month)
    {
        $start_date = new DateTime("$year-$month-01");
        $end_date = (clone $start_date)->modify('last day of this month');

        $weeks = [];
         // Iterate over each week
         while ($start_date <= $end_date) {
            $week_start = (clone $start_date)->modify('this week');
            $week_end = (clone $week_start)->modify('+6 days');

            // Ensure week end is within the same month
            if ($week_end > $end_date) {
                $week_end = $end_date;
            }

            $weeks[] = [
                'start' => $week_start->format('Y-m-d'),
                'end' => $week_end->format('Y-m-d')
            ];

            // Move to the next week
            $start_date = (clone $week_end)->modify('+1 day');
        }
        
        return $weeks;
    }
}