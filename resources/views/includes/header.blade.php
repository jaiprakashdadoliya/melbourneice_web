<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ APP_NAME }}</title>

    <!-- Bootstrap -->
    <link rel="shortcut icon" type="image/ico" href="{{ asset('public/web/images/favicon.ico') }}"/>
    <link href="{{ asset('public/web/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/web/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('public/web/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <script src="{{ asset('public/web/js/jquery.min.js') }}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script> -->
    <script src="{{ asset('public/web/js/tether.min.js') }}"></script>
    <script src="{{ asset('public/web/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/web/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> -->
    <script src="{{ asset('public/web/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('public/web/js/additional-methods.min.js') }}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>    -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
<body>
