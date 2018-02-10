<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Transactions extends Model
{


    /**
     * @param $value
     * @return float
     */
    public static function convertToCents($value)
    {
        // strip out commas
        $value = preg_replace("/\,/i", "", $value);
        // strip out all but numbers and dot
        $value = preg_replace("/([^0-9\.])/i", "", $value);
        // make sure we are dealing with a proper number now, no +.4393 or 3...304 or 76.5895,94
        if (!is_numeric($value)) {
            return 0.00;
        }
        // convert to a float explicitly
        $value = (float)$value;
        return round($value, 2) * 100;
    }

    /**
     * get all customer charges from stripe
     *
     * @param $customer
     * @return \Stripe\Collection
     */
    public static function customerCharges($customer)
    {
        $user = User::whereStripeId($customer)->first();
        $charges = \Stripe\Charge::all(array("customer" => $user->stripe_id));
        return $charges;
    }

    /**
     * create new user to stripe without card
     * @param $request
     * @return \Stripe\Customer
     */
    public static function createCustomer($request)
    {

        $key = config('app.stripe_secret');
        \Stripe\Stripe::setApiKey($key);
        $customer = \Stripe\Customer::create(array(
            "email" => $request->email,
            "description" => "Customer for" . config('app.name'),
            "source" => $request->stripeToken
        ));

        //alert admin
        if ($customer->id !== null || $customer->id !== "") {

            Mail::send('emails.billing.user-registered-stripe', [
                'email' => $request->email,
                'first_name' => $request->first_name
            ],
                function ($m) use ($request) {
                    $m->from(config('mail.from.address'), config('app.name'));
                    $m->to(config('mail.from.address'), config('app.name'))->subject('Notice: New user');
                });
        }

        return $customer;
    }

    /**
     * @param $request
     * @param $stripe_id
     * @return mixed
     */
    public static function charge($request, $stripe_id)
    {
        //process payment
        try {
            $charge = \Stripe\Charge::create(array(
                "amount" => self::convertToCents($request->amount),
                "currency" => config('app.currency.abbr'),
                "customer" => $stripe_id,
                "description" => $request->desc
            ));
            //todo add new card to file
        } catch (\Stripe\Error\Card $e) {
            flash()->error('Card has been declined. Please try another card');
            return redirect()->back()->withInput();
        }
        return $charge;
    }

    /**
     * @param $user
     * @param $amount
     */
    public static function sendThankYou($user, $amount, $desc = "")
    {
        Mail::send('emails.billing.support-plan-thank-you', [
            'email' => $user->email,
            'first_name'=>$user->first_name,
            'amount' => $amount,
            'desc' => $desc
        ],
            function ($m) use ($user, $desc) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($user->email, $user->first_name)->subject(config('app.name') . ' Receipt- Thank you!');
            });

    }
}
