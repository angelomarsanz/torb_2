@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">SOCIAL LOGINS</h1>
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

										<form id="socialiteform" method="post" action="{{ url('admin/settings/social-logins') }}" class="form-horizontal" enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-sign-in"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Social Logins</h4>
															<p class="settings-form-subtitle">Enable or disable third-party social login options for users</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Login Methods --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-users"></i>
															<span>Login Methods</span>
														</div>

														{{-- Google Logic --}}
														<div class="settings-field-row">
															<label for="google_login" class="settings-field-label">Google Login</label>
															<div class="settings-field-input">
																<select name="google_login" class="form-select settings-input">
																	<option value="0" {{ isset($social['google_login']) && $social['google_login'] == '0' ? 'selected' : '' }}>Inactive</option>
																	<option value="1" {{ isset($social['google_login']) && $social['google_login'] == '1' ? 'selected' : '' }}>Active</option>
																</select>
															</div>
														</div>

														{{-- Facebook Login --}}
														<div class="settings-field-row">
															<label for="facebook_login" class="settings-field-label">Facebook Login</label>
															<div class="settings-field-input">
																<select name="facebook_login" class="form-select settings-input">
																	<option value="0" {{ isset($social['facebook_login']) && $social['facebook_login'] == '0' ? 'selected' : '' }}>Inactive</option>
																	<option value="1" {{ isset($social['facebook_login']) && $social['facebook_login'] == '1' ? 'selected' : '' }}>Active</option>
																</select>
															</div>
														</div>
													</div>
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save">
															<i class="fa fa-check me-2"></i>Save Changes
														</button>
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings/social-logins') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Update social login preferences</small>
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
