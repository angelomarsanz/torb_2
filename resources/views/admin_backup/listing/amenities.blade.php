@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Amenities</h1>
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
								<h3 class="card-title">Property Amenities</h3>
							</div>
							<div class="card-body">
								@foreach ($amenities_type as $row_type)
									<div class="mb-3">
										<h5 class="fw-bold">
											{{ $row_type->name }}
											@if ($row_type->name == 'Common Amenities')
												<span class="text-danger">*</span>
											@endif
										</h5>

										@if ($row_type->description != '')
											<p class="text-muted f-14">{{ $row_type->description }}</p>
										@endif

										<div class="row">
											<div class="col-md-6 col-sm-12 col-12">
												<ul class="list-unstyled f-14">
													@foreach ($amenities as $amenity)
														@if ($amenity->type_id == $row_type->id)
															<li class="mb-2">
																<label class="label-inline amenity-label">
																	<input type="checkbox" value="{{ $amenity->id }}" name="amenities[]" data-saving="{{ $row_type->id }}" {{ in_array($amenity->id, $property_amenities) ? 'checked' : '' }}> &nbsp;
																	<span>{{ $amenity->title }}</span>
																</label>
																@if ($amenity->description != '')
																	<span data-bs-toggle="tooltip" class="icon" title="{{ $amenity->description }}"></span>
																@endif
															</li>
														@endif
													@endforeach
												</ul>
											</div>
										</div>
									</div>
								@endforeach
								<p id="error"></p>
							</div>
							<div class="card-footer d-flex justify-content-between">
								<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/location') }}" class="btn btn-outline-secondary f-14">
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
