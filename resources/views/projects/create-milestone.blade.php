@if(isset($mms) && count($mms))
    {!! Form::model($mms,['url'=>'projects/update-milestone/'.$mms->id]) !!}
@else
    {!! Form::open(['url'=>'/projects/'.$project->id.'/create-milestone']) !!}
@endif
<div class="modal-body">
    <label>@lang("Title")</label>
    {!! Form::text('name',null,['required'=>'required','class'=>'form-control']) !!}
    <div class="row">
        <div class="col-md-6">
            <label>@lang("Start"):</label>
            {!! Form::input('date','m_start',null,['required'=>'required','class'=>'form-control']) !!}
        </div>
        <div class="col-md-6">
            <label>@lang("End"):</label>
            {!! Form::input('date','m_end',null,['required'=>'required','class'=>'form-control']) !!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label>@lang("Status"):</label>
            {!! Form::select('m_status',['scheduled'=>__("Scheduled"),'in-progress'=>__("In-progress"),'completed'=>__("Completed")],null,['class'=>'form-control']) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
    <button class="btn btn-primary">@lang("Save")</button>
</div>
{!! Form::close() !!}