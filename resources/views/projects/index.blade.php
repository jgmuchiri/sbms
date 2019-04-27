@extends('layout.template')
@section('title')
    @lang("Projects")
@endsection
@section('panel-title')
    <div class="h4">@lang("Projects")</div>
@endsection
@section('container')
    <style>
        .nav-stacked > li > a {
            border-radius: 0;
        }

        .nav-stacked > li {
            border-bottom: solid 1px #c7c7cc;
        }

        .nav-stacked > li > a:hover {
            border-left: solid 1px #ff9460;
        }

    </style>
    <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked" style="padding:0;">
                <li><a class="bg-info" data-toggle="modal" data-target="#newProjectModal" href="#"><i
                                class="fa fa-plus text-warning"></i> @lang("New")</a></li>
                <li><a class="" href="?status=due"><i class="fa fa-hourglass-1 text-warning"></i> @lang("Due")</a></li>
                <li><a class="" href="?status=behind"><i class="fa fa-hourglass-o text-danger"></i> @lang("Behind")</a>
                </li>
                <li><a class="" href="?status=completed"><i
                                class="fa fa-check-square text-success"></i> @lang("Completed")</a>
                </li>
                <li><a class="" href="?status=cancelled"><i class="fa fa-stop text-primary"></i> @lang("Cancelled")</a>
                </li>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="row">
                <div class="col-xs-6 col-md-3 col-lg-3">
                    <div class="panel panel-warning text-center">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4 class="fa-2x">
                                    {{count($projects->where('status','!=','completed'))}}
                                </h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            @lang("Pending")
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 col-lg-3">
                    <div class="panel panel-success text-center">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4 class="fa-2x">
                                    {{count($projects->where('status','completed'))}}
                                </h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            @lang("Completed")
                        </div>
                    </div>
                </div>
            </div>

            <div id="easyPaginate">
                @foreach($projects as $project)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4><i class="fa fa-circle-o text-primary"></i>
                                    <a style="color:#fe9700;font-size: 24px;"
                                       href="/projects/{{$project->id}}/view">{{$project->title}}</a>
                                </h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <i class="fa fa-user"></i> @lang("Client"): <a
                                            href="#">{{\App\User::read($project->client,['first_name','last_name'])}}</a><br/>
                                    <i class="fa fa-calendar"></i> @lang("Start"): <span class="text-muted">
                                    {{date('d M, Y',strtotime($project->p_start))}}</span> <br/>
                                    <i class="fa fa-calendar-o"></i> @lang("End"):
                                    <span class="text-muted"> {{date('d M, Y',strtotime($project->p_end))}}</span><br/>

                                    <i class="fa fa-envelope-o"></i> @lang("Messages"):
                                    <span class="label label-info"> 1</span>
                                </div>
                                <div class="col-sm-3">
                                    <strong><i class="fa fa-paper-plane-o"></i>
                                        @lang("Milestones")</strong><br/>
                                    <span class="label label-danger">
                                    {!! \App\Models\Projects\ProjectMilestones::countByStatus($project->id,'pending') !!}
                                </span>
                                    @lang("Pending")<br/>
                                    <span class="label label-warning">
                                    {!! \App\Models\Projects\ProjectMilestones::countByStatus($project->id,'behind') !!}
                                </span>
                                    @lang("Behind")<br/>
                                    <span class="label label-success">
                                    {!! \App\Models\Projects\ProjectMilestones::countByStatus($project->id,'completed') !!}
                                </span>
                                    @lang("Completed")<br/>
                                </div>
                                <div class="col-sm-3">
                                    <strong><i class="fa fa-check-square-o"></i> @lang("Tasks")</strong><br/>
                                    <span class="label label-danger">
                                    {!! \App\Models\Projects\ProjectTasks::countByStatus($project->id,'pending') !!}
                                </span>
                                    @lang("Pending")<br/>
                                    <span class="label label-warning">
                                    {!! \App\Models\Projects\ProjectTasks::countByStatus($project->id,'behind') !!}
                                </span>
                                    @lang("Behind")<br/>
                                    <span class="label label-success">
                                    {!! \App\Models\Projects\ProjectTasks::countByStatus($project->id,'completed') !!}
                                </span>
                                    @lang("Completed")<br/>
                                </div>
                                <div class="col-sm-3">
                                    <strong><i class="fa fa-money"></i> @lang("Invoices")</strong><br/>
                                    <span class="label label-danger">
                                  0
                                </span>
                                    @lang("Overdue")<br/>
                                    <span class="label label-warning">
                                    0
                                </span>
                                    @lang("Due")<br/>
                                    <span class="label label-success">
                                    0
                                </span>
                                    @lang("Paid")<br/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('modals')
<script src="/js/jquery.paginate.js"></script>
<script>
    $('#easyPaginate').easyPaginate({
        paginateElement: '.panel',
        elementsPerPage: 15,
        effect: 'climb'
    });
    $('.easyPaginateNav').addClass('btn-group');
    $('.easyPaginateNav>a').addClass('btn btn-info');
</script>

<div class="modal fade" id="newProjectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("New project")</h4>
            </div>
            {!! Form::open(['url'=>'projects']) !!}
            <div class="modal-body">
                <label>@lang("Title")</label>
                {!! Form::text('title',null,['required'=>'required']) !!}
                <div class="row">
                    <div class="col-sm-6">
                        <label>@lang("Start")</label>
                        {!! Form::input('date','p_start',date('Y-m-d'),['required'=>'required']) !!}
                    </div>
                    <div class="col-sm-6">
                        <label>@lang("End")</label>
                        {!! Form::input('date','p_end',date('Y-m-d'),['required'=>'required']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <br/>
                        <label>@lang("Client")</label>
                        {!! Form::select('client',App\User::pluckAble()->pluck('name','id'),null,['class'=>'users']) !!}
                    </div>
                    <div class="col-sm-6">
                        <label>@lang("Status")</label>
                        {!! Form::select('p_status',['due'=>__("Due"),'completed'=>__("Completed"),'cancelled'=>__("Cancelled")]) !!}
                    </div>
                </div>
                <label>@lang("Details")</label>
                {!! Form::textarea('details',null,['rows'=>3]) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                <button class="btn btn-primary">@lang("Save") </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endpush
@include('partials.select2',['select2'=>'.users'])