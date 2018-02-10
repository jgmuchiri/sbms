@extends('layout.template')
@section('title')
    <a href="#">@lang("Contacts")</a>
@endsection
@section('container')
    <div class="row">
        <div class="col-sm-8">
            <form role="form" method="get">
                <div class="form-group contact-search m-b-30">
                    <input type="text" name="s" id="search" class="form-control" placeholder="Search...">
                    <button type="submit" class="btn btn-white"><i class="fa fa-search"></i></button>
                </div>
                <!-- form-group -->
            </form>
        </div>
        <div class="col-sm-4">
            <a href="#" class="btn btn-info btn-md" data-toggle="modal" data-target="#newContactModal"><i
                        class="fa fa-plus"></i> @lang("Add contact")</a>
            <a href="#" class="btn btn-primary btn-md showGroups"><i
                        class="fa fa-group"></i> @lang("Manage contact groups")</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 contact-group-nav">
            <ul class="nav nav-pills nav-stacked">
                @foreach($groups->get() as $group)
                    <li class="small"><a href="/contacts/group/{{$group->id}}/view">{{$group->group_name}}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-10">
            @if(count($contacts)==0)
                <div class="alert alert-danger">
                    <h3><i class="fa fa-exclamation-triangle text-danger"></i> @lang("No records found")</h3>
                </div>
            @endif
            <div class="row">
                @foreach($contacts as $contact)
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="card-box">
                            <div class="contact-card">
                                <a class="pull-left" href="#">
                                    @if($contact->photo !==null)
                                        <img class="img-circle" src="/uploads/contacts/{{$contact->photo}}" alt="">
                                    @else
                                        <img class="img-circle" src="/img/no-photo.png" alt="">
                                    @endif
                                </a>

                                <div class="member-info">
                                    <h4 class="m-t-0 m-b-5 header-title"><b>{{$contact->first_name.' '.$contact->last_name}}</b>
                                    </h4>

                                    <p class="text-muted">
                                        @if(!empty($contact->dept))
                                            {{$contact->dept}}/
                                        @endif
                                        @if(!empty($contact->job_title))
                                            {{$contact->job_title}}
                                        @endif
                                    </p>

                                    @if(!empty($contact->company))
                                        <p class="text-dark"><i class="fa fa-building-o"></i>
                                            <small>{{$contact->company}}</small>
                                        </p>
                                    @endif
                                    @if(!empty($contact->phone))
                                        <p class="text-dark">
                                            <i class="fa fa-phone"></i>
                                            {{$contact->phone}}
                                        </p>
                                    @endif
                                    @if(!empty($contact->cell))
                                        <p class="text-dark">
                                            <i class="fa fa-mobile-phone"></i>
                                            {{$contact->cell}}
                                        </p>
                                    @endif
                                    @if(!empty($contact->email))
                                        <p class="text-dark">
                                            <i class="fa fa-envelope-o"></i>
                                            {{$contact->email}}
                                        </p>
                                    @endif
                                    @permission('update-contacts')
                                    <div class="contact-action">
                                        <a href="#" class="btn btn-success btn-sm editContact" id="{{$contact->id}}"><i
                                                    class="fa fa-pencil"></i></a>
                                        @endpermission
                                        @permission('delete-contacts')
                                        <a href="/contacts/{{$contact->id}}/delete" class="delete btn btn-danger btn-sm"><i
                                                    class="fa fa-times"></i></a>
                                        @endpermission
                                    </div>
                                </div>

                                <!--ul class="social-links list-inline m-0">
                                    <li>
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href=""
                                           data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li>
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href=""
                                           data-original-title="Twitter"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li>
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href=""
                                           data-original-title="LinkedIn"><i class="fa fa-linkedin"></i></a>
                                    </li>
                                    <li>
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href=""
                                           data-original-title="Skype"><i class="fa fa-skype"></i></a>
                                    </li>
                                    <li>
                                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href=""
                                           data-original-title="Message"><i class="fa fa-envelope-o"></i></a>
                                    </li>
                                </ul-->
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
@push('modals')
<div class="modal fade" id="newContactModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus-circle"></i>
                    <span id="modal-title"> @lang("New Contact")</span> </h4>

            </div>

            <div id="contact-info">
                {!! Form::open(['url'=>'contacts','files'=>true]) !!}
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#home" aria-controls="home" role="tab"
                               data-toggle="tab">@lang("Home")</a></li>
                        <li role="presentation">
                            <a href="#groups" aria-controls="groups" role="tab" data-toggle="tab">@lang('Groups')</a>
                        </li>
                        <li role="presentation">
                            <a href="#notes" aria-controls="notes" role="tab"
                               data-toggle="tab">@lang("Notes")</a></li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="home">
                            <div class="row">
                                <div class="col-md-6">

                                    <label>@lang("First name")</label>
                                    {!! Form::text('first_name',null,['required'=>'required']) !!}

                                    <label>@lang("Last name")</label>
                                    {!! Form::text('last_name',null,['required'=>'required']) !!}
                                </div>

                                <div class="col-md-6">
                                    <label>@lang("Photo")</label>
                                    {!! Form::file('photo') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang("Email")</label>
                                    {!! Form::input('email','email',null,['required'=>'required']) !!}
                                </div>
                                <div class="col-md-6">
                                    <label>@lang("Cellphone")</label>
                                    {!! Form::text('cell') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang("Phone")</label>
                                    {!! Form::text('phone') !!}
                                </div>
                                <div class="col-md-6">
                                    <label>@lang("Fax")</label>
                                    {!! Form::text('fax') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang("Company")</label>
                                    {!! Form::text('company') !!}
                                </div>
                                <div class="col-md-6">
                                    <label>@lang("Job title")</label>
                                    {!! Form::text('job_title') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang("Department")</label>
                                    {!! Form::text('dept') !!}
                                </div>
                                <div class="col-md-6">
                                    <label>@lang("Website")</label>
                                    {!! Form::text('website') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>@lang("Address")</label>
                                    {!! Form::textarea('address',null,['rows'=>3]) !!}
                                </div>
                                <div class="col-md-6">
                                    <label>@lang("Date of birth")</label>
                                    {!! Form::input('date','dob',date('Y-m-d')) !!}
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="groups">
                            <label>@lang("Contact group")</label>
                            <table class="no-border">
                                @foreach($groups->get() as $gp)
                                    <tr>
                                        <td><input type="checkbox" style="width:16px" name="group_id[]"
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
                            <label>@lang("Notes")</label>
                            {!! Form::textarea('notes',null,['rows'=>3]) !!}
                        </div>
                    </div>


                    {{--<div class="row">--}}
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
                    {{--</div>--}}



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                    <button class="btn btn-primary">@lang("Save")</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="groupsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Contact groups")</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6" id="cGroups">
                        <table class="table table-responsive">
                            @foreach($groups->get() as $cg)
                                <tr>
                                    <td><a class="editGroup" href="#" id="{{$cg->id}}">{{$cg->group_name}}</a></td>
                                    <td>{{$cg->desc}}</td>
                                </tr>
                            @endforeach
                        </table>

                    </div>
                    <div class="col-md-6">
                        {!! Form::open(['url'=>'contacts/groups']) !!}
                        <label>@lang("Name")</label>
                        {!! Form::text('group_name',null,['required'=>'required']) !!}
                        <label>@lang("Description")</label>
                        {!! Form::textarea('desc',null,['rows'=>3]) !!}
                        <button class="btn btn-primary">@lang("Save")</button>
                        {!! Form::close() !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="groupsEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Edit group")</h4>
            </div>
            <div id="e-g"></div>
        </div>
    </div>
</div>
@endpush
@push('scripts')
<script>
    $('document').ready(function () {
        $('.showGroups').click(function () {
            $('#groupsModal').modal('show');
        });

        $('.editGroup').click(function () {
            var gid = $(this).attr('id');
            $('#e-g').load('/contacts/groups/' + gid + '/edit',function () {
                $('#groupsEditModal').modal('show');
            });

        });
        $('.editContact').click(function () {
            var gid = $(this).attr('id');
            $('#newContactModal').find('#contact-info').load('/contacts/' + gid + '/edit');
            $('#newContactModal').find('#modal-title').text('Edit');
            $('#newContactModal').modal('show');
        });
    })
</script>
@endpush