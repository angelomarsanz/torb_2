@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">SMS Settings</h1>
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
							<h3 class="card-title">Twilio SMS Configuration</h3>
						</div>

						<form id="smsform" method="post" action="{{ url('admin/settings/sms') }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<input class="form-control f-14" type="hidden" name="default_country" id="default_country" value="{{ isset($phoneSms['default_country)']) ? $phoneSms['default_country'] : '' }}">
								<input class="form-control f-14" type="hidden" name="carrier_code" id="carrier_code" value="{{ isset($phoneSms['carrier_code']) ? $phoneSms['carrier_code'] : '' }}">
								<input class="form-control f-14" type="hidden" name="formatted_phone" id="formatted_phone" value="{{ isset($phoneSms['formatted_phone']) ? $phoneSms['formatted_phone'] : '' }}">

								<div class="row mb-3">
									<label for="phone" class="col-md-3 col-form-label text-md-end fw-bold">Twilio Phone Number <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="tel" class="form-control f-14" id="phone" name="phone" value="{{ isset($phoneSms['formatted_phone']) ? $phoneSms['formatted_phone'] : '' }}">
										<span id="phone-error" class="text-danger f-12"></span>
										<span id="tel-error" class="text-danger f-12"></span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="sid" class="col-md-3 col-form-label text-md-end fw-bold">Twilio SID <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input class="form-control f-14" type="text" name="twilio_sid" id="sid" placeholder="Twilio SID" value="{{ isset($phoneSms['twilio_sid']) ? $phoneSms['twilio_sid'] : '' }}">
									</div>
								</div>

								<div class="row mb-3">
									<label for="token" class="col-md-3 col-form-label text-md-end fw-bold">Twilio Token <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input class="form-control f-14" type="text" name="twilio_token" id="token" placeholder="Twilio Token" value="{{ isset($phoneSms['twilio_token']) ? $phoneSms['twilio_token'] : '' }}">
									</div>
								</div>

								<div class="row mb-3">
									<label for="defaults" class="col-md-3 col-form-label text-md-end fw-bold">Defaults</label>
									<div class="col-md-6">
										<select name="defaults" class="form-select f-14">
											<option value="no" {{ isset($phoneSms['defaults']) && $phoneSms['defaults'] == 'no' ? 'selected' : '' }}>No</option>
											<option value="yes" {{ isset($phoneSms['defaults']) && $phoneSms['defaults'] == 'yes' ? 'selected' : '' }}>Yes</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="status" class="col-md-3 col-form-label text-md-end fw-bold">Status</label>
									<div class="col-md-6">
										<select name="status" class="form-select f-14">
											<option value="0" {{ isset($phoneSms['status']) && $phoneSms['status'] == '0' ? 'selected' : '' }}>Inactive</option>
											<option value="1" {{ isset($phoneSms['status']) && $phoneSms['status'] == '1' ? 'selected' : '' }}>Active</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								@if (Request::segment(3) == 'email' || Request::segment(3) == '' || Request::segment(3) == 'api_informations' || Request::segment(3) == 'payment_methods' || Request::segment(3) == 'social_links')
									<a class="btn btn-outline-secondary f-14" href="{{ url('admin/settings') }}">Cancel</a>
								@else
									<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/sms') }}">Cancel</a>
									<button type="submit" class="btn btn-info f-14 text-white" id="submitBtn">Submit</button>
								@endif
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
<script type="text/javascript" src="{{ asset('public/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}"></script>
<script src="{{ asset('public/backend/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	'use strict'
	let utilsScript = '{{ asset("public/js/intl-tel-input-13.0.0/build/js/utils.js") }}';
	var countryData = $("#phone").intlTelInput("getSelectedCountryData");
	let validNumberText = "{{ __('Please enter a valid International Phone Number.') }}";
</script>
<script src="{{ asset('public/backend/js/sms.min.js') }}" type="text/javascript"></script>
@endsection
