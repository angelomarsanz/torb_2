@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">CUSTOMERS</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<form action="{{ url('admin/add-customer') }}" id="add_customer" method="post"
								name="add_customer" accept-charset="UTF-8">
								{{ csrf_field() }}
								<input type="hidden" name="default_country" id="default_country">
								<input type="hidden" name="carrier_code" id="carrier_code">
								<input type="hidden" name="formatted_phone" id="formatted_phone">

								<div class="settings-form-card">
									{{-- Form Header --}}
									<div class="settings-form-header">
										<div class="d-flex align-items-center gap-3">
											<div class="settings-form-icon">
												<i class="fa fa-user-plus"></i>
											</div>
											<div>
												<h4 class="settings-form-title">Add Customer</h4>
												<p class="settings-form-subtitle">Register a new customer account in the
													system</p>
											</div>
										</div>
									</div>

									<div class="settings-form-body p-4">
										<div class="settings-field-row">
											<label for="first_name" class="settings-field-label">First Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="first_name"
													id="first_name" placeholder="John">
												<span id="first_name-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="last_name" class="settings-field-label">Last Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="last_name"
													id="last_name" placeholder="Doe">
												<span id="last_name-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="email" class="settings-field-label">Email <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="email"
													id="email" placeholder="john.doe@example.com">
												<span id="email-error" class="text-danger f-12 mt-1 display-block"></span>
												<div id="emailError"></div>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="phone" class="settings-field-label">Phone</label>
											<div class="settings-field-input">
												<input type="tel" class="form-control settings-phone-input w-100" id="phone"
													name="phone">
												<span id="phone-error" class="text-danger f-12 mt-1 display-block"></span>
												<span id="tel-error" class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="password" class="settings-field-label">Password <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="password" class="form-control settings-input" name="password"
													id="password" placeholder="••••••••">
												<span id="password-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row border-bottom-0 pb-0 mb-0">
											<label for="status" class="settings-field-label">Status</label>
											<div class="settings-field-input">
												<select class="form-select settings-input" name="status" id="status">
													<option value="Active">Active</option>
													<option value="Inactive">Inactive</option>
												</select>
											</div>
										</div>
									</div>

									{{-- Form Footer --}}
									<div class="settings-form-footer">
										<div class="d-flex align-items-center gap-2">
											<button type="submit" class="btn settings-btn-save" id="submitBtn">
												Submit
											</button>
											<a class="btn settings-btn-cancel"
												href="{{ url('admin/customers') }}">Cancel</a>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
@endsection

@section('validate_script')
	<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}"
		type="text/javascript"></script>
	<script src="{{ asset('public/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/backend/dist/js/validate.min.js') }}" type="text/javascript"></script>
	<script>
		'use strict'
		let requiredFieldText = "This field is required.";
		let minLengthText = "Please enter at least 6 characters.";
		let maxLengthText = "Please enter no more than 255 characters.";
		let oldLimitationText = "Age must be greater than 18.";
		let validEmailText = "Please enter a valid email address.";
		let checkUserURL = "{{ route('checkUser.check') }}";
		var token = "{{ csrf_token() }}";
		let emailExistText = "Email address is already Existed.";
		let validInternationalNumber = "Please enter a valid International Phone Number.";
		let numberExists = "The number has already been taken!";
		let signedUpText = "Sign Up..";
		let baseURL = "{{ url('/') }}";
		let duplicateNumberCheckURL = "{{ url('duplicate-phone-number-check') }}";
		let minAge = "You are not old enough!";

	</script>
	<script type="text/javascript" src="{{ asset('public/js/sign-up-login.min.js') }}"></script>

@endsection