@extends('layout.template')
@section('title')
    @lang("New invoice")
@endsection

@section('panel-title')
    <a href="/invoice" class="btn btn-default"><i class="fa fa-chevron-left"></i> @lang("Back") </a>
    <a href="/invoice/inventory" class="btn btn-default"><i class="fa fa-list"></i> @lang("Inventory")</a>
@endsection

@push('styles')
    <link href="{{asset('/css/invoice-style.css')}}" rel="stylesheet">
    <link href="{{asset('/css/jquery-ui-1.10.3.custom.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/invoice-print.css')}}" media="print" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">

            {!! Form::open(['url' => 'invoice/create', 'method' => 'post']) !!}

            <div class="row">
                <div class="col-sm-5">


                    <table class="table">
                        <tr>
                            <td>@lang("Date"):</td>
                            <td>
                                {!! Form::input('date','created_at',date('Y-m-d'),['required'=>'required']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("Client"):</td>
                            <td><select name="client">
                                    @foreach(\App\User::get() as $user)
                                        <option value="{{$user->id}}">{{$user->first_name.' '.$user->last_name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("Due date"):</td>
                            <td>
                                {!! Form::input('date','due_date',date('Y-m-d')) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>@lang("Status"):</td>
                            <td> {!! Form::select('status',\App\Models\Billing\Invoices::status()->pluck('name','id')) !!}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-sm-3">
                    <h4> @lang("Total"): <span class="text-danger" id="grandTotalTop">{{config('app.currency.symbol')}}0.00</span></h4>
                    <input type="hidden" name="grandTotal" value=""/>

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
                    <div id="clientName" class=""></div>
                    <input type="hidden" name="clientName" value=""/>

                    <div id="clientAddress" class=""></div>
                    <input type="hidden" name="clientAddress" value=""/>

                    <div id="clientPhone" class=""></div>
                    <input type="hidden" name="clientPhone" value=""/>
                </div>


            </div>

            <hr>


            <table class="table table-striped table-condensed" id="itemsTable">
                <thead>
                <tr>
                    <th style="width: 5%"></th>
                    <th style="width: 10%">@lang("Item")</th>
                    <th style="width: 20%">@lang("Description")</th>
                    <th style="width: 5%">@lang("Quantity")</th>
                    <th style="width: 5%">@lang("Price")</th>
                    <th style="width: 10%">@lang("Total")</th>
                </tr>
                </thead>
                <tbody>

                <tr class="item-row">
                    <td><i id="deleteRow" class="fa fa-times"></i></td>
                    <td>
                        <input id="itemId" name="itemId[]" type="hidden" value="">
                        <input id="itemName"
                               required="required"
                               name="itemName[]" type="text"
                               class="form-control input-sm ui-autocomplete-input"
                               value=""
                               autocomplete="off">
                        <span role="status"
                              aria-live="polite" class="ui-helper-hidden-accessible"></span>
                    </td>
                    <td>
                        <input id="itemDesc"
                               name="itemDesc[]"
                               type="text"
                               class="form-control input-sm"
                               value="">
                    </td>
                    <td>
                        <input id="itemQty" required="required"
                               name="itemQty[]"
                               type="text"
                               class="form-control input-sm" value="">
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input id="itemPrice" required="required" name="itemPrice[]"
                                   class="form-control input-sm" type="text">
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input id="itemLineTotal" name="itemLineTotal[]"
                                   class="form-control input-sm" type="text"
                                   readonly="readonly">
                        </div>
                    </td>
                </tr>
                <tr class="item-row"></tr>
                </tbody>
            </table>

            <a href="#" id="addRowBtn" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> @lang("Add item")</a>


            <hr/>

            <div class="row">

                <div class="col-md-7">
                    <label>@lang("Memo")</label>
                    <textarea class="form-control" name="notes"></textarea>
                    <br/>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                {!! Form::select('allow_online_pay',[1=>'Yes',0=>'No']) !!}

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
                        <div class="col-md-6 text-right"><h5>@lang("Sub total"):</h5></div>
                        <div class="col-md-6 text-right"><h5 id="subTotal"></h5></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-right">
                            <div class="input-group">
                                <input placeholder="@lang("Sales tax")" type="text" name="tax" id="tax"
                                       value=""
                                       class="form-control input-sm"/><span class="input-group-addon">%</span></div>
                        </div>
                        <div class="col-md-6 text-right" id="salesTax"><h5>{{config('app.currency.symbol')}}0.00</h5></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-right"><h4>@lang("Grand total"):</h4></div>
                        <div class="col-md-6 text-right"><h4 id="grandTotal">{{config('app.currency.symbol')}}0.00</h4>
                        </div>
                    </div>

                </div>

            </div>

            <div class="row">
                <div class="col-sm-2 col-xs-6">
                    <a href="/invoice" id="" class="btn btn-danger">
                        <i class="fa fa-chevron-left"></i> @lang("Cancel")
                    </a>
                </div>
                <div class="col-sm-1 col-sm-offset-8 col-xs-6">
                    <button class="btn btn-default" id="saveInvoiceBtn"><i class="fa fa-save"></i> @lang("Save")</button>
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

@push('modals')

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>

                </div>
                <div class="modal-body">
                    <div class="te">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                    <button type="button" class="btn btn-primary">@lang("Save")</button>
                </div>
            </div>
        </div>
    </div>
@endpush