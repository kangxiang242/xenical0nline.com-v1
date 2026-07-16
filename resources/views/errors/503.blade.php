



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Not Found</title>
    <link href="{{ asset('static/errors/css/style.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('static/errors/css/jquery.countdown.css') }}" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #000;
            font-family: 'Nunito', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 2px solid;
            font-size: 26px;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            text-align: center;
        }
        h1{
            font-size: 28px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="message" style="padding: 10px;"><h1>{{ \App\Services\ConfigService::get('close_site_tips','暫時無法訪問~') }}</h1></div>
</div>
</body>
</html>
