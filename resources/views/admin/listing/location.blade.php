@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">LOCATION</h1>
				<div class="dashboard-header-right">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-lg-3 col-12 settings_bar_gap">
						@include('admin.common.property_bar')
					</div>

					<div class="col-lg-9 col-12">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<form id="listing_location" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="form-horizontal" accept-charset="UTF-8">
									{{ csrf_field() }}
									<div class="settings-form-card">
										
										{{-- Form Header --}}
										<div class="settings-form-header">
											<div class="d-flex align-items-center gap-3">
												<div class="settings-form-icon">
													<i class="fa fa-map-marker"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Property Location</h4>
													<p class="settings-form-subtitle">Pinpoint the exact location of your space</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											<input type="hidden" name="latitude" id="latitude">
											<input type="hidden" name="longitude" id="longitude">

											{{-- Section: Address --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-globe"></i>
													<span>Address Details</span>
												</div>

												{{-- Country --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Country <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<select name="country" class="form-select settings-input" id="country">
															@foreach ($country as $key => $value)
																<option value="{{ $key }}" {{ ($key == $result->property_address->country) ? 'selected' : '' }}>{{ $value }}</option>
															@endforeach
														</select>
														<span class="text-danger f-12">{{ $errors->first('country') }}</span>
													</div>
												</div>

												{{-- Address Line 1 --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Address Line 1 <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<input type="text" name="address_line_1" id="address_line_1" value="{{ $result->property_address->address_line_1 }}" class="form-control settings-input" placeholder="House name/number + street/road">
														<span class="text-danger f-12">{{ $errors->first('address_line_1') }}</span>
													</div>
												</div>

												{{-- Map View --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Map Position</label>
													<div class="settings-field-input">
														<div id="map_view" class="rounded-3 border shadow-sm" style="width:100%; height:400px; background: #f8fafc;"></div>
														<p class="text-muted f-13 mt-2 mb-0">
															<i class="fa fa-info-circle me-1"></i> You can move the pointer to set the correct map position
														</p>
														<span class="text-danger f-12">{{ $errors->first('latitude') }}</span>
													</div>
												</div>

												{{-- Address Line 2 --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Address Line 2</label>
													<div class="settings-field-input">
														<input type="text" name="address_line_2" id="address_line_2" value="{{ $result->property_address->address_line_2 }}" class="form-control settings-input" placeholder="Apt., suite, building access code">
													</div>
												</div>

												{{-- City --}}
												<div class="settings-field-row">
													<label class="settings-field-label">City / Town <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<input type="text" name="city" id="city" value="{{ $result->property_address->city }}" class="form-control settings-input">
														<span class="text-danger f-12">{{ $errors->first('city') }}</span>
													</div>
												</div>

												{{-- State --}}
												<div class="settings-field-row">
													<label class="settings-field-label">State / Region <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<input type="text" name="state" id="state" value="{{ $result->property_address->state }}" class="form-control settings-input">
														<span class="text-danger f-12">{{ $errors->first('state') }}</span>
													</div>
												</div>

												{{-- Postal Code --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Postal Code</label>
													<div class="settings-field-input">
														<input type="text" name="postal_code" id="postal_code" value="{{ $result->property_address->postal_code }}" class="form-control settings-input">
														<span class="text-danger f-12">{{ $errors->first('postal_code') }}</span>
													</div>
												</div>
											</div>
										</div>

										{{-- Form Footer --}}
										<div class="settings-form-footer">
											<div class="d-flex align-items-center gap-2">
												<a href="{{ url('admin/listing/' . $result->id . '/description') }}" class="btn settings-btn-cancel">
													<i class="fa fa-arrow-left me-1 f-12"></i> Back
												</a>
												<button type="submit" class="btn settings-btn-save">
													Next <i class="fa fa-arrow-right ms-1 f-12"></i>
												</button>
											</div>
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
<script type="text/javascript" src="{{ asset('public/js/listings.js') }}"></script>
@endsection
