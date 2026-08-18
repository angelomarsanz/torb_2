@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">BOOKING</h1>
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
								<form method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="form-horizontal" accept-charset="UTF-8">
									{{ csrf_field() }}
									<div class="settings-form-card">
										
										{{-- Form Header --}}
										<div class="settings-form-header">
											<div class="d-flex align-items-center gap-3">
												<div class="settings-form-icon">
													<i class="fa fa-calendar-check-o"></i>
												</div>
												<div>
													<h4 class="settings-form-title">Booking Strategy</h4>
													<p class="settings-form-subtitle">Choose how guests will book your property</p>
												</div>
											</div>
										</div>

										<div class="settings-form-body">
											
											{{-- Section: Booking Type --}}
											<div class="settings-section">
												<div class="settings-section-label">
													<i class="fa fa-hand-pointer-o"></i>
													<span>Booking Method</span>
												</div>

												<div class="settings-field-row">
													<label class="settings-field-label">Booking Type <span class="text-danger">*</span></label>
													<div class="settings-field-input">
														<select name="booking_type" id="select-booking_type" class="form-select settings-input" style="max-width: 400px;">
															<option value="request" {{ ($result->booking_type == 'request') ? 'selected' : '' }}>Review Each Request (Manual Approval)</option>
															<option value="instant" {{ ($result->booking_type == 'instant') ? 'selected' : '' }}>Instant Book (Auto-confirm Reservations)</option>
														</select>

													</div>
												</div>
											</div>
										</div>

										{{-- Form Footer --}}
										<div class="settings-form-footer">
											<div class="d-flex align-items-center gap-2">
												<a href="{{ url('admin/listing/' . $result->id . '/pricing') }}" class="btn settings-btn-cancel">
													<i class="fa fa-arrow-left me-1 f-12"></i> Back
												</a>
												<button type="submit" class="btn settings-btn-save">
													<i class="fa fa-check-circle me-1 f-14"></i> Finish Setup
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
