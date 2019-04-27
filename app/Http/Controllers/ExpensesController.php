<?php

namespace App\Http\Controllers;

use App\Models\Billing\Expenses;
use App\Models\Log;
use App\Tools;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id = null)
    {
        if (isset($_GET['year']) && $id == null && $_GET['year'] !== 'all') {
            $expenses = Expenses::orderBy('created_at', 'DESC')->where('created_at', 'LIKE', $_GET['year'] . '%')->get();
        } elseif (isset($_GET['year']) && $_GET['year'] == 'all') {
            $expenses = Expenses::orderBy('created_at', 'DESC')->get();
        } else {
            $expenses = Expenses::orderBy('created_at', 'DESC')->where('created_at', 'LIKE', date('Y') . '%')->get();
        }

        $exp = [];
        if ($id !== null && is_numeric($id)) {
            $exp = Expenses::findOrFail($id);
        }

        return view('billing.expenses', compact('expenses', 'exp'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:50',
            'client' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->get('client') == 0 || $request->has('client') == false) {
            flash()->error(__('Please select a client'));
            return redirect()->back()->withInput();
        }

        unset($request['task_id']);
        $request['user_id'] = auth()->user()->id;
        if (is_string($request->task_id)) {
            $request['task_id'] = null;
        }

        $exp = new Expenses();
        $exp->create($request->all());

        flash()->success(__('Expense has been recorded'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        unset($request['_token']);

        if (!is_numeric($request->task_id)) {
            $request['task_id'] = null;
        }

        $exp = Expenses::findOrFail($id);
        $exp->name = $request->name;
        $exp->amount = $request->amount;
        $exp->category = $request->category;
        $exp->notes = $request->notes;
        $exp->client = $request->client;
        $exp->task_id = $request->task_id;
        $exp->created_at = $request->created_at;
        $exp->save();

        flash()->success(__('Expense record has been updated'));
        return redirect('expenses');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $exp = Expenses::findOrFail($id);
        $exp->delete();
        flash()->success(__('Expense record has been deleted'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCategory(Request $request)
    {
        $rules = [
            'cat_name' => 'required|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::table('expense_cats')->insert(['cat_name' => $request->cat_name]);

        flash()->success(__('Expense category has been saved'));
        return redirect()->back();
    }
}
