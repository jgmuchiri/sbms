@extends('layout.template')
@section('title')
    @lang("Income")
    @lang("and")
    @lang("Invoice payments")
@endsection
@section('panel-title')

    <div class="row">
        <div class="col-sm-4">
            <a href="/invoice" class="btn btn-default"><i class="fa fa-chevron-left"></i> </a>
            {{--<button class="btn btn-warning newExpense" data-toggle="modal" data-target="#newIncome">--}}
            {{--<i class="fa fa-plus"></i>--}}
            {{--New Expense--}}
            {{--</button>--}}
        </div>
        <div class="col-sm-6">
            <button class="btn btn-sm btn-info totalIncome"></button>
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
                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>

                <?php $tt = 0; ?>
                @foreach($income as $in)
                    <?php
                    $tt = $tt + $in['amount'];
                    ?>
                    <tr>
                        <td class="responsive">{{$in['date']}}</td>
                        <td class="responsive">{!! $in['name']!!}</td>
                        <td class="responsive">{{'$'.$in['amount']}}</td>
                        <td class="responsive">

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
<script type="text/javascript">
    $('.totalIncome').text('Total: ${{number_format($tt,2,'.',',')}}');
</script>
@endpush

@push('modals')
<div class="modal fade" id="newExpenseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang("Income")</h4>
            </div>
            {!! Form::open(['url'=>'income']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label>@lang("Date")</label>
                        {!! Form::input('date','created_at',date('Y-m-d'),['placeholder'=>__("Date"),'required'=>'required']) !!}
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
                        <label>@lang("Client")</label>
                        <select name="client">
                            <option value="0">@lang("Assign to client")</option>
                            @foreach(\App\User::get() as $client)
                                <option value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
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