@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">PREFERENCES</h1>
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
										<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">Settings Menu</div>
										@include('admin.common.settings_bar')
									</div>

									<div class="workbench-main">
										@if (Session::has('error'))
											<div class="mb-4">
												<div class="alert alert-warning alert-dismissible fade show shadow-sm" style="border-radius: 8px;" role="alert">
													<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
													<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
												</div>
											</div>
										@endif

										<form id="preferencesform" method="post" action="{{ url('admin/settings/preferences') }}" class="form-horizontal" enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-sliders"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Preferences Setting</h4>
															<p class="settings-form-subtitle">Customize your system behavior, date formats, and timezone</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Display & Search --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-th-list"></i>
															<span>Display & Search</span>
														</div>

														{{-- Row Per Page --}}
														<div class="settings-field-row">
															<label for="row_per_page" class="settings-field-label">Row Per Page <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<select class="form-select settings-input" name="row_per_page" id="row_per_page">
																	@foreach ($row_per_page as $key => $value)
																		<option value="{{ $key }}" {{ $result['row_per_page'] == $key ? 'selected' : '' }}>{{ $value }}</option>
																	@endforeach
																</select>
															</div>
														</div>

														{{-- Min Search Price --}}
														<div class="settings-field-row">
															<label for="min_price" class="settings-field-label">Search Price (Min) <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="number" name="min_search_price" value="{{ isset($result['min_search_price']) ? $result['min_search_price'] : 0 }}" class="form-control settings-input" id="min_price">
																<small class="text-muted mt-1 d-block" style="font-size: 11.5px;">Minimum search price in default currency</small>
															</div>
														</div>

														{{-- Max Search Price --}}
														<div class="settings-field-row">
															<label for="max_price" class="settings-field-label">Search Price (Max) <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="number" value="{{ isset($result['max_search_price']) ? $result['max_search_price'] : 1000 }}" name="max_search_price" class="form-control settings-input" id="max_price">
																<small class="text-muted mt-1 d-block" style="font-size: 11.5px;">Maximum search price in default currency</small>
															</div>
														</div>
													</div>

													{{-- Section: Localization --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-globe"></i>
															<span>Localization</span>
														</div>

														{{-- Date Separator --}}
														<div class="settings-field-row">
															<label for="date_separator" class="settings-field-label">Date Separator</label>
															<div class="settings-field-input">
																<select name="date_separator" class="form-select settings-input">
																	<option value="-" {{ isset($result['date_separator']) && $result['date_separator'] == '-' ? 'selected' : '' }}>-</option>
																	<option value="/" {{ isset($result['date_separator']) && $result['date_separator'] == '/' ? 'selected' : '' }}>/</option>
																	<option value="." {{ isset($result['date_separator']) && $result['date_separator'] == '.' ? 'selected' : '' }}>.</option>
																</select>
															</div>
														</div>

														{{-- Date Format --}}
														<div class="settings-field-row">
															<label for="date_format" class="settings-field-label">Date Format</label>
															<div class="settings-field-input">
																<select name="date_format" class="form-select settings-input">
																	<option value="0" {{ isset($result['date_format']) && $result['date_format'] == 0 ? 'selected' : '' }}>yyyymmdd {2019 12 31}</option>
																	<option value="1" {{ isset($result['date_format']) && $result['date_format'] == 1 ? 'selected' : '' }}>ddmmyyyy {31 12 2019}</option>
																	<option value="2" {{ isset($result['date_format']) && $result['date_format'] == 2 ? 'selected' : '' }}>mmddyyyy {12 31 2019}</option>
																	<option value="3" {{ isset($result['date_format']) && $result['date_format'] == 3 ? 'selected' : '' }}>ddMyyyy &nbsp;&nbsp;&nbsp;{31 Dec 2019}</option>
																	<option value="4" {{ isset($result['date_format']) && $result['date_format'] == 4 ? 'selected' : '' }}>yyyyMdd &nbsp;&nbsp;&nbsp;{2019 Dec 31}</option>
																</select>
															</div>
														</div>

														{{-- TimeZone --}}
														<div class="settings-field-row">
															<label for="dflt_timezone" class="settings-field-label">TimeZone</label>
															<div class="settings-field-input">
																<select class="form-select settings-input" name="dflt_timezone" id="dflt_timezone">
																	@foreach ($timezones as $timezone)
																		<option value="{{ $timezone['zone'] }}" {{ isset($result['dflt_timezone']) && $result['dflt_timezone'] == $timezone['zone'] ? 'selected' : '' }}>
																			{{ $timezone['diff_from_GMT'] . ' - ' . $timezone['zone'] }}
																		</option>
																	@endforeach
																</select>
															</div>
														</div>
													</div>

													{{-- Section: Security & Misc --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-shield"></i>
															<span>Security & Misc</span>
														</div>

														{{-- Google reCaptcha --}}
														<div class="settings-field-row">
															<label for="recaptcha_preference" class="settings-field-label">Google reCaptcha</label>
															<div class="settings-field-input">
																<select class="recaptcha_preference form-select settings-input select2" multiple name="recaptcha_preference[]" id="recaptcha_preference">
																	<option value="disable">Disable</option>
																	<option value="user_login">User Login</option>
																	<option value="user_reg">User Registration</option>
																	<option value="admin_login">Admin Login</option>
																</select>
																<span class="text-danger f-12 recaptchaError"></span>
																@if($errors->has('recaptcha_preference'))
																	<span class="text-danger f-12 d-block">{{ $errors->first('recaptcha_preference') }}</span>
																@endif
															</div>
														</div>

														{{-- Money Symbol Position --}}
														<div class="settings-field-row">
															<label for="money_format" class="settings-field-label">Money Position</label>
															<div class="settings-field-input">
																<select name="money_format" class="form-select settings-input">
																	<option value="before" {{ isset($result['money_format']) && $result['money_format'] == 'before' ? 'selected' : '' }}>Before { $500 }</option>
																	<option value="after" {{ isset($result['money_format']) && $result['money_format'] == 'after' ? 'selected' : '' }}>After { 500$ }</option>
																</select>
															</div>
														</div>

														{{-- Property Approval --}}
														<div class="settings-field-row">
															<label for="property_approval" class="settings-field-label">Property Approval</label>
															<div class="settings-field-input">
																<select name="property_approval" class="form-select settings-input">
																	<option value="Yes" {{ isset($result['property_approval']) && $result['property_approval'] == 'Yes' ? 'selected' : '' }}>Yes</option>
																	<option value="No" {{ isset($result['property_approval']) && $result['property_approval'] == 'No' ? 'selected' : '' }}>No</option>
																</select>
															</div>
														</div>

														{{-- Google Map Key Type --}}
														<div class="settings-field-row">
															<label for="restrict_api_key" class="settings-field-label">Restricted API Key</label>
															<div class="settings-field-input">
																<select name="restrict_api_key" class="form-select settings-input">
																	<option value="Yes" {{ isset($result['restrict_api_key']) && $result['restrict_api_key'] == 'Yes' ? 'selected' : '' }}>Yes</option>
																	<option value="No" {{ isset($result['restrict_api_key']) && $result['restrict_api_key'] == 'No' ? 'selected' : '' }}>No</option>
																</select>
																<small class="text-muted d-block mt-2" style="font-size: 11.5px;">For more details - <a href="https://docs.vrent.techvill.net/google-map-setup/#using-a-restricted-google-maps-api-key" target="_blank" class="text-primary">Read here</a></small>
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
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Fields marked with <span class="text-danger">*</span> are required</small>
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
	var selectedRecaptchaPlace = "{{ !empty(settings('recaptcha_preference')) ? settings('recaptcha_preference') : NULL }}";
	var reCaptchaKey = "{{ !empty(settings('recaptcha_key')) ? settings('recaptcha_key') : NULL }}";
	var reCaptchaSecret = "{{ !empty(settings('recaptcha_secret')) ? settings('recaptcha_secret') : NULL }}";
	var recaptchaPlaceholder = "{{ __('  Selected Preference Place') }}";
	var reCaptchaURL = "{{ url('admin/settings/google-recaptcha-api-information') }}";
	var getreCaptcha = "{{ url('admin/getreCaptchaCredential') }}";
	var hereText = "{{ 'here' }}";
	var errorText = "The credentails of Google reCaptcha is missing, setup reCaptcha credentials in ";
</script>
<script src="{{ asset('public/backend/js/googlereCaptcha.min.js') }}"></script>
@endsection
