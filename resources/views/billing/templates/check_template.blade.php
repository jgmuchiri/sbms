<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>

    <style type="text/css">
        body{
            margin:0;
        }
        #check {
            background: url('/img/check.png') no-repeat;
            height:600px;
            margin:0;
            padding-top:1px;
        }
        div#check-number{
            font-size: 22px;
            margin-top: -31px;
            margin-left: 600px;
        }

        div#date-box {
            margin-left: 670px;
            margin-top: 11px;
            font-size: 22px;
            font-family: monotype corsiva, cursive;
        }

        div#payee-box {
            margin-left: 173px;
            margin-top: 46px;
            font-size: 32px;
            font-family: monotype corsiva, cursive;
        }

        div#amount {
            margin-left: 913px;
            margin-top: -34px;
            font-size: 30px;
            font-family: monospace;
        }

        div#amount-text {
            margin-left: 57px;
            margin-top: 30px;
            font-size: 28px;
            font-family: monotype corsiva, cursive;
        }

        div#payee-address {
            margin-left: 319px;
            margin-top: 10px;
        }

        #memo-sign{
            position: relative;
        }
        div#memo {
            margin-left: 105px;
            margin-top: 112px;
            font-size: 21px;
            font-family: monotype corsiva, cursive;
        }

        #company-info {
            margin-top: 40px;
            margin-left: 50px;
        }

        #bank-info {
            margin-left: 400px;
            margin-top: -77px;
            position: absolute;
        }

        #bank-info img {
            width: 250px;
        }

        #signature-box {
            margin-left: 630px;
            font-family: monotype corsiva, cursive;
            margin-top: -23px;
            font-size: 22px;
        }
        #check-summary{
            padding:10px;
            margin:10px;
        }
        #account-number,#routing-number{
            font-family: GnuMICR;
            font-size:22px;
        }
        #account-number{
            margin-left: 81px;
            margin-top: 30px;
        }
        #routing-number{
            margin-left: 312px;
            margin-top: -15px;
        }
    </style>
</head>
<body>
<div id="check">
    <div id="company-info">
        <strong>{!! config('app.name') !!}</strong><br/>
        {!! config('app.company.address') !!}
        <br/>
        {!! config('app.company.phone') !!}
    </div>

    <div id="bank-info">
        <img src="/img/bankofamerica.png"/>
        <br/>
        http://bankofamerica.com
        <div id="check-number">
            {{$check->check_no}}
        </div>
    </div>
    <div id="date-box">
        {{date('M d, Y',strtotime($check->created_at))}}
    </div>
    <div id="payee-box">
        {!! $check->payee_name !!}
    </div>
    <div id="amount">
        {!! number_format($check->amount,2,'.',',') !!}
    </div>
    <div id="amount-text">
        {{ucwords(\App\Tools::NumberToText($check->amount))}} *****
    </div>
    <div id="payee-address">
        {!! $check->payee_address !!}
    </div>

    <div id="memo-sign">
        <div id="memo">
            {{$check->memo}}
        </div>
        <div id="signature-box">
            {{$check->payee_name}}
        </div>
    </div>

    <div id="account-number">
        {{env('ACH_ACCOUNT')}}
    </div>
    <div id="routing-number">
        {{env('ACH_ROUTING')}}
    </div>

</div>
<hr style="border:dashed 1px #333;top:580px;"/>

<div id="check-summary">
    {{config('app.name')}}
    <br/>
    {!! config('app.company.address') !!}

    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    {{$check->payee}}
    <br/>
    {{$check->payeeAddr}}
    <br/><br/>
    ${!! number_format($check->amount,2,'.',',') !!}

    <br/>
    <br/>
    <br/>
    <br/>
    {{$check->memo}}

    <br/>
    <br/>
    <br/>
    <br/>
    {!! $check->notes !!}
</div>
</body>
</html>