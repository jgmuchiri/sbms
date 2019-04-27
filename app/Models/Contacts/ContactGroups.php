<?php

namespace App\Models\Contacts;

use Illuminate\Database\Eloquent\Model;

class ContactGroups extends Model
{
    protected $fillable=[
        'group_name',
        'desc'
    ];
    /**
     * group with many contacts
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    function contacts(){
        return $this->belongsToMany(\App\Models\Contacts\Contacts::class,'contact_group','group_id','contact_id');
    }
}
