@extends('layout.template')
@section('title')
    @lang("Users")
@endsection
@section('panel-title')

    <button type="button" class="btn btn-default btn-sm addUser" data-toggle="modal" data-target="#myModal">
        <i class="fa fa-plus-circle"></i> @lang("Add user")
    </button>

    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exportModal">
        <i class="fa fa-file-excel-o"></i>
        @lang("Export")
    </button>

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive table-striped" id="table">
                <thead>
                <tr>
                    <th></th>
                    <th>@lang("Username")</th>
                    <th>@lang("Name")</th>
                    <th>@lang("Email")</th>
                    <th>@lang("Phone")</th>
                    <th>@lang("Role")</th>
                    <th>@lang("Registered on")</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr class="cursor" onclick="window.location.href='/users/{{$user->id}}/view'">
                        <td>
                            <img src="https://www.gravatar.com/avatar/{{md5($user->email)}}?s=40"/><br/>
                        </td>
                        <td>{{$user->username}}</td>
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->phone}}</td>
                        <td>
                            @foreach($user->Roles() as $role)
                                {{ucwords($role)}}
                            @endforeach
                        </td>
                        <td>{{$user->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('modals')

<?php
$table = "$('#table').dataTable( {'pageLength': 50} );";
?>
@include('partials.datatables',['advanced'=>$table])

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Register user")</h4>
            </div>
            <div class="modal-body">
                {{Form::open(['url'=>'users/register','id'=>'payment-form'])}}

                <div class="row">
                    <div class="col-sm-6">
                        {{Form::text('first_name',null,['required'=>'required','placeholder'=>__("First name")])}}
                        <br/>
                        {{Form::text('last_name',null,['required'=>'required','placeholder'=>__("Last name")])}}
                        <br/>
                        {{Form::input('email','email',null,['required'=>'required','placeholder'=>__("Email")])}}
                        <br/>

                        {{Form::text('phone',null,['required'=>'required','placeholder'=>__("Phone")])}}
                        <br/>

                        {!! Form::text('company',null,['placeholder'=>'Company']) !!}
                        {!! Form::checkbox('notify-user',1) !!} @lang("Notify user?")
                    </div>
                    <div class="col-md-6">
                        {{Form::text('username',null,['required'=>'required','placeholder'=>__("Username")])}}
                        <br/>
                        {{Form::input('password','password',null,['required'=>'required','placeholder'=>__("Password")])}}
                        <br/>
                        {{Form::input('password','password_confirmation',null,['required'=>'required','placeholder'=>'Confirm password'])}}

                        <br/>
                        {!! Form::textarea('address',null,['rows'=>3,'placeholder'=>__("Address"),'required'=>'required']) !!}
                        <br/>

                        <div class="input-group">
                            <span class="input-group-addon">@lang("Role"):</span>
                            {!! Form::select('role',DB::table('roles')->pluck('name','name')) !!}
                        </div>
                        <br/>
                        <input type="submit" class="submit btn btn-success" value="Submit">
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>
@endpush
@push('modals')
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Export users")</h4>
            </div>
            {!! Form::open(['url'=>'users/export']) !!}
            <div class="modal-body">
                <div class="row">
                    @foreach(Schema::getColumnListing('users') as $col)
                        @if(
                        $col == 'id'
                        || $col == 'first_name'
                        || $col == 'last_name'
                        || $col == 'username'
                        || $col == 'email'
                        || $col == 'phone'
                        || $col == 'dob'
                        || $col == 'company')
                            <div class="col-sm-4">
                                {!! Form::checkbox('col[]',$col,true,['style'=>'width:20px;height:20px']) !!} {{ucwords($col)}}
                            </div>
                        @else
                        @endif

                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                <button class="btn btn-primary">@lang("Export")</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endpush