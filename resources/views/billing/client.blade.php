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
            @can('create invoices')
            <a class="btn btn-warning" href="/invoice/create"><i class="fa fa-plus"></i>
                @lang("New")</a>
            <a class="btn btn-info" href="/income"><i class="fa fa-dollar"></i>
                @lang("Income")</a>

            <a href="/invoice/inventory" class="btn btn-default"><i class="fa fa-th-list"></i> @lang("Inventory")</a>
            @endcan
        </div>
        <div class="col-sm-8">
            <button class="btn btn-sm btn-warning totalDue"></button>
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
                <a class="btn btn-warning" href="?status=due">@lang("Due")</a>
                <a class="btn btn-danger" href="?status=overdue">@lang("Overdue")</a>
                <a class="btn btn-success" href="?status=paid">@lang("Paid")</a>
                @can('create invoices')
                <a class="btn btn-default" href="?status=draft">@lang("Draft")</a>
                @endcan
            </div>
            <br/>
            <table class="table table-striped table-striped" id="table">

                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>@lang("Date due")</th>
                    <th>@lang("Amount")</th>
                    <th>@lang("Paid")</th>
                    <th>@lang("Due")</th>
                    <th>@lang("Status")</th>
                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>

                <?php
                $gt = 0;
                $tp = 0;
                $td = 0;
                ?>
                @foreach($invoices as $invoice)
                    <?php
                    $grandTotal = str_replace(',', '', \App\Models\Billing\Invoices::grandTotal($invoice->id));
                    $totalPaid = str_replace(',', '', \App\Models\Billing\Invoices::totalPaid($invoice->id));
                    $totalDue = str_replace(',', '', \App\Models\Billing\Invoices::totalDue($invoice->id));
                    $gt = $gt + $grandTotal;
                    $tp = $tp + $totalPaid;
                    $td = $td + $totalDue;
                    ?>
                    <tr>

                        <td>
                            <a target="_blank" href="{{url('/invoice/'.$invoice->guid.'/view')
                            }}">{{$invoice->id}}</a>
                        </td>
                        <td class="responsive">{{date('d-M-y',strtotime($invoice->created_at))}}</td>
                        <td class="responsive">{{date('d M, Y',strtotime($invoice->due_date))}}</td>
                        <td class="responsive text-info text-right">{{'$'.number_format($grandTotal,2)}}</td>
                        <td class="responsive text-success text-right">{{'$'.number_format($totalPaid,2)}}</td>

                        <td class="responsive text-danger text-right">{{'$'.number_format($totalDue,2)}}</td>
                        <td class="responsive text-capitalize text-right">
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
                                <ul class="dropdown-menu" role="menu">
                                <!--li><a href="/invoice/{{$invoice->id}}/edit">
                                                        <i class="fa fa-edit"></i>Edit</a>
                                                </li-->
                                <!--li><a data-toggle="modal" id="{{$invoice->id}}" class="sendInvoice"
                                                       data-target="#myModal"><i id="{{$invoice->id}}"
                                                                                 class="fa fa-envelope" style="cursor: pointer"></i> Email</a></li-->

                                    <li><a id="genPDFbtn"
                                           href="/invoice/{{$invoice->guid}}/view?pdf"
                                           target="_blank"><i
                                                    class="fa
                                                fa-cloud-download"></i> PDF</a></li>
                                    {{--<li>--}}
                                    {{--<a href="#" class="send-to-email"--}}
                                    {{--data-invoice="{{$invoice->id}}"--}}
                                    {{--data-user="{{$invoice->user_id}}">--}}
                                    {{--<i class="fa fa-mail-forward"></i> Send to Email</a>--}}
                                    {{--</li> --}}

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
    $('.grandTotal').text('Total: ${{number_format($gt,2,'.',',')}}');
    $('.totalPaid').text('Paid: ${{number_format($tp,2,'.',',')}}');
    $('.totalDue').text('Due: ${{number_format($gt-$tp,2,'.',',')}}');
</script>
@endpush
