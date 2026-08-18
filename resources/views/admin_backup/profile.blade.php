@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Profile <small class="text-muted fw-normal">Edit your profile</small></h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">

			@if (Session::has('error'))
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					<i class="fa fa-exclamation-triangle me-2"></i>
					<strong>Warning!</strong> Whoops, there was an error. Please verify your information below.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<div class="row">
				{{-- Profile Info Card --}}
				<div class="col-lg-4 col-md-5">
					<div class="card card-outline card-info shadow-sm profile-card">
						<div class="card-body text-center pt-4 pb-3">
							<div class="profile-avatar-wrap mx-auto">
								<img
									src="{{ Auth::guard('admin')->user()->profile_src }}"
									alt="{{ $result->username }}"
								>
							</div>
							<h5 class="mt-3 mb-1 fw-bold">{{ $result->username }}</h5>
							<p class="text-muted f-14 mb-2">{{ $result->email }}</p>
							<span class="badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>
						<div class="card-footer bg-light text-center f-14">
							<small class="text-muted">Manage your account settings and profile photo</small>
						</div>
					</div>
				</div>

				{{-- Profile Edit Form Card --}}
				<div class="col-lg-8 col-md-7">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title"><i class="fa fa-user-edit me-2"></i>Edit Profile</h3>
						</div>

						<form id="profile_edit" method="post" action="{{ url('admin/profile') }}" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">

								<div class="mb-3 row">
									<label for="name" class="col-sm-3 col-form-label fw-bold text-md-end">
										Name <span class="text-danger">*</span>
									</label>
									<div class="col-sm-8">
										<input type="text" name="name" class="form-control f-14" id="name"
											placeholder="Name" value="{{ $result->username }}">
										@if ($errors->has('name'))
											<div class="text-danger f-12 mt-1">{{ $errors->first('name') }}</div>
										@endif
									</div>
								</div>

								<div class="mb-3 row">
									<label for="email" class="col-sm-3 col-form-label fw-bold text-md-end">
										Email <span class="text-danger">*</span>
									</label>
									<div class="col-sm-8">
										<input type="text" name="email" class="form-control f-14" id="email"
											placeholder="Email" value="{{ $result->email }}">
										@if ($errors->has('email'))
											<div class="text-danger f-12 mt-1">{{ $errors->first('email') }}</div>
										@endif
									</div>
								</div>

								<hr class="my-3">

								<div class="mb-3 row">
									<label for="password" class="col-sm-3 col-form-label fw-bold text-md-end">
										New Password
									</label>
									<div class="col-sm-8">
										<input type="password" name="password" class="form-control f-14 new_password"
											id="password" placeholder="New Password">
										@if ($errors->has('password'))
											<div class="text-danger f-12 mt-1">{{ $errors->first('password') }}</div>
										@endif
										<div class="form-text f-12">Leave blank to keep current password.</div>
									</div>
								</div>

								<div class="mb-3 row">
									<label for="password_confirmation" class="col-sm-3 col-form-label fw-bold text-md-end">
										Confirm Password
									</label>
									<div class="col-sm-8">
										<input type="password" name="password_confirmation" class="form-control f-14"
											id="password_confirmation" placeholder="Re-enter Password">
										@if ($errors->has('password_confirmation'))
											<div class="text-danger f-12 mt-1">{{ $errors->first('password_confirmation') }}</div>
										@endif
									</div>
								</div>

								<hr class="my-3">

								<div class="mb-3 row">
									<label for="profile_pic" class="col-sm-3 col-form-label fw-bold text-md-end">
										Profile Photo
									</label>
									<div class="col-sm-8">
										<input type="file" name="profile_pic" class="form-control f-14"
											id="profile_pic" accept="image/*">
										@if ($errors->has('profile_pic'))
											<div class="text-danger f-12 mt-1">{{ $errors->first('profile_pic') }}</div>
										@endif
									</div>
								</div>

							</div>

							<div class="card-footer d-flex gap-2">
								<button type="submit" class="btn btn-info text-white f-14">
									<i class="fa fa-save me-1"></i> Save Changes
								</button>
								<a class="btn btn-outline-secondary f-14" href="{{ url('admin/admin-users') }}">
									<i class="fa fa-times me-1"></i> Cancel
								</a>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script type="text/javascript">
	'use strict'
	var message = "{{ __('The file must be an image (jpg, gif or png)') }}";
</script>
<script type="text/javascript" src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
