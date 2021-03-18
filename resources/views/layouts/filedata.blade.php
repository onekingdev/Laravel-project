<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{{--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">--}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
{{--    https://www.codexworld.com/adding-google-map-on-website-within-5-minutes/ refrense --}}
    <style>
        .mapContainer{
           /* width:50%; */
           position: relative;
        }
        #map
        {
            width: 600px;
            height: 435px;
        }
        .mapContainer a.direction-link {
            position: absolute;
            top: 1px;
            right: 0px;
            z-index: 100010;
            color: #FFF;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            line-height: 25px;
            padding: 8px 20px 8px 50px;
            background: #0094de;
            background-image: url(direction-icon.png);
            background-position: left center;
            background-repeat: no-repeat;
        }
        .mapContainer a.direction-link:hover {
            text-decoration: none;
            background: #0072ab;
            color: #FFF;
            background-image: url('direction-icon.png');
            background-position: left center;
            background-repeat: no-repeat;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZzxXd5SgqMddUdrhdqaIW2Zw0CJLsHWY&callback=initialize"></script>
</head>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = { csrfToken: '{{ csrf_token() }}' }
    </script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.css') }}">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<script>
    var myCenter = new google.maps.LatLng({{$file->address_latitude}}, {{$file->address_longitude}});

    function initialize(){
        var mapProp = {
            center:myCenter,
            zoom:10,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map"),mapProp);

        var marker = new google.maps.Marker({
            position:myCenter,
            animation:google.maps.Animation.BOUNCE
        });

        marker.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>
<div id="app">
    <nav class="navbar is-info">
        <div class="navbar-brand">
            <a class="navbar-item" href="{{ url('/') }}">
                {{--                    <img src="https://bulma.io/images/bulma-logo-white.png" alt="File Hosting" width="112" height="28">--}}
                <img src="https://nauss.edu.sa/_layouts/15/1033/styles/nauss/images/master_nauss_logo.png" alt="File Hosting" width="112" height="28">
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="{{ url('/') }}">
                    Home
                </a>
            </div>
            <div class="navbar-end">
                @guest
                    <a class="navbar-item" href="{{ route('login') }}">Login</a>
                    <a class="navbar-item" href="{{ route('register') }}">Register</a>
                @else
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            <span class="fa fa-user-o"></span> &nbsp;
                            {{ Auth::user()->name }}
                        </a>

                        <div class="navbar-dropdown is-right">
                            <a class="navbar-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </nav>
</div>
<div class="container">
    <table class="table">
        <thead>
        <tr>
            <th><img class='file-img' src="{{ asset('storage/' . Auth::user()->name . '_' . Auth::id()) . '/' . $file->type . '/' . $file->name . '.' . $file->extension }}"></th>
            <th>
                <div class="mapContainer">
                    <a class="direction-link" target="_blank" href="//maps.google.com/maps?f=d&amp;daddr={{$file->address_latitude}},{{$file->address_longitude}}&amp;hl=en">Get Directions</a>
                    <div id="map"></div>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>File Name</td>
            <td>{{$file->name}}</td>
        </tr>
        <tr>
            <td>File Type</td>
            <td>{{$file->type}}</td>
        </tr>
        <tr>
            <td>File Extension</td>
            <td>{{$file->extension}}</td>
        </tr>
        <tr>
            <td>File Created</td>
            <td>{{$file->created_at}}</td>
        </tr>
        </tbody>
    </table>
</div>
