<?php

namespace App\Http\Controllers;

use App\Models\Billing\Transactions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Cashier\Subscription;
use Mockery\CountValidator\Exception;


class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'], [
            'except' => ['checkout',
        'customSubscriptionPlan']]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        if (isset($_GET['s']) && $_GET['s'] !== "") {
            $term = $_GET['s'];
            $txns = Transactions::where('txn_id', 'LIKE', "%$term%")
                ->orWhere('item', 'LIKE', "%$term%")
                ->simplePaginate(25);
        } else {
            $txns = Transactions::simplePaginate(50);
        }
        return view('giving.transactions', compact('txns'));
    }

    /**
     * process manual gift
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function supportSubscribe(Request $request)
    {
        $rules = [
            'amount' => 'required|max:50'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        \Stripe\Stripe::setApiKey(config('app.stripe_secret'));

        $request->interval = 'month';

        //find user
        if ($request->has('email')) { //guest giving
            $user = User::whereEmail($request->email)->first();
        } else {
            $user = User::find($request->user_id);
        }

        if (count($user) == 0) { //user does not exist so create one
            $user = new User();
            $username = explode('@', $request->email);
            $user->username = $username[0] . rand(1111, 9999); //give them auto username
            $user->phone = '123456789';
            $user->email = $request->email;
            $user->password = bcrypt(str_random(6));
            $user->first_name = 'guest';
            $user->last_name = 'guest';
            $user->created_at = date('Y-m-d H:i:s');
            $user->confirmation_code = str_random(30);
            //create stripe customer
            $customer = Transactions::createCustomer($request);
            $stripe_id = $customer->id;
            $user->stripe_id = $stripe_id;
            $user->save();
        }
        // Create a Customer if not exists
        $stripe_id = $user->stripe_id;
        if ($stripe_id == null) {
            //just in case...
            try {
                $customer = \Stripe\Customer::create(array(
                        "source" => $request->stripeToken,
                        "email" => $user->email,
                        "description" => $user->email)
                );
            } catch (\Stripe\Error\Base $e) {
                flash()->error(__("Unable to create a Stripe Account. Please contact us"));
                return redirect()->back()->withInput();
            }
            $stripe_id = $customer->id;
            //update stripe customer id
            $user->stripe_id = $stripe_id;
            $user->save();
        }

        //save new source
        $token = $request->stripeToken;
        try {
            $cu = \Stripe\Customer::retrieve($stripe_id);
            $cu->source = $token;
            $cu->save();
        } catch (\Stripe\Error\Base $e) {
            //create customer if not exist.
            //e.g. They have stripe id on local database but not in stripe account
            $error = $e->jsonBody['error'];
            if ($error['type'] == "invalid_request_error") {
                if (strpos($error['message'], 'No such customer') !== false) {
                    $request->email = $user->email;
                    $request->first_name = $user->first_name;
                    $customer = Transactions::createCustomer($request);
                    $stripe_id = $customer->id;
                    $user->stripe_id = $stripe_id;
                    $user->save();
                }
            } else {
                flash()->error(__("Unable to create a Stripe Account. Please contact us"));
                return redirect()->back()->withInput();
            }

        }

        //process card payment
        try {
            $request->email = $user->email;
            $charge = self::customSubscriptionPlan($request, $user->email);
        } catch (\Stripe\Error\Card $e) {
            flash()->error(__("Card has been declined. Please try another card"));
            return redirect()->back()->withInput();
        }

        //log transaction
        $txn = new Transactions();
        $txn->txn_id = $charge->id;
        $txn->user_id = $user->id;
        $txn->stripeToken = $request->stripeToken;
        $txn->item = $request->plan_id . ' support';
        $txn->desc = $request->plan_id . ' support membership';
        $txn->amount = number_format(($request->amount), 2);
        $txn->customer_id = $user->stripe_id;
        $txn->currency = config('app.currency.abbr');
        $txn->save();

        //send thank you
        Transactions::sendThankYou($user, $request->amount, $txn->desc);

        flash()->success(__("Thank you! We have sent email confirmation"));
        return redirect()->back();
    }

    function checkout(Request $request)
    {
        if (env('APP_ENV') == 'local') {
            $key = config('app.stripe_test_secret');
        } else {
            $key = config('app.stripe_secret');
        }
        \Stripe\Stripe::setApiKey($key);

        $token = $_POST['stripeToken'];
//
//        // Create a Customer:
//        $customer = \Stripe\Customer::create(array(
//            "email" =>$request->stripeEmail,
//            "source" => $token,
//        ));

        //charge
        $charge = \Stripe\Charge::create(array(
            "amount" => str_replace('.','',$request->amount),
            "currency" => "USD",
            "description" => $request->desc,
            "source" => $token
        ));


        Mail::send('emails.billing.checkout-thankyou', [
            'item' =>$request->item,
            'amount' => '$'.$request->amount,
            'desc' => $request->desc], function ($m) use ($request) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($request->stripeEmail,config('app.name').' Guest')->subject(config('app.name') . ' Receipt- Thank you!');
            });
        flash()->success(__("Payment successful. Please check your email for confirmation"));
        return redirect()->back();
    }

    /**
     * create a custom subscription for user
     * @param $request
     * @return \Stripe\Subscription
     */
    function customSubscriptionPlan($request, $email)
    {
        //recurrent contribution
        //create a plan for this customer
        $user = User::whereEmail($email)->first();

        $current_time = time();
        $plan_name = strval($current_time);
        $customer_plan = \Stripe\Plan::create(array(
                "amount" => Transactions::convertToCents($request->amount),
                "interval" => $request->interval,
                "name" => " $request->desc " . $user->email . '_' . rand(1111, 9999),
                "currency" => "usd",
                "id" => $plan_name
            )
        );

        //subscribe this customer to plan and charge now
        $charge = \Stripe\Subscription::create(array(
            "customer" => $user->stripe_id,
            "plan" => $plan_name
        ));

        //remember locally to allow user to cancel or suspend
        $subsc = new Subscription();
        $subsc->user_id = $user->id;
        $subsc->subscription_id = $charge->id;
        $subsc->amount = $request->amount;
        $subsc->interval = $request->interval;
        $subsc->status = 'active';
        $subsc->save();

        return $charge;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function subscriptions()
    {
        $gifts = Subscription::whereUserId(auth()->user()->id)->get();
        return view('transactions.recurring', compact('gifts'));
    }

    /**
     * @param $id
     * @param $action
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateSubscription($id, $action)
    {
        \Stripe\Stripe::setApiKey(config('app.stripe_secret'));

        $subsc = Subscription::whereUserId(auth()->user()->id)->whereId($id)->first();

        if (count($subsc) > 0) {
            switch ($action) {

                case "cancel":
                    try {
                        $subscription = \Stripe\Subscription::retrieve($subsc->subscription_id);
                        //remove the array to cancel immediately,
                        //otherwise, subscription will end at the end of the current cycle.
                        $subscription->cancel(array('at_period_end' => true));

                        //update db
                        $subsc->status = "cancelled";
                        $subsc->save();
                    } catch (Exception $e) {
                        flash()->error(__("Unable to deactivate your recurring plan. Please contact us"));
                        return redirect()->back();
                    }
                    break;

                case "suspend":

                    break;

                case "activate": //activates cancelled plan before trial end
                    $subscription = \Stripe\Subscription::retrieve($subsc->subscription_id);
                    $subscription->plan = $subscription->plan->id;
                    $subscription->save();

                    //update db
                    $subsc->status = "active";
                    $subsc->save();
                    flash()->error(__("Plan has reactivated"));
                    break;
                default:
                    flash()->error(__("Unable to process your request"));
                    break;
            }
        } else {
            flash()->error('Transaction not found');
        }
        return redirect()->back();
    }

}
