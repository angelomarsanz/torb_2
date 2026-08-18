@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">API CREDENTIALS</h1>
			</div>
		</section>

		<section class="content">
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

										<form id="api_credentials" method="post" action="{{ url('admin/settings/api-informations') }}" class="form-horizontal">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-key"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Api Credentials</h4>
															<p class="settings-form-subtitle">Manage your third-party API keys and client secrets</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Social Authenticators --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-facebook-square"></i>
															<span>Facebook Credentials</span>
														</div>

														{{-- Facebook Client ID --}}
														<div class="settings-field-row">
															<label for="facebook_client_id" class="settings-field-label">Client ID <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="facebook_client_id" class="form-control settings-input" id="facebook_client_id" placeholder="Facebook Client ID" value="{{ $facebook['client_id'] }}">
																<span class="text-danger f-12">{{ $errors->first("facebook_client_id") }}</span>
															</div>
														</div>

														{{-- Facebook Client Secret --}}
														<div class="settings-field-row">
															<label for="facebook_client_secret" class="settings-field-label">Client Secret <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="facebook_client_secret" class="form-control settings-input" id="facebook_client_secret" placeholder="Facebook Client Secret" value="{{ $facebook['client_secret'] }}">
																<span class="text-danger f-12">{{ $errors->first("facebook_client_secret") }}</span>
															</div>
														</div>
													</div>

													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-google-plus-square"></i>
															<span>Google Credentials</span>
														</div>

														{{-- Google Client ID --}}
														<div class="settings-field-row">
															<label for="google_client_id" class="settings-field-label">Client ID <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="google_client_id" class="form-control settings-input" id="google_client_id" placeholder="Google Client ID" value="{{ $google['client_id'] }}">
																<span class="text-danger f-12">{{ $errors->first("google_client_id") }}</span>
															</div>
														</div>

														{{-- Google Client Secret --}}
														<div class="settings-field-row">
															<label for="google_client_secret" class="settings-field-label">Client Secret <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="google_client_secret" class="form-control settings-input" id="google_client_secret" placeholder="Google Client Secret" value="{{ $google['client_secret'] }}">
																<span class="text-danger f-12">{{ $errors->first("google_client_secret") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Maps --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-map-marker"></i>
															<span>Maps Configuration</span>
														</div>

														{{-- Google Map Key --}}
														<div class="settings-field-row">
															<label for="google_map_key" class="settings-field-label">Browser Key <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="google_map_key" class="form-control settings-input" id="google_map_key" placeholder="Google Map Browser Key" value="{{ config("vrent.google_map_key") ? base64_encode(config("vrent.google_map_key")) : base64_encode($google_map['key']) }}">
																<span class="text-danger f-12">{{ $errors->first("google_map_key") }}</span>
															</div>
														</div>

														@if (settings('restrict_api_key') == 'Yes')
															{{-- GeoCode Google Map Key --}}
															<div class="settings-field-row">
																<label for="geocode_google_map_key" class="settings-field-label">GeoCode Key <span class="text-danger">*</span></label>
																<div class="settings-field-input">
																	<input type="text" name="geocode_google_map_key" class="form-control settings-input" id="geocode_google_map_key" placeholder="Google Map GeoCode Key" value="{{ config("vrent.google_geocode_map_key") ? base64_encode(config("vrent.google_geocode_map_key")) : (isset($google_map['geocode_key']) ? base64_encode($google_map['geocode_key']) : NULL) }}">
																	<span class="text-danger f-12">{{ $errors->first("geocode_google_map_key") }}</span>
																</div>
															</div>
														@endif
													</div>
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save">
															<i class="fa fa-check me-2"></i>Save Changes
														</button>
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings/social-links') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">API keys are sensitive information</small>
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
