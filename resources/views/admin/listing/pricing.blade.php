@extends('admin.template')
@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
					<h1 class="dashboard-page-title m-0">PRICING</h1>
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
								<div class="card-body p-3 p-md-4 pt-md-3">
									<form id="listing_pricing" method="post"
										action="{{ url('admin/listing/' . $result->id . '/' . $step) }}"
										class="form-horizontal" accept-charset="UTF-8">
										{{ csrf_field() }}
										<div class="settings-form-card">

											{{-- Custom Styles for Professional Feedback --}}
											<style>
												.stunning-input {
													transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
												}
												.stunning-input:focus, .stunning-input:active {
													border-color: #3b82f6 !important;
													box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
													outline: 0 !important;
												}
											</style>

											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-money"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Pricing & Availability</h4>
														<p class="settings-form-subtitle">Set your rates and additional fees
														</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Base Price --}}
												<div class="settings-section">
													<div class="settings-section-label mb-3">
														<i class="fa fa-tag"></i>
														<span>Base Nightly Rate</span>
													</div>

													<div class="row g-4 mb-4">
														{{-- Nightly Price --}}
														<div class="col-lg-6">
															<div class="mb-2">
																<label class="settings-field-label fw-700 f-14 text-dark mb-0">Nightly Price <span class="text-danger">*</span></label>
																<p class="text-muted f-12 mt-1 mb-0 fw-500">Base rate for a single night</p>
															</div>
															<div class="stunning-input-group shadow-sm w-100">
																<span class="stunning-input-group-text">{!! $result->property_price->currency->org_symbol !!}</span>
																<input type="text" id="price-night" name="price" value="{{ ($result->property_price->original_price == 0) ? '' : $result->property_price->original_price }}" class="form-control stunning-input" placeholder="0.00">
															</div>
															<span class="text-danger f-11 mt-1 d-block fw-500">{{ $errors->first('price') }}</span>
														</div>

														{{-- Preferred Currency --}}
														<div class="col-lg-6">
															<div class="mb-2">
																<label class="settings-field-label fw-700 f-14 text-dark mb-0">Preferred Currency</label>
																<p class="text-muted f-12 mt-1 mb-0 fw-500">Currency used for your listing</p>
															</div>
															<select name="currency_code" class="form-select shadow-sm stunning-input w-100">
																@foreach ($currency as $key => $value)
																	<option value="{{ $key }}" {{ $result->property_price->currency_code == $key ? 'selected' : '' }}>{{ $value }}</option>
																@endforeach
															</select>
														</div>
													</div>

													@if ($result->property_price->weekly_discount == 0 && $result->property_price->monthly_discount == 0)
														<div class="row mt-3" id="js-set-long-term-prices">
															<div class="col-lg-6">
																<div class="p-3 bg-blue-50 border border-blue-100 rounded-3 d-inline-flex align-items-center gap-3 shadow-sm w-100"
																	style="background: #f0f7ff;">
																	<div class="bg-primary text-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
																		style="width: 32px; height: 32px;">
																		<i class="fa fa-lightbulb-o f-14"></i>
																	</div>
																	<p class="text-slate-700 f-14 mb-0 fw-500">
																		Offer discounts for longer stays? <a
																			href="javascript:void(0)"
																			class="text-primary fw-700 hover-underline ms-1"
																			id="show_long_term">Set weekly & monthly rates</a>
																	</p>
																</div>
															</div>
														</div>
													@endif
												</div>

												{{-- Section: Long-term Prices --}}
												<div class="settings-section {{ ($result->property_price->weekly_discount == 0 && $result->property_price->monthly_discount == 0) ? 'display-off' : 'mt-4 pt-3 border-top' }}"
													id="long-term-div">
													<div class="settings-section-label mb-3">
														<i class="fa fa-percent"></i>
														<span>Long-stay Discounts</span>
													</div>

													<div class="row align-items-center mb-3">
														<div class="col-lg-4">
															<label
																class="settings-field-label fw-700 f-14 text-dark mb-0">Weekly
																Discount</label>
															<p class="text-muted f-12 mt-1 mb-0 fw-500">Applied for stays of
																7+ nights</p>
														</div>
														<div class="col-lg-8">
															<div class="stunning-input-group shadow-sm w-100">
																<input type="text" name="weekly_discount"
																	value="{{ $result->property_price->weekly_discount }}"
																	class="form-control stunning-input">
																<span
																	class="stunning-input-group-text border-start border-end-0">%</span>
															</div>
														</div>
													</div>

													<div class="row align-items-center mb-3">
														<div class="col-lg-4">
															<label
																class="settings-field-label fw-700 f-14 text-dark mb-0">Monthly
																Discount</label>
															<p class="text-muted f-12 mt-1 mb-0 fw-500">Applied for stays of
																28+ nights</p>
														</div>
														<div class="col-lg-8">
															<div class="stunning-input-group shadow-sm w-100">
																<input type="text" name="monthly_discount"
																	value="{{ $result->property_price->monthly_discount }}"
																	class="form-control stunning-input">
																<span
																	class="stunning-input-group-text border-start border-end-0">%</span>
															</div>
														</div>
													</div>
												</div>

												{{-- Section: Additional Fees --}}
												<div class="settings-section mt-4 pt-3 border-top">
													<div class="settings-section-label mb-3">
														<i class="fa fa-plus-circle text-slate-400"></i>
														<span
															class="text-uppercase fw-700 ls-1 f-13 text-slate-500">Additional
															Fees & Rules</span>
													</div>

													{{-- Cleaning Fee --}}
													<div class="row align-items-center py-3 border-bottom-dashed">
														<div class="col-lg-4">
															<label
																class="d-flex align-items-center gap-3 cursor-pointer f-14 fw-700 text-dark mb-0">
																<div
																	class="custom-control custom-checkbox custom-checkbox-lg">
																<input type="checkbox"
																		class="settings-checkbox pricing_checkbox custom-control-input"
																		id="check_cleaning" data-rel="cleaning" {{ ($result->property_price->original_cleaning_fee == 0) ? '' : 'checked' }}>
																	<label class="custom-control-label"
																		for="check_cleaning"></label>
																</div>
																<span>Cleaning Fee</span>
															</label>
														</div>
														<div class="col-lg-8 {{ ($result->property_price->original_cleaning_fee == 0) ? 'display-off' : '' }}"
															id="cleaning">
															<div class="stunning-input-group shadow-sm w-100">
																<span
																	class="stunning-input-group-text">{!! $result->property_price->currency->org_symbol !!}</span>
																<input type="text" name="cleaning_fee"
																	value="{{ $result->property_price->original_cleaning_fee }}"
																	class="form-control stunning-input">
															</div>
														</div>
													</div>

													{{-- Additional Guests --}}
													<div class="row align-items-center py-3 border-bottom-dashed">
														<div class="col-lg-4">
															<label
																class="d-flex align-items-center gap-3 cursor-pointer f-14 fw-700 text-dark mb-0">
																<div
																	class="custom-control custom-checkbox custom-checkbox-lg">
																	<input type="checkbox"
																		class="settings-checkbox pricing_checkbox custom-control-input"
																		id="check_guests" data-rel="additional-guests" {{ ($result->property_price->original_guest_fee == 0) ? '' : 'checked' }}>
																	<label class="custom-control-label"
																		for="check_guests"></label>
																</div>
																<span>Additional Guests</span>
															</label>
														</div>
														<div class="col-lg-8 {{ ($result->property_price->original_guest_fee == 0) ? 'display-off' : '' }}"
															id="additional-guests">
															<div class="d-flex align-items-center gap-3 flex-wrap">
																<div class="stunning-input-group shadow-sm flex-grow-1"
																	style="min-width: 150px;">
																	<span
																		class="stunning-input-group-text">{!! $result->property_price->currency->org_symbol !!}</span>
																	<input type="text" name="guest_fee"
																		value="{{ $result->property_price->original_guest_fee }}"
																		class="form-control stunning-input">
																</div>
																<span class="f-13 fw-600 text-slate-500">charged
																	after</span>
																<select name="guest_after"
																	class="form-select shadow-sm stunning-input flex-grow-1"
																	style="min-width: 140px;">
																	@for ($i = 1; $i <= 16; $i++)
																		<option value="{{ $i }}" {{ ($result->property_price->guest_after == $i) ? 'selected' : '' }}>{{ ($i == 16) ? '16+' : $i }}
																			Guests</option>
																	@endfor
																</select>
															</div>
														</div>
													</div>

													{{-- Security Deposit --}}
													<div class="row align-items-center py-3 border-bottom-dashed">
														<div class="col-lg-4">
															<label
																class="d-flex align-items-center gap-3 cursor-pointer f-14 fw-700 text-dark mb-0">
																<div
																	class="custom-control custom-checkbox custom-checkbox-lg">
																	<input type="checkbox"
																		class="settings-checkbox pricing_checkbox custom-control-input"
																		id="check_security" data-rel="security" {{ ($result?->property_price?->original_security_fee == 0) ? '' : 'checked' }}>
																	<label class="custom-control-label"
																		for="check_security"></label>
																</div>
																<span>Security Deposit</span>
															</label>
														</div>
														<div class="col-lg-8 {{ ($result?->property_price?->original_security_fee == 0) ? 'display-off' : '' }}"
															id="security">
															<div class="stunning-input-group shadow-sm w-100">
																<span
																	class="stunning-input-group-text">{!! $result->property_price->currency->org_symbol !!}</span>
																<input type="text" name="security_fee"
																	value="{{ $result->property_price->original_security_fee }}"
																	class="form-control stunning-input">
															</div>
														</div>
													</div>

													{{-- Weekend Pricing --}}
													<div class="row align-items-center py-3">
														<div class="col-lg-4">
															<label
																class="d-flex align-items-center gap-3 cursor-pointer f-14 fw-700 text-dark mb-0">
																<div
																	class="custom-control custom-checkbox custom-checkbox-lg">
																	<input type="checkbox"
																		class="settings-checkbox pricing_checkbox custom-control-input"
																		id="check_weekend" data-rel="weekend" {{ ($result->property_price->original_weekend_price == 0) ? '' : 'checked' }}>
																	<label class="custom-control-label"
																		for="check_weekend"></label>
																</div>
																<span>Weekend Rate</span>
															</label>
														</div>
														<div class="col-lg-8 {{ ($result->property_price->original_weekend_price == 0) ? 'display-off' : '' }}"
															id="weekend">
															<div class="stunning-input-group shadow-sm w-100">
																<span
																	class="stunning-input-group-text">{!! $result->property_price->currency->org_symbol !!}</span>
																<input type="text" name="weekend_price"
																	value="{{ $result->property_price->original_weekend_price }}"
																	class="form-control stunning-input">
															</div>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<a href="{{ url('admin/listing/' . $result->id . '/photos') }}"
														class="btn settings-btn-cancel">
														<i class="fa fa-arrow-left me-1 f-12"></i> Back
													</a>
													<button type="submit" class="btn settings-btn-save">
														Next Step <i class="fa fa-arrow-right ms-1 f-12"></i>
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
	<script src="{{ asset('public/backend/js/backend.min.js') }}"></script>
	<script type="text/javascript">
			let currencySymbolURL = '{{ url("currency-symbol") }}';
			let nextText = "{{ __('Next') }}..";
			var token = "{{ csrf_token() }}";
			let fieldRequiredText = "{{ __('This field is required.') }}";
			let validNumberText = "{{ __('Please enter a valid number.') }}";
			let priceMinValue = "{{ __('Please enter a value greater than or equal to 5.') }}";
			let discountsMinValue = "{{ __('Please enter a value greater than or equal to 0.') }}";
			let discountsMaxValue = "{{ __('Please enter a value less than or equal to 99.') }}";
			let page = 'pricing';
	</script>
	<script type="text/javascript" src="{{ asset('public/js/listings.js') }}?v={{ time() }}"></script>
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection