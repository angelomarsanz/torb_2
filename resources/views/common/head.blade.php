<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="keyword"              	   content="{{ $keyword ?? Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'keywords') }}" />
		<!-- Metas For sharing property in social media -->
		<meta property="og:url"                content="{{ isset($shareLink) ? $shareLink : url('/') }}" />
		<meta property="og:type"               content="article" />
		<meta property="og:title"              content="{{ $title ?? Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'title') }}" />
		<meta property="og:description"        content="{{ isset($result->property_description->summary) ? $result->property_description->summary : ( Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'description'))  }}" />
		<meta property="og:image"              content="{{ (isset($property_id) && !empty($property_id && isset($property_photos[0]->photo) )) ? url('public/images/property/' . $property_id . '/' . $property_photos[0]->photo) : getBanner() }}" />



		<link rel="shortcut icon" href="{!! getFavicon() !!}">

		<title>{{ $title ?? Helpers::meta((!isset($exception)) ? Route::current()->uri() : '', 'title') }} {{ $additional_title ?? '' }} </title>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- CSS  new version start-->
		@stack('css')
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('public/css/vendors/bootstrap/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/css/vendors/fontawesome/css/all.min.css') }}">
		<link rel="stylesheet" href="{{ asset('public/css/style.min.css?v='.time()) }}">
		<link rel="stylesheet" href="{{ asset('public/css/rtl-style.min.css') }}">
		<!--CSS new version end-->

		<!--Google Analytics code-start-->
		@if(!empty($head_code))
			<script async src="https://www.googletagmanager.com/gtag/js?id={{ $head_code }}"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', '{{ $head_code }}');
			</script>
		@endif
		<!--Google Analytics code-end-->
        @include('reda-alojamiento::general.main_head')
	</head>
<body>
