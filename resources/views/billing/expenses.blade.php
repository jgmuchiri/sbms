@extends('layout.template')
@section('title')
    @lang("Expenses")
@endsection
@section('panel-title')

    <div class="row">
        <div class="col-sm-6">

            <button class="btn btn-success" data-toggle="modal" data-target="#expense-yr-modal">
                <i class="fa fa-calendar-check-o"></i> @lang("Select year")
            </button>
            <button class="btn btn-warning newExpense" data-toggle="modal" data-target="#newExpenseModal">
                <i class="fa fa-plus"></i>
                @lang("New expense")
            </button>
            <a href="/checks" class="btn btn-default"><i class="fa fa-list-alt"></i> @lang("Checks")</a>
        </div>
        <div class="col-sm-4">
            <button class="btn btn-sm btn-info totalExpenses"></button>
        </div>
        <div class="col-sm-2">
            <button class="btn btn-default" data-toggle="modal" data-target="#newCatModal"><i class="fa fa-plus"></i>
                @lang("New category")
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="col-sm-12">

            <table class="table table-striped table-striped" id="table">

                <thead>
                <tr>
                    <th>@lang("Date")</th>
                    <th>@lang("Name")</th>
                    <th>@lang("Amount")</th>
                    <th>@lang("Category")</th>
                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>

                <?php $tt = 0; ?>
                @foreach($expenses as $expense)
                    <?php
                    $tt = $tt + $expense->amount;
                    ?>
                    <tr>
                        <td class="responsive">{{date('d M, Y',strtotime($expense->created_at))}}</td>
                        <td class="responsive">{{$expense->name}}</td>
                        <td class="responsive">{{'$'.$expense->amount}}</td>
                        <td class="responsive">{{\App\Models\Billing\Expenses::getCatName($expense->category)}}</td>

                        <td class="responsive">

                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown">
                                    <i class="fa fa-cog"></i> @lang("Action") <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    @can('update user')
                                    <li>
                                        <a href="/expenses/{{$expense->id}}/edit"><i class="fa fa-pencil-square-o"></i>
                                            @lang("Edit") </a>
                                    </li>
                                    @endcan
                                    @can('delete user')
                                    <li>
                                        <a href="/expenses/{{$expense->id}}/delete"
                                           class="delete text-danger">
                                            <i class="fa fa-times text-danger"></i> @lang("Delete")</a>
                                    </li>
                                    @endcan


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

@push('styles')
<link href="/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
@endpush


@push('scripts')
<?php
$advanced = "
    $('#table').dataTable( {
        'aaSorting': [[ 0, 'desc']]
    } );
    ";
?>
@include('partials.datatables',['advanced'=>$advanced])

<script src="/plugins/select2/select2.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $('.totalExpenses').text('Total: ${{number_format($tt,2,'.',',')}}');
    $(".select2").select2();
    $(".selectClient").select2();
</script>

@endpush

@push('modals')

<div class="modal fade" id="expense-yr-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Select Expense Year")</h4>
            </div>
            <div class="modal-body">
                <a href="/expenses?year=all">@lang("All")</a>
                @for($i=2016; $i<=date('Y'); $i++)
                    <a href="?year={{$i}}">{{$i}}</a>
                @endfor
            </div>
        </div>
    </div>
</div>

<div class="modal {{(count($exp))?'show':'fade'}}" id="newExpenseModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                @if(count($exp))
                    <a href="/expenses" class="close"><span aria-hidden="true">&times;</span> </a>
                @else
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                @endif
                <h4 class="modal-title" id="myModalLabel">@lang("Expense")</h4>
            </div>
            @if(count($exp))
                {!! Form::model($exp,['url'=>'/expenses/'.$exp->id.'/update']) !!}
            @else
                {!! Form::open(['url'=>'expenses']) !!}
            @endif
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label>@lang("Date")</label>
                        @if(count($exp))
                            {!! Form::input('date','created_at',date('Y-m-d',strtotime($exp->created_at)),['placeholder'=>__("Date"),'required'=>'required']) !!}
                        @else
                            {!! Form::input('date','created_at',date('Y-m-d'),['placeholder'=>__("Date"),'required'=>'required']) !!}
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label>@lang("Amount")</label>
                        {!! Form::text('amount',null,['required'=>'required','placeholder'=>__("Amount")]) !!}
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>@lang("Name")</label>
                        {!! Form::text('name',null,['required'=>'required','placeholder'=>__("Name")]) !!}
                    </div>
                    <div class="col-sm-6">
                        <label>@lang("Category")</label>
                        {!! Form::select('category',\App\Models\Billing\Expenses::getCats()->pluck('cat_name','id')) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label>@lang("Notes")</label>
                        {!! Form::textarea('notes',null,['placeholder'=>__("Notes"),'rows'=>3]) !!}
                    </div>
                    <div class="col-sm-6">
                        <br/>
                        <label>@lang("Client"):</label>
                        <select name="client" class="selectClient">
                            <option value="0">@lang("Assign to client")</option>
                            @foreach(\App\User::get() as $client)
                                <option
                                        {{(count($exp) && ($exp->client == $client->id))?'selected':''}}
                                        value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
                            @endforeach
                        </select>
<br/>
<br/>
                        <label>@lang("Attach task"):</label>
                        <select name="task_id" class="select2">
                            <option>@lang("Select task")</option>

                            @foreach(\App\Models\Projects\ProjectTasks::get() as $task)
                                <option
                                        @if(count($exp) && ($exp->task_id == $task->id)) selected @endif
                                value="{{$task->id}}">{{$task->task_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                @if(count($exp))
                    <a href="/expenses" class="btn btn-default">@lang("Close")</a>
                @else
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                @endif
                <button class="btn btn-primary">@lang("Save")</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>


<div class="modal fade" id="newCatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("New category")</h4>
            </div>
            {!! Form::open(['url'=>'expenses/newCat']) !!}
            <div class="modal-body">
                {!! Form::text('cat_name',null,['required'=>'required']) !!}
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
@include('partials.select2',['select2'=>['.selectClient','.select2']])