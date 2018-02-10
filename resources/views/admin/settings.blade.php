@extends('layout.template')
@section('title')
    @lang("System Settings")
@endsection
@section('panel-title')
    <i class="fa fa-gears"></i> @lang("Settings")
@endsection
@section('content')
    <div class="row">
        @include('admin.settings-menu')

        <div class="col-sm-10" style="border-left:solid 1px #ccc;">

            <div class="alert content-alert alert-danger alert-white rounded">
                {{--<a href="#" class="close"><i class="fa fa-times-circle-o"></i> </a>--}}
                <div class="icon">
                    <i class="fa fa-warning"></i>
                </div>
                <p class="category small">
                    @lang("All site configurations are managed in") <code>.evn</code> @lang("file located in the root
                            of your application.")<br/>
                    <span class="text-danger">
                    @lang("Change these settings only if you know what you are doing!")
                </span>
                </p>
            </div>
            <div class="alert alert-danger">
                {!! Form::open(['url'=>'settings/backup']) !!}
                <button class="btn btn-warning"><i class="fa fa-database"></i> @lang("Backup First!")</button>
                {!! Form::close() !!}
            </div>
            {!! Form::open() !!}
            {!! Form::textarea('envContent',$envContent,['rows'=>20,'class'=>'form-controller']) !!}
            <button class="btn btn-default"><i class="fa fa-save"></i> @lang("Update")</button>
            {!! Form::close() !!}

            <hr/>
            {!! Form::open(['url'=>'settings/logo','method'=>'post','files'=>'true']) !!}
            <label>@lang("Upload logo")</label>

            <img class="thumbnail" src="/images/logo.png"
                 style="width:300px;"/>

            {{Form::file('logo')}}
            <hr/>

            <button class="btn btn-success">@lang("Update")</button>

            {!! Form::close() !!}
        </div>
    </div>

@endsection
