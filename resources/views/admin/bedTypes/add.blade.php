@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">ADD BED TYPE</h1>
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

										<form id="add_bed" method="post" action="{{ url('admin/settings/add-bed-type') }}"
											class="form-horizontal" enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">

												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-bed"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Add Bed Type</h4>
															<p class="settings-form-subtitle">Register a new bed
																configuration option for property listings</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Bed Details --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-info-circle"></i>
															<span>Bed Configuration</span>
														</div>

														{{-- Name --}}
														<div class="settings-field-row">
															<label for="name" class="settings-field-label">Name <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="name"
																	class="form-control settings-input" id="name"
																	placeholder="King, Queen, Double, Single..."
																	value="{{ old('name') }}">
																<span
																	class="text-danger f-12">{{ $errors->first("name") }}</span>
															</div>
														</div>

														{{-- Status --}}
														<div class="settings-field-row">
															<label for="status" class="settings-field-label">Status <span
																	class="text-danger">*</span></label>
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
														<button type="submit" class="btn settings-btn-save">
															Submit
														</button>
														<a class="btn settings-btn-cancel"
															href="{{ url('admin/settings/bed-type') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block"
														style="font-size: 11.5px; opacity: 0.7;">This will be selectable by
														hosts when adding room details</small>
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
@endsection