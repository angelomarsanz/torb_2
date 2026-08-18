@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Testimonial <small class="text-muted fw-normal">Add Testimonial</small></h1>
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
							<h3 class="card-title">Testimonial Add Form</h3>
						</div>

						<form class="form-horizontal" action="{{ url('admin/add-testimonials') }}" id="add_testimonials" method="post" name="add_testimonials" accept-charset="UTF-8" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="name" class="col-md-3 col-form-label text-md-end fw-bold">Name <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" class="form-control f-14" name="name" id="name" value="{{ old('name') }}" placeholder="Enter Reviewer Name..">
										<span class="text-danger f-12">{{ $errors->first('name') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="designation" class="col-md-3 col-form-label text-md-end fw-bold">Designation <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" class="form-control f-14" name="designation" id="designation" placeholder="Reviewer Designation.." value="{{ old('designation') }}">
										<span class="text-danger f-12">{{ $errors->first('designation') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="description" class="col-md-3 col-form-label text-md-end fw-bold">Description <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<textarea name="description" id="description" class="form-control f-14" rows="4" placeholder="Description..">{{ old('description') }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('description') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="image" class="col-md-3 col-form-label text-md-end fw-bold">Image <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="file" class="form-control f-14" name="image" id="image">
										<span class="text-danger f-12">{{ $errors->first('image') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-md-3 col-form-label text-md-end fw-bold">Rating <span class="text-danger">*</span></label>
									<input type="hidden" name="rating_1" id="rating">
									<div class="col-md-6 pt-2">
										@for ($i = 1; $i <= 5; $i++)
											<i id="rating-{{ $i }}" class="fa fa-star {{ $i >= 0 ? 'icon-light-gray' : 'fa-star-beach' }} icon-click"></i>
										@endfor
										<span class="text-danger f-12">{{ $errors->first('rating_1') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="status" class="col-md-3 col-form-label text-md-end fw-bold">Status</label>
									<div class="col-md-6">
										<select class="form-select f-14" name="status" id="status">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/testimonials') }}">Cancel</a>
								<button type="submit" class="btn btn-info text-white f-14" id="submitBtn">Submit</button>
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
