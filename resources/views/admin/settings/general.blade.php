@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">GENERAL SETTINGS</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-12 mt-0">
							<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
								<div class="card-body p-4 pt-3">
									<div class="workbench-container">

										<div class="workbench-sidebar">
											<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">
												Settings Menu</div>
											@include('admin.common.settings_bar')
										</div>

										<div class="workbench-main">
											@if (Session::has('error'))
												<div class="mb-4">
													<div class="alert alert-warning alert-dismissible fade show shadow-sm"
														style="border-radius: 8px;" role="alert">
														<strong>Warning!</strong> Whoops there was an error. Please verify your
														below information.
														<button type="button" class="btn-close" data-bs-dismiss="alert"
															aria-label="Close"></button>
													</div>
												</div>
											@endif

											<form id="general_form" method="post" action="{{ url('admin/settings') }}"
												class="form-horizontal" enctype="multipart/form-data">
												{{ csrf_field() }}
												<div class="settings-form-card">

													{{-- Form Header --}}
													<div class="settings-form-header">
														<div class="d-flex align-items-center gap-3">
															<div class="settings-form-icon">
																<i class="fa fa-cog"></i>
															</div>
															<div>
																<h4 class="settings-form-title">General Settings</h4>
																<p class="settings-form-subtitle">Configure your site's
																	basic information and preferences</p>
															</div>
														</div>
													</div>

													<div class="settings-form-body">

														{{-- Section: Site Identity --}}
														<div class="settings-section">
															<div class="settings-section-label">
																<i class="fa fa-id-card-o"></i>
																<span>Site Identity</span>
															</div>

															{{-- Name --}}
															<div class="settings-field-row">
																<label for="name" class="settings-field-label">Name <span
																		class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<input type="text" name="name"
																		class="form-control settings-input" id="name"
																		placeholder="Enter site name"
																		value="{{ $result['name'] }}">
																	<span
																		class="text-danger f-12">{{ $errors->first("name") }}</span>
																</div>
															</div>

															{{-- Email --}}
															<div class="settings-field-row">
																<label for="email" class="settings-field-label">Email <span
																		class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<input type="email" name="email"
																		class="form-control settings-input" id="email"
																		placeholder="admin@example.com"
																		value="{{ $result['email'] }}">
																	<span
																		class="text-danger f-12">{{ $errors->first("email") }}</span>
																</div>
															</div>
														</div>

														{{-- Section: Brand Assets --}}
														<div class="settings-section">
															<div class="settings-section-label">
																<i class="fa fa-paint-brush"></i>
																<span>Brand Assets</span>
															</div>

															{{-- Logo --}}
															<div class="settings-field-row">
																<label for="photos[logo]" class="settings-field-label">Logo
																	<span class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<div class="settings-upload-zone">
																		<input type="file" name="photos[logo]"
																			class="form-control settings-input"
																			id="photos[logo]" placeholder="Logo">
																		<span
																			class="text-danger f-12">{{ $errors->first('photos[logo]') }}</span>
																	</div>
																	<div class="settings-preview-area">
																		{!! getLogo('file-img') !!}
																	</div>
																	<input id="hidden_company_logo"
																		name="hidden_company_logo"
																		data-rel="' {{ $result['logo'] }}'" type="hidden">
																	<span name="mySpan" class="remove_logo_preview"
																		id="mySpan"></span>
																</div>
															</div>

															{{-- Favicon --}}
															<div class="settings-field-row">
																<label for="photos[favicon]"
																	class="settings-field-label">Favicon<span
																		class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<div class="settings-upload-zone">
																		<input type="file" name="photos[favicon]"
																			class="form-control settings-input"
																			id="photos[favicon]" placeholder="Favicon">
																		<span
																			class="text-danger f-12">{{ $errors->first('photos[favicon]') }}</span>
																	</div>
																	<div class="settings-preview-area">
																		{!! getFavicon('file-img') !!}
																	</div>
																	<input id="hidden_company_logo"
																		name="hidden_company_logo"
																		data-rel="' {{ $result['logo'] }}'" type="hidden">
																	<span name="mySpan2" class="remove_favicon_preview"
																		id="mySpan2"></span>
																	<input id="hidden_company_favicon"
																		name="hidden_company_favicon"
																		data-rel="{{ $result['favicon'] }}" type="hidden">
																</div>
															</div>
														</div>

														{{-- Section: Integrations & Defaults --}}
														<div class="settings-section">
															<div class="settings-section-label">
																<i class="fa fa-sliders"></i>
																<span>Integrations & Defaults</span>
															</div>

															{{-- Google Analytics --}}
															<div class="settings-field-row">
																<label for="head_code"
																	class="settings-field-label">{{ __('Google Analytics') }}
																	<span class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<input type="text" name="head_code"
																		placeholder="G-XXXXXXXXXX"
																		class="form-control settings-input validate_field"
																		value="{{ $result['head_code'] }}">
																	<small class="text-muted d-block mt-1"
																		style="font-size: 11.5px;">Enter your Google
																		Analytics Measurement ID</small>
																	<span
																		class="text-danger f-12">{{ $errors->first('head_code') }}</span>
																</div>
															</div>

															{{-- Default Currency --}}
															<div class="settings-field-row">
																<label for="default_currency"
																	class="settings-field-label">Default Currency</label>
																<div class="settings-field-input">
																	<select class="form-select settings-input"
																		id="default_currency" name="default_currency">
																		@foreach ($currency as $key => $item)
																			<option value="{{ $key }}" {{ $result['default_currency'] == $key ? 'selected' : '' }}>{{ $item }}</option>
																		@endforeach
																	</select>
																	<span
																		class="text-danger f-12">{{ $errors->first('default_currency') }}</span>
																</div>
															</div>

															{{-- Default Language --}}
															<div class="settings-field-row">
																<label for="default_language"
																	class="settings-field-label">Default Language</label>
																<div class="settings-field-input">
																	<select class="form-select settings-input"
																		id="default_language" name="default_language">
																		@foreach ($language as $key => $item)
																			<option value="{{ $key }}" {{ $result['default_language'] == $key ? 'selected' : '' }}>{{ $item }}</option>
																		@endforeach
																	</select>
																	<span
																		class="text-danger f-12">{{ $errors->first('default_language') }}</span>
																</div>
															</div>
														</div>
													</div>

													{{-- Form Footer --}}
													<div class="settings-form-footer">
														<div class="d-flex align-items-center gap-2">
															<button type="submit" class="btn settings-btn-save">
																<i class="fa fa-check me-2"></i>Save Changes
															</button>
															<a class="btn settings-btn-cancel"
																href="{{ url('admin/settings') }}">Cancel</a>
														</div>
														<small class="text-muted d-none d-md-block"
															style="font-size: 11.5px; opacity: 0.7;">Fields marked with
															<span class="text-danger">*</span> are required</small>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
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