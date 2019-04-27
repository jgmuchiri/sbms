@extends('layout.template')
@section('title') @lang("Edit profile")
@endsection
@section('panel-title')
@lang("Update your account information")
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <img src="https://www.gravatar.com/avatar/{{md5($user->email)}}?s=150"/><br/>
            <a href="https://www.gravatar.com" target="_blank" class="label label-default">@lang("change")</a>
        </div>
        <div class="col-md-8">
            {!! Form::model($user,['url'=>'account/profile']) !!}
            <style scoped>
                .table td:first-child {
                    text-align: right;
                }
            </style>
            <table class="table table-striped no-border">
                <tr>
                    <td>@lang("Username"):</td>
                    <td>{!! Form::text('username',null,['required'=>'required']) !!}</td>
                </tr>
                <tr>
                    <td>@lang("First name"):</td>
                    <td>{{Form::text('first_name')}}</td>
                </tr>
                <tr>
                    <td>@lang("Last name"):</td>
                    <td>{{Form::text('last_name')}}</td>
                </tr>

                <tr>
                    <td>@lang("Email"):</td>
                    <td>{{Form::input('email','email')}}</td>
                </tr>

                <tr>
                    <td>@lang("Phone"):</td>
                    <td>{{Form::text('phone')}}</td>
                </tr>
                <tr>
                    <td>@lang("Address"):</td>
                    <td>{{Form::textarea('address',null,['rows'=>3])}}</td>
                </tr>

                <tr>
                    <td>@lang("Date of birth"):</td>
                    <td>{{Form::input('date','dob')}}</td>
                </tr>
                <tr>
                    <td>@lang("Password"):</td>
                    <td>
                        {!! Form::input('password','password') !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang("Confirm password"):</td>
                    <td> {!! Form::input('password','password_confirmation') !!}</td>
                </tr>
                <tr>
                    <td>@lang("About me"):</td>
                    <td>{!! Form::textarea('about',null,['rows'=>4]) !!}</td>
                </tr>

                <tr>
                    <td>TXN ID:</td>
                    <td>{{$user->stripe_id}}</td>
                </tr>
                <tr>
                    <td>@lang("Registered"):</td>
                    <td>{{$user->created_at}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        {{Form::submit('Update',['class'=>'btn btn-primary'])}}
                    </td>
                </tr>
            </table>
            {!! Form::close() !!}
        </div>
    </div>
@stop