<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralMessage;
use App\Mail\SendGeneralMessage;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{

    public function index()
    {
        return auth()->guest() ? view('auth.login') : redirect('/dashboard');
    }

    public function dashboard()
    {
        if (auth()->user()->role == 'admin' || auth()->user()->role == 'manager') {

            $logs = Log::paginate(20);

            return request()->ajax()
            ? response()->json(View::make('logs.index', ['logs' => $logs])->render())
            : view('admin.dashboard', compact('logs'));
        }

        return view('account.dashboard');
    }

    /**
     * from contact form
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(GeneralMessage $request)
    {

        Mail::to(config('email.from.address'))
            ->send(new SendGeneralMessage(
                $request->email,
                config('email.from.address'),
                $request->message)
            );

        flash()->success(__("Thank you! We will get back with you shortly"));
        return redirect()->back();
    }

}
