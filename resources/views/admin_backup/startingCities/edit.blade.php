@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Edit Starting City</h1>
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
				<div class="col-lg-3 col-12">
					@include('admin.common.settings_bar')
				</div>

				<div class="col-lg-9 col-12">
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
							<h3 class="card-title">Edit Starting City</h3>
						</div>

						<form id="edit_starting_city" method="post" action="{{ url('admin/settings/edit-starting-cities/' . $result->id) }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="name" class="col-md-3 col-form-label text-md-end fw-bold">Starting City Name <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="name" class="form-control f-14" id="name" placeholder="Starting City Name" value="{{ $result->name }}">
										<span class="text-danger f-12">{{ $errors->first("name") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="image" class="col-md-3 col-form-label text-md-end fw-bold">Image <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="file" name="image" class="form-control f-14" id="image" placeholder="Image" accept="image/*">
										<span class="text-danger f-12">{{ $errors->first('image') }}</span>
										<div class="mt-2">
											<img class="file-img" src="{{ url('public/front/images/starting_cities/' . $result['image']) }}">
										</div>
									</div>
									<div class="col-md-3">
										<small class="text-muted">(Width:640px and Height:360px)</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="status" class="col-md-3 col-form-label text-md-end fw-bold">Status</label>
									<div class="col-md-6">
										<select class="form-select f-14" id="status" name="status">
											<option value="Active" {{ $result->status == "Active" ? 'selected' : '' }}>Active</option>
											<option value="Inactive" {{ $result->status == "Inactive" ? 'selected' : '' }}>Inactive</option>
										</select>
										<span class="text-danger f-12">{{ $errors->first('status') }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/starting-cities') }}">Cancel</a>
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
<script type="text/javascript">
	'use strict'
	var message = "{{ __('The file must be an image (jpg, jpeg or png)') }}";
</script>
<script src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
