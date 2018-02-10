<!DOCTYPE html>
<html lang="en">
<head>
    <title>@lang("Invoice")</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{asset('/css/invoice-style.css')}}" rel="stylesheet">
    <link href="{{asset('/css/jquery-ui-1.10.3.custom.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/invoice-print.css')}}" media="print" rel="stylesheet">

</head>

<body>


<div id="wrap">

    <div class="container page-header">

        @include('flash::message')
        <div class="row">
            <div class="col-md-8" style="box-shadow: 0 0 4px #ddd;
    border: 1px solid #ddd;">

                <div class="row">
                    <div class="col-md-6">
                        <div class="logo clearfix">
                            <img src="{{url()->to('/img/logo.png')}}"/>
                            <br/>
                        </div>
                        <h4>@lang("Invoice") # {{$invoice->id}}</h4>

                        <div>@lang("Due on"): {{date('m/d/Y',strtotime($invoice->due_date))}}</div>
                    </div>

                    <div class="col-md-6 logoContainer">

                        <strong>{{config('app.name')}}</strong><br/>
                        <span style="font-size:12px;">{!! config('app.company.address') !!}</span>
                    </div>
                </div>

                <hr/>

                <div class="row">

                    <div class="col-md-6 clientInfo">
                        <div id="clientName" class="">{{$client->name}}</div>
                        <input type="hidden" name="clientName" value="{{$client->name}}"/>

                        <div id="clientAddress" class="">{{$client->address}}</div>
                        <input type="hidden" name="clientAddress" value="{{$client->address}} "/>

                        <div id="clientPhone" class="">{{$client->phone}}</div>
                        <input type="hidden" name="clientPhone" value="{{$client->phone}}"/>
                    </div>

                    <div class="col-md-6 companyInfo">

                        <div>{{config('mail.from.address')}}</div>
                        <div>{{config('app.company.phone')}}</div>
                        <div>{{url()->to('')}}</div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <table class="table table-striped table-condensed" id="itemsTable">
                        <thead>
                        <tr>

                            <th style="width: 30%">@lang("Item")</th>
                            <th style="width: 40%">@lang("Description")</th>
                            <th style="width: 10%">@lang("Quantity")</th>
                            <th style="width: 10%">@lang("Price")</th>
                            <th style="width: 10%">@lang("Total")</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoiceItems as $item)
                            <tr class="item-row">
                                <td>{{$item->itemName}}</td>
                                <td>{{$item->itemDesc}}</td>
                                <td>{{$item->itemQty}}</td>
                                <td>{{'$'.$item->itemPrice}}</td>
                                <td>${{number_format( $item->itemQty * str_replace(',', '', $item->itemPrice), 2, '.', ',')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <hr/>

                <div class="row">
                    <div class="col-md-7">
                        <div id="notes" class="alert alert-info notes">
                            @lang("billing.invoice_note")
                        </div>
                        <blockquote>{{$invoice->notes}}</blockquote>
                    </div>

                    <div class="col-md-5 text-right">
                        <div class="row">
                            <div class="col-md-6 text-right"><h5>@lang("Sub total"):</h5></div>
                            <div class="col-md-6 text-right"><h5 id="subTotal">{{'$'.$subTotal}}</h5></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 text-right">
                                <div class="input-group">
                                    <span class="input-group-addon">@lang("Sales tax"):{{($invoice->tax>0)?$invoice->tax:0}}
                                        %</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-right" id="salesTax"><h5>{{'$'.$totalTax}}</h5></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 text-right"><h4>@lang("Grand total"):</h4></div>
                            <div class="col-md-6 text-right text-info"><h4
                                        id="grandTotal">{{'$'.\App\Models\Billing\Invoices::grandTotal($invoice->id)}}</h4>
                            </div>
                        </div>
                        <div class="row bg-success">
                            <div class="col-md-6 text-right"><h4>@lang("Paid"):</h4></div>
                            <div class="col-md-6 text-right text-success">
                                <h4>${{number_format(\App\Models\Billing\Invoices::totalPaid($invoice->id),2,'.',',')}}</h4>
                            </div>
                        </div>
                        <div class="row bg-danger">
                            <div class="col-md-6 text-right"><h4>@lang("Due"):</h4></div>
                            <div class="col-md-6 text-right text-danger">
                                <h4>{{'$'.\App\Models\Billing\Invoices::totalDue($invoice->id)}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 no-print" style="border-left: solid 1px #ccc5b9;">
                @if($invoice->totalDue >0)
                    <div class="row">
                        @ability('admins','update-invoice')
                        <div class="col-sm-6">
                            <a href="{{url('/invoice/'.$invoice->id.'/email-reminder')}}"
                               style="width:100%"
                               class="btn btn-info">
                                <i class="fa fa-envelope"></i>
                                @lang("Send reminder")
                            </a>
                        </div>
                        @endability

                        <div class="col-sm-6">
                            <a href="?pdf" class="btn btn-default" style="width:100%"><i
                                        class="fa fa-cloud-download"></i>
                                PDF</a>
                        </div>
                    </div>
                    <br/>
                    @if($invoice->allow_online_pay ==1)
                        <div class="stripe-pay-box" style="border:solid 1px #ccc5b9;padding:10px;">
                            <div class="alert alert-info"
                                 style="-webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;"><i
                                        class="fa fa-cc-stripe"></i> @lang("Pay with Stripe")
                            </div>
                            {!! Form::open(['url'=>'/invoice/stripe-pay','id'=>'payment-form']) !!}
                            {!! Form::hidden('invoice_id',$invoice->id) !!}
                            {!! Form::hidden('child_id',$invoice->child_id) !!}

                            <div class="input-group">
                                <span class="input-group-addon" style="width:110px">@lang("Email"):</span>
                                {!! Form::input('email','email',$client->email,['required'=>'required']) !!}
                            </div>
                            <br/>

                            <div class="input-group">
                                <span class="input-group-addon" style="width:110px">@lang("Amount"):</span>
                                {!! Form::text('amount',$invoice->totalDue,['placeholder'=>'Amount','required'=>'required','class'=>'amount']) !!}
                            </div>
                            <br/>
                            <button class="btn btn-success btn-xlg charge"
                                    data-key="{{env('APP_ENV')=='local'? config('app.stripe_test_public') : config('app.stripe_public')}}"
                                    data-image="/img/checkout.png"
                                    data-email="{{$client->email}}"
                                    data-currency="{{config('app.currency.abbr')}}"
                                    data-name="Invoice #{{$invoice->id}}"
                                    data-description="AMDT,LLC Invoice #{{$invoice->id}} payment"
                                    data-label="Invoice Pay"><i class="ti-credit-card"></i> @lang("Process Payment")
                            </button>

                            <br/>
                            <br/>
                            <img src="/img/stripe.png" style="width:80%"/>

                            {!! Form::close() !!}
                        </div>
                        {{--<hr/>--}}
                        {{--<div>--}}
                        {{--<a href="{{url('/invoice/'.$invoice->id.'/pay/'.$client->id)}}" class="btn btn-primary"--}}
                        {{--style="width:100%">--}}
                        {{--<i class="fa fa-paypal"></i>--}}
                        {{--Pay with PayPal {{'$'.$invoice->totalDue}}</a>--}}
                        {{--</div>--}}
                    @else
                        <div class="alert alert-info">
                            @lang("billing.online_pay_disabled")
                        </div>
                    @endif
                @else
                    <button class="btn btn-success"><i class="fa fa-check-square">@lang('PAID IN FULL')</i></button>
                @endif

                <hr/>
                @if(Auth::guest())
                    @lang('billing.login-to-view-all-invoices')
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8" style="box-shadow: 0 0 4px #ddd;
    border: 1px solid #ddd;">
                <h3>@lang("History")</h3>

                <table class="table table-responsive table-striped">
                    @foreach(\App\Models\Billing\InvoicePayments::where('invoice_id',$invoice->id)->get() as $pay)
                        <tr>
                            <td>
                                {{($pay->txn_amount>0)?'Payment':'Refund'}}
                            </td>
                            <td>{{date('d M, Y',strtotime($pay->txn_date))}}</td>
                            <td>${{$pay->txn_amount}}</td>
                            <td>{{ucwords($pay->pay_method)}}</td>
                            <td>{{$pay->remarks}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

<div id="footer">
    <div class="container">
        <p class="muted credit">&copy; {{config('app.name')}}</p>
    </div>
</div>

<script type="text/javascript" src="{{ asset('js/jquery-1.11.1.min.js')}}"></script>
@if(env('APP_ENV')=='local')
    <script src="/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/js/jquery-ui.js" type="text/javascript"></script>
    <script src="/js/numeral.min.js"></script>
@else
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>
@endif

<script src="/js/global.js" type="text/javascript"></script>

<script src="https://checkout.stripe.com/v2/checkout.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.amount').val(numeral($('.amount').val()).format('0.00'));

        $('.charge').on('click', function (event) {
            event.preventDefault();

            if (!validCurrency()) return;

            var $button = $(this),
                    $form = $button.parents('form');
            var opts = $.extend({}, $button.data(), {
                token: function (result) {
                    $form.append($('<input>').attr({type: 'hidden', name: 'stripeToken', value: result.id}));
                    $form.submit();
                }
            });

            StripeCheckout.open(opts);
        });
    });
</script>

<script type="text/javascript" src="{{ asset('/js/invoice-script.js')}}"></script>
<script type="text/javascript" src="{{ asset('/js/invoice-general.js')}}"></script>


</body>
</html>