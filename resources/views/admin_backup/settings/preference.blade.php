@extends('admin.template')

@push('css')
<link href="{{ asset('public/backend/css/preferences.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Preferences</h1>
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
						<div class="card-header">
							<h3 class="card-title">Preferences Setting Form</h3>
						</div>

						<form id="preferencesform" method="post" action="{{ url('admin/settings/preferences') }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="row_per_page" class="col-md-3 col-form-label text-md-end fw-bold">Row Per Page <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<select class="form-select f-14" name="row_per_page" id="row_per_page">
											@foreach ($row_per_page as $key => $value)
												<option value="{{ $key }}" {{ $result['row_per_page'] == $key ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="min_price" class="col-md-3 col-form-label text-md-end fw-bold">Search Price (Min) <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="number" name="min_search_price" value="{{ isset($result['min_search_price']) ? $result['min_search_price'] : 0 }}" class="form-control f-14" id="min_price">
										<small class="text-muted">In default currency</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="max_price" class="col-md-3 col-form-label text-md-end fw-bold">Search Price (Max) <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="number" value="{{ isset($result['max_search_price']) ? $result['max_search_price'] : 1000 }}" name="max_search_price" class="form-control f-14" id="max_price">
										<small class="text-muted">In default currency & greater than min price</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="date_separator" class="col-md-3 col-form-label text-md-end fw-bold">Date Separator:</label>
									<div class="col-md-6">
										<select name="date_separator" class="form-select f-14">
											<option value="-" {{ isset($result['date_separator']) && $result['date_separator'] == '-' ? 'selected' : '' }}>-</option>
											<option value="/" {{ isset($result['date_separator']) && $result['date_separator'] == '/' ? 'selected' : '' }}>/</option>
											<option value="." {{ isset($result['date_separator']) && $result['date_separator'] == '.' ? 'selected' : '' }}>.</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="date_format" class="col-md-3 col-form-label text-md-end fw-bold">Date Format:</label>
									<div class="col-md-6">
										<select name="date_format" class="form-select f-14">
											<option value="0" {{ isset($result['date_format']) && $result['date_format'] == 0 ? 'selected' : '' }}>yyyymmdd {2019 12 31}</option>
											<option value="1" {{ isset($result['date_format']) && $result['date_format'] == 1 ? 'selected' : '' }}>ddmmyyyy {31 12 2019}</option>
											<option value="2" {{ isset($result['date_format']) && $result['date_format'] == 2 ? 'selected' : '' }}>mmddyyyy {12 31 2019}</option>
											<option value="3" {{ isset($result['date_format']) && $result['date_format'] == 3 ? 'selected' : '' }}>ddMyyyy &nbsp;&nbsp;&nbsp;{31 Dec 2019}</option>
											<option value="4" {{ isset($result['date_format']) && $result['date_format'] == 4 ? 'selected' : '' }}>yyyyMdd &nbsp;&nbsp;&nbsp;{2019 Dec 31}</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="dflt_timezone" class="col-md-3 col-form-label text-md-end fw-bold">TimeZone</label>
									<div class="col-md-6">
										<select class="form-select f-14" name="dflt_timezone" id="dflt_timezone">
											@foreach ($timezones as $timezone)
												<option value="{{ $timezone['zone'] }}" {{ isset($result['dflt_timezone']) && $result['dflt_timezone'] == $timezone['zone'] ? 'selected' : '' }}>
													{{ $timezone['diff_from_GMT'] . ' - ' . $timezone['zone'] }}
												</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="recaptcha_preference" class="col-md-3 col-form-label text-md-end fw-bold">Google reCaptcha</label>
									<div class="col-md-6">
										<select class="recaptcha_preference form-select f-14 select2" multiple name="recaptcha_preference[]" id="recaptcha_preference">
											<option value="disable">Disable</option>
											<option value="user_login">User Login</option>
											<option value="user_reg">User Registration</option>
											<option value="admin_login">Admin Login</option>
										</select>
										<span>
											<strong class="text-danger recaptchaError"></strong>
										</span>
										@if($errors->has('recaptcha_preference'))
											<span class="text-danger f-12">{{ $errors->first('recaptcha_preference') }}</span>
										@endif
									</div>
								</div>

								<div class="row mb-3">
									<label for="money_format" class="col-md-3 col-form-label text-md-end fw-bold">Money Symbol Position:</label>
									<div class="col-md-6">
										<select name="money_format" class="form-select f-14 select2">
											<option value="before" {{ isset($result['money_format']) && $result['money_format'] == 'before' ? 'selected' : '' }}>Before { $500 }</option>
											<option value="after" {{ isset($result['money_format']) && $result['money_format'] == 'after' ? 'selected' : '' }}>After { 500$ }</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="property_approval" class="col-md-3 col-form-label text-md-end fw-bold">Property Approval:</label>
									<div class="col-md-6">
										<select name="property_approval" class="form-select f-14 select2">
											<option value="Yes" {{ isset($result['property_approval']) && $result['property_approval'] == 'Yes' ? 'selected' : '' }}>Yes</option>
											<option value="No" {{ isset($result['property_approval']) && $result['property_approval'] == 'No' ? 'selected' : '' }}>No</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="restrict_api_key" class="col-md-3 col-form-label text-md-end fw-bold">Using restricted Google Map API key:</label>
									<div class="col-md-6">
										<select name="restrict_api_key" class="form-select f-14 select2">
											<option value="Yes" {{ isset($result['restrict_api_key']) && $result['restrict_api_key'] == 'Yes' ? 'selected' : '' }}>Yes</option>
											<option value="No" {{ isset($result['restrict_api_key']) && $result['restrict_api_key'] == 'No' ? 'selected' : '' }}>No</option>
										</select>
										<small class="text-muted">If using Restricted API key then you will need non-restricted GEOCode API key. For more details - <a href="https://docs.vrent.techvill.net/google-map-setup/#using-a-restricted-google-maps-api-key" target="_blank">Read here</a></small>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/preferences') }}">Cancel</a>
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
