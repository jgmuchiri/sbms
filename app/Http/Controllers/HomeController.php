<?php

namespace App\Http\Controllers;

use App\Members;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Settings;

class HomeController extends Controller
{


    /**
     * from contact form
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function sendMessage(Request $request)
    {
        $rules = [
            'name' => 'required|max:50',
            'email' => 'required|max:50',
            'subject' => 'required|max:50',
            'message' => 'required|max:50',
            'catch_bots' => 'required',
            'catch_bots_confirm' => 'required|same:catch_bots'
        ];
        $messages = [
            'catch_bots_confirm.same' => __("Invalid captcha entered"),
        ];
        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            $request->catch_bots_confirm = '';
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Mail::send('emails.general', ['subject' => $request->subject, 'msg' => $request->message], function ($m) use ($request) {
            $m->from($request->email, $request->name);
            $m->to(config('mail.from.address'), config('app.name'))->subject($request->subject);
        });

        flash()->success(__("Thank you! We will get back with you shortly"));
        return redirect()->back();
    }

}
