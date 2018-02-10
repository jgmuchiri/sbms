@extends('auth.template')
@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><h4>@lang("Reset password")</h4></div>
                </div>

                <form method="POST" action="/password/reset">
                    <div class="panel-body">
                        {!! csrf_field() !!}
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div>
                            @lang("Email")
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        </div>

                        <div>
                            @lang("Password")
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div>
                            @lang("Confirm Password")
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div>
                            <br/>

                        </div>

                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-warning">
                            @lang("Reset Password")
                        </button>

                        <a href="/login" class="text-danger pull-right">@lang("Cancel")</a>
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection