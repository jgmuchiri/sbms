<?php

namespace App\Models\Contacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contacts extends Model
{

    protected $guarded = ['group_id'];

    /**
     * @param $id
     *
     * @return mixed
     */
    function deleteContact($id)
    {
        return DB::table('contact_groups')->whereId($id)->delete();
    }

    /**
     * @param $s
     *
     * @return mixed
     */
    function search($s)
    {
        return $contacts = self::where('first_name', 'LIKE', '%'.$s.'%')
            ->orWhere('last_name', 'LIKE', '%'.$s.'%')
            ->orWhere('cell', 'LIKE', '%'.$s.'%')
            ->orWhere('email', 'LIKE', '%'.$s.'%')
            ->orWhere('phone', 'LIKE', '%'.$s.'%')
            ->orWhere('company', 'LIKE', '%'.$s.'%')
            ->orWhere('address', 'LIKE', '%'.$s.'%')
            ->orWhere('job_title', 'LIKE', '%'.$s.'%')
            ->get();
    }
}
