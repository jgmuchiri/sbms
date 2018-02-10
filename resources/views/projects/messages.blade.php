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
                    <div class="panel-title"><i class="fa fa-envelope-o text-info"></i> @lang("Project messages")</div>         </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                       @lang("New message")
                    </div>
                </div>
                <div class="panel-body">
                    {!! Form::open(['url'=>'projects/create-message']) !!}
                    {!! Form::hidden('project_id',$project->id) !!}
                    {!! Form::textarea('message',null,['required'=>'required','placeholder'=>__("Enter your message"),'rows'=>3]) !!}
                    <button class="btn btn-primary btn-md">@lang("Send")</button>
                    {!! Form::close() !!}
                </div>
            </div>

            @foreach($messages as $msg)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <i class="fa fa-user"></i> {{\App\User::read($msg->user_id,['first_name','last_name'])}}
                            <span class="text-muted pull-right">{{date('d M, Y @ h:iA',strtotime($msg->created_at))}}</span>
                        </div>
                    </div>
                    <div class="panel-body">

                        {!! $msg->message !!}


                        <div id="easyPaginate_{{$msg->id}}" style="margin-left:20px;">
                            @foreach(\App\Models\Projects\ProjectMessages::replies($msg->id) as $reply)
                                <div class="reply"
                                     style="margin-top:10px;border-left: solid 1px #ccc;padding:5px;background:rgba(231, 231, 237, 0.42)">
                                    <a href="/projects/delete-message/{{$reply->id}}" class="delete pull-right"><i class="fa fa-trash-o text-warning"></i> </a>
                                    {!! $reply->message !!}
                                    <br/>


                                    <div class="text-muted pull-right">
                                        <i class="fa fa-user"></i> {{\App\User::read($reply->user_id,['first_name','last_name'])}}
                                        on {{date('d M, Y @ h:iA',strtotime($reply->created_at))}}

                                    </div>
                                    <br/>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-left:20px;margin-top:10px">
                            <button class="btn btn-info btn-xs" onclick="$(this).next('.reply-box').toggle('slow')"><i class="fa fa-reply"></i> reply</button>
                            {!! Form::open(['url'=>'projects/reply-message/'.$msg->id,'class="reply-box" style="display:none"']) !!}
                            {!! Form::hidden('project_id',$project->id) !!}
                            {!! Form::textarea('message',null,['required'=>'required','placeholder'=>__("Enter your response"),'rows'=>2]) !!}
                            <button class="btn btn-info btn-md">@lang("Send")</button>
                            {!! Form::close() !!}
                        </div>
                        <a href="/projects/delete-message/{{$msg->id}}" class="delete pull-right"><i class="fa fa-trash text-danger"></i> </a>
                    </div>
                </div>

            @endforeach
            {{$messages->links()}}
        </div>
    </div>
@endsection



@push('scripts')
    <script src="/js/jquery.paginate.js"></script>
    @foreach($messages as $msg)
        <script>
            $('#easyPaginate_' + "{{$msg->id}}").easyPaginate({
                paginateElement: '.reply',
                elementsPerPage: 5,
                effect: 'climb',
                nextButton: false,
                prevButton: false
            });
        </script>
    @endforeach
    <script>
        $('.easyPaginateNav').addClass('btn-group').css('margin-left', '20px');
        $('.easyPaginateNav>a').addClass('btn btn-info');
    </script>

@endpush