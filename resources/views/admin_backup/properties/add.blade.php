@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">List Your Space</h1>
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
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title">List Your Space</h3>
						</div>

						<form id="add_pr" class="form-horizontal" method="post" action="{{ url('admin/add-properties') }}" accept-charset="UTF-8">
							{{ csrf_field() }}

							<div class="card-body">
								<input type="hidden" name="street_number" id="street_number">
								<input type="hidden" name="route" id="route">
								<input type="hidden" name="postal_code" id="postal_code">
								<input type="hidden" name="city" id="city">
								<input type="hidden" name="state" id="state">
								<input type="hidden" name="country" id="country">
								<input type="hidden" name="latitude" id="latitude">
								<input type="hidden" name="longitude" id="longitude">

								<div class="row mb-3">
									<label for="host_id" class="col-md-3 col-form-label text-md-end fw-bold">User <span class="text-danger">*</span></label>
									<div class="col-md-5" id="respo">
										<select id="host_id" name="host_id" class="form-select f-14">
											<option value="">Select</option>
											@foreach($users as $key => $value)
												<option value="{{ $value->id }}">{{ $value->first_name . ' ' . $value->last_name }}</option>
											@endforeach
										</select>
										@if ($errors->has('host_id'))
											<span class="text-danger f-12">{{ $errors->first('host_id') }}</span>
										@endif
									</div>
									<div class="col-md-2">
										<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#customerModal" class="btn btn-primary btn-sm"><span class="fa fa-user"></span></a>
									</div>
								</div>

								<div class="row mb-3">
									<label for="property_type_id" class="col-md-3 col-form-label text-md-end fw-bold">Home Type</label>
									<div class="col-md-5">
										<select name="property_type_id" class="form-select f-14">
											@foreach($property_type as $key => $value)
												<option value="{{ $key }}">{{ $value }}</option>
											@endforeach
										</select>
										@if ($errors->has('property_type_id'))
											<span class="text-danger f-12">{{ $errors->first('property_type_id') }}</span>
										@endif
									</div>
								</div>

								<div class="row mb-3">
									<label for="space_type" class="col-md-3 col-form-label text-md-end fw-bold">Room Type</label>
									<div class="col-md-5">
										<select name="space_type" class="form-select f-14">
											@foreach($space_type as $key => $value)
												<option value="{{ $key }}">{{ $value }}</option>
											@endforeach
										</select>
										@if ($errors->has('space_type'))
											<span class="text-danger f-12">{{ $errors->first('space_type') }}</span>
										@endif
									</div>
								</div>

								<div class="row mb-3">
									<label for="accommodates" class="col-md-3 col-form-label text-md-end fw-bold">Accommodates</label>
									<div class="col-md-5">
										<select name="accommodates" class="form-select f-14">
											@for ($i=1;$i<=16;$i++)
												<option class="accommodates" data-accommodates="{{ ($i == '16') ? $i . '+' : $i }}" value="{{ ($i == '16') ? $i . '+' : $i }}">{{ $i }}</option>
											@endfor
										</select>
										@if ($errors->has('accommodates'))
											<span class="text-danger f-12">{{ $errors->first('accommodates') }}</span>
										@endif
									</div>
								</div>

								<div class="row mb-3">
									<label for="map_address" class="col-md-3 col-form-label text-md-end fw-bold">City <span class="text-danger">*</span></label>
									<div class="col-md-5">
										<input type="text" class="form-control f-14" id="map_address" name="map_address" placeholder="">
										@if ($errors->has('map_address'))
											<span class="text-danger f-12">{{ $errors->first('map_address') }}</span>
										@endif
									</div>
									<div id="us3"></div>
								</div>
							</div>

							<div class="card-footer text-end">
								<button type="reset" class="btn btn-outline-secondary f-14 me-2">Reset</button>
								<button type="submit" class="btn btn-info text-white f-14">Continue</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="customerModalLabel">Add Customer</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="signup_form" method="post" name="signup_form" action="{{ url('admin/add-ajax-customer') }}" accept-charset="UTF-8">
						{{ csrf_field() }}

						<h4 class="text-info text-center mb-3">Customer Information</h4>
						<input type="hidden" name="default_country" id="default_country" class="form-control">
						<input type="hidden" name="carrier_code" id="carrier_code" class="form-control">
						<input type="hidden" name="formatted_phone" id="formatted_phone" class="form-control">

						<div class="row mb-3">
							<label for="first_name" class="col-sm-3 col-form-label text-sm-end fw-bold">First Name <span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input type="text" class="form-control f-14" name="first_name" id="first_name" placeholder="">
							</div>
						</div>

						<div class="row mb-3">
							<label for="last_name" class="col-sm-3 col-form-label text-sm-end fw-bold">Last Name <span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input type="text" class="form-control f-14" name="last_name" id="last_name" placeholder="">
							</div>
						</div>

						<div class="row mb-3">
							<label for="email" class="col-sm-3 col-form-label text-sm-end fw-bold">Email <span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input type="text" class="form-control f-14" name="email" id="email" placeholder="">
								<div id="emailError"></div>
							</div>
						</div>

						<div class="row mb-3">
							<label for="phone" class="col-sm-3 col-form-label text-sm-end fw-bold">Phone</label>
							<div class="col-sm-8">
								<input type="tel" class="form-control f-14" id="phone" name="phone">
								<span id="phone-error" class="text-danger f-12"></span>
								<span id="tel-error" class="text-danger f-12"></span>
							</div>
						</div>

						<div class="row mb-3">
							<label for="password" class="col-sm-3 col-form-label text-sm-end fw-bold">Password <span class="text-danger">*</span></label>
							<div class="col-sm-8">
								<input type="password" class="form-control f-14" name="password" id="password" placeholder="">
							</div>
						</div>

						<div class="row mb-3">
							<label for="status" class="col-sm-3 col-form-label text-sm-end fw-bold">Status</label>
							<div class="col-sm-8">
								<select class="form-select f-14" name="status" id="status">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>

						<div class="modal-footer">
							<button type="submit" id="customerModalBtn" class="btn btn-info text-white f-14">Submit</button>
							<button type="button" class="btn btn-outline-secondary f-14" data-bs-dismiss="modal">Close</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/js/intl-tel-input-13.0.0/build/js/intlTelInput.js') }}" type="text/javascript"></script>
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
</script>
<script src="{{ asset('public/backend/js/add_customer_for_properties.min.js') }}" type="text/javascript"></script>

@endsection
