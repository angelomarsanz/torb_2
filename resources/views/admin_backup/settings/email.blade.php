@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Email Settings</h1>
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
							<h3 class="card-title">Email Setting Form</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="email_setting" method="post" action="{{ url('admin/settings/email') }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="driver" class="col-md-3 col-form-label text-md-end fw-bold">Email Protocol</label>
									<div class="col-md-6">
										<select class="form-select f-14 validate_field protocol_type" id="driver" name="driver">
											@foreach ($drivers as $key => $driver)
												<option value="{{ $key }}" {{ $result['driver'] == $key ? 'selected' : '' }}>{{ $driver }}</option>
											@endforeach
										</select>
										<span class="text-danger f-12">{{ $errors->first('status') }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="host" class="col-md-3 col-form-label text-md-end fw-bold">Host <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="host" class="form-control f-14" id="host" placeholder="Host" value="{{ $result['host'] }}">
										<span class="text-danger f-12">{{ $errors->first("host") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="port" class="col-md-3 col-form-label text-md-end fw-bold">Port <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="port" class="form-control f-14" id="port" placeholder="Port" value="{{ $result['port'] }}">
										<span class="text-danger f-12">{{ $errors->first("port") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="from_address" class="col-md-3 col-form-label text-md-end fw-bold">From Address <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="from_address" class="form-control f-14" id="from_address" placeholder="From Address" value="{{ $result['from_address'] }}">
										<span class="text-danger f-12">{{ $errors->first("from_address") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="from_name" class="col-md-3 col-form-label text-md-end fw-bold">From Name <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="from_name" class="form-control f-14" id="from_name" placeholder="From Name" value="{{ $result['from_name'] }}">
										<span class="text-danger f-12">{{ $errors->first("from_name") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="encryption" class="col-md-3 col-form-label text-md-end fw-bold">Encryption <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="encryption" class="form-control f-14" id="encryption" placeholder="Encryption" value="{{ $result['encryption'] }}">
										<span class="text-danger f-12">{{ $errors->first("encryption") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="username" class="col-md-3 col-form-label text-md-end fw-bold">Username <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="username" class="form-control f-14" id="username" placeholder="Username" value="{{ $result['username'] }}">
										<span class="text-danger f-12">{{ $errors->first("username") }}</span>
									</div>
								</div>

								<div class="row mb-3">
									<label for="password" class="col-md-3 col-form-label text-md-end fw-bold">Password <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="password" class="form-control f-14" id="password" placeholder="Password" value="{{ $result['password'] }}">
										<span class="text-danger f-12">{{ $errors->first("password") }}</span>
									</div>
								</div>

								<input type="hidden" class="email_status_check" name="email_status" value="{{ $result['email_status'] }}">
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/email') }}">Cancel</a>
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
<script type="text/javascript" src="{{ asset('public/backend/js/backend.min.js') }}"></script>
@endsection
