@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">DESCRIPTION</h1>
				<div class="dashboard-header-right">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-lg-3 col-12 settings_bar_gap">
						@include('admin.common.property_bar')
					</div>

					<div class="col-lg-9 col-12">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<form id="list_des" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="form-horizontal" accept-charset="UTF-8">
									{{ csrf_field() }}
									<div class="settings-form-card">
										
										{{-- Form Header --}}
										<div class="settings-form-header">
											<div class="d-flex align-items-center gap-3">
												<div class="settings-form-icon">
													<i class="fa fa-file-text-o"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Listing Description</h4>
													<p class="settings-form-subtitle">Tell travelers about your space and hosting style</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-pencil"></i>
													<span>Primary Information</span>
												</div>

												{{-- Listing Name --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Listing Name <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<input type="text" name="name" class="form-control settings-input" value="{{ old('name', $description->properties->name) }}" placeholder="e.g. Cozy Apartment in Heart of the City" maxlength="100">
														<span class="text-danger f-12">{{ $errors->first('name') }}</span>
													</div>
												</div>

												{{-- Summary --}}
												<div class="settings-field-row">
													<label class="settings-field-label">Summary <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<textarea class="form-control settings-input" name="summary" rows="6" placeholder="Describe your property's best features...">{{ old('summary', $description->summary) }}</textarea>
														<span class="text-danger f-12">{{ $errors->first('summary') }}</span>
													</div>
												</div>

												{{-- Details Link --}}
												<div class="settings-field-row">
													<label class="settings-field-label"></label>
													<div class="settings-field-input">
														<p class="f-13 text-muted mb-0">
															You can add more <a href="{{ url('admin/listing/' . $result->id . '/details') }}" class="text-primary fw-600" id="js-write-more">details</a> about your space and neighborhood.
														</p>
													</div>
												</div>
											</div>
										</div>

										{{-- Form Footer --}}
										<div class="settings-form-footer">
											<div class="d-flex align-items-center gap-2">
												<a href="{{ url('admin/listing/' . $result->id . '/basics') }}" class="btn settings-btn-cancel">
													<i class="fa fa-arrow-left me-1 f-12"></i> Back
												</a>
												<button type="submit" class="btn settings-btn-save">
													Next <i class="fa fa-arrow-right ms-1 f-12"></i>
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
