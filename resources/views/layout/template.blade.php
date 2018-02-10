<!DOCTYPE html>
<html>
<head>
    <title>{{config('app.name')}}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"/>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/datepicker.css" rel="stylesheet">
    <link href="/css/bootstrap-table.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
    <link href="/css/font-awesome.css" rel="stylesheet">
    <link href="/css/sweetalert.css" rel="stylesheet">

    @stack('styles')
    <script src="/js/lumino.glyphs.js"></script>

    <!--[if lt IE 9]>
    <script src="/js/html5shiv.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]-->

    {{--<link rel="manifest" href="/manifest.json">--}}
</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#sidebar-collapse">
                <span class="sr-only">@lang("Toggle navigation")</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/" target="_blank">
                <img src="/img/logo.png"/>
            </a>

            <ul class="user-menu">
                <li class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="fa fa-user"></span>
                        @lang("Account") <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/account/profile">
                                <span class="fa fa-user"></span>
                                @lang("Profile")</a></li>
                        @role('admin')
                        <li><a href="/settings">
                                <span class="fa fa-cogs"></span>
                                @lang("Settings")</a></li>
                        @endrole

                        <li><a href="/logout">
                                <span class="fa fa-lock"></span>
                                @lang("Logout")</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    {{--<form role="search">--}}
    {{--<div class="form-group">--}}
    {{--<input type="text" class="form-control" placeholder="Search">--}}
    {{--</div>--}}
    {{--</form>--}}
    <ul class="nav menu">
        <li><a href="/dashboard"><span class="fa fa-dashboard"></span> @lang("Dashboard")</a></li>
        @permission('read-invoices')
        <li><a href="/invoice"><span class="fa fa-money"></span> @lang("Invoices")</a></li>
        @endpermission

        @permission('read-projects')
        <li><a href="/projects"><span class="fa fa-tasks"></span> @lang("Projects")</a></li>
        @endpermission

        @permission('read-expenses')
        <li><a href="/expenses"><span class="fa fa-dollar"></span> @lang("Expenses")</a></li>
        @endpermission
        @permission('read-contacts')
        <li><a href="/contacts"><span class="fa fa-group"></span> @lang("Contacts")</a></li>
        @endpermission

        @permission('read-users')
        <li><a href="/users"><span class="fa fa-users"></span> @lang("Users")</a></li>
        @endpermission

        @role('admin')
        <li><a href="/roles"><span class="fa fa-key"></span> @lang("Roles")/@lang("Permissions")</a></li>
        @endrole

        @permission('read-logs')
        <li><a href="/debug-log"><span class="fa fa-bug"></span> @lang("Debug log")</a></li>
        @endpermission
    </ul>
    <div class="attribution">&copy; {{date('Y')}}
        <a href="https://amdtllc.com">A&M Digital Technologies</a>
    </div>
</div>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="/dashboard">
                    <svg class="glyph stroked home">
                        <use xlink:href="#stroked-home"></use>
                    </svg>
                </a></li>
            <li class="active">@yield('title')</li>
        </ol>
    </div>
    <br/>
    {{--<div class="row">--}}
    {{--<div class="col-lg-12">--}}
    {{--<h1 class="page-header">@yield('title')</h1>--}}
    {{--</div>--}}
    {{--</div>--}}


    @yield('container')

    @if (View::hasSection('content'))
        <div class="panel panel-default">

            @if (View::hasSection('panel-title'))
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>
                            @yield('panel-title')
                        </h4>
                    </div>
                </div>
            @endif
            <div class="panel-body">
                @yield('content')
            </div>

            @if (View::hasSection('panel-footer'))
                <div class="panel-footer">
                    @yield('panel-footer')
                </div>
            @endif

        </div>
    @endif

</div>

<script src="/js/jquery-1.11.1.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/bootstrap-table.js"></script>
<script src="/js/numeral.min.js"></script>
<script src="/js/sweetalert2.js"></script>
<script src="/js/global.js"></script>

<script type="text/javascript">
    var amount = $('.amount').val();
    if (amount != 'undefined') {
        //amount.val(numeral(amount).format('0.00'));
    }
</script>

@include('partials.flash')
<script>
    $(document).on('click', function (e) {
        $('[data-toggle="popover"],[data-original-title]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false  // fix for BS 3.3.6
            }

        });
    });

    !function ($) {
        $(document).on("click", "ul.nav li.parent > a > span.icon", function () {
            $(this).find('em:first').toggleClass("glyphicon-minus");
        });
        $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
    }(window.jQuery);

    $(window).on('resize', function () {
        if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
    })
    $(window).on('resize', function () {
        if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
    })
</script>
@stack('scripts')
@stack('modals')

</body>

</html>
