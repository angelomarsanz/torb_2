@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Fee Settings</h1>
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
							<h3 class="card-title">Fees Setting Form</h3>
							<span class="ms-2 badge bg-success"><i class="fa fa-check me-1"></i>Verified</span>
						</div>

						<form id="fees_setting" method="post" action="{{ url('admin/settings/fees') }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<div class="row mb-3">
									<label for="guest_service_charge" class="col-md-3 col-form-label text-md-end fw-bold">Guest service charge (%) <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="guest_service_charge" class="form-control f-14" id="guest_service_charge" placeholder="Guest service charge (%)" value="{{ $result['guest_service_charge'] }}">
										<span class="text-danger f-12">{{ $errors->first("guest_service_charge") }}</span>
									</div>
									<div class="col-md-3">
										<small class="text-muted">service charge of guest for booking</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="iva_tax" class="col-md-3 col-form-label text-md-end fw-bold">I.V.A Tax (%) <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="iva_tax" class="form-control f-14" id="iva_tax" placeholder="I.V.A Tax (%)" value="{{ $result['iva_tax'] }}">
										<span class="text-danger f-12">{{ $errors->first("iva_tax") }}</span>
									</div>
									<div class="col-md-3">
										<small class="text-muted">I.V.A Tax of guest for booking</small>
									</div>
								</div>

								<div class="row mb-3">
									<label for="accomodation_tax" class="col-md-3 col-form-label text-md-end fw-bold">Accomadation Tax (%) <span class="text-danger">*</span></label>
									<div class="col-md-6">
										<input type="text" name="accomodation_tax" class="form-control f-14" id="accomodation_tax" placeholder="Accomadation Tax (%)" value="{{ $result['accomodation_tax'] }}">
										<span class="text-danger f-12">{{ $errors->first("accomodation_tax") }}</span>
									</div>
									<div class="col-md-3">
										<small class="text-muted">accomadation Tax of guest for booking</small>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a class="btn btn-outline-secondary f-14 me-2" href="{{ url('admin/settings/country') }}">Cancel</a>
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
