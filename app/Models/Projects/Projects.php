<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    function milestones(){
        return $this->hasMany(\App\Models\Projects\ProjectMilestones::class,'project_id','id');
    }
}
