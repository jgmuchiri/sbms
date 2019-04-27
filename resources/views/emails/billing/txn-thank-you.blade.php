@extends('emails.template')

@section('content')

    <strong>Hello, {{$first_name}}</strong>
    <p>Thank you for your recent payment.</p>

    <p>We have processed your payment</p>
    <h3>{{config('app.currency.symbol').number_format($amount,2)}}</h3>
    <hr/>
    {{$desc}}
    <hr/>
    <p>
        <br/>
        Sincerely,
        <br/>
        Your support team at<br/>
        {{config('app.name')}}
    </p>
@endsection