<!DOCTYPE html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="{{ url('assets') }}/" data-template="horizontal-menu-template" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ url('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ url('assets/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/node-waves.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/select2.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ url('assets/datatables/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/datatables/css/responsive.bootstrap5.min.css') }}">
    
    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ url('assets/js/helpers.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.js') }}"></script>
    <script src="{{ url('assets/js/config.js') }}"></script>
    
	<script>
		var APP_URL = {!! json_encode(url('/')) !!};
	</script>
</head>

<body>