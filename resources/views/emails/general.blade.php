@extends('emails.template')

@section('content')
    Dear {{isset($user)?$user: 'valued member'}},
    <p></p>
    {!! $msg !!}
@endsection

