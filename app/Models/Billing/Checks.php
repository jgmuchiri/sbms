<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checks extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
