@extends('layout.template')
@section('title')
    @lang("Projects")
@endsection
@section('container')
    <div class="row">
        @include('projects.project-nav',['project'=>$project])

        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><i class="fa fa-tasks"></i> {{$project->title}}</div>
                </div>
                <div class="panel-body">

                    {!! Form::model($project,['url'=>'projects/'.$project->id.'/update']) !!}

                    <label>@lang("Project title")</label>
                    {!! Form::text('title',null,['required'=>'required']) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <label>@lang("Start")</label>
                            {!! Form::input('date','p_start',null,['required'=>'required']) !!}
                        </div>
                        <div class="col-sm-6">
                            <label>@lang("End")</label>
                            {!! Form::input('date','p_end',null,['required'=>'required']) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>@lang("Client")</label>
                            {!! Form::select('client',App\User::pluck('first_name','id')) !!}
                        </div>
                        <div class="col-sm-6">
                            <label>@lang("Status")</label>
                            {!! Form::select('p_status',['due'=>__("Due"),'completed'=>__("Completed"),'cancelled'=>__("Cancelled")]) !!}
                        </div>
                    </div>
                    <label>@lang("Details")</label>
                    {!! Form::textarea('details',null,['rows'=>3]) !!}
                    <br/>
                    <button class="btn btn-primary">@lang("Save")</button>
                    {!! Form::close() !!}
                </div>

                <div class="panel-footer">
                    <a href="/projects/{{$project->id}}/delete" class="btn delete btn-danger btn-sm">
                        <i class="fa fa-trash"></i> @lang("Delete")
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection
