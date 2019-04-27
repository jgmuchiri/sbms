<?php

namespace App\Models\Projects;

use App\Models\Billing\Expenses;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectTasks extends Model
{
    public static function countByStatus($pid,$status=null){
        $now = date('Y-m-d');

        switch($status){
            case "pending":
                $tasks = self::where('t_end','<=',$now)->where('t_status','!=','completed')->count();
                break;
            case "behind":
                $tasks = self::where('t_end','>',$now)->where('t_status','!=','completed')->count();
                break;
            case "completed":
                $tasks = self::where('t_status','completed')->count();
                break;
            default:
                $tasks=0;
                break;

        }
        return $tasks;
    }
    public static function isPaid($task,$status){
        $exp = Expenses::whereTaskId($task)->first();

        if($status == 'completed' && count($exp)>0)
            return true;
        return false;
    }

    function project(){
        return $this->belongsTo(\App\Models\Projects\Projects::class,'project_id','id');
    }
    function milestone(){
        return $this->belongsTo(\App\Models\Projects\ProjectMilestones::class,'milestone_id','id');
    }
}
