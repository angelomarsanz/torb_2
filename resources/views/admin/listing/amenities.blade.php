@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">AMENITIES</h1>
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
								<form method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="form-horizontal" accept-charset="UTF-8">
									{{ csrf_field() }}
									<div class="settings-form-card">
										
										{{-- Form Header --}}
										<div class="settings-form-header">
											<div class="d-flex align-items-center gap-3">
												<div class="settings-form-icon">
													<i class="fa fa-bullseye"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Property Amenities</h4>
													<p class="settings-form-subtitle">Select the features and services available to your guests</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											@foreach ($amenities_type as $row_type)
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-dot-circle-o"></i>
														<span>
															{{ $row_type->name }}
															@if ($row_type->name == 'Common Amenities')
																<span class="text-danger">*</span>
															@endif
														</span>
													</div>

													@if ($row_type->description != '')
														<p class="text-muted f-13 mb-3" style="margin-top: -0.25rem;">{{ $row_type->description }}</p>
													@endif

													<div class="row">
														@foreach ($amenities as $amenity)
															@if ($amenity->type_id == $row_type->id)
																<div class="col-md-6 col-lg-4 mb-2">
																	<label class="d-flex align-items-center gap-2 cursor-pointer f-14 mb-0 py-1">
																		<input type="checkbox" class="settings-checkbox" value="{{ $amenity->id }}" name="amenities[]" data-saving="{{ $row_type->id }}" {{ in_array($amenity->id, $property_amenities) ? 'checked' : '' }}>
																		<span class="text-slate-700">{{ $amenity->title }}</span>
																		@if ($amenity->description != '')
																			<i class="fa fa-info-circle text-muted f-12" data-bs-toggle="tooltip" title="{{ $amenity->description }}"></i>
																		@endif
																	</label>
																</div>
															@endif
														@endforeach
													</div>
												</div>
											@endforeach
											<p id="error" class="text-danger f-12 px-4"></p>
										</div>

										{{-- Form Footer --}}
										<div class="settings-form-footer">
											<div class="d-flex align-items-center gap-2">
												<a href="{{ url('admin/listing/' . $result->id . '/location') }}" class="btn settings-btn-cancel">
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
