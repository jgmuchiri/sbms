@extends('emails.template')
@section('header')
    {{$subject}}
@endsection
@section('content')
    {{$msg}}
@endsection

