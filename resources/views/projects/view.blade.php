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
                    <div class="panel-title">
                        <h4 style="color:#fe9700;font-size: 24px;">
                            <i class="fa fa-circle"></i> {{$project->title}}

                            <button class="btn btn-default btn-sm pull-right"
                                    data-toggle="modal" data-target="#newMsModal"><i
                                        class="fa fa-plus"></i> @lang("New milestone") </button>
                        </h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-muted">
                                {{$project->details}}
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-3 cursor"
                     onclick="window.location.href='/projects/{{$project->id}}/milestones'">
                    <div class="panel panel-blue panel-widget ">
                        <div class="row no-padding">
                            <div class="col-sm-3 col-lg-5 widget-left">
                                <svg class="glyph stroked hourglass">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#stroked-hourglass"></use>
                                </svg>
                            </div>
                            <div class="col-sm-9 col-lg-7 widget-right">
                                <div class="large"> {{\App\Models\Projects\ProjectMilestones::whereProjectId($project->id)->count()}}</div>
                                <div class="text-muted">@lang("Milestones")</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-3 cursor"
                     onclick="window.location.href='/projects/{{$project->id}}/files'">
                    <div class="panel panel-blue panel-widget ">
                        <div class="row no-padding">
                            <div class="col-sm-3 col-lg-5 widget-left">
                                <svg class="glyph stroked landscape">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#stroked-landscape"></use>
                                </svg>
                            </div>
                            <div class="col-sm-9 col-lg-7 widget-right">
                                <div class="large"> {{\App\Models\Projects\ProjectFiles::whereProjectId($project->id)->count()}}</div>
                                <div class="text-muted">@lang("Files")</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-3 cursor"
                     onclick="window.location.href='/projects/{{$project->id}}/messages'">
                    <div class="panel panel-blue panel-widget ">
                        <div class="row no-padding">
                            <div class="col-sm-3 col-lg-5 widget-left">
                                <svg class="glyph stroked email">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-email"></use>
                                </svg>
                            </div>
                            <div class="col-sm-9 col-lg-7 widget-right">
                                <div class="large">{{\App\Models\Projects\ProjectMessages::whereProjectId($project->id)->count()}}</div>
                                <div class="text-muted">@lang("Messages")</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-3 cursor"
                     onclick="window.location.href='/projects/{{$project->id}}/members'">
                    <div class="panel panel-blue panel-widget ">
                        <div class="row no-padding">
                            <div class="col-sm-3 col-lg-5 widget-left">
                                <svg class="glyph stroked male-user">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#stroked-male-user"></use>
                                </svg>
                            </div>
                            <div class="col-sm-9 col-lg-7 widget-right">
                                <div class="large">  {{\App\Models\Projects\ProjectMembers::whereProjectId($project->id)->count()}}</div>
                                <div class="text-muted">@lang("Members")</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('modals')
<div class="modal fade" id="newMsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("New milestone")</h4>
            </div>
            @include('projects.create-milestone')
        </div>
    </div>
</div>
@endpush