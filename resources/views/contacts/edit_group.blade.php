{!! Form::model($group,['url'=>'contacts/groups/'.$group->id.'/update']) !!}
<div class="modal-body">
    <label>@lang("Name")</label><br/>
    {!! Form::text('group_name',null,['required'=>'required']) !!}
    <br/>
    <label>@lang("Description")</label><br/>
    {!! Form::textarea('desc',null,['rows'=>3]) !!}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
    <button class="btn btn-primary">@lang("Save")</button>
</div>
{!! Form::close() !!}
