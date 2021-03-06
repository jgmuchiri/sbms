<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>{{config('app.name')}}</title>

    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width
    =device-width"/>

    @if(env('APP_ENV') =="production")
        <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    @endif

    <link href="/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="/css/font-awesome.css" rel="stylesheet">
    <link href="/css/auth.css" rel="stylesheet">


    @yield('styles')

    @if(env('APP_ENV') =="production" || env('APP_ENV')=='demo')
        <script type="text/javascript">
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', '{{env('GOOGLE_ANALYTICS')}}', 'auto');
            ga('send', 'pageview');
        </script>
    @endif
</head>
<body>

<div class="wrapper">
    <div class="container">

        <div class="row">
            <div class="col-sm-4 col-sm-offset-3">
                <div class="logo"> <a href="/"><img src="/img/logo.png"></a> </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 col-sm-offset-3">
                @if(isset($errors) && $errors->any())
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;
                            </button>
                            {{$error}}</div>
                    @endforeach
                @endif

                @if(!empty(session('status')))
                    <div class="alert alert-success">
                        {{session('status')}}
                    </div>
                @endif

            </div>
        </div>

        @yield('content')

        <footer class="footer">
            <div class="row">
                <div class="col-sm-4 col-sm-offset-3">
                    @yield('footer')
                    <div class="copyright"> &copy;
                        <script>document.write(new Date().getFullYear())</script>
                        <a href="https://amdtllc.com">A&M Digital Technologies</a>
                    </div>
                </div>

            </div>

        </footer>
    </div>
</div>


<script src="/js/jquery-1.11.1.min.js"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<script src="/js/bootstrap-notify.js"></script>
<script src="{{asset('js/global.js')}}"></script>
{{--@include('partials.flash')--}}
@stack('scripts')
@stack('modals')

</body>
</html>
