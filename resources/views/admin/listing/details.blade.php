@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">DESCRIPTION DETAILS</h1>
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
													<i class="fa fa-info-circle"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Detailed Description</h4>
													<p class="settings-form-subtitle">Provide in-depth information about the experience</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											{{-- Section: The Trip --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-suitcase"></i>
													<span>The Trip</span>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">About Place</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="about_place" rows="4" placeholder="Highlight what makes your space unique...">{{ $result->property_description->about_place }}</textarea>
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Great for</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="place_is_great_for" rows="4" placeholder="e.g. Couples, solo travelers, business trips...">{{ $result->property_description->place_is_great_for }}</textarea>
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Guest Access</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="guest_can_access" rows="4" placeholder="Describe which areas guests can use...">{{ $result->property_description->guest_can_access }}</textarea>
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Guest Interaction</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="interaction_guests" rows="4" placeholder="How much interaction will you have with guests?">{{ $result->property_description->interaction_guests }}</textarea>
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Other Notes</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="other" rows="4" placeholder="Any other important information...">{{ $result->property_description->other }}</textarea>
													</div>
												</div>
											</div>

											{{-- Section: The Neighborhood --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-map-o"></i>
													<span>The Neighborhood</span>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Overview</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="about_neighborhood" rows="4" placeholder="Describe the local area and its vibes...">{{ $result->property_description->about_neighborhood }}</textarea>
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Getting Around</label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="get_around" rows="4" placeholder="Public transport, parking, walking distance to attractions...">{{ $result->property_description->get_around }}</textarea>
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
