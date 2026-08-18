@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Add Admin User</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						@if (Session::has('error'))
							<div class="p-3 pb-0">
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
							</div>
						@endif

						<div class="card-header">
							<h3 class="card-title">Admin Add Form</h3>
						</div>

						<form id="add_admin" method="post" action="{{ url('admin/add-admin') }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="username" class="col-md-3 col-form-label text-md-end fw-bold">Username <span class="text-danger">*</span></label>
									<div class="col-md-5">
										<input type="text" name="username" class="form-control f-14" id="username" placeholder="Username">
										<span class="text-danger f-12">{{ $errors->first("username") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="email" class="col-md-3 col-form-label text-md-end fw-bold">Email <span class="text-danger">*</span></label>
									<div class="col-md-5">
										<input type="text" name="email" class="form-control f-14" id="email" placeholder="Email">
										<span class="text-danger f-12">{{ $errors->first("email") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="password" class="col-md-3 col-form-label text-md-end fw-bold">Password <span class="text-danger">*</span></label>
									<div class="col-md-5">
										<input type="password" name="password" class="form-control f-14" id="password" placeholder="Password">
										<span class="text-danger f-12">{{ $errors->first('password') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="role" class="col-md-3 col-form-label text-md-end fw-bold">Role</label>
									<div class="col-md-5">
										<select class="form-select f-14" id="role" name="role">
											@foreach ($roles as $key => $item)
												<option value="{{ $key }}">{{ $item }}</option>
											@endforeach
										</select>
										<span class="text-danger f-12">{{ $errors->first('role') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="status" class="col-md-3 col-form-label text-md-end fw-bold">Status</label>
									<div class="col-md-5">
										<select class="form-select f-14" id="status" name="status">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
										</select>
										<span class="text-danger f-12">{{ $errors->first('status') }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/admin-users') }}">Cancel</a>
								<button type="submit" class="btn btn-info text-white f-14">Submit</button>
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
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
