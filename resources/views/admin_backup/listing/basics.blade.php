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
				<div class="col-lg-3 col-12 settings_bar_gap">
					@include('admin.common.property_bar')
				</div>
				<div class="col-lg-9 col-12">
					<form method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="signup-form login-form" accept-charset="UTF-8">
						{{ csrf_field() }}
						<div class="card card-outline card-info shadow-sm">
							<div class="card-header">
								<h3 class="card-title">Rooms and Beds</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Bedrooms</label>
										<select name="bedrooms" id="basics-select-bedrooms" data-saving="basics1" class="form-select f-14">
											@for ($i=1;$i<=10;$i++)
												<option value="{{ $i }}" {{ ($i == $result->bedrooms) ? 'selected' : '' }}>
													{{ $i }}
												</option>
											@endfor
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Beds</label>
										<select name="beds" id="basics-select-beds" data-saving="basics1" class="form-select f-14">
											@for ($i=1;$i<=16;$i++)
												<option value="{{ $i }}" {{ ($i == $result->beds) ? 'selected' : '' }}>
													{{ ($i == '16') ? $i . '+' : $i }}
												</option>
											@endfor
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Bathrooms</label>
										<select name="bathrooms" id="basics-select-bathrooms" data-saving="basics1" class="form-select f-14">
											@for ($i=1;$i<=8;$i++)
												<option class="bathrooms" value="{{ $i }}" {{ ($i == $result->bathrooms) ? 'selected' : '' }}>
													{{ ($i == '8') ? $i . '+' : $i }}
												</option>
											@endfor
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Bed Type</label>
										<select id="basics-select-bed_type" name="bed_type" data-saving="basics1" class="form-select f-14">
											@foreach ($bed_type as $key => $value)
												<option value="{{ $key }}" {{ ($key == $result->bed_type) ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<hr>
								<h5 class="mb-3">Listings</h5>

								<div class="row">
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Property Type</label>
										<select name="property_type" data-saving="basics1" class="form-select f-14">
											@foreach ($property_type as $key => $value)
												<option value="{{ $key }}" {{ ($key == $result->property_type) ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Room Type</label>
										<select name="space_type" data-saving="basics1" class="form-select f-14">
											@foreach ($space_type as $key => $value)
												<option value="{{ $key }}" {{ ($key == $result->space_type) ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Accommodates</label>
										<select name="accommodates" id="basics-select-accommodates" class="form-select f-14">
											@for ($i=1;$i<=16;$i++)
												<option class="accommodates" value="{{ $i }}" {{ ($i == $result->accommodates) ? 'selected' : '' }}>
													{{ ($i == '16') ? $i . '+' : $i }}
												</option>
											@endfor
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Recomended</label>
										<select name="recomended" id="basics-select-recomended" class="form-select f-14">
											<option value="1" {{ ($result->recomended == 1) ? 'selected' : '' }}>Yes</option>
											<option value="0" {{ ($result->recomended == 0) ? 'selected' : '' }}>No</option>
										</select>
									</div>
									<div class="col-md-4 mb-3">
										<label class="fw-bold f-14">Verified</label>
										<select name="verified" class="form-select f-14">
											<option value="Pending" {{ ($result->is_verified == 'Pending') ? 'selected' : '' }}>Pending</option>
											<option value="Approved" {{ ($result->is_verified == 'Approved' || $result->is_verified == '') ? 'selected' : '' }}>Approved</option>
										</select>
									</div>
								</div>
							</div>
							<div class="card-footer text-end">
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
