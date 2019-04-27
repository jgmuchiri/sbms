@extends('layout.template')
@section('title')
    @lang("Checks")
@endsection
@section('panel-title')
    <div class="row">
        <div class="col-sm-4">
            <button class="btn btn-warning" data-toggle="modal" data-target="#newCheckModal">
                <i class="fa fa-plus"></i>
                @lang("New Check")
            </button>
            <a href="/expenses" class="btn btn-default"><i class="fa fa-dollar"></i> @lang("Expenses")</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-striped table-striped" id="table">

                <thead>
                <tr>
                    <th>@lang("Status")</th>
                    <th>@lang("Check") #</th>
                    <th>@lang("Payee")</th>
                    <th>@lang("Amount")</th>
                    <th>@lang("Date")</th>
                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>

                @foreach($checks as $check)
                    <tr>
                        <td class="responsive">{{$check->status}}</td>
                        <td class="responsive"><a href="/checks/{{$check->id}}/view">{{$check->check_no}}</a></td>
                        <td class="responsive">{{$check->payee_name}}</td>
                        <td class="responsive">{{'$'.$check->amount}}</td>
                        <td class="responsive">{{date('d-M-y',strtotime($check->created_at))}}</td>

                        <td class="responsive">

                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown">
                                    <i class="fa fa-cog"></i> @lang("Action") <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="/checks/{{$check->id}}/view">
                                            <i class="fa fa-external-link-square"></i>
                                            @lang("View")
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/checks/{{$check->id}}/status/sent">
                                            <i class="fa fa-check-square-o"></i>
                                            @lang("Mark as sent")
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/checks/{{$check->id}}/status/cashed">
                                            <i class="fa fa-check-square-o"></i>
                                           @lang("Mark as cashed")
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/checks/{{$check->id}}/status/void">
                                            <i class="fa fa-check-square-o"></i>
                                            @lang("Mark as void")
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/checks/{{$check->id}}/delete"
                                           class="delete text-danger">
                                            <i class="fa fa-times text-danger"></i> @lang("Delete")</a>
                                    </li>

                                </ul>
                            </div>


                        </td>
                    </tr>
                @endforeach

                </tbody>

            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <?php
    $advanced = "
    $('#table').dataTable( {
        'aaSorting': [[ 0, 'desc']]
    } );
    ";
    ?>
    @include('partials.datatables',['advanced'=>$advanced])
@endpush

@push('modals')

    <div class="modal fade" id="newCheckModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("New check")</h4>
                </div>
                {!! Form::open(['url'=>'checks']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>@lang("Date")</label>
                            {!! Form::input('date','created_at',date('Y-m-d'),['placeholder'=>__("Date"),'required'=>'required']) !!}
                        </div>

                        <div class="col-sm-6">
                            <label>@lang("Check") #</label>
                            {!! Form::text('check_no',null,['required'=>'required']) !!}

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>@lang("Memo")</label>
                            {!! Form::text('memo',null,['placeholder'=>'Memo']) !!}
                        </div>
                        <div class="col-sm-6">
                            <label>@lang("Amount")</label>
                            {!! Form::text('amount',null,['required'=>'required','placeholder'=>__("Amount")]) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>@lang("Status")</label>
                            {!! Form::select('status',['draft'=>__("Draft"),'sent'=>__("Sent"),'cashed'=>__("Cashed"),'void'=>'Void']) !!}
                        </div>
                        <div class="col-sm-6">
                            <br/>
                            <label>@lang("Client")</label>
                            <select name="payee_id" id="user">
                                <option value="0">@lang("Assign to client")</option>
                                @foreach(\App\User::get() as $client)
                                    <option value="{{$client->id}}">
                                        {{$client->first_name.' '.$client->last_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                    <button class="btn btn-primary">@lang("Save")</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


@endpush
@include('partials.select2',['select2'=>'#user'])