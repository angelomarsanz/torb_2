@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-12 mt-0">
							@include('admin.customerdetails.customer_menu')
							<form action="{{ url('admin/edit-customer') }}/{{ $user->id }}" id="edit_customer" method="post"
								name="signup_form" accept-charset="UTF-8">
								{{ csrf_field() }}
								<input type="hidden" name="customer_id" id="user_id" value="{{ $user->id }}">
								<input type="hidden" name="default_country" id="default_country"
									value="{{ $user->default_country }}">
								<input type="hidden" name="carrier_code" id="carrier_code"
									value="{{ $user->carrier_code }}">
								<input type="hidden" name="formatted_phone" id="formatted_phone"
									value="{{ $user->formatted_phone }}">

								<div class="settings-form-card">
									{{-- Form Header --}}
									<div class="settings-form-header">
										<div class="d-flex align-items-center gap-3">
											<div class="settings-form-icon">
												<i class="fa fa-pencil-square-o"></i>
											</div>
											<div>
												<h4 class="settings-form-title">Edit Customer</h4>
												<p class="settings-form-subtitle">Update customer account information</p>
											</div>
										</div>
									</div>

									<div class="settings-form-body p-4">
										<div class="settings-field-row">
											<label for="first_name" class="settings-field-label">First Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="first_name"
													id="first_name" value="{{ $user->first_name }}">
												<span id="first_name-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="last_name" class="settings-field-label">Last Name <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="last_name"
													id="last_name" value="{{ $user->last_name }}">
												<span id="last_name-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="email" class="settings-field-label">Email <span
													class="text-danger">*</span></label>
											<div class="settings-field-input">
												<input type="text" class="form-control settings-input" name="email"
													id="email" value="{{ $user->email }}">
												<span id="email-error" class="text-danger f-12 mt-1 display-block"></span>
												<div id="emailError"></div>
												@if ($errors->has('email'))
													<p class="text-danger f-12 mt-1 mb-0">{{ $errors->first('email') }}</p>
												@endif
											</div>
										</div>

										<div class="settings-field-row">
											<label for="phone" class="settings-field-label">Phone</label>
											<div class="settings-field-input">
												<input type="tel" class="form-control settings-phone-input w-100" id="phone"
													name="phone" value="{{ $user->formatted_phone }}">
												<span id="phone-error" class="text-danger f-12 mt-1 display-block"></span>
												<span id="tel-error" class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row">
											<label for="password" class="settings-field-label">Password</label>
											<div class="settings-field-input">
												<input type="password" class="form-control settings-input" name="password"
													id="password" placeholder="Leave blank to keep current">
												<span id="password-error"
													class="text-danger f-12 mt-1 display-block"></span>
											</div>
										</div>

										<div class="settings-field-row border-bottom-0 pb-0 mb-0">
											<label for="status" class="settings-field-label">Status</label>
											<div class="settings-field-input">
												<select class="form-select settings-input" name="status" id="status">
													<option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>
														Active</option>
													<option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
												</select>
											</div>
										</div>
									</div>

									{{-- Form Footer --}}
									<div class="settings-form-footer">
										<div class="d-flex align-items-center gap-2">
											<button type="submit" class="btn settings-btn-save" id="submitBtn">
												<i class="fa fa-save me-2"></i>Save Changes
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
	<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}"
		type="text/javascript"></script>
	<script src="{{ asset('public/backend/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/backend/dist/js/validate.min.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
		'use strict'
		var hasPhoneError = false;
		var hasEmailError = false;
		let requiredFieldText = "This field is required.";
		let minLengthText = "Please enter at least 6 characters.";
		let maxLengthText = "Please enter no more than 255 characters.";
		let emailExistText = "Email address is already Existed.";
		let checkUserURL = "{{ route('checkUser.check') }}";
		var url = '{{ asset("public/js/intl-tel-input-13.0.0/build/js/utils.js") }}';
		var duplicate_check_url = "{{ url('duplicate-phone-number-check-for-existing-customer') }}";
		var tel_error = '{{ __("Please enter a valid International Phone Number.") }}'
		var token = "{{ csrf_token() }}";
	</script>
	<script src="{{ asset('public/backend/js/customer_edit.min.js') }}"></script>
@endsection