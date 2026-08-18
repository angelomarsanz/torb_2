@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Pricing</h1>
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
					<form id="listing_pricing" method="post" action="{{ url('admin/listing/' . $result->id. '/' . $step) }}" class="signup-form login-form" accept-charset="UTF-8">
						{{ csrf_field() }}
						<div class="card card-outline card-info shadow-sm">
							<div class="card-header">
								<h3 class="card-title">Base Price</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-8 mb-3">
										<label for="listing_price_native" class="fw-bold f-14">Nightly Price <span class="text-danger">*</span></label>
										<div class="input-addon">
											<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
											<input type="text" data-suggested="" id="price-night" value="{{ ($result->property_price->original_price == 0) ? '' : $result->property_price->original_price }}" name="price" class="money-input form-control f-14">
										</div>
										<span class="text-danger f-12">{{ $errors->first('price') }}</span>
									</div>
									<div class="col-md-8 mb-3">
										<label class="fw-bold f-14">Currency</label>
										<select id="price-select-currency_code" name="currency_code" class="form-select f-14">
											@foreach ($currency as $key => $value)
												<option value="{{ $key }}" {{ $result->property_price->currency_code == $key ? 'selected' : '' }}>{{ $value }}</option>
											@endforeach
										</select>
									</div>
									<div class="col-md-8">
										@if ($result->property_price->weekly_discount == 0 && $result->property_price->monthly_discount == 0)
											<p id="js-set-long-term-prices" class="text-center text-muted set-long-term-prices f-14 mt-1">
												You can offer discounts for longer stays by setting <a data-prevent-default="" href="javascript:void(0)" id="show_long_term">weekly and monthly</a> prices.
											</p>
											<hr class="set-long-term-prices">
										@endif
									</div>
								</div>

								<div class="row {{ ($result->property_price->weekly_discount == 0 && $result->property_price->monthly_discount == 0) ? 'display-off' : '' }}" id="long-term-div">
									<div class="col-md-12">
										<h5 class="fw-bold mb-3">Long-term prices</h5>
									</div>
									<div class="col-md-8 mb-3">
										<label for="listing_price_native" class="fw-bold f-14 mb-1">Weekly Discount Percent (%)</label>
										<div class="input-addon">
											<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
											<input type="text" data-suggested="" id="price-week" value="{{ $result->property_price->weekly_discount }}" name="weekly_discount" data-saving="long_price" class="money-input form-control f-14">
										</div>
									</div>
									<div class="col-md-8 mb-3">
										<label for="listing_price_native" class="fw-bold f-14 mb-1">Monthly Discount Percent (%)</label>
										<div class="input-addon">
											<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
											<input type="text" data-suggested="₹16905" id="price-month" class="money-input form-control f-14" value="{{ $result->property_price->monthly_discount }}" name="monthly_discount" data-saving="long_price">
										</div>
									</div>
								</div>

								<hr>
								<h5 class="fw-bold mb-3">Additional Pricing Options</h5>

								<div class="row">
									<div class="col-md-12 mb-2">
										<label class="fw-bold f-14 label-inline">
											<input type="checkbox" data-extras="true" class="pricing_checkbox" data-rel="cleaning" {{ ($result->property_price->original_cleaning_fee == 0) ? '' : 'checked = "checked"' }}>&nbsp;
											Cleaning fee
										</label>
									</div>
									<div id="cleaning" class="col-md-12 mb-3 {{ ($result->property_price->original_cleaning_fee == 0) ? 'display-off' : '' }}">
										<div class="col-md-4">
											<div class="input-addon">
												<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
												<input type="text" data-extras="true" id="price-cleaning" value="{{ $result->property_price->original_cleaning_fee }}" name="cleaning_fee" class="money-input form-control f-14" data-saving="additional-saving">
											</div>
										</div>
									</div>

									<div class="col-md-12 mb-2">
										<label class="fw-bold f-14 label-inline">
											<input type="checkbox" class="pricing_checkbox" data-rel="additional-guests" {{ ($result->property_price->original_guest_fee == 0) ? '' : 'checked = "checked"' }}>&nbsp;
											Additional guests
										</label>
									</div>
									<div id="additional-guests" class="col-md-12 mb-3 {{ ($result->property_price->original_guest_fee == 0) ? 'display-off' : '' }}">
										<div class="row align-items-center">
											<div class="col-md-4">
												<div class="input-addon">
													<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
													<input type="text" data-extras="true" value="{{ $result->property_price->original_guest_fee }}" id="price-extra_person" name="guest_fee" class="money-input form-control f-14" data-saving="additional-saving">
												</div>
											</div>
											<div class="col-md-4 text-end">
												<label class="fw-bold f-14">For each guest after</label>
											</div>
											<div class="col-md-4">
												<select id="price-select-guests_included" name="guest_after" data-saving="additional-saving" class="form-select f-14">
													@for ($i=1;$i<=16;$i++)
														<option value="{{ $i }}" {{ ($result->property_price->guest_after == $i) ? 'selected' : '' }}>
															{{ ($i == '16') ? $i . '+' : $i }}
														</option>
													@endfor
												</select>
											</div>
										</div>
									</div>

									<div class="col-md-12 mb-2">
										<label class="fw-bold f-14 label-inline">
											<input type="checkbox" class="pricing_checkbox" data-rel="security" {{ ($result?->property_price?->original_security_fee == 0) ? '' : 'checked = "checked"' }}>&nbsp;
											Security deposit
										</label>
									</div>
									<div id="security" class="col-md-12 mb-3 {{ ($result->property_price->original_security_fee == 0) ? 'display-off' : '' }}">
										<div class="col-md-4">
											<div class="input-addon">
												<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
												<input type="text" class="money-input form-control f-14" data-extras="true" value="{{ $result->property_price->original_security_fee }}" id="price-security" name="security_fee" data-saving="additional-saving">
											</div>
										</div>
									</div>

									<div class="col-md-12 mb-2">
										<label class="fw-bold f-14 label-inline">
											<input type="checkbox" class="pricing_checkbox" data-rel="weekend" {{ ($result->property_price->original_weekend_price == 0) ? '' : 'checked = "checked"' }}>&nbsp;
											Weekend pricing
										</label>
									</div>
									<div id="weekend" class="col-md-12 mb-3 {{ ($result->property_price->original_weekend_price == 0) ? 'display-off' : '' }}">
										<div class="col-md-4">
											<div class="input-addon">
												<span class="input-prefix pay-currency">{!! $result->property_price->currency->org_symbol !!}</span>
												<input type="text" data-extras="true" value="{{ $result->property_price->original_weekend_price }}" id="price-weekend" name="weekend_price" class="money-input form-control f-14" data-saving="additional-saving">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="card-footer d-flex justify-content-between">
								<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/photos') }}" class="btn btn-outline-secondary f-14">
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
<script type="text/javascript" src="{{ asset('public/js/listings.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
