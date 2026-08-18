@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">ADMIN USERS</h1>
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

										<form id="edit_admin" method="post"
											action="{{ url('admin/edit-admin/' . $result->id) }}" class="form-horizontal"
											enctype="multipart/form-data">
											{{ csrf_field() }}
											<div class="settings-form-card">

												{{-- Form Header --}}
												<div class="settings-form-header">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-user"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Edit Admin User</h4>
															<p class="settings-form-subtitle">Modify administrative account
																details for {{ $result->username }}</p>
														</div>
													</div>
												</div>

												<div class="settings-form-body">

													{{-- Section: Account Identity --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-id-card"></i>
															<span>Account Identity</span>
														</div>

														{{-- Username --}}
														<div class="settings-field-row">
															<label for="username" class="settings-field-label">Username
																<span class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="username"
																	class="form-control settings-input" id="username"
																	placeholder="Username" value="{{ $result->username }}">
																<span
																	class="text-danger f-12">{{ $errors->first("username") }}</span>
															</div>
														</div>

														{{-- Email --}}
														<div class="settings-field-row">
															<label for="email" class="settings-field-label">Email <span
																	class="text-danger">*</span></label>
															<div class="settings-field-input">
																<input type="text" name="email"
																	class="form-control settings-input" id="email"
																	placeholder="Email" value="{{ $result->email }}">
																<span
																	class="text-danger f-12">{{ $errors->first("email") }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Security & Access --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-shield"></i>
															<span>Security & Access</span>
														</div>

														{{-- Password --}}
														<div class="settings-field-row align-items-start">
															<label for="password"
																class="settings-field-label pt-2">Password</label>
															<div class="settings-field-input">
																<input type="password" name="password"
																	class="form-control settings-input" id="password"
																	placeholder="••••••••">
																<small class="text-muted mt-2 d-block f-12">Enter new
																	password only. Leave blank to use existing
																	password.</small>
																<span
																	class="text-danger f-12">{{ $errors->first('password') }}</span>
															</div>
														</div>

														{{-- Role --}}
														<div class="settings-field-row">
															<label for="role" class="settings-field-label">Role</label>
															<div class="settings-field-input">
																<select class="form-select settings-input" id="role"
																	name="role">
																	@foreach ($roles as $key => $item)
																		<option value="{{ $key }}" {{ $result->role_id == $key ? 'selected' : '' }}>{{ $item }}</option>
																	@endforeach
																</select>
																<span
																	class="text-danger f-12">{{ $errors->first('role') }}</span>
															</div>
														</div>
													</div>

													{{-- Section: Configuration --}}
													<div class="settings-section">
														<div class="settings-section-label">
															<i class="fa fa-toggle-on"></i>
															<span>Configuration</span>
														</div>

														{{-- Status --}}
														<div class="settings-field-row border-bottom-0 pb-0 mb-0">
															<label for="status" class="settings-field-label">Status</label>
															<div class="settings-field-input">
																<select class="form-select settings-input" id="status"
																	name="status">
																	<option value="Active" {{ $result->status == "Active" ? 'selected' : '' }}>Active</option>
																	<option value="Inactive" {{ $result->status == "Inactive" ? 'selected' : '' }}>Inactive</option>
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
															<i class="fa fa-check me-2"></i>Update
														</button>
														<a class="btn settings-btn-cancel"
															href="{{ url('admin/admin-users') }}">Cancel</a>
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
@endsection