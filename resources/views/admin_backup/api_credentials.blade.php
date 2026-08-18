@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Api Credentials</h1>
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
							<h3 class="card-title">Api Credentials Form</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="api_credentials" method="post" action="{{ url('admin/settings/api-informations') }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="facebook_client_id" class="col-md-3 col-form-label text-md-end fw-bold">Facebook Client ID <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="facebook_client_id" class="form-control f-14" id="facebook_client_id" placeholder="Facebook Client ID" value="{{ $facebook['client_id'] }}">
										<span class="text-danger f-12">{{ $errors->first("facebook_client_id") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="facebook_client_secret" class="col-md-3 col-form-label text-md-end fw-bold">Facebook Client Secret <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="facebook_client_secret" class="form-control f-14" id="facebook_client_secret" placeholder="Facebook Client Secret" value="{{ $facebook['client_secret'] }}">
										<span class="text-danger f-12">{{ $errors->first("facebook_client_secret") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="google_client_id" class="col-md-3 col-form-label text-md-end fw-bold">Google Client ID <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="google_client_id" class="form-control f-14" id="google_client_id" placeholder="Google Client ID" value="{{ $google['client_id'] }}">
										<span class="text-danger f-12">{{ $errors->first("google_client_id") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="google_client_secret" class="col-md-3 col-form-label text-md-end fw-bold">Google Client Secret <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="google_client_secret" class="form-control f-14" id="google_client_secret" placeholder="Google Client Secret" value="{{ $google['client_secret'] }}">
										<span class="text-danger f-12">{{ $errors->first("google_client_secret") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="google_map_key" class="col-md-3 col-form-label text-md-end fw-bold">Google Map Browser Key <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="google_map_key" class="form-control f-14" id="google_map_key" placeholder="Google Map Browser Key" value="{{ config("vrent.google_map_key") ? base64_encode(config("vrent.google_map_key")) : base64_encode($google_map['key']) }}">
										<span class="text-danger f-12">{{ $errors->first("google_map_key") }}</span>
									</div>
								</div>

								@if (settings('restrict_api_key') == 'Yes')
									<div class="row mb-3">
										<label for="geocode_google_map_key" class="col-md-3 col-form-label text-md-end fw-bold">GeoCode Google Map Key <span class="text-danger">*</span></label>
										<div class="col-md-6">
											<input type="text" name="geocode_google_map_key" class="form-control f-14" id="geocode_google_map_key" placeholder="Google Map GeoCode Key" value="{{ config("vrent.google_geocode_map_key") ? base64_encode(config("vrent.google_geocode_map_key")) : (isset($google_map['geocode_key']) ? base64_encode($google_map['geocode_key']) : NULL) }}">
											<span class="text-danger f-12">{{ $errors->first("geocode_google_map_key") }}</span>
										</div>
									</div>
								@endif
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
