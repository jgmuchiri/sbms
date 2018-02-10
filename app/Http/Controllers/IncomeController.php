<?php

namespace App\Http\Controllers;

use App\Models\Billing\InvoicePayments;
use App\Models\Billing\Invoices;
use Illuminate\Http\Request;

use App\Http\Requests;

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin|manager']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $payments = InvoicePayments::get();

        $invPayments = array();
        foreach ($payments as $pay) {
            $invoice = Invoices::find($pay->invoice_id);

            $invPayments[] = array(
                'name' => __("Payment for invoice").' #<a target="_blank" href="/invoice/' . $invoice->guid . '/view">' . $pay->invoice_id . '</a>',
                'date' => date('d M, Y',strtotime($pay->txn_date)),
                'amount' => $pay->txn_amount
            );
        }

        $income = $invPayments;

        return view('billing.income', compact('income'));
    }
}
