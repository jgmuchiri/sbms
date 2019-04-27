@extends('layout.template')
@section('title')
    @lang("Inventory items")
@endsection

@section('panel-title')

    <a class="btn btn-default btn-sm" href="/invoice"><i class="fa fa-chevron-left"></i>
        @lang("Invoices")</a>
    <a class="btn btn-warning btn-sm" href="/invoice/create"><i class="fa fa-plus"></i>
        @lang("New")</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-7">
            <div class="alert content-alert alert-info alert-white rounded">
                {{--<a href="#" class="close"><i class="fa fa-times-circle-o"></i> </a>--}}
                <div class="icon">
                    <i class="fa fa-info"></i>
                </div>

                <p class="category small">
                    @lang("Enter items that you wish to auto-populate when adding items to an invoice")
                </p>
            </div>

            <h3>@lang("Inventory items")</h3>
            <table class="table table-responsive table-striped" id="table">
                <thead>
                <tr>
                    <th>@lang("Code")</th>
                    <th>@lang("Name")</th>
                    <th>@lang("Description")</th>
                    <th>@lang("Price")</th>
                    <th>@lang("Quantity")</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($inventoryItems as $item)
                    <tr>
                        <td>{{$item->itemCode}}</td>
                        <td>{{$item->itemName}}</td>
                        <td>{{$item->itemDesc}}</td>
                        <td>{{$item->itemPrice}}</td>
                        <td>{{$item->qtyOnHand}}</td>
                        <td><a href="/invoice/delete-inventory/{{$item->id}}" class="delete btn btn-danger btn-xs">
                                <i class="fa fa-trash-o"></i>
                            </a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-sm-4 col-sm-offset-1">
            <h3>@lang("New item")</h3>
            {!! Form::open(['url'=>'invoice/addInventoryItem']) !!}
            <label>@lang("Name")</label>
            {!! Form::text('itemName',null,['required'=>'required','class'=>'form-control']) !!}
            <label>@lang("Item code")</label>
            {!! Form::text('itemCode',null,['required'=>'required','class'=>'form-control']) !!}
            <label>@lang("Description")</label>
            {!! Form::text('itemDesc',null,['class'=>'form-control']) !!}
            <label>@lang("Price")</label>
            {!! Form::text('itemPrice',null,['required'=>'required','class'=>'form-control']) !!}
            <label>@lang("Available")"</label>
            {!! Form::text('qtyOnHand',null,['required'=>'required','placeholder'=>'Enter -1 for unlimited']) !!}
            <br/>
            <button class="btn btn-default">@lang("Save")</button>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
@include('partials.datatables',['table'=>'#table'])