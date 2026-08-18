@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">TESTIMONIALS</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
								<div class="card-body p-4 pt-3">
									<div class="workbench-main w-100 ps-0">
										@if (Session::has('error'))
											<div class="mb-4">
												<div class="alert alert-warning alert-dismissible fade show shadow-sm"
													style="border-radius: 8px;" role="alert">
													<strong>Warning!</strong> Whoops there was an error. Please verify your
													below information.
													<button type="button" class="btn-close" data-bs-dismiss="alert"
														aria-label="Close"></button>
												</div>
											</div>
										@endif

										<form id="add_testimonials" method="post"
											action="{{ url('admin/add-testimonials') }}" class="form-horizontal"
											enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">

												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-quote-left"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Add Testimonial</h4>
															<p class="settings-form-subtitle">Create a new customer story to
																display on the homepage</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Reviewer Details --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-user"></i>
															<span>Reviewer Details</span>
														</div>

														{{-- Name --}}
														<div class="settings-field-row">
															<label for="name" class="settings-field-label">Name <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="name"
																	class="form-control settings-input" id="name"
																	placeholder="Enter Reviewer Name.."
																	value="{{ old('name') }}">
																<span
																	class="text-danger f-12">{{ $errors->first("name") }}</span>
															</div>
														</div>

														{{-- Designation --}}
														<div class="settings-field-row">
															<label for="designation"
																class="settings-field-label">Designation <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="designation"
																	class="form-control settings-input" id="designation"
																	placeholder="Reviewer Designation.."
																	value="{{ old('designation') }}">
																<span
																	class="text-danger f-12">{{ $errors->first("designation") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Review Content --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-comment"></i>
															<span>Review Content</span>
														</div>

														{{-- Description --}}
														<div class="settings-field-row align-items-start">
															<label for="description"
																class="settings-field-label pt-2">Description <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<textarea name="description" id="description"
																	class="form-control settings-input" rows="4"
																	placeholder="Description..">{{ old('description') }}</textarea>
																<span
																	class="text-danger f-12">{{ $errors->first("description") }}</span>
															</div>
														</div>

														{{-- Rating --}}
														<div class="settings-field-row">
															<label class="settings-field-label">Rating <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input pt-1">
																<input type="hidden" name="rating_1" id="rating">
																<div class="d-flex gap-1 rating-stars-container">
																	@for ($i = 1; $i <= 5; $i++)
																		<i id="rating-{{ $i }}"
																			data-value="{{ $i }}"
																			class="fa fa-star rating-star-item"
																			style="font-size: 1.2rem;"></i>
																	@endfor
																</div>
																<span
																	class="text-danger f-12 d-block mt-1">{{ $errors->first('rating_1') }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Media & Status --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-cog"></i>
															<span>Media & Configuration</span>
														</div>

														{{-- Image --}}
														<div class="settings-field-row">
															<label for="image" class="settings-field-label">Image <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="file" name="image"
																	class="form-control settings-input" id="image">
																<span
																	class="text-danger f-12">{{ $errors->first("image") }}</span>
															</div>
														</div>

														{{-- Status --}}
														<div class="settings-field-row">
															<label for="status" class="settings-field-label">Status</label>
															<div class="settings-field-input">
																<select class="form-select settings-input" id="status"
																	name="status">
																	<option value="Active">Active</option>
																	<option value="Inactive">Inactive</option>
																</select>
																<span
																	class="text-danger f-12">{{ $errors->first('status') }}</span>
															</div>
														</div>
													</div>
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save" id="submitBtn">
															<i class="fa fa-check me-2"></i>Submit
														</button>
														<a class="btn settings-btn-cancel"
															href="{{ url('admin/testimonials') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block"
														style="font-size: 11.5px; opacity: 0.7;">Fields marked with <span
															class="text-danger">*</span> are required</small>
												</div>
											</div>
										</form>
									</div>
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

	<script type="text/javascript">
		$(document).ready(function() {
			$(document).on('click', '.rating-star-item', function() {
				var value = $(this).data('value');
				$('#rating').val(value);
				
				// Update visual state
				$('.rating-star-item').removeClass('rating-star-active');
				$('.rating-star-item').each(function() {
					if ($(this).data('value') <= value) {
						$(this).addClass('rating-star-active');
					}
				});
			});
		});
	</script>
@endsection