<?php

namespace App\Models\Projects;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectMilestones extends Model
{
    public static function countByStatus($pid, $status = null)
    {
        return self::where('project_id',$pid)->where('m_status',$status)->count();
    }

    public static function statusHtml($status){
        switch($status){
            case 'schedule':
                $class = 'default';
                break;
            case 'in-progress':
                $class = 'info';
                break;
            case 'completed':
                $class='success';
                break;
            default:
                $class='default';
                break;
        }
        $status = strtoupper($status);
        return "<span class=\"label label-{$class}\">{$status}</span>";
    }

}
