@extends('emails.template')

@section('content')

    <strong>Hello, {{$first_name}}</strong>
    <p>Thank you for your recent membership subscription.</p>

    <p>We have processed your payment:</p>
        <hr/>
    {{$desc}}
       -  {{config('app.currency.symbol').number_format($amount,2)}}

<hr/>
    <p>Enjoy the benefits of your membership at any time by contacting us.</p>
    Thank you once again.

    <p>
        <br/>
        Sincerely,
        <br/>
        Your support team at<br/>
        {{config('app.name')}}
    </p>
@endsection