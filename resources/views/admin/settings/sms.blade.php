@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">SMS SETTINGS</h1>
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

										<form id="smsform" method="post" action="{{ url('admin/settings/sms') }}" class="form-horizontal" enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-commenting-o"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Twilio SMS Configuration</h4>
															<p class="settings-form-subtitle">Configure your Twilio account to enable SMS notifications</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">
													<input class="form-control settings-input" type="hidden" name="default_country" id="default_country" value="{{ isset($phoneSms['default_country']) ? $phoneSms['default_country'] : '' }}">
													<input class="form-control settings-input" type="hidden" name="carrier_code" id="carrier_code" value="{{ isset($phoneSms['carrier_code']) ? $phoneSms['carrier_code'] : '' }}">
													<input class="form-control settings-input" type="hidden" name="formatted_phone" id="formatted_phone" value="{{ isset($phoneSms['formatted_phone']) ? $phoneSms['formatted_phone'] : '' }}">

													{{-- Section: Twilio Credentials --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-key"></i>
															<span>API Credentials</span>
														</div>

														{{-- Twilio Phone Number --}}
														<div class="settings-field-row">
															<label for="phone" class="settings-field-label">Twilio Number <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="tel" class="form-control settings-input" id="phone" name="phone" value="{{ isset($phoneSms['formatted_phone']) ? $phoneSms['formatted_phone'] : '' }}">
																<span id="phone-error" class="text-danger f-12"></span>
																<span id="tel-error" class="text-danger f-12"></span>
															</div>
														</div>

														{{-- Twilio SID --}}
														<div class="settings-field-row">
															<label for="sid" class="settings-field-label">Twilio SID <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input class="form-control settings-input" type="text" name="twilio_sid" id="sid" placeholder="ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" value="{{ isset($phoneSms['twilio_sid']) ? $phoneSms['twilio_sid'] : '' }}">
															</div>
														</div>

														{{-- Twilio Token --}}
														<div class="settings-field-row">
															<label for="token" class="settings-field-label">Twilio Token <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input class="form-control settings-input" type="text" name="twilio_token" id="token" placeholder="Token" value="{{ isset($phoneSms['twilio_token']) ? $phoneSms['twilio_token'] : '' }}">
															</div>
														</div>
													</div>

													{{-- Section: Preferences --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-toggle-on"></i>
															<span>Settings & Status</span>
														</div>

														{{-- Defaults --}}
														<div class="settings-field-row">
															<label for="defaults" class="settings-field-label">Set as Default</label>
															<div class="settings-field-input">
																<select name="defaults" class="form-select settings-input">
																	<option value="no" {{ isset($phoneSms['defaults']) && $phoneSms['defaults'] == 'no' ? 'selected' : '' }}>No</option>
																	<option value="yes" {{ isset($phoneSms['defaults']) && $phoneSms['defaults'] == 'yes' ? 'selected' : '' }}>Yes</option>
																</select>
															</div>
														</div>

														{{-- Status --}}
														<div class="settings-field-row">
															<label for="status" class="settings-field-label">Status</label>
															<div class="settings-field-input">
																<select name="status" class="form-select settings-input">
																	<option value="0" {{ isset($phoneSms['status']) && $phoneSms['status'] == '0' ? 'selected' : '' }}>Inactive</option>
																	<option value="1" {{ isset($phoneSms['status']) && $phoneSms['status'] == '1' ? 'selected' : '' }}>Active</option>
																</select>
															</div>
														</div>
													</div>
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save" id="submitBtn">
															<i class="fa fa-check me-2"></i>Save Changes
														</button>
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings/sms') }}">Cancel</a>
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
