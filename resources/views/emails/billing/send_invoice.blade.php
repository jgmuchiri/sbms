@extends('emails.template')

@section('content')
    <h5>Hello {{$name}}.</h5>

    We hope you are having a great day! <br/>

    <p>{{config('app.name')}} has sent you a receipt/invoice. Please see attachment</p>

    <p>{{$msg}}</p>

    Regards,<br/>

    <strong>{{config('app.name')}}</strong><br/>
    <em><a href="{{url()->to('/')}}" target="_blank">{{url()->to('/')}}</a> </em><br/>
    <em>{{config('app.company.phone')}}</em><br/>
@endsection