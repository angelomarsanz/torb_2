@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Edit Review</h1>
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
						@if (Session::has('error'))
							<div class="p-3 pb-0">
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<strong>Warning!</strong> Please verify your information below.
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
							</div>
						@endif

						<div class="card-header">
							<h3 class="card-title">Edit Review Form</h3>
						</div>

						<form id="rev_form" action="{{ url('admin/edit_review/' . $result->id) }}" method="post" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="booking_id" class="col-md-3 col-form-label text-md-end fw-bold">Booking Id</label>
									<div class="col-md-6">
										<p class="mb-0 f-14">{{ $result->booking_id }}</p>
									</div>
								</div>

								<div class="row mb-3">
									<label for="property_name" class="col-md-3 col-form-label text-md-end fw-bold">Property Name</label>
									<div class="col-md-6">
										<p class="mb-0 f-14">{{ $result->property_name }}</p>
									</div>
								</div>

								<div class="row mb-3">
									<label for="sender" class="col-md-3 col-form-label text-md-end fw-bold">Guest</label>
									<div class="col-md-6">
										<p class="mb-0 f-14">{{ $result->sender }}</p>
									</div>
								</div>

								<div class="row mb-3">
									<label for="receiver" class="col-md-3 col-form-label text-md-end fw-bold">Host</label>
									<div class="col-md-6">
										<p class="mb-0 f-14">{{ $result->receiver }}</p>
									</div>
								</div>

								<div class="row mb-3">
									<label for="reviewer" class="col-md-3 col-form-label text-md-end fw-bold">Reviewed By</label>
									<div class="col-md-6">
										<p class="mb-0 f-14">{{ $result->reviewer }}</p>
									</div>
								</div>

								@if ($result->reviewer == 'guest')
									<div class="row mb-3">
										<label for="rating" class="col-md-3 col-form-label text-md-end fw-bold">Rating</label>
										<div class="col-md-6">
											<input type="number" name="rating" id="rating" min="1" max="5" class="form-control f-14" value="{{ $result->rating }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="accuracy" class="col-md-3 col-form-label text-md-end fw-bold">Accuracy</label>
										<div class="col-md-6">
											<input type="number" name="accuracy" id="accuracy" min="1" max="5" class="form-control f-14" value="{{ $result->accuracy }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="location" class="col-md-3 col-form-label text-md-end fw-bold">Location</label>
										<div class="col-md-6">
											<input type="number" name="location" id="location" min="1" max="5" class="form-control f-14" value="{{ $result->location }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="communication" class="col-md-3 col-form-label text-md-end fw-bold">Communication</label>
										<div class="col-md-6">
											<input type="number" name="communication" id="communication" min="1" max="5" class="form-control f-14" value="{{ $result->communication }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="checkin" class="col-md-3 col-form-label text-md-end fw-bold">Check In</label>
										<div class="col-md-6">
											<input type="number" name="checkin" id="checkin" min="1" max="5" class="form-control f-14" value="{{ $result->checkin }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="cleanliness" class="col-md-3 col-form-label text-md-end fw-bold">Cleanliness</label>
										<div class="col-md-6">
											<input type="number" name="cleanliness" id="cleanliness" min="1" max="5" class="form-control f-14" value="{{ $result->cleanliness }}" />
										</div>
									</div>

									<div class="row mb-3">
										<label for="value" class="col-md-3 col-form-label text-md-end fw-bold">Value</label>
										<div class="col-md-6">
											<input type="number" name="value" id="value" min="1" max="5" class="form-control f-14" value="{{ $result->value }}" />
										</div>
									</div>
								@endif

								<div class="row mb-3">
									<label for="message" class="col-md-3 col-form-label text-md-end fw-bold">Message <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<textarea name="message" id="message" class="form-control f-14" rows="4">{{ $result->message }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('message') }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<button type="submit" name="cancel" value="cancel" class="btn btn-outline-secondary f-14 me-2">Cancel</button>
								<button type="submit" name="submit" value="submit" class="btn btn-info text-white f-14">Submit</button>
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
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
