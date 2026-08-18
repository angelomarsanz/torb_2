@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">ADD BANNER</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-12 mt-0">
							<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
								<div class="card-body p-4 pt-3">
									<div class="workbench-container">
										
										<div class="workbench-sidebar">
											<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">Settings Menu</div>
											@include('admin.common.settings_bar')
										</div>

										<div class="workbench-main">
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

										<form id="add_banners" method="post"
											action="{{ url('admin/settings/add-banners') }}" class="form-horizontal"
											enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">

												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-image"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Add Banner</h4>
															<p class="settings-form-subtitle">Create a new hero banner for
																the homepage carousel</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Content --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-font"></i>
															<span>Banner Content</span>
														</div>

														{{-- Heading --}}
														<div class="settings-field-row">
															<label for="heading" class="settings-field-label">Heading <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="heading"
																	class="form-control settings-input" id="heading"
																	placeholder="Main title of the banner"
																	value="{{ old('heading') }}">
																<span
																	class="text-danger f-12">{{ $errors->first("heading") }}</span>
															</div>
														</div>

														{{-- Subheading --}}
														<div class="settings-field-row">
															<label for="subheading"
																class="settings-field-label">Subheading</label>
															<div class="settings-field-input">
																<input type="text" name="subheading"
																	class="form-control settings-input" id="subheading"
																	placeholder="Brief subtitle text"
																	value="{{ old('subheading') }}">
																<span
																	class="text-danger f-12">{{ $errors->first("subheading") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Media --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-picture-o"></i>
															<span>Banner Media</span>
														</div>

														{{-- Image --}}
														<div class="settings-field-row">
															<label for="image" class="settings-field-label">Image <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="file" name="image"
																	class="form-control settings-input" id="image"
																	accept="image/*">
																<span
																	class="text-danger f-12">{{ $errors->first('image') }}</span>
																<small class="text-muted d-block mt-1"
																	style="font-size: 11px;">Recommended Dimensions:
																	1920x860px</small>
															</div>
														</div>
													</div>

													{{-- Section: Visibility --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-toggle-on"></i>
															<span>Configuration</span>
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

														{{-- Default --}}
														<div class="settings-field-row">
															<label for="default" class="settings-field-label">Default
																Banner</label>
															<div class="settings-field-input">
																<select class="form-select settings-input" id="default"
																	name="default">
																	<option value="Yes">Yes</option>
																	<option value="No">No</option>
																</select>
																<span
																	class="text-danger f-12">{{ $errors->first('default') }}</span>
															</div>
														</div>
													</div>
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save">
															Submit
														</button>
														<a class="btn settings-btn-cancel"
															href="{{ url('admin/settings/banners') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block"
														style="font-size: 11.5px; opacity: 0.7;">Banners will be displayed
														in the order of creation</small>
												</div>
											</div>
										</form>
									</div>
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
	<script src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection