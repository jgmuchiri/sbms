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
                    <div class="panel-title"><i class="fa fa-users"></i> @lang("Project members")</div>
                </div>
                <div class="panel-body">

                </div>
            </div>
        </div>

    </div>
@endsection