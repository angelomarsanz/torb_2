@extends('admin.template')

@push('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('public/js/ninja/ninja-slider.min.css') }}" />
@endpush

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
										<i class="fa fa-book"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Booking Details</h4>
										<p class="settings-form-subtitle">Reference ID: #{{ $result->id }} • Status: 
											@php
												$status = $result->status;
												$badgeClass = 'status-' . strtolower($status);
												$icon = 'fa-circle';
												switch ($status) {
													case 'Accepted': $icon = 'fa-check-circle'; break;
													case 'Pending': $icon = 'fa-clock-o'; break;
													case 'Cancelled': $icon = 'fa-times-circle'; break;
													case 'Expired': $icon = 'fa-exclamation-circle'; break;
													case 'Processing': $icon = 'fa-spinner fa-spin'; break;
												}
											@endphp
											<span class="status-badge {{ $badgeClass }} ms-1">
												<i class="fa {{ $icon }}"></i> {{ $status }}
											</span>
										</p>
									</div>
								</div>
							</div>

							<div class="settings-form-body p-4 pt-0">
								<form action="{{ url('admin/bookings/detail/' . $result->id) }}" method="post" class='form-horizontal'>
									{{ csrf_field() }}
									
									{{-- Section: Property & Participants --}}
									<div class="settings-section mt-4">
										<div class="settings-section-label">
											<i class="fa fa-info-circle"></i>
											<span>Core Information</span>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Property Name</label>
											<div class="settings-field-input pt-2 fw-600 text-primary">
												{{ $result->properties->name }}
											</div>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Host Name</label>
											<div class="settings-field-input pt-2">
												{{ ucfirst($result->properties->users->first_name) }}
											</div>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Guest Name</label>
											<div class="settings-field-input pt-2">
												{{ ucfirst($result->users->first_name) }}
											</div>
										</div>
									</div>

									{{-- Section: Stay Details --}}
									<div class="settings-section">
										<div class="settings-section-label">
											<i class="fa fa-calendar-check-o"></i>
											<span>Stay Details</span>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Checkin / Checkout</label>
											<div class="settings-field-input pt-2">
												<span class="badge bg-light-success text-success p-2 px-3 rounded-pill me-2">
													<i class="fa fa-sign-in me-1"></i> {{ onlyFormat($result->start_date) }}
												</span>
												<span class="badge bg-light-danger text-danger p-2 px-3 rounded-pill">
													<i class="fa fa-sign-out me-1"></i> {{ onlyFormat($result->end_date) }}
												</span>
											</div>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Total Duration</label>
											<div class="settings-field-input pt-2">
												{{ $result->total_night }} {{ $result->total_night > 1 ? 'Nights' : 'Night' }} • {{ $result->guest }} {{ $result->guest > 1 ? 'Guests' : 'Guest' }}
											</div>
										</div>
									</div>

									{{-- Section: Financial Summary --}}
									<div class="settings-section">
										<div class="settings-section-label">
											<i class="fa fa-money"></i>
											<span>Financial Summary</span>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Price per Night</label>
											<div class="settings-field-input pt-2">
												{!! moneyFormat($result->currency->org_symbol, $result->original_per_night) !!}
											</div>
										</div>

										@if ($date_price)
											<div class="settings-field-row">
												<label class="settings-field-label">Daily Breakdown</label>
												<div class="settings-field-input pt-2 f-13">
													<div class="bg-light p-3 rounded-3 border">
														@foreach ($date_price as $datePrice)
															<div class="d-flex justify-content-between mb-1">
																<span class="text-muted">{{ $datePrice->date }}</span>
																<span class="fw-bold">{!! moneyFormat($result->currency->org_symbol, $datePrice->price) !!}</span>
															</div>
														@endforeach
													</div>
												</div>
											</div>
										@endif

										<div class="settings-field-row">
											<label class="settings-field-label">Base Price (Subtotal)</label>
											<div class="settings-field-input pt-2">
												{!! moneyFormat($result->currency->org_symbol, $result->original_base_price) !!}
											</div>
										</div>

										@if($result->original_cleaning_charge > 0)
										<div class="settings-field-row">
											<label class="settings-field-label">Cleaning Fee</label>
											<div class="settings-field-input pt-2">
												{!! moneyFormat($result->currency->org_symbol, $result->original_cleaning_charge) !!}
											</div>
										</div>
										@endif

										@if($result->iva_tax > 0)
										<div class="settings-field-row">
											<label class="settings-field-label">I.V.A Tax</label>
											<div class="settings-field-input pt-2">
												{!! moneyFormat($result->currency->org_symbol, $result->iva_tax) !!}
											</div>
										</div>
										@endif

										@if($result->accomodation_tax > 0)
										<div class="settings-field-row">
											<label class="settings-field-label">Accomodation Tax</label>
											<div class="settings-field-input pt-2">
												{!! moneyFormat($result->currency->org_symbol, $result->accomodation_tax) !!}
											</div>
										</div>
										@endif

										<div class="settings-field-row bg-light py-3 rounded-3 mt-3">
											<label class="settings-field-label fw-bold h5 mb-0">Total Amount</label>
											<div class="settings-field-input pt-1 fw-bold h5 mb-0 text-success">
												{!! moneyFormat($result->currency->org_symbol, $result->original_total) !!}
											</div>
										</div>
									</div>

									{{-- Section: Payment & Transaction --}}
									<div class="settings-section">
										<div class="settings-section-label">
											<i class="fa fa-credit-card"></i>
											<span>Payment Details</span>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Payment Method</label>
											<div class="settings-field-input pt-2">
												{{ $result?->payment_methods?->name ?? 'N/A' }}
											</div>
										</div>

										@if ($result->transaction_id)
										<div class="settings-field-row">
											<label class="settings-field-label">Transaction ID</label>
											<div class="settings-field-input pt-2 text-muted">
												<code>{{ $result->transaction_id }}</code>
											</div>
										</div>
										@endif

										@if ($result->status == "Cancelled")
											<div class="settings-field-row">
												<label class="settings-field-label">Cancellation Info</label>
												<div class="settings-field-input pt-2 text-danger">
													Cancelled by {{ $result->cancelled_by }} on {{ dateFormat($result->cancelled_at) }}
												</div>
											</div>
										@endif

										@if ($result->payment_methods?->alias == 'directbanktransfer')
											<div class="settings-field-row">
												<label class="settings-field-label">Bank Details</label>
												<div class="settings-field-input pt-2">
													<div class="f-13 text-muted">
														<div><strong>A/C Name:</strong> {{ ($result->bank && $result->bank->account_name) ? $result->bank->account_name : 'N/A' }}</div>
														<div><strong>A/C No:</strong> {{ ($result->bank && $result->bank->iban) ? $result->bank->iban : 'N/A' }}</div>
														<div><strong>Bank:</strong> {{ ($result->bank && $result->bank->bank_name) ? $result->bank->bank_name : 'N/A' }}</div>
													</div>
												</div>
											</div>

											@if($result->attachment && count($result->attachment) > 0)
											<div class="settings-field-row">
												<label class="settings-field-label">Bank Attachments</label>
												<div class="settings-field-input pt-2">
													<div class="d-flex flex-wrap gap-2">
														@foreach ($result->attachment as $i => $item)
															<img src="{{ $item }}" alt="attachment" class="rounded border shadow-sm cursor-pointer hover-scale" style="width: 80px; height: 60px; object-fit: cover;" onclick="lightbox({{ $i }})" />
														@endforeach
													</div>
												</div>
											</div>
											@endif
										@endif
									</div>

									@if (($result->status == 'Pending' && $result->booking_type == 'instant') || $result->status == 'Processing')
									<div class="settings-section bg-light-info p-4 rounded-4 mt-4 text-center">
										<p class="mb-3 fw-bold">Action Required: Confirm this booking?</p>
										<div class="d-flex justify-content-center gap-3">
											<a href="{{ url('/admin/bookings/edit/confirm/' . $result->id) }}" class="btn settings-btn-save h-42 px-5">Accept</a>
											<a href="{{ url('/admin/bookings/edit/decline/' . $result->id) }}" class="btn settings-btn-cancel h-42 px-5">Decline</a>
										</div>
									</div>
									@endif

									<div class="settings-form-footer border-0 pt-4 mt-2">
										<div class="d-flex align-items-center gap-2">
											<a class="btn settings-btn-save px-4" href="{{ url('admin/bookings') }}">
												<i class="fa fa-arrow-left me-2"></i>Back to List
											</a>
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

{{-- Hidden slider components for lightbox --}}
<div class="d-none">
	<div id="ninja-slider-mobile">
		<div class="slider-inner">
			<ul>
				@foreach ($result->attachment ?? [] as $item)
				<li><a class="ns-img" href="{{ $item }}"></a></li>
				@endforeach
			</ul>
			<div id="fsBtn" class="fs-icon" title="Expand/Close"></div>
		</div>
	</div>
	<div id="ninja-slider">
		<div class="slider-inner">
			<ul>
				@foreach ($result->attachment ?? [] as $item)
				<li><a class="ns-img" href="{{ $item }}"></a></li>
				@endforeach
			</ul>
			<div id="fsBtn" class="fs-icon" title="Expand/Close"></div>
		</div>
	</div>
</div>
@endsection

@section('validate_script')
	<script type="text/javascript" src="{{ asset('public/js/ninja/ninja-slider.js') }}"></script>
	<script src="{{ asset('public/backend/js/booking-detail.min.js') }}"></script>
@endsection
