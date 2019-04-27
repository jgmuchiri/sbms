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

                    @if(count($milestone))
                        <div class="panel-title text-warning"><i class="fa fa-paper-plane-o"></i> @lang("Milestone"):
                            <strong> {{$milestone->name}}</strong> {!! \App\Models\Projects\ProjectMilestones::statusHtml($milestone->m_status) !!}

                            <button data-toggle="modal" data-target="#newTaskModal"
                                    class="btn btn-default btn-sm pull-right">
                                <i class="fa fa-plus"></i> @lang("New task")
                            </button>
                        </div>
                    @else
                        <div class="panel-title"><i class="fa fa-list text-info"></i>
                            @lang("Project tasks")
                            <button data-toggle="modal" data-target="#newTaskModal"
                                    class="btn btn-default btn-sm pull-right">
                                <i class="fa fa-plus"></i> @lang("New task")
                            </button>
                        </div>
                    @endif


                </div>

                <div class="panel-body">
                    @if(count($milestone))
                        <i class="fa fa-calendar"></i> @lang("Start"):
                        <span class="text-muted">{{date('d M, Y',strtotime($milestone->m_start))}}</span>
                        |
                        <i class="fa fa-calendar-o"></i> @lang("End"):
                        <span class="text-muted">{{date('d M, Y',strtotime($milestone->m_end))}}</span>
                        |
                        <i class="fa fa-calendar"></i> @lang("Created"):
                        <span class="text-muted">{{date('d M, Y',strtotime($milestone->created_at))}}</span>

                        <hr/>
                    @endif
                    <h4 class="text-danger">@lang("Tasks")</h4>
                </div>
                @foreach($tasks as $task)

                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-sm-1">
                                @if($task->t_status =='completed')
                                    <span class="cursor" id="{{$task->id}}"><i
                                                class="fa fa-check-square-o fa-2x"></i></span>
                                @else
                                    <span class="completeTask cursor" id="{{$task->id}}"><i
                                                class="fa fa-square-o fa-2x"></i></span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                {{$task->task_name}}
                                <br/>
                                <span class="text-muted">{{$task->desc}}</span>
                                <br/>
                                {!! \App\Models\Projects\ProjectMilestones::statusHtml($task->t_status) !!}
                            </div>
                            <div class="col-sm-3">
                                @lang("Start"): {{date('d M, Y',strtotime($task->t_start))}}<br/>
                                @lang("End"): {{date('d M, Y',strtotime($task->t_end))}}<br/>
                                @lang("Assigned to"): {{\App\User::read($task->assigned_to,['first_name','last_name'])}}<br/>
                                @if($task->actual_hours !==null)
                                    @lang("Timer"): <span class="badge bg-info">{{$task->actual_hours}} @lang("hours")</span>
                                @endif
                            </div>
                            <div class="col-sm-2">
                                <button
                                        title="Edit Task"
                                        id="{{$task->id}}" class="btn btn-default btn-xs edit-task-btn"><i
                                            class="fa fa-pencil"></i>
                                </button>

                                @if(\App\Models\Projects\ProjectTasks::isPaid($task->id,$task->t_status) ==false)
                                    <a data-toggle="tooltip"
                                       title="Pay Task"
                                       href="/projects/pay-task/{{$task->id}}"
                                       class="btn btn-info btn-xs"><i
                                                class="fa fa-shopping-cart"></i></a>
                                @else
                                    <span data-toggle="tooltip" title="Paid"
                                            class="btn btn-success btn-xs"><i class="fa fa-shopping-cart"></i> </span>
                                @endif

                                <a data-toggle="tooltip"
                                   title="delete"
                                   href="/projects/delete-task/{{$task->id}}" class="delete btn btn-danger btn-xs"><i
                                            class="fa fa-trash"></i></a>
                            </div>


                        </div>

                    </div>
                @endforeach


            </div>
        </div>

    </div>
@endsection

@push('modals')

    <div class="modal fade" id="newTaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("New task")</h4>
                </div>
                @if(count($project) && count($milestone))
                    @include('projects.create-task',['project'=>$project,'milestone'=>$milestone])
                @else
                    @include('projects.create-task',['project'=>$project])
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("Update task")</h4>
                </div>
                <div class="edit-task-modal"></div>
            </div>
        </div>
    </div>
@endpush
@push('scripts')
    <script>
        $('.completeTask').popover({
            html: true,
            content: function () {
                return $('#completeTask').html();
            }
        }).click(function () {
            var id = $(this).attr('id');
            $('<input>').attr({
                type: 'hidden',
                id: 'task_id',
                name: 'task_id',
                value: id
            }).appendTo('#taskQForm');

        });
        $('.edit-task-btn').click(function () {
            var id = $(this).attr('id');
            $('.edit-task-modal').load('/projects/edit-task/' + id);
            $('#editTaskModal').modal('show');
        })
    </script>
    <style>
        .popover {
            width: 200px;
        }
    </style>
    <div id="completeTask" style="display: none;">
        {!! Form::open(['url'=>'projects/update-task-status','id'=>'taskQForm']) !!}
        <div id="h"></div>
        <label>@lang("Status"):</label>
        {!! Form::select('t_status',['scheduled'=>__("Scheduled"),'in-progress'=>__("In-progress"),'completed'=>__("Completed")],null,['class'=>'form-control']) !!}
        <button class="btn btn-default btn-xs">@lang("Update")</button>
        {!! Form::close() !!}
    </div>
@endpush