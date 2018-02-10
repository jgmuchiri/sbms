@extends('layout.template')
@section('title')
   @lang("New invoice")
@endsection

@section('panel-title')
    <a href="/invoice" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i> @lang("Back") </a>
    <a href="/invoice/inventory" class="btn btn-default btn-sm"><i class="fa fa-plus"></i> @lang("Inventory")</a>
    <a href="/invoice/{{$invoice->id}}/replicate" class="btn btn-success btn-sm"><i class="fa fa-copy"></i>
        @lang("Duplicate") </a>
    <a class="btn btn-warning btn-sm" href="/invoice/create"><i class="fa fa-plus"></i>
        @lang("New")</a>

@endsection

@push('styles')
    <link href="{{asset('/css/invoice-style.css')}}" rel="stylesheet">
    <link href="{{asset('/css/jquery-ui-1.10.3.custom.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/invoice-print.css')}}" media="print" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">

            {!! Form::open(['url' => 'invoice/'.$invoice->id.'/update', 'method' => 'post']) !!}

            <div class="row">
                <div class="col-sm-5">


                    <table class="table">
                        <tr>
                            <td>@lang("Date"):</td>
                            <td>
                                {!! Form::input('date','created_at',date('Y-m-d',strtotime($invoice->created_at)),['required'=>'required']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("Client"):</td>
                            <td>
                                <select name="client">
                                    @foreach(\App\User::get() as $user)
                                        <option
                                                {{($user->id == $invoice->user_id)?'selected':''}}
                                                value="{{$user->id}}">{{$user->first_name.' '.$user->last_name}}</option>

                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("Due date"):</td>
                            <td>
                                {!! Form::input('date','due_date',date('Y-m-d',strtotime($invoice->due_date))) !!}
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-sm-3">
                    <h4>@lang("Total due"): <span class="text-danger"
                                          id="grandTotalTop">{{config('app.currency.symbol')}}{{\App\Models\Billing\Invoices::totalDue($invoice->id)}}</span>
                    </h4>
                    {!! Form::select('status',\App\Models\Billing\Invoices::status()->pluck('name','id'),$invoice->status) !!}
                </div>

                <div class="col-sm-4 companyInfo" style="border-left: solid 1px #ccc5b9;">
                    <img src="/img/logo.png" style="width:90px"/>

                    <div> {{config('app.company.phone')}}</div>
                    <div>{{config('mail.from.address')}}</div>
                    <div>{{url()->to('/')}}</div>
                    <div>{!! config('app.company.address') !!}</div>
                </div>
            </div>
            <br/>

            <hr/>

            <div class="row">
                <div class="col-md-6 clientInfo">
                    <div id="clientName" class="">{{App\User::read($invoice->user_id,['first_name','last_name'])}}</div>
                    <div id="clientAddress" class="">{!! App\User::read($invoice->user_id,'address')!!}</div>
                    <div id="clientPhone" class="">{{App\User::read($invoice->user_id,'phone')}}</div>
                </div>
            </div>

            <hr>


            <table class="table table-striped table-condensed" id="itemsTable">
                <thead>
                <tr>
                    <th style="width: 5%"></th>
                    <th style="width: 10%">@lang("Item")</th>
                    <th style="width: 20%">@lang("Description")</th>
                    <th style="width: 5%">@lang("quantity")</th>
                    <th style="width: 5%">@lang("Price")</th>
                    <th style="width: 10%" class="text-right">@lang("Total")</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr class="item-row">
                        <td><a href="/invoice/{{$invoice->id}}/removeItem/{{$item->id}}" class="delete"><i
                                        id="deleteRow" class="fa fa-times"></i></a></td>
                        <td>
                            {{$item->itemName}}
                        </td>
                        <td>
                            {{$item->itemDesc}}
                        </td>
                        <td>
                            {{$item->itemQty}}
                        </td>
                        <td>
                            ${{$item->itemPrice}}
                        </td>
                        <td class="text-right">
                            ${{$item->itemQty*str_replace(',','',$item->itemPrice)}}

                        </td>
                    </tr>
                @endforeach

                <tr class="item-row"></tr>
                </tbody>
            </table>

            <a href="#" id="addRowBtn" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> @lang("Add item")</a>


            <hr/>

            <div class="row">

                <div class="col-md-7">
                    <label>@lang("Memo")</label>
                    <textarea class="form-control" name="notes">{{$invoice->notes}}</textarea>
                    <br/>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                {!! Form::select('allow_online_pay',[1=>'Yes',0=>'No'],$invoice->allow_online_pay) !!}

                                <span class="input-group-addon">
                                <label>@lang("Allow online payment")</label>
                            </span>
                            </div>
                        </div>
                    </div>

                    <br/>
                </div>

                <div class="col-md-5 text-right">

                    <div class="row">
                        <div class="col-md-6 text-right"><h5>@lang("Total to add"):</h5></div>
                        <div class="col-md-6 text-right"><h5 id="subTotal"></h5></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-right">
                            <div class="input-group">
                                <input placeholder="Sales Tax" type="text" name="tax" id="tax"
                                       value=""
                                       class="form-control input-sm"/><span class="input-group-addon">%</span></div>
                        </div>
                        <div class="col-md-6 text-right"><h5>{{config('app.currency.symbol')}}{{$invoice->tax}}</h5></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-right"><h4>@lang("Grand total"):</h4></div>
                        <div class="col-md-6 text-right">
                            <h4>{{config('app.currency.symbol')}}{{\App\Models\Billing\Invoices::grandTotal($invoice->id)}}</h4>
                        </div>
                    </div>

                </div>

            </div>

            <div class="row">
                <div class="col-sm-2 col-xs-6">
                    <a href="/invoice" id="" class="btn btn-danger  btn-sm">
                        <i class="fa fa-times"></i> @lang("Close")
                    </a>
                </div>
                <div class="col-sm-1 col-sm-offset-8 col-xs-6">
                    <button class="btn btn-default btn-sm" id="saveInvoiceBtn"><i class="fa fa-save"></i> @lang("Update")</button>
                </div>
            </div>

            {!! Form::close() !!}

        </div>

    </div>

@endsection

@push('scripts')
    <script type="text/javascript" src="/js/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ asset('/js/invoice-script.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/js/invoice-general.js')}}"></script>
@endpush
