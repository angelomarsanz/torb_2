@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">ADD AMENITIES</h1>
				</div>
			</section>

			<section class="content mb-5">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-8 col-12 mx-auto">
							<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
								<div class="card-body p-2 p-md-4 pt-md-3">
									@if (Session::has('error'))
										<div class="mb-4">
											<div class="alert alert-warning alert-dismissible fade show shadow-sm"
												style="border-radius: 8px;" role="alert">
												<strong>Warning!</strong> Whoops there was an error. Please verify your below
												information.
												<button type="button" class="btn-close" data-bs-dismiss="alert"
													aria-label="Close"></button>
											</div>
										</div>
									@endif

									<form id="add_amenities" method="post" action="{{ url('admin/add-amenities') }}"
										class="form-horizontal">
										{{ csrf_field() }}
										<div class="settings-form-card">

											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-list"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Add Amenity</h4>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Amenity Identity --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-info-circle"></i>
														<span>Amenity Details</span>
													</div>

													{{-- Name --}}
													<div class="settings-field-row">
														<label for="title" class="settings-field-label">Name <span
																class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="title"
																class="form-control settings-input" id="title"
																placeholder="WiFi, Pool, AC..." value="{{ old('title') }}">
															<span
																class="text-danger f-12">{{ $errors->first("title") }}</span>
														</div>
													</div>

													{{-- Description --}}
													<div class="settings-field-row">
														<label for="description" class="settings-field-label">Description
															<span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<textarea name="description"
																placeholder="Briefly describe this amenity..." rows="3"
																class="form-control settings-input" id="description"
																style="height: auto !important;">{{ old('description') }}</textarea>
															<span
																class="text-danger f-12">{{ $errors->first('description') }}</span>
														</div>
													</div>
												</div>

												{{-- Section: Categorization --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-tags"></i>
														<span>Categorization</span>
													</div>

													{{-- Type --}}
													<div class="settings-field-row">
														<label for="type_id" class="settings-field-label">Category Type
															<span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<select class="form-select settings-input" id="type_id"
																name="type_id">
																@foreach ($amenities_type as $row)
																	<option value="{{ $row->id }}">{{ $row->name }}</option>
																@endforeach
															</select>
															<span
																class="text-danger f-12">{{ $errors->first('type_id') }}</span>
														</div>
													</div>

													{{-- Symbol --}}
													<div class="settings-field-row">
														<label for="symbol" class="settings-field-label">Icon Class <span
																class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="symbol"
																class="form-control settings-input" id="symbol"
																placeholder="fa fa-wifi" value="{{ old('symbol') }}">
															<span
																class="text-danger f-12">{{ $errors->first("symbol") }}</span>
															<small class="text-muted d-block mt-1"
																style="font-size: 11px;">Use FontAwesome icon classes (e.g.,
																fa fa-wifi)</small>
														</div>
													</div>
												</div>

												{{-- Section: Visibility --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-toggle-on"></i>
														<span>Configuration</span>
													</div>

													{{-- Status --}}
													<div class="settings-field-row">
														<label for="status" class="settings-field-label">Status</label>
														<div class="settings-field-input">
															<select class="form-select settings-input" id="status"
																name="status">
																<option value="Active">Active</option>
																<option value="Inactive">Inactive</option>
															</select>
															<span
																class="text-danger f-12">{{ $errors->first('status') }}</span>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														Submit
													</button>
													<a class="btn settings-btn-cancel"
														href="{{ url('admin/amenities') }}">Cancel</a>
												</div>
											</div>
										</div>
									</form>
								</div>
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
@endsection