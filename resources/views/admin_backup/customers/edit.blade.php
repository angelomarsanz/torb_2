@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header py-3">
		<div class="container-fluid">
			<div class="row align-items-center">
				<div class="col-sm-6">
					<h1 class="stunning-page-title mb-0">Edit Customer</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content mb-5">
		<div class="container-fluid">
			@include('admin.customerdetails.customer_menu')

			<div class="row mt-4">
				<div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
					<div class="card stunning-table-card">
						<div class="card-header py-3">
							<h3 class="card-title mb-0">{{ $form_name ?? 'Update Customer' }}</h3>
						</div>

						<form action="{{ url('admin/edit-customer') }}/{{ $user->id }}" id="edit_customer" method="post" name="signup_form" accept-charset="UTF-8">
							{{ csrf_field() }}
							<input type="hidden" name="customer_id" id="user_id" value="{{ $user->id }}">
							<input type="hidden" name="default_country" id="default_country" value="{{ $user->default_country }}">
							<input type="hidden" name="carrier_code" id="carrier_code" value="{{ $user->carrier_code }}">
							<input type="hidden" name="formatted_phone" id="formatted_phone" value="{{ $user->formatted_phone }}">

							<div class="card-body p-4 p-md-5">
								<div class="row stunning-form-group">
									<label for="first_name" class="col-md-4 col-form-label stunning-label text-md-end pt-2">First Name <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="first_name" id="first_name" value="{{ $user->first_name }}">
										<span id="first_name-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="last_name" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Last Name <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="last_name" id="last_name" value="{{ $user->last_name }}">
										<span id="last_name-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="email" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Email <span class="text-danger">*</span></label>
									<div class="col-md-7 col-lg-6">
										<input type="text" class="form-control stunning-input" name="email" id="email" value="{{ $user->email }}">
										<span id="email-error" class="text-danger f-12 mt-1 display-block"></span>
										<div id="emailError"></div>
										@if ($errors->has('email'))
											<p class="text-danger f-12 mt-1 mb-0">{{ $errors->first('email') }}</p>
										@endif
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="password" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Password</label>
									<div class="col-md-7 col-lg-6">
										<input type="password" class="form-control stunning-input" name="password" id="password" placeholder="Leave blank to keep current">
										<span id="password-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group">
									<label for="phone" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Phone</label>
									<div class="col-md-7 col-lg-6">
										<input type="tel" class="form-control stunning-input w-100" id="phone" name="phone" value="{{ $user->formatted_phone }}">
										<span id="phone-error" class="text-danger f-12 mt-1 display-block"></span>
										<span id="tel-error" class="text-danger f-12 mt-1 display-block"></span>
									</div>
								</div>

								<div class="row stunning-form-group mb-0">
									<label for="status" class="col-md-4 col-form-label stunning-label text-md-end pt-2">Status</label>
									<div class="col-md-7 col-lg-6">
										<select class="form-select stunning-select" name="status" id="status">
											<option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>Active</option>
											<option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-footer bg-light p-4 text-end">
								<button type="reset" class="stunning-btn-secondary me-2">Reset</button>
								<button type="submit" class="stunning-btn-primary" id="submitBtn">Save Changes</button>
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
<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}" type="text/javascript"></script>
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
