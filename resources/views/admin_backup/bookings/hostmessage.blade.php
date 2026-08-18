@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Message <small class="text-muted fw-normal">Host message</small></h1>
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
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title">Conversation</h3>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-9 col-sm-9 col-12">
									@if ($messages[0]->type_id == 4)
										<div class="alert alert-info text-center" role="alert">
											<h5 class="alert-heading mb-1">{{ __('Request sent') }}</h5>
											<p class="mb-0 f-14">{{ __('Your booking isn\'t confirmed yet. You\'ll get reply within 24 hours.') }}</p>
										</div>
									@endif

									@if ($messages[0]->type_id == 5)
										<div class="alert alert-success text-center" role="alert">
											<h5 class="alert-heading mb-1">{{ __('Booking confirmed.') }} {{ optional($messages[0]->bookings)->properties->property_address->city }}, {{ optional($messages[0]->bookings)->properties->property_address->country_name }}</h5>
										</div>
									@endif

									@if ($messages[0]->type_id == 6)
										<div class="alert alert-warning text-center" role="alert">
											<h5 class="alert-heading mb-1">{{ __('Request declined') }}</h5>
											<p class="mb-1 f-14">{{ __('There are more place around.') }}</p>
											<a href="{{ url('search?location=' . optional($messages[0]->bookings)->properties->property_address->city) }}" class="btn btn-sm btn-outline-primary">{{ __('Keep Searching') }}</a>
										</div>
									@endif

									<div class="card shadow-sm mb-3">
										<div class="card-body">
											<form action="{{ url('admin/reply/' . $messages[0]->booking_id) }}" method="post" id="msg_reply">
												{{ csrf_field() }}
												<input type="hidden" value="{{ $messages[0]->booking_id }}" name="booking_id">
												<input type="hidden" name="property_id" value="{{ optional($messages[0]->bookings)->property_id }}">
												<input type="hidden" name="start_date" value="{{ optional($messages[0]->bookings)->start_date }}">
												<input type="hidden" name="end_date" value="{{ optional($messages[0]->bookings)->end_date }}">
												<input type="hidden" name="price" value="{{ optional($messages[0]->bookings)->total }}">
												<div class="mb-3">
													<label class="form-label fw-bold" for="message_text">Reply Message</label>
													<textarea rows="3" class="form-control f-14" id="message_text" name="message" placeholder="Type your message..."></textarea>
												</div>
												<div class="text-end">
													<input type="submit" class="btn btn-success f-14" id="reply_message" value="{{ __('Send Message') }}">
												</div>
											</form>
											@if ($errors->has('message'))
												<p class="text-danger f-12 mt-1 mb-0">{{ $errors->first('message') }}</p>
											@endif
										</div>
									</div>

									<div id="message-list">
										@for ($i=0; $i<count($messages); $i++)
											@if ($messages[$i]->sender_id == optional($messages[0]->bookings)->host_id)
												<div class="row mb-3 align-items-start">
													<div class="col-md-2 col-sm-3 col-3">
														<div class="text-center">
															<a href="{{ url('admin/edit-customer/' . optional($messages[$i]->bookings)->host_id) }}">
																<img class="rounded-circle" width="50" height="50" src="{{ optional($messages[$i]->bookings)->properties->users->profile_src }}">
															</a>
														</div>
													</div>
													<div class="col-md-10 col-sm-9 col-9">
														<div class="card bg-light shadow-sm">
															<div class="card-body py-2 px-3">
																<p class="mb-1 f-14">{{ $messages[$i]->message }}</p>
																<small class="text-muted">{{ dateFormat($messages[$i]->created_at) }}</small>
															</div>
														</div>
													</div>
												</div>
											@endif

											@if ($messages[$i]->sender_id != optional($messages[0]->bookings)->host_id)
												@if ($messages[$i]->type_id == 4)
													<div class="row mb-3">
														<div class="col-md-10 col-sm-9 col-9">
															<div class="card shadow-sm border-info">
																<div class="card-body py-2 px-3">
																	<p class="fw-semibold mb-1">
																		{{ __('Inquiry about') }} <a href="{{ url('properties/' . optional($messages[$i]->bookings)->properties->slug) }}">{{ optional($messages[$i]->bookings)->properties->name }}</a>
																	</p>
																	<p class="text-muted f-14 mb-0">
																		{{ optional($messages[$i]->bookings)->date_range }}
																		&middot;
																		{{ optional($messages[$i]->bookings)->guest }} {{ __('Guests') }}{{ (optional($messages[$i]->bookings)->guest > 1) ? 's' : '' }}
																		<br>
																		{{ __('You will get') }} {!! moneyFormat(optional($messages[$i]->bookings)->currency->symbol, optional($messages[$i]->bookings)->host_payout) !!}
																	</p>
																</div>
															</div>
														</div>
													</div>
												@endif

												<div class="row mb-3 align-items-start">
													<div class="col-md-10 col-sm-9 col-9">
														<div class="card shadow-sm">
															<div class="card-body py-2 px-3">
																<p class="mb-1 f-14">{{ $messages[$i]->message }}</p>
																<small class="text-muted">{{ dateFormat($messages[$i]->created_at) }}</small>
															</div>
														</div>
													</div>
													<div class="col-md-2 col-sm-3 col-3">
														<div class="text-center">
															<a href="{{ url('admin/edit-customer/' . optional($messages[$i]->bookings)->user_id) }}">
																@if ((optional($messages[$i]->bookings)->users->profile_image != '') && (optional($messages[$i]->bookings)->users->profile_image != NULL))
																	<img class="rounded-circle" width="50" height="50" src="{{ optional($messages[$i]->bookings)->users->profile_src }}">
																@else
																	<img class="rounded-circle" width="50" height="50" src="{{ optional($messages[0]->bookings)->users->profile_src }}">
																@endif
															</a>
														</div>
													</div>
												</div>
											@endif
										@endfor
									</div>
								</div>

								<div class="col-md-3 col-sm-3 col-12">
									<div class="card shadow-sm">
										<div class="card-body text-center">
											<a href="{{ url('admin/edit-customer/' . optional($messages[0]->bookings)->user_id) }}">
												<img width="100" height="100" class="rounded-circle mb-2" alt="{{ optional($messages[0]->bookings)->users->first_name }}" src="{{ optional($messages[0]->bookings)->users->profile_src }}">
											</a>
											<h5 class="mb-1">
												<a class="text-decoration-none text-dark" href="{{ url('admin/edit-customer/' . optional($messages[0]->bookings)->user_id) }}">
													{{ optional($messages[0]->bookings)->users->first_name . ' ' . optional($messages[0]->bookings)->users->last_name }}
												</a>
											</h5>
											<small class="text-muted">{{ __('Member since') }} {{ date('Y', strtotime(optional($messages[0]->bookings)->users->created_at)) }}</small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
	<script src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endpush
