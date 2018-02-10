<?php

namespace App\Http\Controllers;

use App\Models\Billing\Checks;
use App\Models\Log;
use App\Tools;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChecksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-checks', ['only' => ['store']]);
        $this->middleware('permission:read-checks', ['only' => ['index', 'view']]);
        $this->middleware('permission:update-checks', ['only' => ['updateStatus']]);
        $this->middleware('permission:delete-checks', ['only' => ['deleteCheck']]);
    }

    /**
     * @return mixed
     */
    function index()
    {
        $checks = Checks::orderBy('created_at', 'DESC')->get();
        return view('billing.checks', compact('checks'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    function store(Request $request)
    {
        $rules = [
            'created_at' => 'required',
            'amount' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $check = new Checks();
        $check->check_no = $request->check_no;
        $check->created_by = Auth::user()->id;
        $check->amount = $request->amount;
        $check->payee_id = $request->payee_id;
        $check->payee_name = User::read($request->payee_id, ['first_name', 'last_name']);
        $check->status = $request->status;
        $check->memo = $request->memo;
        $check->save();

        flash()->success(__("Check created"));
        return redirect()->back();

    }

    /**
     * @param $id
     * @return mixed
     */
    function view($id)
    {
        $check = Checks::findOrFail($id);
        return view('billing.templates.check_template', compact('check'));
    }

    /**
     * @param $id
     * @param $status
     * @return mixed
     */
    function updateStatus($id, $status)
    {
        $check = Checks::findOrFail($id);
        $check->status = $status;
        $check->save();
        flash()->success(__("Check status updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteCheck($id)
    {
        $check = Checks::findOrFail($id);
        $check->delete();
        flash()->success(__("Check has been deleted"));
        Log::add('Deleted check #' . $check->check_no, 'delete', 'general', $check);
        return redirect()->back();
    }
}
