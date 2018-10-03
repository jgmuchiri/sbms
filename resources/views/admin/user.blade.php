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
<ul class="nav nav-tabs">
   <li class="active"><a data-toggle="tab" href="#home">User</a></li>
   <li><a data-toggle="tab" href="#roles">@lang('Roles')</a></li>
   <li><a data-toggle="tab" href="#permissions">@lang('Permissions')</a></li>
</ul>
<div class="tab-content">
   <div id="home" class="tab-pane fade in active">
      <div class="h3">Stripe ID: {{$user->stripe_id}}</div>
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
   <div id="roles" class="tab-pane fade">
      <h3>@lang("Role")</h3>
      {!! Form::open(['url'=>'users/'.$user->id.'/update-role']) !!}
      @foreach($roles as $role)
      {{Form::radio('role',$role->name,!empty($user->roles[0]) && $role->id==$user->roles[0]->id)}}
      {{$role->name}}<br/>
      @endforeach
      <br/>
      <button class="btn btn-default">@lang("Update")</button>
      {!! Form::close() !!}
      <br/>
      <h3>@lang("Permissions via role")</h3>
      <div class="row">
         @foreach($user->getPermissionsViaRoles() as $perm)
         <div class="col-xs-3">
            <a class="delete" href="{{url('role/'.$user->roles[0]->id.'/'.$perm->id.'/revoke')}}">
            <i class="fa fa-trash text-warning"></i>
            </a>
            {{$perm->name}}
         </div>
         @endforeach
      </div>
   </div>
   <div id="permissions" class="tab-pane fade">
      <h3>
         @lang("Direct permissions")
         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#permsModal">
         <i class="fa fa-plus"></i>
         </button>
      </h3>
      <strong>Note:</strong>
      <i>The user might still have permission via role.</i>
      <div class="row">
         @foreach($user->getDirectPermissions() as $perm)
         <div class="col-xs-4" style="border-bottom:solid 1px #ccc;">
            <i data-user="{{$user->id}}" data-perm="{{$perm->name}}" class="revoke-perm cursor fa fa-trash text-warning"></i>   {{ucwords($perm->name)}}
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection
@push('modals')
<div class="modal fade" id="permsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{__('Permissions')}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         {!! Form::open(['url'=>'users/'.$user->id.'/update-permissions']) !!}
         <div class="modal-body">
            <div class="row">
               @foreach($permissions as $perm)
               <div class="col-xs-6">
                  <input
                     value="{{$perm->name}}"
                     type="checkbox" name="permissions[]"> {{ucwords($perm->name)}}
               </div>
               @endforeach
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button class="btn btn-primary">Save changes</button>
         </div>
         {!! Form::close() !!}
      </div>
   </div>
</div>
@endpush
@push('scripts')
<script>
   $('.revoke-perm').click(function(){
       let token = $('meta[name="csrf-token"]').attr('content');
       let user_id = $(this).attr('data-user');
       let perm = $(this).attr('data-perm');
       console.log(perm);
          $.ajax({
              type: "POST",
              url: '/revoke-permission',
              data: {_token:token,user_id:user_id,perm_name:perm},
              // dataType: 'text',
              success: function(){
                  window.location.reload();
              },
              error: function( jqXhr, textStatus, errorThrown ){
                              console.log( errorThrown );
              }
          });
   })
</script>
@endpush