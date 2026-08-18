@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content">
			<div class="container-fluid px-0">
				<div class="row justify-content-center">
					<div class="col-lg-10 col-xl-8 mt-0">
						<div class="settings-form-card">
							<div class="settings-form-header">
								<div class="d-flex align-items-center gap-3">
									<div class="settings-form-icon">
										<i class="fa fa-money"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Payout Details</h4>
										<p class="settings-form-subtitle">Reference ID: #{{ $withDrawal->id }}</p>
									</div>
								</div>
							</div>

							<div class="settings-form-body p-4 pt-0">
								<div class="settings-section mt-4">
									<div class="settings-section-label">
										<i class="fa fa-info-circle"></i>
										<span>Withdrawal Information</span>
									</div>

									<div class="settings-field-row">
										<label class="settings-field-label">User Name</label>
										<div class="settings-field-input pt-2 fw-600">
											{{ $withDrawal->user->full_name }}
										</div>
									</div>

									<div class="settings-field-row">
										<label class="settings-field-label">Payment Method</label>
										<div class="settings-field-input pt-2">
											{{ $withDrawal->payment_methods->name }}
										</div>
									</div>

									<div class="settings-field-row">
										<label class="settings-field-label">Payout Amount</label>
										<div class="settings-field-input pt-2 fw-bold text-dark h5 mb-0">
											{!! $withDrawal->currency->symbol !!} {{ $withDrawal->amount }}
										</div>
									</div>

									<div class="settings-field-row">
										<label class="settings-field-label">Status</label>
										<div class="settings-field-input pt-2">
											@php
												$status = $withDrawal->status;
												$badgeClass = ($status == 'Success') ? 'status-accepted' : (($status == 'Pending') ? 'status-pending' : 'status-expired');
												$icon = ($status == 'Success') ? 'fa-check-circle' : (($status == 'Pending') ? 'fa-clock-o' : 'fa-info-circle');
											@endphp
											<span class="status-badge {{ $badgeClass }}">
												<i class="fa {{ $icon }}"></i> {{ $status }}
											</span>
										</div>
									</div>
								</div>

								<div class="settings-form-footer border-0 pt-4 mt-2">
									<div class="d-flex align-items-center gap-2">
										<a class="btn settings-btn-save px-4" href="{{ url('admin/payouts') }}">
											<i class="fa fa-arrow-left me-2"></i>Back to List
										</a>
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
