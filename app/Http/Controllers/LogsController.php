<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    function index()
    {
        $logs = Log::paginate(20);

        if (Request()->ajax()) {
            return Response()->json(View::make('logs.index', array('logs' => $logs))->render());
        } else {
            return view('logs.index', compact('logs'));
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    function add(Request $request)
    {
        $rules = [
            'event' => 'required|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $log = new Log();
        $log->user_id = auth()->user()->id;
        $log->action = $request->action;
        $log->event = $request->event;
        $log->category = $request->category;
        $log->save();
        flash()->success('Event recorded');
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteLog($id)
    {
        $log = Log::findOrFail($id);
        $log->delete();
        flash()->success('Log entry deleted!');
        return redirect()->back();
    }
}
