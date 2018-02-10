<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class ProjectMessages extends Model
{
    public static function replies($id){
        $replies = self::where('parent_id',$id)->orderBy('created_at','DESC')->get();
        return $replies;
    }
}
