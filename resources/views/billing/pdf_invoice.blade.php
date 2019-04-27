<html lang="en">
<head>
    <style>
        input[type="text"] {
            margin-bottom: 0 !important;
        }

        #footer {
            height: 60px;
        }

        html,
        body {
            height: 100%;
            font-family: 'Economica', sans-serif;
        }

        #wrap {
            height: auto !important;
            margin: 0 auto;
            max-width: 780px;
            position: relative;
        }

        /* Lastly, apply responsive CSS fixes as necessary */
        @media (max-width: 700px) {

            #footer {
                margin-left: -20px;
                margin-right: -20px;
                padding-left: 20px;
                padding-right: 20px;
            }

            .logo {
                font-size: 10px;
            }

            .table-condensed tbody > tr > td {
                padding: 2px;
            }

            hr {
                margin-top: 10px;
                margin-bottom: 10px;
            }

        }

        .page-header {
            padding-top: 15px !important;
        }

        .companyInfo div {
            padding-top: 4px;
        }

        div.inline {
            display: inline;
        }

        #invGrandTotalTop > h4 {
            margin: 0;
            padding-top: 10px;
            color: red !important;
        }

        .container {
            width: auto;
            max-width: 760px;
        }

        .container .credit {
            margin: 20px 0;
        }

        .logoContainer {
            text-align: right;
            font-size: 12px;
        }

        .logo {
            font-size: 14px;
            line-height: 20px;
            font-family: 'Economica', sans-serif;
            margin-top: -60px;
        }

        .logo img {
            background: #555;
            margin-top:65px;
            width:150px;
            padding: 8px;
            border-radius: 3px;
        }

        .companyInfo {
            text-align: right;
        }

        .additionalInfo h4 {
            margin: 0 0 4px 0 !important;
        }

        .additionalInfo span {
            margin-left: -999em;
            position: absolute;
            font-size: 0.9em;
        }

        .additionalInfo:hover span {
            border-radius: 5px 5px;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.2);
            -webkit-box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 2px 2px rgba(0, 0, 0, 0.2);
            font-family: Calibri, Tahoma, Geneva, sans-serif;
            color: #000;
            position: absolute;
            left: 100px;
            top: 10px;
            z-index: 99;
            margin-left: 0;
            width: 200px;
        }

        .additionalInfo:hover img {
            border: 0;
            margin: -10px 0 0 -55px;
            float: left;
            position: absolute;
        }

        .clientInfo div {
            width: 300px;
            padding-left: 5px;
            padding-right: 20px;
            margin: 0;
            border-left: 3px solid #428bca;
            /*border-bottom: 1px #ccc solid;*/
        }

        .row {
            margin-right: -15px;
            margin-left: -15px;
        }

        .row:before {
            display: table;
            content: " ";
        }

        .row:after {
            clear: both;
        }

        .table {
            width: 100%;
            margin-bottom: 20px;
        }

        .table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th {
            padding: 5px;
        }

        .table > thead > tr > th {
            vertical-align: bottom;
            border-bottom: 1px solid #ddd;
        }

        #itemsTable tbody tr td:last-child, .totals {
            text-align: right;
        }

        .paid-stamp {
            padding: 5px;
            color: #145609;
            background: #19CA4F;
            border: none;
            margin: 3px 0 5px 18px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            width: 130px;
        }

        #itemsTable, .totals {
            font-family: monospace, Courier;
        }

        #itemsTable tr:nth-child(even) td {
        }

        #itemsTable tr:nth-child(odd) {
            background-color: #e3e3e8;
        }

        #itemsTable #header {
            background-color: #dedede;
        }

        #itemsTable th {
            text-align: left;
        }

        .title {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        #clientName {
            font-weight: bolder;
        }

        #notes {
            padding-left: 3px;
            font-size: 12px;
        }
    </style>
</head>

