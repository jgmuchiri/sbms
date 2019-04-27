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

	<meta name="csrf-token" content="{{ csrf_token() }}">

	@stack('styles')
	<script src="/js/lumino.glyphs.js"></script>

	<!--[if lt IE 9]>
	<script src="/js/html5shiv.js"></script>
	<script src="/js/respond.min.js"></script>
	<![endif]-->

	{{--<link rel="manifest" href="/manifest.json">--}}
</head>

<body>
<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
	{{--<form role="search">--}}
	{{--<div class="form-group">--}}
	{{--<input type="text" class="form-control" placeholder="Search">--}}
	{{--</div>--}}
	{{--</form>--}}
	<ul class="nav menu">
		<li class="sidebar-logo">
			<a href="/dashboard">
				@if(file_exists('img/logo.png'))
					<img src="{{asset('img/logo.png')}}"/>
				@elseif(file_exists('img/logo.jpg'))
					<img src="{{asset('img/logo.jpg')}}"/>
				@elseif(file_exists('img/default-logo.png'))
					<img src="{{asset('img/default-logo.png')}}"/>
				@endif
			</a>
		</li>

		<li><a href="/dashboard"><span class="fa fa-dashboard"></span> <span>@lang("Dashboard")</span></a></li>
		<li><a href="/invoice"><span class="fa fa-money"></span> <span>@lang("Invoices")</span></a></li>
		<li><a href="/projects"><span class="fa fa-tasks"></span> <span>@lang("Projects")</span></a></li>

		<li><a href="/contacts"><span class="fa fa-group"></span> <span>@lang("Contacts")</span></a></li>

		@if(has_role(['admin','manager']))
		<li><a href="/expenses"><span class="fa fa-cc-visa"></span> <span>@lang("Expenses")</span></a></li>
		<li><a href="/users"><span class="fa fa-users"></span> <span>@lang("Users")</span></a></li>
		<li><a href="/roles"><span class="fa fa-key"></span> <span>@lang("Roles")</span></a></li>
		@endif

        @if(has_role(['admin']))
        <li><a href="/debug-log"><span class="fa fa-bug"></span> <span>@lang("Debug log")</span></a></li>
		<li><a href="/settings"><span class="fa fa-cogs"></span> <span>@lang("Settings")</span></a></li>
		@endif

		<li><a href="/account/profile"><span class="fa fa-user"></span> <span>@lang("Profile")</span></a></li>
		<li><a href="/logout"><span class="fa fa-lock"></span> <span>@lang("Logout")</span></a></li>
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
			<li class="sidebar-collapse-btn  hidden-lg hidden-md hidden-sm pull-right">
				<a type="button" class="" data-toggle="collapse"
				   data-target="#sidebar-collapse">
					<span class="fa fa-bars"></span>
				</a>
			</li>
		</ol>
	</div>
	<br/>
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
@stack('scripts')
@stack('modals')

</body>

</html>
