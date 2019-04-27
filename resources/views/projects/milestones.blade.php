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
                    <div class="panel-title"><i class="fa fa-paper-plane-o"></i> @lang("Project milestones")

                        <button class="btn btn-default btn-sm pull-right"
                                data-toggle="modal" data-target="#newMsModal">
                            <i class="fa fa-plus"></i> @lang("New milestone")
                        </button>
                    </div>
                </div>
            </div>

            @foreach($milestones as $ms)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title"><i class="fa fa-th"></i>
                            {{$ms->name}}

                            {!! \App\Models\Projects\ProjectMilestones::statusHtml($ms->m_status) !!}
                            <button id="{{$ms->id}}" class="btn btn-info btn-xs pull-right edit-ms-btn"><i class="fa fa-pencil"></i></button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <i class="fa fa-calendar"></i> @lang("Start"):
                                <span class="text-muted">{{date('d M, Y',strtotime($ms->m_start))}}</span>
                                <br/>
                                <i class="fa fa-calendar-o"></i> @lang("End"):
                                <span class="text-muted">{{date('d M, Y',strtotime($ms->m_end))}}</span>
                                <br/>
                                </div>


                            <div class="col-md-4">
                                <span class="label label-default">{{\App\Models\Projects\ProjectTasks::whereMilestoneId($ms->id)->count()}}</span>
                                @lang("Tasks")
                                <br/>
                            </div>

                            <div class="col-md-4">
                                <a class="btn btn-info btn-xs"
                                   href="/projects/{{$project->id}}/milestone/{{$ms->id}}/tasks">
                                    <i class="fa fa-check-square-o"></i> @lang("Tasks")</a>
                                <br/>
                                <br/>
                                <a class="delete btn btn-danger btn-xs"
                                   href="/projects/delete-milestone/{{$ms->id}}">
                                    <i class="fa fa-trash-o"></i> @lang("delete")</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
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
    <div class="modal fade" id="editMsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("Update milestone")</h4>
                </div>
                <div class="edit-ms"></div>
            </div>
        </div>
    </div>
    <script>
        $('.edit-ms-btn').click(function(){
            var mid = $(this).attr('id');
            $('.edit-ms').load('/projects/edit-milestone/'+mid);
            $('#editMsModal').modal('show');
        })
    </script>
@endpush
