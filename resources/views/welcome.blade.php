<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Evidencio Patient Portal</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
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

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .topnav {
                background-color: rgba(39, 106, 255, 0.31);
                overflow: hidden;
            }

            .topnav #menuHomeItem {
                float: left;
                display: block;
                color: #f2f2f2;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
                font-size: 17px;
            }

            .topnav #menuDesignerItem {
                float: right;
                display: block;
                color: #f2f2f2;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
                font-size: 17px;
            }

            .topnav a:hover {
                background-color: #ddd;
                color: black;
            }

            active {
                background-color: #276aff;
                color: white;
            }

            .topnav .icon {
                display: none;
            }

            #homeLink {
                position: absolute;
                left: 10px;
                top: 10px;
                color: #000000;
                font-size: 30px;
                text-decoration: none;
            }
            #designerLink {
                position: absolute;
                right: 10px;
                top: 10px;
                color: #000000;
                font-size: 30px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="topnav" id="myTopnav">
            <a id="menuHomeItem" href="/public/index.php" class="active">Home</a>
            <a id="menuDesignerItem" href="./designerView.php">Designer</a>
            {{--This can be uncommented for drop menu on smaller screens. But more files need to be added--}}
            {{--<a href="javascript:void(0);" class="icon" onclick="myFunction()">&#9776;</a>--}}
        </div>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif
            {{--<a id="homeLink" href="/public/index.php" >--}}
                {{--Evidencio--}}
            {{--</a>--}}
            {{--<a id="designerLink" href="./designerView.php">--}}
               {{--Designer Part--}}
            {{--</a>--}}
            <div class="content">
                <div class="title m-b-md" >
                    Evidencio Patient Portal
                </div>
            </div>
        </div>
    </body>
</html>
