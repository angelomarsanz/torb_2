@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">SOCIAL LINKS</h1>
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

										<form id="social_setting" method="post" action="{{ url('admin/settings/social-links') }}" class="form-horizontal">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-share-alt"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Social Setting</h4>
															<p class="settings-form-subtitle">Connect your social media profiles to the platform</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Social Network Links --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-link"></i>
															<span>Network Profiles</span>
														</div>

														{{-- Facebook --}}
														<div class="settings-field-row">
															<label for="facebook" class="settings-field-label">Facebook <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="facebook" class="form-control settings-input" id="facebook" placeholder="https://facebook.com/your-profile" value="{{ $result['facebook'] }}">
																<span class="text-danger f-12">{{ $errors->first("facebook") }}</span>
															</div>
														</div>

														{{-- Google Plus --}}
														<div class="settings-field-row">
															<label for="google_plus" class="settings-field-label">Google Plus <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="google_plus" class="form-control settings-input" id="google_plus" placeholder="https://plus.google.com/your-profile" value="{{ $result['google_plus'] }}">
																<span class="text-danger f-12">{{ $errors->first("google_plus") }}</span>
															</div>
														</div>

														{{-- Twitter --}}
														<div class="settings-field-row">
															<label for="twitter" class="settings-field-label">Twitter <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="twitter" class="form-control settings-input" id="twitter" placeholder="https://twitter.com/your-handle" value="{{ $result['twitter'] }}">
																<span class="text-danger f-12">{{ $errors->first("twitter") }}</span>
															</div>
														</div>

														{{-- Linkedin --}}
														<div class="settings-field-row">
															<label for="linkedin" class="settings-field-label">Linkedin <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="linkedin" class="form-control settings-input" id="linkedin" placeholder="https://linkedin.com/in/your-profile" value="{{ $result['linkedin'] }}">
																<span class="text-danger f-12">{{ $errors->first("linkedin") }}</span>
															</div>
														</div>

														{{-- Pinterest --}}
														<div class="settings-field-row">
															<label for="pinterest" class="settings-field-label">Pinterest <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="pinterest" class="form-control settings-input" id="pinterest" placeholder="https://pinterest.com/your-profile" value="{{ $result['pinterest'] }}">
																<span class="text-danger f-12">{{ $errors->first("pinterest") }}</span>
															</div>
														</div>

														{{-- Youtube --}}
														<div class="settings-field-row">
															<label for="youtube" class="settings-field-label">Youtube <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="youtube" class="form-control settings-input" id="youtube" placeholder="https://youtube.com/c/your-channel" value="{{ $result['youtube'] }}">
																<span class="text-danger f-12">{{ $errors->first("youtube") }}</span>
															</div>
														</div>

														{{-- Instagram --}}
														<div class="settings-field-row">
															<label for="instagram" class="settings-field-label">Instagram <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="instagram" class="form-control settings-input" id="instagram" placeholder="https://instagram.com/your-profile" value="{{ $result['instagram'] }}">
																<span class="text-danger f-12">{{ $errors->first("instagram") }}</span>
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
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings/social-links') }}">Cancel</a>
													</div>
													<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Fields marked with <span class="text-danger">*</span> are required</small>
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
