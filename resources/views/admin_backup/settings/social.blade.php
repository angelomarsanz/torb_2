@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Social Links</h1>
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
							<h3 class="card-title">Social Setting Form</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="social_setting" method="post" action="{{ url('admin/settings/social-links') }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="facebook" class="col-md-3 col-form-label text-md-end fw-bold">Facebook <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="facebook" class="form-control f-14" id="facebook" placeholder="Facebook" value="{{ $result['facebook'] }}">
										<span class="text-danger f-12">{{ $errors->first("facebook") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="google_plus" class="col-md-3 col-form-label text-md-end fw-bold">Google Plus <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="google_plus" class="form-control f-14" id="google_plus" placeholder="Google Plus" value="{{ $result['google_plus'] }}">
										<span class="text-danger f-12">{{ $errors->first("google_plus") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="twitter" class="col-md-3 col-form-label text-md-end fw-bold">Twitter <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="twitter" class="form-control f-14" id="twitter" placeholder="Twitter" value="{{ $result['twitter'] }}">
										<span class="text-danger f-12">{{ $errors->first("twitter") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="linkedin" class="col-md-3 col-form-label text-md-end fw-bold">Linkedin <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="linkedin" class="form-control f-14" id="linkedin" placeholder="Linkedin" value="{{ $result['linkedin'] }}">
										<span class="text-danger f-12">{{ $errors->first("linkedin") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="pinterest" class="col-md-3 col-form-label text-md-end fw-bold">Pinterest <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="pinterest" class="form-control f-14" id="pinterest" placeholder="Pinterest" value="{{ $result['pinterest'] }}">
										<span class="text-danger f-12">{{ $errors->first("pinterest") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="youtube" class="col-md-3 col-form-label text-md-end fw-bold">Youtube <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="youtube" class="form-control f-14" id="youtube" placeholder="Youtube" value="{{ $result['youtube'] }}">
										<span class="text-danger f-12">{{ $errors->first("youtube") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="instagram" class="col-md-3 col-form-label text-md-end fw-bold">Instagram <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="instagram" class="form-control f-14" id="instagram" placeholder="Instagram" value="{{ $result['instagram'] }}">
										<span class="text-danger f-12">{{ $errors->first("instagram") }}</span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/social-links') }}">Cancel</a>
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
