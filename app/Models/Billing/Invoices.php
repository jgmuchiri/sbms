<?php

namespace App\Models\Billing;

use App\Settings;
use DateTime;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use stdClass;

class Invoices extends Model
{
    //
    public static function invoiceBalance($invoice_id)
    {

    }

    function invoiceItems($invoice_id)
    {

    }

    function invoicePayments($invoice_id)
    {

    }

    /**
     * just the total for all items
     *
     * @param $invoice_id
     * @return int
     */
    public static function subTotal($invoice_id)
    {
        $inv = DB::table('invoice_items')->where('invoice_id', $invoice_id)->get();
        $price = 0;
        foreach ($inv as $i) {
            $itemPrice = str_replace(',', '', $i->itemPrice);
            $price = $price + ($i->itemQty * $itemPrice);
        }

        return $price;
    }

    /**
     * include tax in total
     *
     * @param $invoice_id
     * @return int|string
     */
    public static function grandTotal($invoice_id)
    {
        $subTotal = self::subTotal($invoice_id);

        $totalTax = Invoices::select('tax')->whereId($invoice_id)->first();

        if ($totalTax == null)
            $tax = 0;
        else
            $tax = number_format($totalTax->tax * $subTotal / 100, 2);

        $total = $tax + $subTotal;

        return $total;
    }

    /**
     * total for all invoices
     * @return int|string
     */
    public static function invoicesTotal($status = null)
    {
        if ($status == null) {
            $invoices = Invoices::select('id', 'tax', 'status')->where('status', '!=', 'draft')->get();
        }
        if ($status == 'paid') {
            $paid = InvoicePayments::select('invoice_id', 'txn_amount', 'txn_tax')->get();
            $totalPaid = 0;
            foreach ($paid as $p) {
                if ($p->txn_tax == null)
                    $pTax = 0;
                else
                    $pTax = number_format($p->txn_tax * $p->txn_amount / 100, 2);
                $totalPaid += $p->txn_amount + $pTax;
            }
            return $totalPaid;
        }
        if ($status == 'due') {
            $invoices = Invoices::select('id', 'tax', 'status')->whereStatus('due')->orWhere('status', 'overdue')->get();
        }

        $total = 0;
        foreach ($invoices as $invoice) {
            $subTotal = self::subTotal($invoice->id);

            if ($invoice->tax == null)
                $tax = 0;
            else
                $tax = number_format($invoice->tax * $subTotal / 100, 2);

            $total += $tax + $subTotal;

            //subtract paid for due
            $totalPaid = 0;
            if ($status == 'due' || $status == 'overdue') {
                $paid = InvoicePayments::select('invoice_id', 'txn_amount', 'txn_tax')->whereInvoiceId($invoice->id)->get();
                foreach ($paid as $p) {
                    if ($p->txn_tax == null)
                        $pTax = 0;
                    else
                        $pTax = number_format($p->txn_tax * $p->txn_amount / 100, 2);
                    $totalPaid += $p->txn_amount + $pTax;
                }
            }
            $total = $total - $totalPaid;
        }
        return $total;
    }

    /**
     * @param $invoice_id
     * @return int
     */
    public static function totalPaid($invoice_id)
    {
        $inv = InvoicePayments::select('txn_amount')->where('invoice_id', $invoice_id)->sum('txn_amount');
        if ($inv == null)
            return 0;
        return $inv;
    }

    /**
     * @param $invoice_id
     * @return int
     */
    public static function totalDue($invoice_id)
    {
        $due = Invoices::grandTotal($invoice_id) - self::totalPaid($invoice_id);
        return number_format($due, 2);
    }

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
     * create new user to stripe without card
     * @param $request
     * @return \Stripe\Customer
     */
    public static function createCustomer($request)
    {

        if (env('APP_ENV') == 'local'):
            \Stripe\Stripe::setApiKey(config('app.stripe_test_secret'));
        else:
            \Stripe\Stripe::setApiKey(config('app.stripe_secret'));
        endif;

        $customer = \Stripe\Customer::create(array(
            "email" => $request->email,
            "description" => 'customer for ' . config('app.name'),
            "source" => $request->stripeToken
        ));

        //alert admin
        if ($customer->id !== null || $customer->id !== "") {
            Mail::send('emails.billing.user-registered-stripe', [
                'email' => $request->email,
                'first_name' => $request->first_name
            ],
                function ($m) use ($request) {
                    $m->from(config('mail.from.address'), config('mail.from.name'));
                    $m->to(config('mail.from.address'), config('mail.from.name'))->subject('Notice: New user');
                });
        }

        return $customer;
    }

    /**
     * @param $data
     */
    public static function sendThankYou($data)
    {
        Mail::send('emails.billing.txn-thank-you', [
            'email' => $data['email'],
            'first_name' => $data['name'],
            'amount' => $data['amount'],
            'desc' => $data['desc']
        ],
            function ($m) use ($data) {
                $m->from(config('mail.from.address'), config('mail.from.name'));
                $m->to($data['email'], $data['name'])->subject(config('mail.from.name') . ' Receipt- Thank you!');
            });

    }

    /**
     * @return int
     */
    public static function totalInvoicesDue()
    {
        $invoices = self::get();
        $total = 0;
        foreach ($invoices as $invoice) {
            $total = $total + self::totalDue($invoice->id);
        }

        return $total;
    }

    /**
     * @param $parent_id
     * @return mixed
     */
    public static function parentInvoices($parent_id)
    {
        return DB::table('invoices')
            ->select('invoices.*', 'invoices.id as inv_id', 'child_parents.*')
            ->join('child_parents', 'child_parents.child_id', '=', 'invoices.child_id')
            ->where('child_parents.parent_id', $parent_id)
            ->get();
    }

    /**
     * @param $parent_id
     * @return int
     */
    public static function parentInvoiceDue($parent_id)
    {
        $invoices = self::parentInvoices($parent_id);
        $total = 0;
        foreach ($invoices as $invoice) {
            $total = $total + self::totalDue($invoice->id);
        }
        return $total;
    }

    //

    public static function paid()
    {
        return DB::table('invoice_payments')->sum('txn_amount');
    }

    /**
     * @param $id
     * @return string
     */
    public static function updateStatus($id)
    {
        //update invoice status
        $inv = self::find($id);

        //get invoice balance
        $due = self::totalDue($id);

        $dueDate = new DateTime($inv->due_date);
        $today = new DateTime(date('Y-m-d'));
        $interval = $dueDate->diff($today);
        $diff = $interval->format('%R%a');

        if ($due > 0) {//pending
            if ($diff > 1) {
                $status = 'overdue';
            } else {
                $status = 'due';
            }
        } else {
            $status = 'paid';
        }

        $inv->status = $status;
        $inv->save();
    }

    public static function status()
    {

        $collection = collect([
            ['id' => 'draft', 'name' => 'Draft'],
            ['id' => 'due', 'name' => 'Due'],
            ['id' => 'paid', 'name' => 'Paid'],
            ['id' => 'overdue', 'name' => 'Overdue']
        ]);


        return $collection;
    }

    public static function invoiceLogo()
    {
        $path = 'img/invoice-logo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        try {
            $data = file_get_contents($path);
            $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (Exception $e) {
            $logo = '';
        }
        return $logo;
    }
}
