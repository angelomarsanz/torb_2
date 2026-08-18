@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">ADD COUNTRY</h1>
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
											<div class="alert alert-warning alert-dismissible fade show shadow-sm" style="border-radius: 8px;" role="alert">
												<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										</div>
									@endif

									<form id="add_country" method="post" action="{{ url('admin/settings/add-country') }}" class="form-horizontal">
										{{ csrf_field() }}
										<div class="settings-form-card">
											
											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-globe"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Add Country</h4>
														<p class="settings-form-subtitle">Register a new country in the system for listings and addresses</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Country Identity --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-map-o"></i>
														<span>Country Details</span>
													</div>

													{{-- Short Name --}}
													<div class="settings-field-row">
														<label for="short_name" class="settings-field-label">Short Name <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="short_name" class="form-control settings-input" id="short_name" placeholder="US" value="{{ old('short_name') }}">
															<span class="text-danger f-12">{{ $errors->first("short_name") }}</span>
														</div>
													</div>

													{{-- Long Name --}}
													<div class="settings-field-row">
														<label for="name" class="settings-field-label">Long Name <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="name" class="form-control settings-input" id="name" placeholder="United States" value="{{ old('name') }}">
															<span class="text-danger f-12">{{ $errors->first("name") }}</span>
														</div>
													</div>
												</div>

												{{-- Section: Identification Codes --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-hashtag"></i>
														<span>Identification Codes</span>
													</div>

													{{-- ISO3 --}}
													<div class="settings-field-row">
														<label for="iso3" class="settings-field-label">ISO3 <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="iso3" class="form-control settings-input" id="iso3" placeholder="USA" value="{{ old('iso3') }}">
															<span class="text-danger f-12">{{ $errors->first("iso3") }}</span>
														</div>
													</div>

													{{-- Num Code --}}
													<div class="settings-field-row">
														<label for="number_code" class="settings-field-label">Num Code <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="number_code" class="form-control settings-input" id="number_code" placeholder="840" value="{{ old('number_code') }}">
															<span class="text-danger f-12">{{ $errors->first("number_code") }}</span>
														</div>
													</div>

													{{-- Phone Code --}}
													<div class="settings-field-row">
														<label for="phone_code" class="settings-field-label">Phone Code <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="phone_code" class="form-control settings-input" id="phone_code" placeholder="1" value="{{ old('phone_code') }}">
															<span class="text-danger f-12">{{ $errors->first("phone_code") }}</span>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														<i class="fa fa-plus me-2"></i>Add Country
													</button>
													<a class="btn settings-btn-cancel" href="{{ url('admin/settings/country') }}">Cancel</a>
												</div>
												<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Fill all required codes accurately</small>
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
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
