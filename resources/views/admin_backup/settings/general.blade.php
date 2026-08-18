@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">General Settings</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-3 col-12">
					@include('admin.common.settings_bar')
				</div>

				<div class="col-lg-9 col-12">
					<div class="card card-outline card-info shadow-sm">
						@if (Session::has('error'))
							<div class="p-3 pb-0">
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
							</div>
						@endif

						<div class="card-header">
							<h3 class="card-title">General Setting Form</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="general_form" method="post" action="{{ url('admin/settings') }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="name" class="col-md-3 col-form-label text-md-end fw-bold">Name <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="name" class="form-control f-14" id="name" placeholder="Name" value="{{ $result['name'] }}">
										<span class="text-danger f-12">{{ $errors->first("name") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="email" class="col-md-3 col-form-label text-md-end fw-bold">Email <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="email" name="email" class="form-control f-14" id="email" placeholder="Email" value="{{ $result['email'] }}">
										<span class="text-danger f-12">{{ $errors->first("email") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="photos[logo]" class="col-md-3 col-form-label text-md-end fw-bold">Logo <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="file" name="photos[logo]" class="form-control f-14" id="photos[logo]" placeholder="Logo">
										<span class="text-danger f-12">{{ $errors->first('photos[logo]') }}</span>
										<div class="mt-2">{!! getLogo('file-img') !!}</div>
										<input id="hidden_company_logo" name="hidden_company_logo" data-rel="' {{ $result['logo'] }}'" type="hidden">
										<span name="mySpan" class="remove_logo_preview" id="mySpan"></span>
									</div>
									<div class="col-md-3">
										<small class="text-muted">{{ $field['hint'] ?? '' }}</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="photos[favicon]" class="col-md-3 col-form-label text-md-end fw-bold">Favicon <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="file" name="photos[favicon]" class="form-control f-14" id="photos[favicon]" placeholder="Favicon">
										<span class="text-danger f-12">{{ $errors->first('photos[favicon]') }}</span>
										<div class="mt-2">{!! getFavicon('file-img') !!}</div>
										<input id="hidden_company_logo" name="hidden_company_logo" data-rel="' {{ $result['logo'] }}'" type="hidden">
										<span name="mySpan2" class="remove_favicon_preview" id="mySpan2"></span>
										<input id="hidden_company_favicon" name="hidden_company_favicon" data-rel="{{ $result['favicon'] }}" type="hidden">
									</div>
									<div class="col-md-3">
										<small class="text-muted">{{ $field['hint'] ?? '' }}</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="head_code" class="col-md-3 col-form-label text-md-end fw-bold">{{ __('Google Analytics') }} <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="head_code" placeholder="Measurement ID" class="form-control f-14 validate_field" value="{{ $result['head_code'] }}">
										<span class="text-danger f-12">{{ $errors->first('head_code') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="default_currency" class="col-md-3 col-form-label text-md-end fw-bold">Default Currency</label>
									<div class="col-md-6">
										<select class="form-select f-14" id="default_currency" name="default_currency">
											@foreach ($currency as $key => $item)
												<option value="{{ $key }}" {{ $result['default_currency'] == $key ? 'selected' : '' }}>{{ $item }}</option>
											@endforeach
										</select>
										<span class="text-danger f-12">{{ $errors->first('default_currency') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="default_language" class="col-md-3 col-form-label text-md-end fw-bold">Default Language</label>
									<div class="col-md-6">
										<select class="form-select f-14" id="default_language" name="default_language">
											@foreach ($language as $key => $item)
												<option value="{{ $key }}" {{ $result['default_language'] == $key ? 'selected' : '' }}>{{ $item }}</option>
											@endforeach
										</select>
										<span class="text-danger f-12">{{ $errors->first('default_language') }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings') }}">Cancel</a>
								<button type="submit" class="btn btn-info text-white f-14">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script type="text/javascript">
	'use strict'
	var message = "{{ __('The file must be an image (jpg, jpeg, png or gif)') }}";
	var message_ico = "{{ __('The file must be an image (jpg, jpeg, png or ico)') }}";
</script>
<script type="text/javascript" src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/js/backend.min.js') }}"></script>
@endsection
