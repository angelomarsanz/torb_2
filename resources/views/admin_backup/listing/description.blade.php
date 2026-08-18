@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Description</h1>
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
				<div class="col-lg-3 col-12 settings_bar_gap">
					@include('admin.common.property_bar')
				</div>
				<div class="col-lg-9 col-12">
					<form id="list_des" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="signup-form login-form" accept-charset="UTF-8">
						{{ csrf_field() }}
						<div class="card card-outline card-info shadow-sm">
							<div class="card-header">
								<h3 class="card-title">Listing Description</h3>
							</div>
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-md-8 col-sm-12 col-12">
										<label class="fw-bold f-14">Listing Name <span class="text-danger">*</span></label>
										<input type="text" name="name" class="form-control f-14" value="{{ old('name', $description->properties->name) }}" placeholder="" maxlength="100">
										<span class="text-danger f-12">{{ $errors->first('name') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8 col-sm-12 col-12">
										<label class="fw-bold f-14">Summary <span class="text-danger">*</span></label>
										<textarea class="form-control f-14" name="summary" rows="6" placeholder="" ng-model="summary">{{ old('summary', $description->summary) }}</textarea>
										<span class="text-danger f-12">{{ $errors->first('summary') }}</span>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col-md-8 col-sm-12 col-12">
										<p class="f-14 text-muted">
											You can add more <a href="{{ url('admin/listing/' . $result->id . '/details') }}" class="secondary-text-color" id="js-write-more">details</a>. Tell travelers about your space and hosting style.
										</p>
									</div>
								</div>
							</div>
							<div class="card-footer d-flex justify-content-between">
								<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/basics') }}" class="btn btn-outline-secondary f-14">
									<i class="fa fa-arrow-left me-1"></i> Back
								</a>
								<button type="submit" class="btn btn-info text-white f-14">
									<i class="fa fa-arrow-right me-1"></i> Next
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
