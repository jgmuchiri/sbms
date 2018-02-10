<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct(){
        $this->middleware(['auth']);
    }

}
