@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Edit Space Type</h1>
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
							<h3 class="card-title">Edit Space Type Form</h3>
						</div>

						<form id="edit_space" method="post" action="{{ url('admin/settings/edit-space-type/' . $result->id) }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="name" class="col-md-3 col-form-label text-md-end fw-bold">Name <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="name" class="form-control f-14" id="name" placeholder="Name" value="{{ $result->name }}">
										<span class="text-danger f-12">{{ $errors->first("name") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="description" class="col-md-3 col-form-label text-md-end fw-bold">Description <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<textarea name="description" placeholder="Description" rows="3" class="form-control f-14" id="description">{{ $result->description }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('description') }}</span>
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
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/space-type') }}">Cancel</a>
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
