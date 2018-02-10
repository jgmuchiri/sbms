<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Log extends Model
{
    protected $table ='logs';

    /**
     * @param $event
     * @param null $action
     * @param null $category
     */
    public static function add($event,$action=null,$category=null,$data=null){
        $log = new self;
        $log->user_id = Auth::user()->id;
        $log->action = $action;
        $log->event = $event;
        $log->category = $category;
        if($data !==null)
            $log->data = serialize($data);
        $log->save();
    }

    /**
     * @param $cat
     * @return string
     */
    public static function colorCat($cat){
        switch($cat){
            case 'delete':
                $color = 'danger';
                break;
            case 'remove':
                $color = 'danger';
                break;
            case 'update':
                $color = 'info';
                break;
            case 'add':
                $color = 'success';
                break;
            default:
                $color ='default';
                break;

        }
        return '<span class="label label-'.$color.'">'.$cat.'</span>';
    }
}
