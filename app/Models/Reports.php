<?php

namespace App\Models;

use App\Models\Billing\Expenses;
use App\Models\Billing\InvoicePayments;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{

    /**
     * @param null $year
     * @return string
     */
    public static function usersByMonth($year = null)
    {
        if ($year == null) $year = date('Y');
        $stats = array();
        for ($m = 1; $m <= 12; $m++) {
            if ($m < 10)
                $m = '0' . $m;
            $date = $year . '-' . $m;
            $stats[] =User::where('created_at', 'LIKE', $date . '%')->count();
        }
        return implode(',', $stats);

    }

    /**
     * @param null $year
     * @return string
     */
    public static function income($year = null)
    {
        if ($year == null) $year = date('Y');
        $stats = array();
        for ($m = 1; $m <= 12; $m++) {
            if ($m < 10)
                $m = '0' . $m;
            $date = $year . '-' . $m;
            $stats[] =InvoicePayments::where('created_at', 'LIKE', $date . '%')->count();
        }
        return implode(',', $stats);
    }

    /**
     * @param null $year
     * @return string
     */
    public static function expenses($year = null){
        if ($year == null) $year = date('Y');
        $stats = array();
        for ($m = 1; $m <= 12; $m++) {
            if ($m < 10)
                $m = '0' . $m;
            $date = $year . '-' . $m;
            $stats[] =Expenses::where('created_at', 'LIKE', $date . '%')->count();
        }
        return implode(',', $stats);
    }
}
