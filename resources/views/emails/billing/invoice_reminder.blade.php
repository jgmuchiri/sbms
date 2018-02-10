@extends('emails.template')

@section('content')
    <h5>@lang("Hello") {{$client->first_name}}.</h5>

    We hope you are having a great day! <br/>
    Your invoice ready to be viewed. <br/>
    Please visit the link below to view and make payments. We have also attached it for your records.
    <p><a href="{{$invoiceURL}}"
          style="display: inline-block; font-size: 18px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; background-color: #00b050; padding-top: 10px; padding-bottom: 10px; padding-left: 25px; padding-right: 25px; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-bottom: 3px solid #007334;"
          class="mobile-button">View/Make Payment</a></p>
    <p><a href="{{$invoiceURL}}">{{$invoiceURL}} </a></p>
    <p>
        If you would like to make payment arrangements, please contact us as soon a possible to
        avoid late payment fees.
    </p>

    Regards,<br/>

    <strong>{{config('app.name')}}</strong><br/>
    <em><a href="{{url()->to('/')}}" target="_blank">{{url()->to('/')}}</a> </em><br/>
    <em>{{config('app.company.phone')}}</em><br/>
@endsection