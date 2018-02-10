<?php

/**
 * @class   :InvoiceController
 * @author  :jgmuchiri
 * @date    :1/17/16
 * (c) 2016 All Rights Reserved
 */


namespace App\Http\Controllers;
;


use App\Models\Billing\Inventory;
use App\Models\Billing\InvoiceItems;
use App\Models\Billing\InvoicePayments;
use App\Models\Billing\Invoices;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Omnipay\Omnipay;

use Illuminate\Support\Facades\File;
use Stripe;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read-invoices', ['only' => ['index', 'client', 'viewInvoice', 'payInvoice', 'manualPay', 'stripePay'
        ]]);
        $this->middleware('permission:create-invoices', ['only' => ['create', 'storeInvoice', 'replicateInvoice']]);
        $this->middleware('permission:update-invoices', ['only' => [
            'editInvoice',
            'updateInvoice',
            'invoiceRemoveItem',
            'sendToEmail',
            'inventoryJson',
            'inventory',
            'addInventory',
            'deleteInventory'
        ]]);
        $this->middleware('permission:delete-invoices', ['only' => ['deleteInvoice']]);
    }

    /**
     * @return Factory|\Illuminate\View\View
     * @internal param Request $request
     */
    public function index()
    {
        $user = User::findOrFail(Auth::user()->id);

        if ($user->hasRole('admin')) {
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'not-paid') {
                    $invoices = Invoices::where('status', 'due')->orWhere('status', 'overdue')->get();
                } else {
                    $invoices = Invoices::where('status', $_GET['status'])->get();
                }
            } else {
                $invoices = Invoices::get();
            }
            return view('billing.billing', compact('invoices'));

        } else {
            if (isset($_GET['status'])) {
                $invoices = Invoices::where('status', $_GET['status'])->where('user_id', $user->id)->where('status', '!=', 'draft')->get();
            } else {
                $invoices = Invoices::where('user_id', $user->id)->where('status', '!=', 'draft')->get();
            }
            return view('billing.client', compact('invoices'));
        }

    }


    /**
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    function client($id)
    {
        $user = User::findOrFail(Auth::user()->id);
        if ($user->hasRole('admin') || $user->id == $id) {
            if (isset($_GET['status'])) {
                $invoices = Invoices::where('status', $_GET['status'])->where('user_id', $id)->get();
            } else {
                $invoices = Invoices::where('user_id', $id)->get();
            }

            $client = User::findOrFail($id);
            return view('billing.billing', compact('invoices', 'client'));
        }
    }

    /**
     * @param Request $request
     * @return Factory|\Illuminate\View\View
     */
    function create()
    {
        return view('billing.create');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function storeInvoice(Request $request)
    {
        $rules = array(
            'due_date' => 'required',
            'itemName' => 'required'
        );
        $this->validate($request, $rules);

        //create invoice
        $invoice = new Invoices();
        $invoice->guid = str_random(39);
        $invoice->user_id = $request->client;
        $invoice->tax = ($request->has('tax') ? number_format((float)$request->tax, 2) : '0.00');
        $invoice->due_date = $request->due_date;
        $invoice->notes = $request->notes;
        $invoice->created_by = Auth::user()->id;
        $invoice->created_at = $request->created_at;
        $invoice->allow_online_pay = $request->allow_online_pay;
        $invoice->status = $request->status;
        $invoice->save();


        //add items for invoice
        foreach ($request->itemId as $item => $val) {

            $invoiceItems = array(
                'invoice_id' => $invoice->id,
                'itemName' => $request->itemName[$item],
                'itemDesc' => $request->itemDesc[$item],
                'itemQty' => $request->itemQty[$item],
                'itemPrice' => $request->itemPrice[$item],
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d')
            );
            DB::table('invoice_items')->insert($invoiceItems);
        }

        flash()->success(__("Invoice was created successfully"));
        return redirect('invoice/' . $invoice->id . '/edit');

    }

    /**
     * @param $id
     * @return Factory|\Illuminate\View\View
     */
    function editInvoice($id)
    {
        $invoice = Invoices::findOrFail($id);
        $items = InvoiceItems::whereInvoiceId($id)->get();
        return view('billing.edit_invoice', compact('invoice', 'items'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|\Illuminate\Routing\Redirector
     */
    function updateInvoice(Request $request, $id)
    {
        $rules = array(
            'due_date' => 'required'
        );
        $this->validate($request, $rules);

        //create invoice
        $invoice = Invoices::find($id);
        $invoice->user_id = $request->client;
        $invoice->tax = ($request->has('tax') ? number_format((float)$request->tax, 2) : '0.00');
        $invoice->due_date = $request->due_date;
        $invoice->notes = $request->notes;
        $invoice->created_at = $request->created_at;
        $invoice->allow_online_pay = $request->allow_online_pay;
        $invoice->status = $request->status;
        $invoice->updated_at = date('Y-m-d H:i:s');
        $invoice->save();


        //add items for invoice
        if (count($request->itemId)) {
            foreach ($request->itemId as $item => $val) {

                $invoiceItems = array(
                    'invoice_id' => $id,
                    'itemName' => $request->itemName[$item],
                    'itemDesc' => $request->itemDesc[$item],
                    'itemQty' => $request->itemQty[$item],
                    'itemPrice' => $request->itemPrice[$item],
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d')
                );
                DB::table('invoice_items')->insert($invoiceItems);
            }
        }

        flash()->success(__("Invoice has been updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    function replicateInvoice($id)
    {
        $invoice = Invoices::findOrFail($id);
        $newInvoice = new Invoices();
        $newInvoice->user_id = $invoice->user_id;
        $newInvoice->tax = $invoice->tax;
        $newInvoice->due_date = date('Y-m-d');
        $newInvoice->notes = $invoice->notes;
        $newInvoice->created_by = Auth::user()->id;
        $newInvoice->status = 'draft';
        $newInvoice->guid = str_random(39);
        $newInvoice->allow_online_pay = 1;
        $newInvoice->save();

        $invoiceItems = InvoiceItems::whereInvoiceId($invoice->id)->get();


        foreach ($invoiceItems as $item) {
            $newItems = new InvoiceItems();
            $newItems->invoice_id = $newInvoice->id;
            $newItems->itemName = $item->itemName;
            $newItems->itemDesc = $item->itemDesc;
            $newItems->itemQty = $item->itemQty;
            $newItems->itemPrice = $item->itemPrice;
            $newItems->created_by = Auth::user()->id;
            $newItems->save();
        }

        flash()->success(__("Invoice has been replicated"));
        return redirect('invoice/' . $newInvoice->id . '/edit');
    }

    /**
     * @param $id
     * @return RedirectResponse|\Illuminate\Routing\Redirector
     */
    function invoiceRemoveItem($invoice, $id)
    {
        $item = InvoiceItems::where('invoice_id', $invoice)->where('id', $id)->first();
        if (count($item) == 0) {
            flash()->error(__("Something went wrong! Try again"));
            return redirect()->back();
        }
        $item->delete();
        flash()->success(__("Item deleted"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return Factory|\Illuminate\View\View
     * @internal param Request $request
     */
    function viewInvoice($guid)
    {
        $invoice = Invoices::whereGuid($guid)->first();
        if (count($invoice) == 0)
            return view('errors.404');

        $id = $invoice->id;

        if (isset($_GET['success'])):
            $this->paypalSucess($_GET['u']);
            return redirect('invoice/' . $guid . '/view');
        else:
            $invoice->totalDue = Invoices::totalDue($id);

            $client = User::find($invoice->user_id);

            $client->name = $client->first_name . ' ' . $client->last_name;

            $invoiceItems = DB::table('invoice_items')->where('invoice_id', $id)->get();
            $subTotal = Invoices::subTotal($id);

            $data = array(
                'invoice' => $invoice,
                'client' => $client,
                'invoiceItems' => $invoiceItems,
                'subTotal' => $subTotal,
                'totalTax' => number_format(($invoice->tax * $subTotal / 100), 2),
                'payments' => InvoicePayments::whereInvoiceId($id)->get(),
                'logo' => Invoices::invoiceLogo()
            );

            if (isset($_GET['pdf'])) {
                $pdf = PDF::loadView('billing.pdf_invoice', $data);
                return $pdf->download('invoice-' . $id . '.pdf');//stream() to view in browser
            } else {
                return view('billing.view_invoice', $data);
            }
        endif;
    }

    /**
     * @param $id
     */
    function payInvoice($id, $user)
    {
        $guid = Invoices::where('id', $id)->first()->guid;
        //api
        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername(env('PAYPAL_EXPRESS_USERNAME'));
        $gateway->setPassword(env('PAYPAL_EXPRESS_PASSWORD'));
        $gateway->setSignature(env('PAYPAL_EXPRESS_SIGNATURE'));
        if (env('APP_ENV') == 'local'):
            $gateway->setTestMode(true);
        else:
            $gateway->setTestMode(false);
        endif;

        $params = array(
            'invoice_id' => $id,
            'amount' => Invoices::totalDue($id),
            'currency' => 'USD',
            'description' => 'Invoice ID:' . $id,
            'returnUrl' => url('/invoice/' . $guid . '/view/?success&u=' . $user),
            'cancelUrl' => url('/invoice/' . $guid . '/view/?cancel&u=' . $user)
        );
        Session::put('params', $params);
        Session::save();

        $response = $gateway->purchase($params)->send();

        if ($response->isSuccessful()) {

        } elseif ($response->isRedirect()) {
            // redirect to offsite payment gateway
            $response->redirect();
        } else {
            // payment failed: display message to customer
            echo $response->getMessage();

        }

    }


    /**
     *
     */
    function paypalSucess($user)
    {
        $gateway = Omnipay::create('PayPal_Express');
        $gateway->setUsername(env('PAYPAL_EXPRESS_USERNAME'));
        $gateway->setPassword(env('PAYPAL_EXPRESS_PASSWORD'));
        $gateway->setSignature(env('PAYPAL_EXPRESS_SIGNATURE'));
        if (env('APP_ENV') == 'local'):
            $gateway->setTestMode(true);
        else:
            $gateway->setTestMode(false);
        endif;

        $params = Session::get('params');

        $response = $gateway->completePurchase($params)->send();

        $paypalResponse = $response->getData(); // this is the raw response object

        if (isset($paypalResponse['PAYMENTINFO_0_ACK']) && $paypalResponse['PAYMENTINFO_0_ACK'] === 'Success') {

            // Response
            $data = array(
                'invoice_id' => $params['invoice_id'],
                'txn_date' => date('Y-m-d H:i:s'),
                'txn_amount' => $paypalResponse['PAYMENTINFO_0_AMT'],
                'txn_tax' => $paypalResponse['PAYMENTINFO_0_TAXAMT'],
                'txn_status' => $paypalResponse['PAYMENTINFO_0_PAYMENTSTATUS'],
                'txn_id' => $paypalResponse['PAYMENTINFO_0_SECUREMERCHANTACCOUNTID']
            );

            //save txn
            DB::table('invoice_payments')->insert($data);

            //send thank you
            $user = User::find($user);
            $data2 = array(
                'amount' => $data['txn_amount'],
                'desc' => 'Invoice #' . $data['invoice_id'],
                'email' => $user->email,
                'name' => $user->first_name,

            );

            //update status
            Invoices::updateStatus($params['invoice_id']);

            Invoices::sendThankYou($data2);

        } else {
            flash()->error(__("Payment failed"));
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function stripePay(Request $request)
    {
        $rules = [
            'email' => 'required',
            'amount' => 'required|max:50'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (env('APP_ENV') == 'local'):
            \Stripe\Stripe::setApiKey(config('app.stripe_test_secret'));
        else:
            \Stripe\Stripe::setApiKey(config('app.stripe_secret'));
        endif;

        //find user
        if ($request->has('email')) {
            $user = User::whereEmail($request->email)->first();
        } else {
            $user = User::find($request->user_id);
        }

        if (count($user) == 0) { //user does not exist so create one
            $user = new User();
            $user->email = $request->email;
            $user->password = bcrypt(str_random(6));
            $user->first_name = 'guest';
            $user->last_name = 'guest';
            $user->created_at = date('Y-m-d H:i:s');
            $user->confirmation_code = str_random(30);
            //create stripe customer
            $customer = Invoices::createCustomer($request);
            $user->stripe_id = $customer->id;
        }

        // User exists but does not have stripe account
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

        }

        //save new payment source (credit card)
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
                    $customer = Invoices::createCustomer($request);
                    $stripe_id = $customer->id;
                    $user->stripe_id = $stripe_id;
                }
            } else {
                flash()->error(__("Unable to create a Stripe Account. Please contact us"));
                return redirect()->back()->withInput();
            }

        }

        //all changes to users are done
        $user->save();

        //process card payment
        try {
            //one time payment
            $charge = \Stripe\Charge::create(array(
                "amount" => Invoices::convertToCents($request->amount),
                "currency" => config('app.currency.abbr'),
                "customer" => $stripe_id,
                "description" => $request->desc
            ));

        } catch (\Stripe\Error\Card $e) {
            flash()->error(__("Card has been declined. Please try another card"));
            return redirect()->back()->withInput();
        }

        //log payment
        $ipay = new InvoicePayments();
        $ipay->invoice_id = $request->invoice_id;
        $ipay->txn_id = $charge->id;;
        $ipay->txn_amount = $request->amount;
        $ipay->txn_date = date('Y-m-d H:i:s');
        $ipay->txn_status = 'complete';
        $ipay->created_at = date('Y-m-d H:i:s');
        $ipay->pay_method = 'stripe';
        $ipay->save();

        //update invoice status
        Invoices::updateStatus($request->invoice_id);

        //send thank you
        $data = array(
            'amount' => $request->amount,
            'desc' => $request->desc,
            'email' => $user->email,
            'name' => $user->first_name
        );
        Invoices::sendThankYou($data);

        flash()->success(__("Thank you! We have sent email confirmation"));
        return redirect()->back();

    }


    /**
     * @param $id
     * @return RedirectResponse
     */
    function deleteInvoice(Request $request, $id)
    {
        $invoice = Invoices::find($id);
        if ($invoice == null) {
            echo 'error';
        } else {
            InvoiceItems::whereInvoiceId($id)->delete();
            $invoice->delete();
        }
        echo 'success';
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    function sendToEmail(Request $request)
    {
        $invoice = Invoices::find($request->invoice_id);
        $invoice->totalDue = Invoices::totalDue($request->invoice_id);

        $client = User::findOrFail($invoice->user_id);

        $client->name = $client->first_name . ' ' . $client->last_name;

        $invoiceItems = DB::table('invoice_items')->where('invoice_id', $request->invoice_id)->get();

        $subTotal = Invoices::subTotal($request->invoice_id);

        $data = array(
            'invoice' => $invoice,
            'client' => $client,
            'invoiceItems' => $invoiceItems,
            'subTotal' => $subTotal,
            'totalTax' => number_format($invoice->tax * $subTotal / 100, 2),
            'payments' => InvoicePayments::whereInvoiceId($invoice->id)->get(),
            'logo'=>Invoices::invoiceLogo()
        );
        $destination = 'downloads/';

        $pdf = PDF::loadView('billing.pdf_invoice', $data);
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 493, true);
        }
        $filename = 'amdt_invoice_' . $request->invoice_id . '.pdf';
        $filePath = $destination . $filename;
        $pdf->save($filePath);

        Mail::send('emails.billing.send_invoice', ['msg' => $request->message, 'name' => $request->name],
            function ($m) use ($client, $request, $filePath, $filename) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($request->email, $request->name)->subject(config('app.name') . ' sent you invoice #' . $request->invoice_id . '!');
                $m->attach($filePath, array(
                        'as' => $filename,
                        'mime' => 'application/pdf')
                );
            });

        //keep server clean
        @unlink($filePath);

        flash()->success(__("Invoice has been sent"));
        return redirect()->back();

        //return \Response::download($filename);


    }

    /**
     * send invoice to an email
     *
     * @param $id
     * @return RedirectResponse
     * @internal param Request $request
     */
    public function sendReminder($id)
    {
        $invoice = Invoices::findOrFail($id);
        //invoice url
        $invoiceURL = url('invoice/' . $invoice->guid . '/view');
        $client = User::findOrFail($invoice->user_id);


        $invoice->totalDue = Invoices::totalDue($id);
        $client->name = $client->first_name . ' ' . $client->last_name;
        $invoiceItems = DB::table('invoice_items')->where('invoice_id', $id)->get();
        $subTotal = Invoices::subTotal($id);

        $data = array(
            'invoice' => $invoice,
            'client' => $client,
            'invoiceItems' => $invoiceItems,
            'subTotal' => $subTotal,
            'totalTax' => number_format($invoice->tax * $subTotal / 100, 2),
            'payments' => InvoicePayments::whereInvoiceId($id)->get(),
            'logo'=>Invoices::invoiceLogo()
        );

        $destination = 'downloads/';

        $pdf = PDF::loadView('billing.pdf_invoice', $data);

        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 493, true);
        }
        $filename = 'amdt_invoice_' . $id . '.pdf';
        $filePath = $destination . $filename;
        $pdf->save($filePath);


        Mail::send('emails.billing.invoice_reminder', ['client' => $client, 'invoiceURL' => $invoiceURL],
            function ($m) use ($client, $filePath, $filename, $id) {
                $m->from(config('mail.from.address'), config('app.name'));
                $m->to($client->email, $client->first_name)->subject('Your Reminder for Invoice #' . $id . '!');
                $m->attach($filePath, array(
                        'as' => $filename,
                        'mime' => 'application/pdf')
                );
            });

        sleep(3);
        //keep server clean
        @unlink($filePath);
        flash()->success(__("Reminder has been sent"));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function manualPay(Request $request)
    {
        $rules = [
            'txn_date' => 'required',
            'invoice_id' => 'required',
            'amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $ipay = new InvoicePayments();
        $ipay->invoice_id = $request->invoice_id;
        $ipay->txn_id = rand(111111, 999999);
        $ipay->txn_amount = number_format(str_replace(',', '', (float)$request->amount), 2, '.', '');
        $ipay->txn_date = $request->txn_date;
        $ipay->txn_status = 'complete';
        $ipay->pay_method = $request->pay_method;
        $ipay->remarks = $request->remarks;
        $ipay->created_at = date('Y-m-d H:i:s');
        $ipay->save();

        Invoices::updateStatus($request->invoice_id);
        flash()->success(__("Payment has been recorded"));
        return redirect()->back();

    }

    /**
     * @param Request $request
     */
    function inventoryJson(Request $request)
    {
        $inventory = DB::table('inventory')->get();

        if ($request->itemName !== "") {
            echo json_encode($inventory);
        }
    }

    /**
     * @return Factory|\Illuminate\View\View
     */
    function inventory()
    {
        $inventoryItems = Inventory::get();
        return view('billing.inventory_items', compact('inventoryItems'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function addInventoryItem(Request $request)
    {
        $rules = [
            'itemName' => 'required|max:50',
            'itemCode' => 'required',
            'qtyOnHand' => 'required|integer',
            'itemPrice' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $inv = new Inventory();
        $inv->itemName = $request->itemName;
        $inv->itemDesc = $request->itemDesc;
        $inv->itemCode = $request->itemCode;
        $inv->itemPrice = $request->itemPrice;
        $inv->qtyOnHand = $request->qtyOnHand;
        $inv->save();

        flash()->success(__("Item added"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    function deleteInventoryItem($id)
    {
        $inv = Inventory::find($id);
        $inv->delete();
        flash()->success(__("Item deleted"));
        return redirect()->back();
    }

}