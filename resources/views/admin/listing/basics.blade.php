@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">LIST YOUR SPACE</h1>
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
													<i class="fa fa-list-ul"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Listing Basics</h4>
													<p class="settings-form-subtitle">Configure the core physical details of your property</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											{{-- Section: Rooms and Beds --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-bed"></i>
													<span>Rooms and Beds</span>
												</div>

												{{-- Bedrooms --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Bedrooms</label>
													<div class="settings-field-input">
														<select name="bedrooms" id="basics-select-bedrooms" class="form-select settings-input">
															@for ($i=1;$i<=10;$i++)
																<option value="{{ $i }}" {{ ($i == $result->bedrooms) ? 'selected' : '' }}>
																	{{ $i }}
																</option>
															@endfor
														</select>
													</div>
												</div>

												{{-- Beds --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Beds</label>
													<div class="settings-field-input">
														<select name="beds" id="basics-select-beds" class="form-select settings-input">
															@for ($i=1;$i<=16;$i++)
																<option value="{{ $i }}" {{ ($i == $result->beds) ? 'selected' : '' }}>
																	{{ ($i == '16') ? $i . '+' : $i }}
																</option>
															@endfor
														</select>
													</div>
												</div>

												{{-- Bathrooms --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Bathrooms</label>
													<div class="settings-field-input">
														<select name="bathrooms" id="basics-select-bathrooms" class="form-select settings-input">
															@for ($i=1;$i<=8;$i++)
																<option class="bathrooms" value="{{ $i }}" {{ ($i == $result->bathrooms) ? 'selected' : '' }}>
																	{{ ($i == '8') ? $i . '+' : $i }}
																</option>
															@endfor
														</select>
													</div>
												</div>

												{{-- Bed Type --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Bed Type</label>
													<div class="settings-field-input">
														<select id="basics-select-bed_type" name="bed_type" class="form-select settings-input">
															@foreach ($bed_type as $key => $value)
																<option value="{{ $key }}" {{ ($key == $result->bed_type) ? 'selected' : '' }}>{{ $value }}</option>
															@endforeach
														</select>
													</div>
												</div>
											</div>

											{{-- Section: Listing Configuration --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-cog"></i>
													<span>Listing Configuration</span>
												</div>

												{{-- Property Type --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Property Type</label>
													<div class="settings-field-input">
														<select name="property_type" class="form-select settings-input">
															@foreach ($property_type as $key => $value)
																<option value="{{ $key }}" {{ ($key == $result->property_type) ? 'selected' : '' }}>{{ $value }}</option>
															@endforeach
														</select>
													</div>
												</div>

												{{-- Room Type --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Room Type</label>
													<div class="settings-field-input">
														<select name="space_type" class="form-select settings-input">
															@foreach ($space_type as $key => $value)
																<option value="{{ $key }}" {{ ($key == $result->space_type) ? 'selected' : '' }}>{{ $value }}</option>
															@endforeach
														</select>
													</div>
												</div>

												{{-- Accommodates --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Accommodates</label>
													<div class="settings-field-input">
														<select name="accommodates" id="basics-select-accommodates" class="form-select settings-input">
															@for ($i=1;$i<=16;$i++)
																<option class="accommodates" value="{{ $i }}" {{ ($i == $result->accommodates) ? 'selected' : '' }}>
																	{{ ($i == '16') ? $i . '+' : $i }}
																</option>
															@endfor
														</select>
													</div>
												</div>

												{{-- Recommended --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Recommended</label>
													<div class="settings-field-input">
														<select name="recomended" id="basics-select-recomended" class="form-select settings-input">
															<option value="1" {{ ($result->recomended == 1) ? 'selected' : '' }}>Yes</option>
															<option value="0" {{ ($result->recomended == 0) ? 'selected' : '' }}>No</option>
														</select>
													</div>
												</div>

												{{-- Verified Status --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Status</label>
													<div class="settings-field-input">
														<select name="verified" class="form-select settings-input">
															<option value="Pending" {{ ($result->is_verified == 'Pending') ? 'selected' : '' }}>Pending</option>
															<option value="Approved" {{ ($result->is_verified == 'Approved' || $result->is_verified == '') ? 'selected' : '' }}>Approved</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										{{-- Form Footer --}}
										<div class="settings-form-footer">
											<div class="d-flex align-items-center gap-2">
												<button type="submit" class="btn settings-btn-save">
													Next <i class="fa fa-arrow-right ms-1 f-12"></i>
												</button>
											</div>
											<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Fill out basis details to proceed to Description</small>
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