<body>
<div id="wrap">
    <div class="container page-header">

        <table class="table">
            <tr>
                <td>
                    <div class="logo clearfix">
                           <img src="{{$logo}}"/>
                        <br/>
                    </div>

                    <h4>@lang("Invoice") # {{$invoice->id}}</h4>
                </td>
                <td rowspan="3" class="logoContainer" valign="top">

                    <div class="companyInfo">
                        <div class="clearfix title">{{config('app.name')}}</div>
                        {!! config('app.company.address') !!}
                    </div>

                </td>
            </tr>
            <tr>
                <td>
                    {{--<p>Created On: {{date('m/d/Y',strtotime($invoice->created_at))}}</p>--}}
@lang("Due date") {{date('m/d/Y',strtotime($invoice->due_date))}}
                    <h4 id="grandTotalTop">@lang("Amount due"): {{'$'.$invoice->totalDue}}</h4>
                </td>
            </tr>

        </table>
        @if($invoice->totalDue <=0)
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="paid-stamp">@lang("PAID IN FULL")</div>
                </div>
            </div>
        @endif
        <table class="table" style="border-top:solid 1px #ccc;">
            <tr>
                <td><p></p></td>
            </tr>
            <tr>
                <td class="clientInfo">
                    <div id="clientName" class="">{{$client->name}}</div>

                    <div id="clientAddress" class="">{{$client->address}}</div>

                    <div id="clientPhone" class="">{{$client->phone}}</div>
                </td>
            </tr>
        </table>
        <br/><br/>

        <table class="table-striped table-condensed" id="itemsTable" style="border-top:solid 1px #ccc;width:100%">
            <thead>
            <tr id="header">
                <th>@lang("Item")</th>
                <th>@lang("Description")</th>
                <th style="width: auto">@lang("Quantity")</th>
                <th style="width:auto" class="right">@lang("Price")</th>
                <th style="width: auto">@lang("Total")</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoiceItems as $item)
                <?php $price = $item->itemQty * str_replace(',', '', $item->itemPrice); ?>
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
        <hr/>
        <table class="table">
            <tr>
                <td>
                    <div id="notes">{{$invoice->notes}}</div>
                    <div class="alert alert-info notes">
                       @lang("billing.invoice_note")
                    </div>
                </td>
                <td style="width:30%">
                    <table class="table totals">
                        <tr>
                            <td><strong>@lang("Sub total"):</strong></td>
                            <td>{{'$'.$subTotal}}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang("Sales tax"):</strong></td>
                            <td>{{($invoice->tax>0)?$invoice->tax:0}}%</td>
                        </tr>
                        <tr>
                            <td><strong>@lang("Grand total"):</strong></td>
                            <td>{{'$'.\App\Models\Billing\Invoices::grandTotal($invoice->id)}}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang("Paid")</strong></td>
                            <td>${{number_format(\App\Models\Billing\Invoices::totalPaid($invoice->id),2,'.',',')}}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang("Due"):</strong></td>
                            <td>{{'$'.\App\Models\Billing\Invoices::totalDue(($invoice->id))}}</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </div>


    <div id="footer" style="position: fixed;bottom:0;width:100%;">
        <div class="container">
            <hr style="border:solid 1px #ccc;height:1px;"/>
            <div>
                <strong>{{config('app.name')}}</strong>
                | {{env('APP_URL')}} |
                {{config('mail.from.address')}} | {{config('app.company.phone')}}</div>
            <span style="font-size:12px;color:#ce8940">
                :::business automation through web applications, cloud solutions, web & business email hosting, domain registration and more:::
            </span>
        </div>
    </div>
</div>

<div class="wrap" style="page-break-before: always;">

    <div class="container">
        <h3>Payment History</h3>
        <table class="table table-responsive table-striped">
            @foreach($payments as $pay)
                <tr style="font-family: monotype corsiva, cursive;">
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

    <div id="footer" style="position: fixed;bottom:0;width:100%">
        <div class="container">
            <hr style="border:solid 1px #ccc;height:1px;"/>
            <div>
                <strong>{{config('app.name')}}</strong>
                | {{env('APP_URL')}} |
                {{config('mail.from.address')}} | {{config('app.company.phone')}}</div>
            <span style="font-size:12px;color:#ce8940">
                :::business automation through web applications, cloud solutions, web & business email hosting, domain registration and more:::
            </span>
        </div>
    </div>
</div>

</body>
</html>