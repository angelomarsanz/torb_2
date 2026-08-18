@extends('admin.template')
@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner">
					<h1 class="dashboard-page-title">EDIT REVIEW</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<div class="settings-form-card">
								<div class="settings-form-header">
									<div class="d-flex align-items-center gap-3">
										<div class="settings-form-icon">
											<i class="fa fa-star"></i>
										</div>
										<div>
											<h4 class="settings-form-title">Edit Review</h4>
											<p class="settings-form-subtitle">Moderate and update the details of this review
											</p>
										</div>
									</div>
								</div>

								<div class="settings-form-body">
									@if (Session::has('error'))
										<div class="alert alert-warning alert-dismissible fade show mx-4 mt-4" role="alert">
											<strong>Warning!</strong> Please verify your information below.
											<button type="button" class="btn-close" data-bs-dismiss="alert"
												aria-label="Close"></button>
										</div>
									@endif

									<form id="rev_form" action="{{ url('admin/edit_review/' . $result->id) }}" method="post"
										class="form-horizontal">
										{{ csrf_field() }}

										{{-- Read-only Info --}}
										<div class="settings-section">
											<div class="settings-section-label"><i class="fa fa-info-circle"></i> Review
												Info</div>

											<div class="settings-field-row">
												<label class="settings-field-label">Booking ID</label>
												<div class="settings-field-input">
													<p class="mb-0 f-14 text-dark fw-500" style="padding-top:0.55rem;">
														{{ $result->booking_id }}</p>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label">Property</label>
												<div class="settings-field-input">
													<p class="mb-0 f-14 text-dark fw-500" style="padding-top:0.55rem;">
														{{ $result->property_name }}</p>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label">Guest</label>
												<div class="settings-field-input">
													<p class="mb-0 f-14 text-dark fw-500" style="padding-top:0.55rem;">
														{{ $result->sender }}</p>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label">Host</label>
												<div class="settings-field-input">
													<p class="mb-0 f-14 text-dark fw-500" style="padding-top:0.55rem;">
														{{ $result->receiver }}</p>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label">Reviewed By</label>
												<div class="settings-field-input">
													@php
														$reviewer = strtolower($result->reviewer);
														$badgeClass = ($reviewer == 'host') ? 'status-accepted' : 'status-processing';
														$icon = ($reviewer == 'host') ? 'fa-user-circle' : 'fa-user';
													@endphp
													<span class="status-badge {{ $badgeClass }} mt-1 d-inline-flex"><i
															class="fa {{ $icon }}"></i> {{ ucfirst($reviewer) }}</span>
												</div>
											</div>
										</div>

										{{-- Editable Rating Fields (guest reviews only) --}}
										@if ($result->reviewer == 'guest')
											<div class="settings-section">
												<div class="settings-section-label"><i class="fa fa-star-half-o"></i> Ratings
													<span class="text-muted f-12 ms-1 text-lowercase"
														style="font-weight:400;">(1 – 5)</span></div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="rating">Overall Rating</label>
													<div class="settings-field-input">
														<input type="number" name="rating" id="rating" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->rating }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="accuracy">Accuracy</label>
													<div class="settings-field-input">
														<input type="number" name="accuracy" id="accuracy" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->accuracy }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="location">Location</label>
													<div class="settings-field-input">
														<input type="number" name="location" id="location" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->location }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label"
														for="communication">Communication</label>
													<div class="settings-field-input">
														<input type="number" name="communication" id="communication" min="1"
															max="5" class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->communication }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="checkin">Check In</label>
													<div class="settings-field-input">
														<input type="number" name="checkin" id="checkin" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->checkin }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="cleanliness">Cleanliness</label>
													<div class="settings-field-input">
														<input type="number" name="cleanliness" id="cleanliness" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->cleanliness }}">
													</div>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label" for="value">Value</label>
													<div class="settings-field-input">
														<input type="number" name="value" id="value" min="1" max="5"
															class="form-control settings-input" style="max-width:120px;"
															value="{{ $result->value }}">
													</div>
												</div>
											</div>
										@endif

										{{-- Message --}}
										<div class="settings-section">
											<div class="settings-section-label"><i class="fa fa-comment-o"></i> Review
												Message</div>
											<div class="settings-field-row">
												<label class="settings-field-label" for="message">Message <span
														class="text-danger">*</span></label>
												<div class="settings-field-input">
													<textarea name="message" id="message"
														class="form-control settings-input" rows="5"
														placeholder="Review message…">{{ $result->message }}</textarea>
													<span
														class="text-danger f-12 mt-1 d-block">{{ $errors->first('message') }}</span>
												</div>
											</div>
										</div>

										<div class="settings-form-footer">
											<div></div>
											<div class="d-flex gap-2">
												<button type="submit" name="cancel" value="cancel"
													class="btn settings-btn-cancel"><i class="fa fa-times me-1"></i>
													Cancel</button>
												<button type="submit" name="submit" value="submit"
													class="btn settings-btn-save"><i class="fa fa-check me-1"></i> Save
													Changes</button>
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
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection