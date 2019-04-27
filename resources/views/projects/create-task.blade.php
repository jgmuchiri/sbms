@if(isset($myTask) && count($myTask))
    {!! Form::model($myTask,['url'=>'projects/update-task']) !!}
    {!! Form::hidden('task_id',$myTask->id) !!}
@else
    {!! Form::open(['url'=>'projects/create-task']) !!}
    {!! Form::hidden('project_id',$project->id) !!}
@endif
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <label>@lang("Name")</label>
            {!! Form::text('task_name',null,['required'=>'required','class'=>'form-control']) !!}
        </div>
        <div class="col-md-6">
            <label>@lang("Milestone")</label>
            {!! Form::select('milestone_id',$project->milestones->pluck('name','id'),null,['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label>@lang("Start"):</label>
            {!! Form::input('date','t_start',date('Y-m-d'),['required'=>'required','class'=>'form-control']) !!}
        </div>
        <div class="col-md-6">
            <label>@lang("End"):</label>
            {!! Form::input('date','t_end',date('Y-m-d'),['required'=>'required','class'=>'form-control']) !!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label>@lang("Status"):</label>
            {!! Form::select('t_status',['scheduled'=>__("Scheduled"),'in-progress'=>__("In-progress"),'completed'=>__("Completed")],null,['class'=>'form-control']) !!}
        </div>
        <div class="col-md-6">
            <label>@lang("Assign to")</label>
            {!! Form::select('assigned_to',App\User::pluck('first_name','id'),null,['class'=>'form-control']) !!}
        </div>
    </div>
    <label>@lang("Notes")</label>
    {!! Form::textarea('desc',null,['rows'=>3,'class'=>'form-control']) !!}
    <div class="row">
        <div class="col-md-6">
            <label>@lang("Estimated Hours")</label>
            {!! Form::input('text','est_hours',0,['class'=>'form-control']) !!}
            <label>@lang("Estimated Cost") ($)</label>
            {!! Form::text('est_cost',"0.00",['class'=>'form-control']) !!}
        </div>
        <div class="col-md-6">
            <label>@lang("Actual hours")</label>
            {!! Form::input('text','actual_hours',"0.00",['class'=>'form-control']) !!}
            <label>@lang("Actual cost") ($)</label>
            {!! Form::text('actual_cost',"0.00",['class'=>'form-control']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
    <button class="btn btn-primary">@lang("Save")</button>
</div>
{!! Form::close() !!}