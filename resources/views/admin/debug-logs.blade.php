@extends('layout.template')
@section('title')
    Debug log
@endsection
@section('crumbs')
    <a href="#" class="current">@lang("Debug log")</a>
@endsection
@section('panel-title')
    <i class="fa fa-bug"></i> @lang("Debug logs")
    @endsection

@section('content')
    <div class="row">
        @include('admin.settings-menu')

        <div class="col-sm-10" style="border-left:solid 1px #ccc;">

            @if(count($logs)==0)
                <div class="alert alert-success">
                   <i class="fa fa-info"></i> @lang("No debug logs at this time")
                </div>
            @else
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    @lang("Select log date") <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    @foreach($logs as $log)
                        <li><a href="?log={{$log}}">{{$log}}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($logContent))
                <h3>@lang("Showing logs for") {{str_replace('laravel-','',$_GET['log'])}}</h3>
                {!! Form::open(['url'=>route('empty-debug-log')]) !!}
                {!! Form::hidden('log_date',$_GET['log']) !!}
                <textarea name="logContent" style="width:100%" class="controls" rows="20">{!! $logContent !!}</textarea>
                <br/>
                <button class="btn btn-danger">@lang("Empty log")</button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>
@endsection
