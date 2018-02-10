<div class="col-sm-3">
    <div class="panel panel-default">
        <div class="panel-body">
            <h4 style="color:#fe9700;font-size: 18px;">
                <i class="fa fa-circle"></i> {{$project->title}}</h4>
            <i class="fa fa-user"></i>
            <a href="#">{{\App\User::read($project->client,['first_name','last_name'])}}</a>
            <br/>
            <i class="fa fa-calendar"></i> @lang("Start"): <span class="text-muted">
                                    {{date('d M, Y',strtotime($project->p_start))}}</span> <br/>
            <i class="fa fa-calendar-o"></i> @lang("End"):
            <span class="text-muted"> {{date('d M, Y',strtotime($project->p_end))}}</span>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <ul class="nav nav-pills nav-stacked">
                <li><a href="/projects/{{$project->id}}/view"><i class="fa fa-home"></i> @lang("Home")</a></li>
                <li><a href="/projects/{{$project->id}}/milestones"><i class="fa fa-paper-plane-o"></i> @lang("Milestones")</a>
                </li>
                <li><a href="/projects/{{$project->id}}/tasks"><i class="fa fa-check-square-o"></i> @lang("Tasks")</a></li>
                <li><a href="/projects/{{$project->id}}/messages"><i class="fa fa-envelope-o"></i> @lang("Messages")</a></li>
                <li><a href="/projects/{{$project->id}}/files"><i class="fa fa-file-pdf-o"></i> @lang("Files")</a></li>
                <li><a href="/projects/{{$project->id}}/members"><i class="fa fa-users"></i> @lang("Members")</a></li>
                <li><a class="text-danger" href="/projects/{{$project->id}}/edit"><i class="fa fa-pencil-square-o"></i> @lang("Edit")</a></li>
            </ul>
        </div>
    </div>
</div>