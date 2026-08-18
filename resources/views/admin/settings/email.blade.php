@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">EMAIL SETTINGS</h1>
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

										<form id="email_setting" method="post" action="{{ url('admin/settings/email') }}" class="form-horizontal">
											{{ csrf_field() }}
											<div class="settings-form-card">
												
												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-envelope-o"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Email Setting Form</h4>
															<p class="settings-form-subtitle">Configure your SMTP server settings and sender information</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Connection --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-plug"></i>
															<span>Connection Detail</span>
														</div>

														{{-- Email Protocol --}}
														<div class="settings-field-row">
															<label for="driver" class="settings-field-label">Email Protocol</label>
															<div class="settings-field-input">
																<select class="form-select settings-input protocol_type" id="driver" name="driver">
																	@foreach ($drivers as $key => $driver)
																		<option value="{{ $key }}" {{ $result['driver'] == $key ? 'selected' : '' }}>{{ $driver }}</option>
																	@endforeach
																</select>
																<span class="text-danger f-12">{{ $errors->first('status') }}</span>
															</div>
														</div>

														{{-- Host --}}
														<div class="settings-field-row">
															<label for="host" class="settings-field-label">Host <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="host" class="form-control settings-input" id="host" placeholder="smtp.example.com" value="{{ $result['host'] }}">
																<span class="text-danger f-12">{{ $errors->first("host") }}</span>
															</div>
														</div>

														{{-- Port --}}
														<div class="settings-field-row">
															<label for="port" class="settings-field-label">Port <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="port" class="form-control settings-input" id="port" placeholder="587" value="{{ $result['port'] }}">
																<span class="text-danger f-12">{{ $errors->first("port") }}</span>
															</div>
														</div>

														{{-- Encryption --}}
														<div class="settings-field-row">
															<label for="encryption" class="settings-field-label">Encryption <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="encryption" class="form-control settings-input" id="encryption" placeholder="tls / ssl" value="{{ $result['encryption'] }}">
																<span class="text-danger f-12">{{ $errors->first("encryption") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Authentication --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-lock"></i>
															<span>Authentication</span>
														</div>

														{{-- Username --}}
														<div class="settings-field-row">
															<label for="username" class="settings-field-label">Username <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="username" class="form-control settings-input" id="username" placeholder="Username" value="{{ $result['username'] }}">
																<span class="text-danger f-12">{{ $errors->first("username") }}</span>
															</div>
														</div>

														{{-- Password --}}
														<div class="settings-field-row">
															<label for="password" class="settings-field-label">Password <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="password" name="password" class="form-control settings-input" id="password" placeholder="••••••••" value="{{ $result['password'] }}">
																<span class="text-danger f-12">{{ $errors->first("password") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Sender Identity --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-user-circle-o"></i>
															<span>Sender Identity</span>
														</div>

														{{-- From Address --}}
														<div class="settings-field-row">
															<label for="from_address" class="settings-field-label">From Address <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="from_address" class="form-control settings-input" id="from_address" placeholder="noreply@example.com" value="{{ $result['from_address'] }}">
																<span class="text-danger f-12">{{ $errors->first("from_address") }}</span>
															</div>
														</div>

														{{-- From Name --}}
														<div class="settings-field-row">
															<label for="from_name" class="settings-field-label">From Name <span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="from_name" class="form-control settings-input" id="from_name" placeholder="John Doe" value="{{ $result['from_name'] }}">
																<span class="text-danger f-12">{{ $errors->first("from_name") }}</span>
															</div>
														</div>
													</div>

													<input type="hidden" class="email_status_check" name="email_status" value="{{ $result['email_status'] }}">
												</div>

												{{-- Form Footer --}}
												<div class="settings-form-footer">
													<div class="d-flex align-items-center gap-2">
														<button type="submit" class="btn settings-btn-save">
															<i class="fa fa-check me-2"></i>Save Changes
														</button>
														<a class="btn settings-btn-cancel" href="{{ url('admin/settings/email') }}">Cancel</a>
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
<script type="text/javascript" src="{{ asset('public/backend/js/backend.min.js') }}"></script>
@endsection
