@extends('layout.template')
@section('panel-title')
    @lang("Access denied")
    @endsection
@section('content')
    <div class="alert alert-danger text-center">

        <h1>@lang("You are not authorized to access this resource")</h1>

        <h4>@lang("Go back and try again")</h4>
    </div>
@endsection