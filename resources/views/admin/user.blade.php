@extends('layout.template')
@section('title')
    @lang("User")- {{$user->username}}
@endsection
@section('title-icon')@lang("User")
@endsection

@section('panel-title')
    <a href="/users" class="btn btn-default"><i class="fa fa-chevron-circle-left"></i> @lang("back") </a>
@endsection
@section('content')

    <div class="h3">Stripe ID: {{$user->stripe_id}}</div>
    <div class="row">
        <div class="col-md-6">
            {!! Form::model($user,['url'=>'users/'.$user->id]) !!}
            <table class="table table-striped">
                <tr>
                    <td>@lang("First name"):</td>
                    <td>{{Form::text('first_name',null,['required'=>'required'])}}</td>
                </tr>
                <tr>
                    <td>@lang("Last name"):</td>
                    <td>{{Form::text('last_name',null,['required'=>'required'])}}</td>
                </tr>
                <tr>
                    <td>@lang("Email"):</td>
                    <td>{{Form::input('email','email',null,['required'=>'required'])}}</td>
                </tr>
                <tr>
                    <td>@lang("Phone"):</td>
                    <td>{{Form::text('phone')}}</td>
                </tr>
                <tr>
                    <td>@lang("Company")</td>
                    <td>{!! Form::text('company',null) !!}</td>
                </tr>
                <tr>
                    <td>@lang("Address"):</td>
                    <td>{{Form::textarea('address',null,['rows'=>3])}}</td>
                </tr>

                <tr>
                    <td>@lang("Password") <em class="text-danger">(@lang("only if changing")</em></td>
                    <td>
                        {!! Form::label('password','Password') !!}
                        {!! Form::input('password','password') !!}

                        {!! Form::label('password_confirm','Confirm password') !!}
                        {{Form::input('password','password_confirmation') }}
                    </td>
                </tr>
                <tr>
                    <td>@lang("Registered on"):</td>
                    <td>{{$user->created_at}}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::submit('Update',['class'=>'btn btn-primary'])}}
                    </td>
                </tr>
            </table>
            {!! Form::close() !!}
        </div>
        <div class="col-md-6">
            <h3>@lang("Roles")</h3>
            {!! Form::open(['url'=>'users/'.$user->id.'/roles', 'files' => true]) !!}
            <?php
            function is_checked($user_id, $role)
            {
                $userRoles = DB::table('role_user')->whereUserId($user_id)->get();
                foreach ($userRoles as $ur) {
                    if ($ur->role_id == $role) return 'true';
                }
            }
            ?>
            @foreach($roles as $role)<br/>
            {{Form::radio('role',$role->id, is_checked($user->id,$role->id))}} {{$role->name}}<br/>
            @endforeach
            <br/>
            <button class="btn btn-default">@lang("Update")</button>
            {!! Form::close() !!}

        </div>
    </div>
@endsection