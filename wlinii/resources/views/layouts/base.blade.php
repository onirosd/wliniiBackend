<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{{URL::asset('/')}}" target="_top">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Winlii Admin</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Fontfaces CSS-->
    <link href="{{{ URL::asset('css/font-face.css') }}}" rel="stylesheet" media="all">
    <link href="{{{ URL::asset('vendor/font-awesome-4.7/css/font-awesome.min.css')}}}" rel="stylesheet" media="all">
    <link href="{{{ URL::asset('vendor/font-awesome-5/css/fontawesome-all.min.css')}}}" rel="stylesheet" media="all">
    <link href="{{{ URL::asset('vendor/mdi-font/css/material-design-iconic-font.min.css')}}}" rel="stylesheet" media="all">

    <link href="{{{ URL::asset('vendor/animsition/animsition.min.css')}}}" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="{{{ URL::asset('css/theme.css')}}}" rel="stylesheet" media="all">

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="{{ asset('js/main.js') }}" rel="stylesheet">
</head>

<body class="">
    <div class="page-wrapper">
        @include('layouts.header')
        @include('layouts.sidebar')
        <div class="page-container">
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                    @yield('content')
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-sm-12">
                    @include('layouts.footer')
                </div>
            </div> -->
        </div>
    </div>
</body>