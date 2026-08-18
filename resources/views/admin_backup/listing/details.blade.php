@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Description Details</h1>
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
								<h3 class="card-title">The Trip</h3>
							</div>
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">About Place</label>
										<textarea class="form-control f-14" name="about_place" rows="4" placeholder="">{{ $result->property_description->about_place }}</textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Place is great for</label>
										<textarea class="form-control f-14" name="place_is_great_for" rows="4" placeholder="">{{ $result->property_description->place_is_great_for }}</textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Guest Access</label>
										<textarea class="form-control f-14" name="guest_can_access" rows="4" placeholder="">{{ $result->property_description->guest_can_access }}</textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Interaction with Guests</label>
										<textarea class="form-control f-14" name="interaction_guests" rows="4" placeholder="">{{ $result->property_description->interaction_guests }}</textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Other Things to Note</label>
										<textarea class="form-control f-14" name="other" rows="4" placeholder="">{{ $result->property_description->other }}</textarea>
									</div>
								</div>

								<hr>
								<h5 class="mb-3">The Neighborhood</h5>

								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Overview</label>
										<textarea class="form-control f-14" name="about_neighborhood" rows="4" placeholder="">{{ $result->property_description->about_neighborhood }}</textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8">
										<label class="fw-bold f-14">Getting Around</label>
										<textarea class="form-control f-14" name="get_around" rows="4" placeholder="">{{ $result->property_description->get_around }}</textarea>
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
