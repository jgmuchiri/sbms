<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expenses extends Model
{
    protected $guarded = [];

    public static function getCats(){
        return DB::table('expense_cats');
    }
    /**
     * @param $id
     * @return string
     */
    public static function getCatName($id){
        $cat= DB::table('expense_cats')->whereId($id)->first();
        if(count($cat)==0)
            return '';
        return $cat->cat_name;
    }
}
