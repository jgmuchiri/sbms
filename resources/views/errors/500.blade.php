<!DOCTYPE html>
<html>
<head>
    <title>500 Error! - server error</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #c54e45;
            display: table;
            font-weight: 400;
            font-family: 'Lato', sans-serif;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 72px;
            margin-bottom: 40px;
        }
        a{
            text-decoration: none;
            font-size: 20px;
            color: #ff9460;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <h1>500 Server Error!</h1>
        <div class="title">@lang("We were unable to process your request")</div>
        <a href="#" onclick="window.history.back()">@lang("Go back")</a> |
        <a href="/">@lang("Go to homepage")</a>
    </div>
</div>
</body>
</html>
