<?php

namespace App\Http\Controllers\Auth;

use App\Models\Modules;
use App\Role;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    /**
     *
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'confirmAccount']]);
        $this->middleware('role:admin', ['except' => ['login', 'confirmAccount'],]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('account');
        }
        flash()->error(__('Username or password is incorrect'));
        return redirect()->back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getLogout()
    {
        Auth::logout();
        return redirect('/');
    }

    /**
     * @param $confirmation_code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirmAccount($confirmation_code)
    {
        if(!$confirmation_code) {
            flash()->error(__('No confirmation code found'));
            return redirect('/');
        }
        $user = User::whereConfirmationCode($confirmation_code)->first();
        if(!$user) {
            flash()->error(__('Confirmation code is invalid or expired'));
            return redirect('/');
        }
        $user->confirmed = 1;
        $user->confirmation_code = NULL;
        $user->save();

        flash()->success(__('You have successfully verified your account'));
        return redirect('/');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function roles()
    {
        $roles = Role::all();
        return view('admin.roles', compact('roles'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newRole(Request $request)
    {
        $rules = [
            'name' => 'required|max:50|unique:roles',
            'display_name' => 'required|max:50|unique:roles',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $request->name = str_clean($request->name);
        Role::create($request->all());

        flash()->success('Role added');
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRole(Request $request, $id)
    {
        $rules = [
            'display_name' => 'required|max:50|unique:roles,display_name,'.$id,
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = Role::find($id);

        $role->fill($request->all());
        $role->save();

        flash()->success(__('Role updated'));
        return redirect()->back();
    }

    /**
     * capture user submitted data
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quickSignUp(Request $request)
    {
        $rules = [
            'email' => 'required|max:50|email|unique:users_temp',
            'phone' => 'unique:users_temp',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            //stay silent
        } else {
            //capture data
            $data = [
                'first_name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'other' => $request->other,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            DB::table('users_temp')->insert($data);
        }

        return view('auth.template');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     *
     * @return User
     * @internal param array $data
     */
    protected function createUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|max:50|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|confirmed|min:6',
                'name' => 'required',
                'phone' => 'required',
            ]
        );
        if($validator->fails()) {
            flash()->error(__('Error').'!'.__('Check fields and try again'));
            return redirect('/login')->withErrors($validator)->withInput();
        }

        $confirmation_code = str_random(30);

        //log transaction
        //$subscription->id;

        $user = new User();
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->name = $request->name;
        $user->created_at = date('Y-m-d H:i:s');
        $user->confirmation_code = $confirmation_code;
        $user->save();

        //delete if in temp table
        DB::table('users_temp')->where('email', $request->email)->delete();

        //notify user to activate account
        Mail::send('emails.accounts-verify', ['confirmation_code' => $confirmation_code], function ($m) use ($request) {
            $m->from(
                env('EMAIL_FROM_ADDRESS'),
                config('app.name')
            );

            $m->to($request['email'], $request['first_name'])->subject('Verify your email address');
        });

        //subscribe to mailchimp
        //Newsletter::subscribe($request['email'],['firstName'=>$request['first_name']]);

        flash()->success(__('Thanks for signing up').__('Please check your email'));

        return redirect('login');
    }

    /**
     * allows posting email to send verification
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendConfirmation(Request $request)
    {
        if($request->email !== NULL) { //post has email
            $user = User::whereEmail($request->email)->first();
        } else {
            if(Auth::guest()) {
                return redirect('login');
            }
            $user = User::find(auth()->user()->id);
        }

        if($user->confirmed == 1) {//check if its verified
            flash()->success(__('This account is already verified'));
            return redirect('account');
        }

        if($user->confirmation_code == NULL) {
            $user->confirmation_code = sha1(time());
            $user->save();
        }
        Mail::send('emails.accounts-verify', ['confirmation_code' => $user->confirmation_code], function ($m) use ($request, $user) {
            $m->from(env('EMAIL_FROM_ADDRESS'), config('app.name'));
            $m->to($user->email, $user->first_name)->subject(__('Verify your email address'));
        });
        flash()->success(__('Please check  email to verify your account'));
        return redirect()->back();
    }
}
