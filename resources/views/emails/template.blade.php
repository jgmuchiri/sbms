<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style type="text/css">
        @import url(http://fonts.googleapis.com/css?family=Droid+Sans);

        a {
            text-decoration: none;
            border: 0;
            outline: none;
            color: #4600B2;
        }

        a img {
            border: none;
        }

        td, h1, h2, h3 {
            font-family: Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            color: #333;
            font-size: 16px;
        }

        .content {
            margin: 0 auto;
            background: transparent;
            color: #332;
            border: solid 1px #44B7B7;
            padding:10px;
        }

    </style>

</head>
<body class="body">
<br>

<div class="content">

    @yield('content')

    <p><br/></p>
    <a style="color:#fff" href="{{url()->to('/')}}">
        {{config('app.name')}}
    </a>
</div>
</body>
</html>