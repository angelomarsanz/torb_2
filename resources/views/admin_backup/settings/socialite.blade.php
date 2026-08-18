@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Social Logins</h1>
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
						<div class="card-header">
							<h3 class="card-title">Social Logins</h3>
						</div>

						<form id="socialiteform" method="post" action="{{ url('admin/settings/social-logins') }}" class="form-horizontal" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="google_login" class="col-md-3 col-form-label text-md-end fw-bold">Google</label>
									<div class="col-md-6">
										<select name="google_login" class="form-select f-14">
											<option value="0" {{ isset($social['google_login']) && $social['google_login'] == '0' ? 'selected' : '' }}>Inactive</option>
											<option value="1" {{ isset($social['google_login']) && $social['google_login'] == '1' ? 'selected' : '' }}>Active</option>
										</select>
									</div>
								</div>

								<div class="row mb-3">
									<label for="facebook_login" class="col-md-3 col-form-label text-md-end fw-bold">Facebook</label>
									<div class="col-md-6">
										<select name="facebook_login" class="form-select f-14">
											<option value="0" {{ isset($social['facebook_login']) && $social['facebook_login'] == '0' ? 'selected' : '' }}>Inactive</option>
											<option value="1" {{ isset($social['facebook_login']) && $social['facebook_login'] == '1' ? 'selected' : '' }}>Active</option>
										</select>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/social-logins') }}">Cancel</a>
								<button type="submit" class="btn btn-info f-14 text-white">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
