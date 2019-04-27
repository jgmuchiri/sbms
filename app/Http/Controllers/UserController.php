<?php

namespace App\Http\Controllers;

use App\Models\Billing\Transactions;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');

    }

    /**
     * list all users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function users()
    {
        if(!auth()->user()->can('read users'))
            abort(403, 'Unauthorized action.');

        $users = User::get();

        return view('admin.users', compact('users'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function user($id)
    {
        $user = User::whereId($id)->first();
        return view('admin.user', compact('user'));
    }

    /**
     * register new user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|max:50|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required',
            ]
        );
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //create stripe customer
        $customer = Transactions::createCustomer($request);

        $request['password'] = bcrypt(Str::random(6));
        $request['confirmation_code']=Str::random(30);
        $request['stripe_id'] = $customer->id;

        //create customer
        $user = new User();
        $user->fill($request->all());
        $user->save();

        //notify user to activate account
        if($request->has('notify-user')) {
            Mail::send(
                'emails.accounts-verify',
                [
                    'email' => $request->email,
                    'password' => $request->password,
                    'confirmation_code' => $request->confirmation_code],
                function ($m) use ($request) {
                    $m->from(config('mail.from.address'), config('app.name'));
                    $m->to($request['email'], $request['first_name'])->subject(__('Your new account'));
                }
            );

            flash()->success(__('Thanks for signing up! Confirmation email has been sent'));
        }

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:users,email,'.$id,
        ];

        if($request->has('password') && trim($request->password) !== '') {
            $rules2 = [
                'password' => 'min:6|confirmed',
                'password_confirmation' => 'min:6',
            ];
            $rules = array_merge($rules, $rules2);
        }

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = User::find($id);

        if(Input::has('password')) {
            $user->password = bcrypt($request['password']);
        }

        //generate username in case it wasn't during installation
        if(empty($user->username)) {
            $user->username = strtolower($request->last_name.rand(1, 100));
        }

        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->company = $request->company;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        flash()->success(__('Profile updated'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateUserRole(Request $request)
    {
        $user = User::find($request->id);

        //admin cant take away their rights

        if(auth()->user()->hasRole('admin') && auth()->user()->id == $request->id) {
            flash()->error(__('You cannot change your own rights. Another admin should'));
        } else {
            //remove al
            $user->syncRoles([$request->role]);
            flash()->success(__('Roles updated'));
        }

        return redirect('users/'.$request->id.'/view#roles');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userAccount()
    {
        $txns = Transactions::whereUserId(auth()->user()->id)->simplePaginate('50');
        return view('account.dashboard', compact('txns'));
    }

    /**
     * get current  user profile
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        return view('account.profile', compact('user'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $id = auth()->user()->id;

        $rules = [
            'username' => 'required|unique:users,username,'.$id,
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:users,email,'.$id,
            'dob' => 'date_format:Y-m-d',
        ];
        if(Input::has('password')) {
            $rules2 = [
                'password' => 'min:6|confirmed',
                'password_confirmation' => 'required|min:6',
            ];
            $rules = array_collapse([$rules, $rules2]);
        }

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::find($id);

        if($user->stripe_id == NULL || $user->stripe_id == '') {//create stripe customer
            $customer = Transactions::createCustomer($request);
            $user->stripe_id = $customer->id;
        }

        if(Input::has('password')) {
            $user->password = bcrypt($request['password']);
        }

        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->phone = $request['phone'];
        if($request->has('dob')) {
            $user->dob = $request->dob;
        }
        $user->address = $request->address;
        $user->about = $request->about;
        $user->updated_at = date('Y-m-d H:i:s');
        $user->save();

        flash()->success(__('Profile updated'));
        return redirect()->back();
    }

    public function updatePermissions(Request $request, $id)
    {
        $user = User::find($id);
        foreach ($request->permissions as $permission) {
            $user->givePermissionTo($permission);
        }

        flash()->success(__('request_success'));
        return redirect('users/'.$id.'/view#permissions');
    }

    public function revokePermission(Request $request)
    {
        $user = User::find($request->user_id);
        $user->revokePermissionTo($request->perm_name);
        flash('success', __('request_success'));
        return response()->json(['state' => 'success'], 200);
    }

    /**
     * find users
     */
    public function findUser()
    {
        $users = User::get();
        echo json_encode($users);
    }

    /**
     * todo
     *
     * @param null $month
     * @param null $year
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function birthdays()
    {
        if(isset($_GET['y'])) {
            $year = $_GET['y'];
        } else {
            $year = '%';
        }

        if(isset($_GET['m'])) {
            $month = sprintf('%02d', $_GET['m']);
        } else {
            $month = date('m');
        }

        if(isset($_GET['d'])) {
            $day = $_GET['d'];
        } else {
            $day = '%';
        }

        $users = User::where('dob', 'LIKE', "$year-$month-$day")->get();

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $months[date('n', $timestamp)] = date('F', $timestamp);
        }
        ksort($months);
        return view('admin.birthdays', compact('users', 'months'));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function export(Request $request)
    {
        $fileName = 'users_export.csv';

        $users = User::select($request->col)->get();
        $fp = fopen($fileName, 'w');
        fputcsv($fp, $request->col);

        foreach ($users as $key => $item) {
            fputcsv($fp, $item->toArray());
        }
        fclose($fp);
        return Response::download($fileName)->deleteFileAfterSend(TRUE);
    }
}
