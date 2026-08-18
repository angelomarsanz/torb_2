<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<title> {{ siteName() }} | Dashboard </title>
		
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

		<!-- Bootstrap v5.3 -->
		<link rel="stylesheet" href="{{ asset('public/backend/bootstrap/css/bootstrap.min.css') }}">
		<!-- Bootstrap Icons (AdminLTE 4) -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css') }}">
		<!-- AdminLTE 4 Theme style -->
		<link rel="stylesheet" href="{{ asset('public/backend/dist/css/adminlte.min.css') }}">
		<!-- iCheck -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/iCheck/flat/blue.min.css') }}">
		<!-- Morris chart -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/morris/morris.min.css') }}">
		<!-- jvectormap -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/jvectormap/jquery-jvectormap-1.2.2.min.css') }}">
		<!-- Daterange picker -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/daterangepicker/daterangepicker.min.css') }}">
		<!-- Date Picker -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/datepicker/datepicker3.min.css') }}">
		<!-- WYSIHTML5 text editor -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
		<!-- DataTables -->
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/datatables/dataTables.bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/datatables/jquery.dataTables.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/backend/plugins/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
		<!-- Intl Tel Input -->
		<link rel="stylesheet" type="text/css" href="{{ asset('public/js/intl-tel-input-13.0.0/build/css/intlTelInput.min.css') }}">
		<!-- Select2 -->
		<link href="{{ asset('public/backend/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/backend/css/style2.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/backend/css/style.min.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/css/glyphicon.min.css') }}" rel="stylesheet" type="text/css" />
		<!-- Custom css -->
		<link rel="stylesheet" href="{{ asset('public/backend/dist/css/custom.css') }}">
		@stack('css')
	</head>
	<body class="layout-fixed sidebar-mini sidebar-expand-lg">
