@extends('layout.template')
@section('title')
    @lang("Invoices")
@endsection
@section('panel-title')


    <div class="row">
        <div class="col-sm-4">
            @if(isset($client))
                <a href="/invoice" class="btn btn-default"><i class="fa fa-chevron-left"></i> </a>
            @endif
            @ability('admin','create-invoice')
            <a class="btn btn-warning" href="/invoice/create"><i class="fa fa-plus"></i>
                @lang("New")</a>
            <a class="btn btn-info" href="/income"><i class="fa fa-dollar"></i>
                @lang("Income")</a>

            <a href="/invoice/inventory" class="btn btn-default"><i class="fa fa-th-list"></i> @lang("Inventory")</a>
            @endability
        </div>
        <div class="col-sm-8">
            <button class="btn btn-sm btn-info grandTotal">
                @lang("Total"): {{config('app.currency.symbol')}}{{\App\Models\Billing\Invoices::invoicesTotal()}}
            </button>
            <button class="btn btn-sm btn-success totalPaid">
                @lang("Paid"): {{config('app.currency.symbol')}}{{\App\Models\Billing\Invoices::invoicesTotal('paid')}}
            </button>
            <button class="btn btn-sm btn-warning totalDue">
                @lang("Due"): {{config('app.currency.symbol')}}{{\App\Models\Billing\Invoices::invoicesTotal('due')}}
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="col-sm-12">
            @if(isset($client))
                <h4><i class="fa fa-user text-info"></i> {{$client->first_name.' '.$client->last_name}}</h4>
                <hr/>
            @endif

            <div class="">
                <a class="btn btn-warning" href="/{{Request()->path()}}">@lang("All")</a>
                <a class="btn btn-warning" href="?status=not-paid">@lang("Due")/@lang("Overdue")</a>
                <a class="btn btn-warning" href="?status=due">@lang("Due")</a>
                <a class="btn btn-danger" href="?status=overdue">@lang("Overdue")</a>
                <a class="btn btn-success" href="?status=paid">@lang("Paid")</a>
                <a class="btn btn-success" href="?status=draft">@lang("Draft")</a>
            </div>
            <br/>
            <table class="table table-striped table-striped" id="table">

                <thead>
                <tr>
                    <th>#</th>
                    <th>@lang("Client")</th>
                    <th>@lang("Date")</th>
                    <th>@lang("Due date")</th>
                    <th>@lang("Amount")</th>
                    <th>@lang("Paid")</th>
                    <th>@lang("Due")</th>
                    <th>@lang("Status")</th>
                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>

                @foreach($invoices as $invoice)
                    <?php
                    $grandTotal = \App\Models\Billing\Invoices::grandTotal($invoice->id);
                    $totalPaid = \App\Models\Billing\Invoices::totalPaid($invoice->id);
                    $totalDue = App\Models\Billing\Invoices::totalDue($invoice->id);
                    ?>
                    <tr>

                        <td>
                            <a target="_blank" href="{{url('/invoice/'.$invoice->guid.'/view')
                            }}">{{$invoice->id}}</a>
                        </td>
                        <td class="responsive">
                            <a data-toggle="tooltip" title="View User" href="/users/{{$invoice->user_id}}/view"><i
                                        class="fa fa-external-link"></i></a>
                            |
                            <a href="/invoice/client/{{$invoice->user_id}}"
                               data-toggle="tooltip" title="All User Invoices">
                                {{\App\User::read($invoice->user_id,['first_name','last_name'])}}
                            </a></td>
                        <td class="responsive">{{date('d-M-y',strtotime($invoice->created_at))}}</td>
                        <td class="responsive">{{date('d M, Y',strtotime($invoice->due_date))}}</td>
                        <td class="responsive text-info">{{'$'.$grandTotal}}</td>
                        <td class="responsive text-success">{{'$'.$totalPaid}}</td>

                        <td class="responsive text-danger">{{'$'.$totalDue}}</td>
                        <td class="responsive text-capitalize">
                            <?php
                            switch ($invoice->status) {
                                case 'due':
                                    echo '<span class="label label-warning">Due</div>';
                                    break;
                                case 'overdue':
                                    echo '<span class="label label-danger">Overdue</div>';
                                    break;
                                case 'paid':
                                    echo '<span class="label label-success">Paid</div>';
                                    break;
                                case 'draft':
                                    echo '<span class="label label-default">Draft</div>';
                                    break;
                                default:
                                    break;
                            }
                            ?>
                        </td>
                        <td class="responsive">

                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-sm btn-default dropdown-toggle"
                                        data-toggle="dropdown">
                                    <i class="fa fa-cog"></i> @lang("Action") <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="left:-100%;">
                                <!--li><a href="/invoice/{{$invoice->id}}/edit">
                                                        <i class="fa fa-edit"></i>Edit</a>
                                                </li-->
                                <!--li><a data-toggle="modal" id="{{$invoice->id}}" class="sendInvoice"
                                                       data-target="#myModal"><i id="{{$invoice->id}}"
                                                                                 class="fa fa-envelope" style="cursor: pointer"></i> Email</a></li-->
                                    <li><a target="_blank" href="{{url('/invoice/'.$invoice->guid.'/view')
                            }}"><i class="fa fa-external-link-square"></i> @lang("View")</a></li>
                                    <li><a id="genPDFbtn"
                                           href="/invoice/{{$invoice->guid}}/view?pdf"
                                           target="_blank"><i
                                                    class="fa
                                                fa-cloud-download"></i> PDF</a></li>
                                    <li>
                                        <a href="#" class="send-to-email"
                                           data-invoice="{{$invoice->id}}"
                                           data-user="{{$invoice->user_id}}">
                                            <i class="fa fa-mail-forward"></i> @lang("Send to email")</a>
                                    </li>
                                    @ability('admin','create-invoice')
                                    <li>
                                        <a href="{{url('invoice/'.$invoice->id.'/email-reminder')}}">
                                            <i class="fa fa-bell"></i> @lang("Send reminder")
                                        </a>
                                    </li>

                                    <li>
                                        <a id="{{$invoice->id}}" data-amount="{{$totalDue}}" href="#"
                                           class="manual-pay">
                                            <i class="fa fa-credit-card-alt"></i> @lang("Manual pay")
                                        </a>
                                    </li>

                                    <li>
                                        <a href="/invoice/{{$invoice->id}}/edit"><i
                                                    class="fa fa-pencil-square-o"></i>
                                            @lang("Edit") </a>
                                    </li>
                                    <li>
                                        <a href="/invoice/{{$invoice->id}}/replicate"><i class="fa fa-copy"></i>
                                            @lang("Duplicate") </a>
                                    </li>
                                    <br/>
                                    <li>
                                        <a href="#"
                                           id="{{$invoice->id}}"
                                           class="delete-invoice text-danger"
                                           data-action="safe"><i
                                                    class="fa fa-times text-danger"></i> @lang("Delete")</a>
                                    </li>
                                    @endability
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
<script>

    $('.send-to-email').click(function () {

        var div = $('#sendToEmailModal');
        var invoice_id = $(this).attr('data-invoice');
        var user_id = $(this).attr('data-user');
        div.find('input[name=invoice_id]').val(invoice_id);
        div.find('input[name=user_id]').val(user_id);
        div.find('.in_no').text(invoice_id);
        div.modal('show');
    });

    $('.manual-pay').click(function () {
        var div = $('#mpModal');
        var invoice_id = $(this).attr('id');
        var invoice_amount = $(this).attr('data-amount');
        var pay_date = "{{date('Y-m-d')}}";
        div.find('input[name=invoice_id]').val(invoice_id);
        div.find('input[name=amount]').val(invoice_amount);
        div.find('input[name=txn_date]').val(pay_date);
        div.find('.in_no').text(invoice_id);
        div.modal('show');
    });

    $('.delete-invoice').click(function () {

        var this_ = $(this);
        var id = this_.attr('id');
        var action = this_.attr('data-action');
        var url = '/invoice/' + id + '/delete';
        var token = "{{ csrf_token() }}";

        swal({
            title: "{{__("Are you sure?")}}",
            text: "{{__("You are about to delete invoice and all related items")}}!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            $.ajax({
                url: url,
                dataType: 'text',
                type: 'post',
                contentType: 'application/x-www-form-urlencoded',
                data: {_token: token, action: action},
                success: function (data, textStatus, jQxhr) {
                    if (data === "success") {
                        swal("Deleted!", "{{__("Invoice has been deleted")}}.", "success");
                        this_.closest('tr').remove();
                    }
                    if (data === 'error') {
                        swal("Error!", "{{__("The invoice has payments. Use force delete")}}", "warning");
                    }
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    swal('Error!');
                }
            });

        });


    });
