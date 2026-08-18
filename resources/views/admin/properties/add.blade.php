@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">LIST YOUR SPACE</h1>
					<div class="dashboard-header-right">
						@include('admin.common.breadcrumb')
					</div>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
								<div class="card-body p-0 pt-md-3">
									<form id="add_pr" method="post" action="{{ url('admin/add-properties') }}"
										accept-charset="UTF-8">
										{{ csrf_field() }}
										<div class="settings-form-card shadow-none border-0 mb-0">

											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-home"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Property Details</h4>
														<p class="settings-form-subtitle">Basic information to get started
															with your listing</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">
												<input type="hidden" name="street_number" id="street_number">
												<input type="hidden" name="route" id="route">
												<input type="hidden" name="postal_code" id="postal_code">
												<input type="hidden" name="city" id="city">
												<input type="hidden" name="state" id="state">
												<input type="hidden" name="country" id="country">
												<input type="hidden" name="latitude" id="latitude">
												<input type="hidden" name="longitude" id="longitude">

												{{-- Section: Ownership & Type --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-user"></i>
														<span>Ownership & Property Type</span>
													</div>

													{{-- Host selection --}}
													<div class="settings-field-row">
														<label for="host_id" class="settings-field-label">User <span
																class="text-danger">*</span></label>
														<div class="settings-field-input">
															<div class="d-flex align-items-start gap-2">
																<div class="flex-grow-1" id="respo">
																	<select id="host_id" name="host_id"
																		class="form-select settings-input">
																		<option value="">Select</option>
																		@foreach($users as $key => $value)
																			<option value="{{ $value->id }}">
																				{{ $value->first_name . ' ' . $value->last_name }}
																			</option>
																		@endforeach
																	</select>
																</div>
																<button type="button" data-bs-toggle="modal"
																	data-bs-target="#customerModal"
																	class="btn settings-btn-save p-0 flex-shrink-0 d-flex align-items-center justify-content-center"
																	style="height: 40px !important; width: 40px !important; min-width: 40px !important; border-radius: 8px !important;">
																	<i class="fa fa-plus" style="font-size: 15px;"></i>
																</button>
															</div>
															@if ($errors->has('host_id'))
																<div class="mt-1">
																	<span class="text-danger f-12 fw-bold"><i
																			class="fa fa-exclamation-circle me-1"></i>{{ $errors->first('host_id') }}</span>
																</div>
															@endif
														</div>
													</div>

													{{-- Home Type --}}
													<div class="settings-field-row">
														<label for="property_type_id" class="settings-field-label">Home
															Type</label>
														<div class="settings-field-input">
															<select name="property_type_id"
																class="form-select settings-input">
																@foreach($property_type as $key => $value)
																	<option value="{{ $key }}">{{ $value }}</option>
																@endforeach
															</select>
															@if ($errors->has('property_type_id'))
																<span
																	class="text-danger f-12">{{ $errors->first('property_type_id') }}</span>
															@endif
														</div>
													</div>

													{{-- Room Type --}}
													<div class="settings-field-row">
														<label for="space_type" class="settings-field-label">Room
															Type</label>
														<div class="settings-field-input">
															<select name="space_type" class="form-select settings-input">
																@foreach($space_type as $key => $value)
																	<option value="{{ $key }}">{{ $value }}</option>
																@endforeach
															</select>
															@if ($errors->has('space_type'))
																<span
																	class="text-danger f-12">{{ $errors->first('space_type') }}</span>
															@endif
														</div>
													</div>
												</div>

												{{-- Section: Capacity & Location --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-map-marker"></i>
														<span>Capacity & Location</span>
													</div>

													{{-- Accommodates --}}
													<div class="settings-field-row">
														<label for="accommodates"
															class="settings-field-label">Accommodates</label>
														<div class="settings-field-input">
															<select name="accommodates" class="form-select settings-input">
																@for ($i = 1; $i <= 16; $i++)
																	<option class="accommodates"
																		data-accommodates="{{ ($i == '16') ? $i . '+' : $i }}"
																		value="{{ ($i == '16') ? $i . '+' : $i }}">{{ $i }}
																	</option>
																@endfor
															</select>
															@if ($errors->has('accommodates'))
																<span
																	class="text-danger f-12">{{ $errors->first('accommodates') }}</span>
															@endif
														</div>
													</div>

													{{-- City/Address --}}
													<div class="settings-field-row">
														<label for="map_address" class="settings-field-label">City <span
																class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" class="form-control settings-input"
																id="map_address" name="map_address"
																placeholder="Enter a city or address">
															<div class="mt-1"><small class="text-muted"
																	style="font-size: 11px;">Select a location from the
																	suggestions for accurate mapping</small></div>
															@if ($errors->has('map_address'))
																<div class="mt-1">
																	<span class="text-danger f-12 fw-bold"><i
																			class="fa fa-exclamation-circle me-1"></i>{{ $errors->first('map_address') }}</span>
																</div>
															@endif
															<div id="us3"></div>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														Continue <i class="fa fa-arrow-right ms-1 f-12"></i>
													</button>
													<button type="reset" class="btn settings-btn-cancel">Reset</button>
												</div>
												<small class="text-muted d-none d-md-block"
													style="font-size: 11.5px; opacity: 0.7;">You will be able to add more
													details in the next steps</small>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content rounded-4 border-0 shadow-lg">
					<div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
						<h5 class="modal-title fw-bold text-dark" id="customerModalLabel" style="font-size: 1.25rem;">Create
							New Customer</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body p-0">
						<form class="form-horizontal" id="signup_form" method="post" name="signup_form"
							action="{{ url('admin/add-ajax-customer') }}" accept-charset="UTF-8">
							{{ csrf_field() }}

							<div class="settings-form-card border-0 mb-0 shadow-none">
								<div class="settings-form-body pt-2">
									<input type="hidden" name="default_country" id="default_country" class="form-control">
									<input type="hidden" name="carrier_code" id="carrier_code" class="form-control">
									<input type="hidden" name="formatted_phone" id="formatted_phone" class="form-control">

									<div class="settings-section">
										<div class="settings-section-label">
											<i class="fa fa-info-circle"></i>
											<span>Basic Information</span>
										</div>

										{{-- First Name --}}
										<div class="settings-field-row">
											<label for="first_name" class="settings-field-label">First Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="first_name"
													id="first_name" placeholder="Enter first name">
											</div>
										</div>

										{{-- Last Name --}}
										<div class="settings-field-row">
											<label for="last_name" class="settings-field-label">Last Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="last_name"
													id="last_name" placeholder="Enter last name">
											</div>
										</div>

										{{-- Email --}}
										<div class="settings-field-row">
											<label for="email" class="settings-field-label">Email <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="email" class="form-control settings-input" name="email"
													id="email" placeholder="customer@example.com">
												<div id="emailError"></div>
											</div>
										</div>
									</div>

									<div class="settings-section">
										<div class="settings-section-label">
											<i class="fa fa-phone"></i>
											<span>Contact & Security</span>
										</div>

										{{-- Phone --}}
										<div class="settings-field-row">
											<label for="phone" class="settings-field-label">Phone</label>
											<div class="settings-field-input">
												<input type="tel" class="form-control settings-input" id="phone"
													name="phone">
												<span id="phone-error" class="text-danger f-12"></span>
												<span id="tel-error" class="text-danger f-12"></span>
											</div>
										</div>

										{{-- Password --}}
										<div class="settings-field-row">
											<label for="password" class="settings-field-label">Password <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="password" class="form-control settings-input" name="password"
													id="password" placeholder="Min 6 characters">
											</div>
										</div>

										{{-- Status --}}
										<div class="settings-field-row">
											<label for="status" class="settings-field-label">Status</label>
											<div class="settings-field-input">
												<select class="form-select settings-input" name="status" id="status">
													<option value="Active">Active</option>
													<option value="Inactive">Inactive</option>
												</select>
											</div>
										</div>
									</div>
								</div>

								<div class="settings-form-footer rounded-bottom-4">
									<div class="d-flex align-items-center gap-2">
										<button type="submit" id="customerModalBtn" class="btn settings-btn-save">Create
											Customer</button>
										<button type="button" class="btn settings-btn-cancel"
											data-bs-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('validate_script')
	<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}"
		type="text/javascript"></script>
	<script src="{{ asset('public/backend/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
	<script type="text/javascript">

		let validEmailText = "Please enter a valid email address.";
		let checkUserURL = "{{ route('checkUser.check') }}";
		var token = "{{ csrf_token() }}";
		let emailExistText = "Email address is already Existed.";
		let validInternationalNumber = "Please enter a valid International Phone Number.";
		let numberExists = "The number has already been taken!";
		let signedUpText = "Sign Up..";
		let baseURL = "{{ url('/') }}";
		let duplicateNumberCheckURL = "{{ url('duplicate-phone-number-check') }}";
		// STUB: intercept .locationpicker() calls before the real plugin loads.
		// The minified JS calls it on doc.ready, but Google Maps + plugin load async.
		// We cache the call and re-init once the real plugin is available.
		var _locationPickerPending = null;
		$.fn.locationpicker = function(options) {
			if (typeof options === 'string') return;
			_locationPickerPending = { el: this, options: options };
		};

		// Called by foot.blade.php after real locationpicker plugin loads
		window.onLocationPickerReady = function () {
			if (_locationPickerPending) {
				_locationPickerPending.el.locationpicker(_locationPickerPending.options);
			}
		};
	</script>
	<script src="{{ asset('public/backend/js/add_customer_for_properties.min.js') }}" type="text/javascript"></script>

@endsection