@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Booking</h1>
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
								<h3 class="card-title">Choose How Your Guests Book</h3>
							</div>
							<div class="card-body">
								<p class="text-muted f-14">Get ready for guests by choosing your booking style.</p>
								<div class="row mb-3">
									<div class="col-md-8 col-12">
										<label class="fw-bold f-14">Booking Type <span class="text-danger">*</span></label>
										<select name="booking_type" id="select-booking_type" class="form-select f-14 mt-1">
											<option value="request" {{ ($result->booking_type == 'request') ? 'selected' : '' }}>Review each request</option>
											<option value="instant" {{ ($result->booking_type == 'instant') ? 'selected' : '' }}>Guests book instantly</option>
										</select>
									</div>
								</div>
							</div>
							<div class="card-footer d-flex justify-content-between">
								<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/pricing') }}" class="btn btn-outline-secondary f-14">
									<i class="fa fa-arrow-left me-1"></i> Back
								</a>
								<button type="submit" class="btn btn-info text-white f-14">
									<i class="fa fa-check me-1"></i> Complete
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
