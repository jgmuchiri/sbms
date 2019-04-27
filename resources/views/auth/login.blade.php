@extends('auth.template')

@section('content')

    <div class="row">
        <div class="col-md-4 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><h4>@lang("Login") in</h4></div>
                </div>

                {!! Form::open(['url'=>'login','role'=>'form']) !!}
                <div class="panel-body">
                    <div class="form-group">
                        <label for="email">@lang("Email")</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="password">@lang("Password")</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="remember"> @lang("Remember Me")
                        </label>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-info">@lang("Sign in")</button>

                    <a href="#" data-toggle="modal" class="pull-right" data-target="#myModal">
                        @lang("Reset Password")</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection
@push('modals')
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:400px;margin:0 auto">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="title" id="myModalLabel">@lang("Reset password")</h4>
                @lang("Enter your email below and if you have an account, we will send a password reset link")
            </div>
            <div class="modal-body" style="padding:10px;">
                {!! Form::open(['url'=>'password/email']) !!}
                <input type="email" class="form-control" required="required" style="padding:18px;" name="email"
                       placeholder="Enter your email address"/>
                <br/>
                <button class="btn btn-primary">@lang("Request password reset")</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endpush