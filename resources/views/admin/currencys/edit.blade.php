@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">EDIT CURRENCY</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<div class="workbench-container">
									
									<div class="workbench-sidebar">
										<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">Settings Menu</div>
										@include('admin.common.settings_bar')
									</div>

									<div class="workbench-main">
									@if (Session::has('error'))
										<div class="mb-4">
											<div class="alert alert-warning alert-dismissible fade show shadow-sm" style="border-radius: 8px;" role="alert">
												<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										</div>
									@endif

									<form id="edit_currency" method="post" action="{{ url('admin/settings/edit-currency/' . $result->id) }}" class="form-horizontal">
										{{ csrf_field() }}
										<div class="settings-form-card">
											
											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-money"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Edit Currency</h4>
														<p class="settings-form-subtitle">Modify the parameters and exchange rate for {{ $result->name }} ({{ $result->code }})</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Currency Details --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-info-circle"></i>
														<span>Currency Information</span>
													</div>

													{{-- Name --}}
													<div class="settings-field-row">
														<label for="name" class="settings-field-label">Name <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="name" class="form-control settings-input" id="name" placeholder="US Dollar" value="{{ $result->name }}">
															<span class="text-danger f-12">{{ $errors->first("name") }}</span>
														</div>
													</div>

													{{-- Code --}}
													<div class="settings-field-row">
														<label for="code" class="settings-field-label">Code <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="code" class="form-control settings-input" id="code" placeholder="USD" value="{{ $result->code }}">
															<span class="text-danger f-12">{{ $errors->first("code") }}</span>
														</div>
													</div>

													{{-- Symbol --}}
													<div class="settings-field-row">
														<label for="symbol" class="settings-field-label">Symbol <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="symbol" class="form-control settings-input" id="symbol" placeholder="$" value="{{ $result->symbol }}">
															<span class="text-danger f-12">{{ $errors->first("symbol") }}</span>
														</div>
													</div>
												</div>

												{{-- Section: Exchange & Status --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-refresh"></i>
														<span>Exchange Rate & Status</span>
													</div>

													{{-- Rate --}}
													<div class="settings-field-row">
														<label for="rate" class="settings-field-label">Rate <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="rate" class="form-control settings-input" id="rate" placeholder="1.0000" value="{{ $result->rate }}">
															<span class="text-danger f-12">{{ $errors->first("rate") }}</span>
														</div>
													</div>

													{{-- Status --}}
													<div class="settings-field-row">
														<label for="status" class="settings-field-label">Status</label>
														<div class="settings-field-input">
															<select class="form-select settings-input" id="status" name="status">
																<option value="Active" {{ $result->status == "Active" ? 'selected' : '' }}>Active</option>
																<option value="Inactive" {{ $result->status == "Inactive" ? 'selected' : '' }}>Inactive</option>
															</select>
															<span class="text-danger f-12">{{ $errors->first('status') }}</span>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														<i class="fa fa-check me-2"></i>Save Changes
													</button>
													<a class="btn settings-btn-cancel" href="{{ url('admin/settings/currency') }}">Cancel</a>
												</div>
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
			</div>
		</section>
	</div>
</div>
@endsection

@section('validate_script')
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
