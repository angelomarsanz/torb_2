@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner">
					<h1 class="dashboard-page-title">ADD PAGE</h1>
				</div>
			</section>

			<section class="content">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<div class="settings-form-card mb-5">
								<div class="settings-form-header">
									<div class="d-flex align-items-center gap-3">
										<div class="settings-form-icon">
											<i class="fa fa-file-text-o"></i>
										</div>
										<div>
											<h4 class="settings-form-title">Create Page</h4>
											<p class="settings-form-subtitle">Add a new static content page to the website
											</p>
										</div>
									</div>
								</div>

								<div class="settings-form-body">
									@if (Session::has('error'))
										<div class="alert alert-warning alert-dismissible fade show mx-6 mt-4" role="alert">
											<strong>Warning!</strong> Whoops there was an error. Please verify your below
											information.
											<button type="button" class="btn-close" data-bs-dismiss="alert"
												aria-label="Close"></button>
										</div>
									@endif

									<form id="add_page" method="post" action="{{ url('admin/add-page') }}"
										class="form-horizontal" accept-charset="UTF-8" enctype="multipart/form-data">
										{{ csrf_field() }}

										<div class="settings-section">
											<div class="settings-section-label"><i class="fa fa-info-circle"></i> Core
												Information</div>

											<div class="settings-field-row">
												<label class="settings-field-label" for="geturl">Name <span
														class="text-danger">*</span></label>
												<div class="settings-field-input">
													<input type="text" name="name" class="form-control settings-input"
														id="geturl" placeholder="Page name (e.g. Privacy Policy)" value="">
													<span class="text-danger f-12">{{ $errors->first('name') }}</span>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label" for="page_url">URL Slug <span
														class="text-danger">*</span></label>
												<div class="settings-field-input">
													<input type="text" name="url" class="form-control settings-input"
														id="page_url" placeholder="e.g. privacy-policy">
													<span class="text-danger f-12">{{ $errors->first('url') }}</span>
												</div>
											</div>
										</div>

										<div class="settings-section">
											<div class="settings-section-label"><i class="fa fa-edit"></i> Content</div>

											<div class="settings-field-row">
												<label class="settings-field-label" for="content">Page Content <span
														class="text-danger">*</span></label>
												<div class="settings-field-input">
													<textarea id="content" name="content" placeholder="Enter page content…"
														rows="12" class="form-control settings-input"></textarea>
													<span id="content-validation-error" class="text-danger f-12"></span>
												</div>
											</div>
										</div>

										<div class="settings-section">
											<div class="settings-section-label"><i class="fa fa-cog"></i> Configuration
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label" for="position">Footer Position</label>
												<div class="settings-field-input">
													<select name="position" class="form-select settings-input"
														id="position">
														<option value="first">First Column</option>
														<option value="second">Second Column</option>
													</select>
												</div>
											</div>

											<div class="settings-field-row">
												<label class="settings-field-label" for="status">Status</label>
												<div class="settings-field-input">
													<select name="status" class="form-select settings-input" id="status">
														<option value="Active">Active</option>
														<option value="Inactive">Inactive</option>
													</select>
												</div>
											</div>
										</div>

										<div class="settings-form-footer">
											<div></div>
											<div class="d-flex gap-2">
												<a class="btn settings-btn-cancel" href="{{ url('admin/pages') }}"><i
														class="fa fa-arrow-left me-1"></i> Back</a>
												<button type="submit" class="btn settings-btn-save" id="submitBtn"><i
														class="fa fa-check me-1"></i> Create Page</button>
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
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/admin.min.js') }}"></script>
	<script src="{{ asset('public/backend/js/ckeditor_5.41.js') }}"></script>
	<script type="text/javascript">
		'use strict'
		let filebrowserUploadUrl = '{{ route("upload", ["_token" => csrf_token()]) }}';
		let _token = "{{ csrf_token() }}";
		let uploadFailmessage = "{{ __('Upload failed') }}";
	</script>
	<script type="text/javascript" src="{{ asset('public/backend/js/static_page_photo_upload.min.js') }}"></script>
@endsection