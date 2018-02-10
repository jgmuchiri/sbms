{!! Form::model($contact,['url'=>'contacts/'.$contact->id.'/update','files'=>true]) !!}

<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                  data-toggle="tab">@lang('Home')</a></li>
        <li role="presentation"><a href="#groups" aria-controls="groups" role="tab" data-toggle="tab">@lang("Groups")</a></li>
        <li role="presentation"><a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">@lang("Notes")</a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="home">
            <div class="row">
                <div class="col-md-6">

                    <label>@lang("First name")</label>
                    {!! Form::text('first_name',null,['class'=>'form-control']) !!}

                    <label>@lang("Last name")</label>
                    {!! Form::text('last_name',null,['class'=>'form-control']) !!}
                </div>

                <div class="col-md-6">
                    <label>@lang("Photo")</label>
                    {!! Form::file('photo',['class'=>'form-control']) !!}

                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>@lang("Email")</label>
                    {!! Form::input('email','email',null,['class'=>'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <label>@lang("Cellphone")</label>
                    {!! Form::text('cell',null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>@lang("Phone")</label>
                    {!! Form::text('phone',null,['class'=>'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <label>@lang("Fax")</label>
                    {!! Form::text('fax',null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>@lang("Company")</label>
                    {!! Form::text('company',null,['class'=>'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <label>@lang("Job title")</label>
                    {!! Form::text('job_title',null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>@lang("Department")</label>
                    {!! Form::text('dept',null,['class'=>'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <label>@lang("Website")</label>
                    {!! Form::text('website',null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>@lang("Address")</label>
                    {!! Form::textarea('address',null,['rows'=>3,'class'=>'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <label>@lang("Date of birth")</label>
                    {!! Form::input('date','dob',null,['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="groups">
            <label>@lang("Contact troup")</label>
            <table class="no-border">

                @foreach($groups->get() as $gp)
                    <tr>
                        <td><input type="checkbox" style="width:16px;height:16px;" name="group_id[]"
                                   {{(in_array($gp->id, $cGroups))?'checked':''}}
                                   value="{{$gp->id}}">
                        </td>
                        <td>
                            {{$gp->group_name}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="notes">
            <label>@lang('Notes')</label>
            {!! Form::textarea('notes',null,['rows'=>3,'class'=>'form-control']) !!}
        </div>
    </div>

    <div class="row">
        {{--<div class="col-md-6">--}}
            {{--<label>Social Networks</label>--}}
            {{--{!! Form::text('social',null,['placeholder'=>"'name1':'url1','name2':'url2'"]) !!}--}}
            {{--(e.g. 'twitter':'http://twitter.com')--}}
        {{--</div>--}}
        {{--<div class="col-md-6">--}}
            {{--<label>Instant Messengers</label>--}}
            {{--{!! Form::text('im',null,['placeholder'=>"'name1':'handle1','name2':'handle2'"]) !!}--}}
            {{--(e.g. 'skype':'amdtllc')--}}
        {{--</div>--}}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
    <button class="btn btn-primary">@lang("Save")</button>
</div>
{!! Form::close() !!}