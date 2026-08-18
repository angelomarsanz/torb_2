@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header py-3">
		<div class="container-fluid">
			<div class="row align-items-center">
				<div class="col-sm-6">
					<h1 class="stunning-page-title mb-0">Add Customer</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content mb-5">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
					<div class="card stunning-table-card">
						<div class="card-header py-3">
							<h3 class="card-title mb-0">Customer Information</h3>
						</div>

						<form class="form-horizontal" action="{{ url('admin/add-customer') }}" id="add_customer" method="post" name="add_customer" accept-charset="UTF-8">
							{{ csrf_field() }}
							<input type="hidden" name="default_country" id="default_country">
							<input type="hidden" name="carrier_code" id="carrier_code">
							<input type="hidden" name="formatted_phone" id="formatted_phone">

							<div class="card-body p-4 p-md-5">
								<div class="row stunning-form-group">
									<label for="first_name" class="col-md-4 col-form-label stunning-label text-md-end pt-2">First Name <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="first_name" id="first_name" placeholder="John">
										<span id="first_name-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="last_name" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Last Name <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="last_name" id="last_name" placeholder="Doe">
										<span id="last_name-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="email" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Email <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="email" id="email" placeholder="john.doe@example.com">
										<span id="email-error" class="text-danger f-12 mt-1 display-block"></span>
										<div id="emailError"></div>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="phone" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Phone</label>
									<div class="col-md-7 col-lg-6">
										<input type="tel" class="form-control stunning-input w-100" id="phone" name="phone">
										<span id="phone-error" class="text-danger f-12 mt-1 display-block"></span>
										<span id="tel-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="password" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Password <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="password" class="form-control stunning-input" name="password" id="password" placeholder="••••••••">
										<span id="password-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group mb-0">
									<label for="status" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Status</label>
									<div class="col-md-7 col-lg-6">
										<select class="form-select stunning-select" name="status" id="status">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-footer bg-light p-4 text-end">
								<button type="reset" class="stunning-btn-secondary me-2">Reset</button>
								<button type="submit" class="stunning-btn-primary" id="submitBtn">Submit</button>
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
<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
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
