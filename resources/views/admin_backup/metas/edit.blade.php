@extends('admin.template')

@push('css')
	<link href="{{ asset('public/backend/css/setting.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Edit Metas</h1>
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
							<h3 class="card-title">Edit Metas</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="edit_meta" method="post" action="{{ url('admin/settings/edit_meta/' . $result->id) }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="url" class="col-md-3 col-form-label text-md-end fw-bold">Page Url <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="url" class="form-control f-14" id="url" placeholder="Page Url" value="{{ $result->url }}">
										<span class="text-danger f-12">{{ $errors->first("url") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="title" class="col-md-3 col-form-label text-md-end fw-bold">Page Title <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="title" class="form-control f-14" id="title" placeholder="Page Title" value="{{ $result->title }}">
										<span class="text-danger f-12">{{ $errors->first("title") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="description" class="col-md-3 col-form-label text-md-end fw-bold">Meta Description <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<textarea name="description" placeholder="Meta Description" rows="3" class="form-control f-14">{{ $result->description }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('description') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="keywords" class="col-md-3 col-form-label text-md-end fw-bold">Keywords</label>
									<div class="col-md-6">
										<textarea name="keywords" placeholder="Keywords" rows="3" class="form-control f-14">{{ $result->keywords }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('keywords') }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/metas') }}">Cancel</a>
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
