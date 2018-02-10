@extends('layout.template')
@section('title')
    @lang("Dashboard")
@endsection
@section('panel-title')
    {{--<a href="#" class="btn btn-default" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus-circle"></i> Update Resources</a>--}}
@endsection

@section('container')
    <div class="row">

        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/invoice'">
            <div class="panel panel-teal panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-money fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">{{\App\Models\Billing\Invoices::count()}}</div>
                        <div class="text-muted">@lang("Invoices")</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/income'">
            <div class="panel panel-teal panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-dollar fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div style="font-size: 18px;color:#333">
                            ${{number_format(\App\Models\Billing\InvoicePayments::sum('txn_amount'),2,'.',',')}}</div>
                        <div class="text-muted">@lang("Income")</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/expenses'">
            <div class="panel panel-red panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-money fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large" style="font-size: 18px;color:#333">${{\App\Models\Billing\Expenses::sum('amount')}}</div>
                        <div class="text-muted">@lang("Expenses")</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/users'">
            <div class="panel panel-blue panel-widget ">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <svg class="glyph stroked male-user">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-male-user"></use>
                        </svg>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">{{\App\User::count()}}</div>
                        <div class="text-muted">@lang("Users")</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/projects'">
            <div class="panel panel-default panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-hourglass fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">{{\App\Models\Projects\Projects::where('p_status','!=','complete')->count()}}</div>
                        <div class="text-muted">@lang("Projects")</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <div class="panel panel-default panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-th-list fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">{{\App\Models\Projects\ProjectMilestones::where('m_status','!=','complete')->count()}}</div>
                        <div class="text-muted">@lang("Milestones")</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3 cursor" onclick="window.location.href='/contacts'">
            <div class="panel panel-info panel-widget">
                <div class="row no-padding">
                    <div class="col-sm-3 col-lg-5 widget-left">
                        <span class="fa fa-phone-square fa-4x"></span>
                    </div>
                    <div class="col-sm-9 col-lg-7 widget-right">
                        <div class="large">{{\App\Models\Contacts\Contacts::count()}}</div>
                        <div class="text-muted">@lang("Contacts")</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-sm-8 col-xs-12">
            <div id="chartActivity"></div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><h4><i class="fa fa-th"></i> @lang("Activity log")</h4></div>
                </div>

                <div class="ajaxNav">
                    @include('logs.index')
                </div>
            </div>
        </div>
    </div>
    {{--{!! \App\Tools::settings('resources') !!}--}}
@endsection
@push('modals')

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang("Update resources")</h4>
                </div> {!! Form::open(['url'=>'admin/update-settings']) !!}
                <div class="modal-body">

                    {{--                    {!! Form::textarea('resources',\App\Tools::settings('resources'),['class'=>'editor']) !!}--}}
                    <br/>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang("Close")</button>
                    <button class="btn btn-primary">@lang("Save changes")</button>
                </div> {!! Form::close() !!}
            </div>
        </div>
    </div>
@endpush
@push('scripts')
@if(env('APP_ENV')=='local')
    <script src="/js/jquery.slimscroll.js"
            type="text/javascript"></script>
@else
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"
            type="text/javascript"></script>
@endif
<script>
    $('table').addClass('table table-responsive table-striped');

    $(document).ready(function () {
        $('.slimScrollLogs').slimScroll({
            height: '500px',
            railVisible: false,
            alwaysVisible: false,
            railColor: 'transparent',
            color: '#bfbfbf'
        })
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.pagination li a', function (e) {
            var page = $(this).attr('href').split('page=')[1];
            $.ajax({
                url: '?page=' + page,
                dataType: 'json',
            }).done(function (data) {
                $('.ajaxNav').html(data);
            }).fail(function () {
                alert('Data could not be loaded.');
            });
            e.preventDefault();
        });
    });
</script>

<script src="/plugins/highcharts/highcharts.js"></script>
<script src="/plugins/highcharts/exporting.js"></script>
<script type="text/javascript">
    $(function () {

        $('#chartActivity').highcharts({
            title: {
                text: 'Income/Expenses',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
                title: {
                    text: 'Views'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [
                {
                    name: 'Income',
                    data: [{{\App\Models\Reports::income()}}]
                },
                {
                    name: 'Expenses',
                    data: [{{\App\Models\Reports::expenses()}}]
                }
            ]
        });
    });
</script>
@endpush
@include('partials.tinymce')