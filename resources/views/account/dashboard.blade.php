@extends('layout.template')
@section('title')
    @lang("Dashboard")
@endsection
@section('container')

    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="btn-group">
                    <a href="/invoices" class="btn btn-info btn-lg"><span class="fa fa-money"></span></a>
                    <a href="/invoice" class="btn btn-default btn-lg">@lang("Invoices")</a>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="btn-group">
                    <a href="/dl" class="btn btn-warning btn-lg"><span class="fa fa-download"></span></a>
                    <a href="/dl" class="btn btn-default btn-lg">@lang("Downloads")</a>
            </div>
        </div>
    </div>
@endsection