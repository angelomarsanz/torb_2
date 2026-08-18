@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Location</h1>
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
				<div class="col-lg-3 col-12 settings_bar_gap">
					@include('admin.common.property_bar')
				</div>
				<div class="col-lg-9 col-12">
					<form id="listing_location" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="signup-form login-form" accept-charset="UTF-8">
						{{ csrf_field() }}
						<div class="card card-outline card-info shadow-sm">
							<div class="card-header">
								<h3 class="card-title">Property Location</h3>
							</div>
							<div class="card-body">
								<input type="hidden" name="latitude" id="latitude">
								<input type="hidden" name="longitude" id="longitude">
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Country <span class="text-danger">*</span></label>
										<select name="country" class="form-select f-14" id="country">
											@foreach ($country as $key => $value)
												<option value="{{ $key }}" {{ ($key == $result->property_address->country) ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
										<span class="text-danger f-12">{{ $errors->first('country') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Address Line 1 <span class="text-danger">*</span></label>
										<input type="text" name="address_line_1" id="address_line_1" value="{{ $result->property_address->address_line_1 }}" class="form-control f-14" placeholder="House name/number + street/road">
										<span class="text-danger f-12">{{ $errors->first('address_line_1') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<div id="map_view" style="width:100%; height:400px;"></div>
									</div>
									<div class="col-md-8 mt-2">
										<p class="text-muted f-14">You can move the pointer to set the correct map position</p>
										<span class="text-danger f-12">{{ $errors->first('latitude') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Address Line 2</label>
										<input type="text" name="address_line_2" id="address_line_2" value="{{ $result->property_address->address_line_2 }}" class="form-control f-14" placeholder="Apt., suite, building access code">
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">City / Town / District <span class="text-danger">*</span></label>
										<input type="text" name="city" id="city" value="{{ $result->property_address->city }}" class="form-control f-14">
										<span class="text-danger f-12">{{ $errors->first('city') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">State / Province / County / Region <span class="text-danger">*</span></label>
										<input type="text" name="state" id="state" value="{{ $result->property_address->state }}" class="form-control f-14">
										<span class="text-danger f-12">{{ $errors->first('state') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">ZIP / Postal Code</label>
										<input type="text" name="postal_code" id="postal_code" value="{{ $result->property_address->postal_code }}" class="form-control f-14">
										<span class="text-danger f-12">{{ $errors->first('postal_code') }}</span>
									</div>
								</div>
							</div>
							<div class="card-footer d-flex justify-content-between">
								<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/description') }}" class="btn btn-outline-secondary f-14">
									<i class="fa fa-arrow-left me-1"></i> Back
								</a>
								<button type="submit" class="btn btn-info text-white f-14">
									<i class="fa fa-arrow-right me-1"></i> Next
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script type="text/javascript">
	'use strict'
	var page = 'location';
	let fieldRequiredText = "{{ __('This field is required.') }}";
	let maxlengthText = "{{ __('Please enter no more than 255 characters.') }}";
	let latitude = "{{ $result->property_address->latitude != '' ? $result->property_address->latitude:0 }}";
	let longitude = "{{ $result->property_address->longitude != '' ? $result->property_address->longitude:0 }}";
</script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/js/listings.min.js') }}"></script>
@endsection