</script>

@endpush

@push('modals')

<div class="modal fade" id="mpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Payment for invoice # <span class="in_no"></span></h4>
            </div>

            {!! Form::open(['url'=>'invoice/payment']) !!}
            <div class="modal-body">
                {!! Form::hidden('invoice_id') !!}
                <div class="row">
                    <div class="col-md-6">
                        <label>Date</label>
                        {!! Form::input('date','txn_date') !!}
                    </div>
                    <div class="col-md-6">
                        <label>Pay Method</label>
                        {!! Form::select('pay_method',['credit'=>'Credit','paypal'=>'PayPal','cash'=>'Cash','check'=>'Check']) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label>Amount</label>
                        {!! Form::text('amount',null,['required'=>'required','class'=>'amount']) !!}
                    </div>
                    <div class="col-md-6">
                        <label>Remarks</label>
                        {!! Form::textarea('remarks',null,['placeholder'=>'e.g. Check #','rows'=>2]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary">Save changes</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade" id="sendToEmailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Email Invoice #<span></span></h4>

            </div>

            {!! Form::open(array('url' => 'invoice/send-to-email', 'method' => 'post')) !!}
            <div class="modal-body">
                <input type="hidden" name="user_id"/>
                <input type="hidden" name="invoice_id"/>

                <div class="form-group">
                    {!! Form::text('name', null ,['required'=>'required','placeholder'=>'Recipient Name']) !!}
                </div>
                <div class="form-group">
                    {!! Form::input('email','email', null,['placeholder'=>'Recipient Email','required'=>'required'] ,[]) !!}
                </div>
                <div class="form-group">
                    {!! Form::textarea('message', null ,['rows'=>3,'placeholder'=>'Message']) !!}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                {!! Form::submit('Send',['class'=>'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endpush

