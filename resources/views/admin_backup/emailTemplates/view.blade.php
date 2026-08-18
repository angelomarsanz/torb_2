@extends('admin.template')

@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Email Templates</h1>
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
				<div class="col-lg-3 col-12">
					@include('admin.common.mail_menu')
				</div>

				<div class="col-lg-9 col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h3 class="card-title mb-0">
								@if ($tempId == 1)
									{{ "Account Information Default Update Template" }}
								@elseif ($tempId == 2)
									{{ "Account Information Update Template" }}
								@elseif ($tempId == 3)
									{{ "Account Information Delete Template" }}
								@elseif ($tempId == 4)
									{{ "Booking Template" }}
								@elseif ($tempId == 5)
									{{ "Email Confirm Template" }}
								@elseif ($tempId == 6)
									{{ "Forget Password Template" }}
								@elseif ($tempId == 7)
									{{ "Need Payment Account Template" }}
								@elseif ($tempId == 8)
									{{ "Payout Sent Template" }}
								@elseif ($tempId == 9)
									{{ "Booking Cancelled Template" }}
								@elseif ($tempId == 10)
									{{ "Booking Accepted/Declined Template" }}
								@elseif ($tempId == 11)
									{{ "Booking Request Send Template" }}
								@elseif ($tempId == 12)
									{{ "Booking Confirmation Template" }}
								@elseif ($tempId == 13)
									{{ "Property Booking Notify Template" }}
								@elseif ($tempId == 14)
									{{ "Property Booking Payment Notify Template" }}
								@elseif ($tempId == 15)
									{{ "Payout Request Received Template" }}
								@elseif ($tempId == 16)
									{{ "Property Listing Approved Template" }}
								@elseif ($tempId == 17)
									{{ "Payout Request Approved Template" }}
								@endif
							</h3>
							<button class="btn btn-success btn-sm f-14" id="available">Available Variable</button>
						</div>

						<div class="card-body d-none" id="variable">
							@if ($tempId == 1)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Site Name : <code>{site_name}</code></p>
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Date Time : <code>{date_time}</code></p>
									</div>
								</div>
							@elseif ($tempId == 2)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Site Name : <code>{site_name}</code></p>
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Date Time : <code>{date_time}</code></p>
									</div>
								</div>
							@elseif ($tempId == 3)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Site Name : <code>{site_name}</code></p>
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Date Time : <code>{date_time}</code></p>
									</div>
								</div>
							@elseif ($tempId == 4)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Start Date : <code>{start_date}</code></p>
										<p class="mb-1 f-14">Total Guest : <code>{total_guest}</code></p>
										<p class="mb-1 f-14">Message : <code>{messages_message}</code></p>
										<p class="mb-1 f-14">Night : <code>{night/nights}</code></p>
										<p class="mb-1 f-14">Payment Method : <code>{payment_method}</code></p>
									</div>
									<div class="col-md-6">
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Owner First Name : <code>{owner_first_name}</code></p>
										<p class="mb-1 f-14">User First Name : <code>{user_first_name}</code></p>
										<p class="mb-1 f-14">Total Nights : <code>{total_night}</code></p>
									</div>
								</div>
							@elseif ($tempId == 5)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Site Name : <code>{site_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 6)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 7)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Currency Symbol : <code>{currency_symbol}</code></p>
										<p class="mb-1 f-14">Payout Amount : <code>{payout_amount}</code></p>
									</div>
								</div>
							@elseif ($tempId == 8)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Site Name : <code>{site_name}</code></p>
										<p class="mb-1 f-14">First Name : <code>{first_name}</code></p>
										<p class="mb-1 f-14">Currency Symbol : <code>{currency_symbol}</code></p>
									</div>
									<div class="col-md-6">
										<p class="mb-1 f-14">Payout Amount : <code>{payout_amount}</code></p>
										<p class="mb-1 f-14">Payment Method : <code>{payout_payment_method}</code></p>
									</div>
								</div>
							@elseif ($tempId == 9)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Accepted/Declined : <code>{Accepted/Declined}</code></p>
										<p class="mb-1 f-14">Guest First Name : <code>{guest_first_name}</code></p>
										<p class="mb-1 f-14">Host First Name : <code>{host_first_name}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 10)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Accepted/Declined : <code>{Accepted/Declined}</code></p>
										<p class="mb-1 f-14">Guest First Name : <code>{guest_first_name}</code></p>
										<p class="mb-1 f-14">Host First Name : <code>{host_first_name}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 11)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Host name : <code>{owner_first_name}</code></p>
										<p class="mb-1 f-14">Total Night : <code>{total_night}</code></p>
										<p class="mb-1 f-14">User First Name : <code>{user_first_name}</code></p>
										<p class="mb-1 f-14">Number of Guest : <code>{total_guest}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Check-in Time : <code>{start_date}</code></p>
									</div>
								</div>
							@elseif ($tempId == 12)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Total Night : <code>{total_night}</code></p>
										<p class="mb-1 f-14">User First Name : <code>{user_first_name}</code></p>
										<p class="mb-1 f-14">Number of Guest : <code>{total_guest}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Check-in Time : <code>{start_date}</code></p>
										<p class="mb-1 f-14">Total amount : <code>{total_amount}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 13)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Host name : <code>{owner_first_name}</code></p>
										<p class="mb-1 f-14">Guest first name : <code>{guest_first_name}</code></p>
										<p class="mb-1 f-14">Guest full name : <code>{guest_name}</code></p>
										<p class="mb-1 f-14">Guest email : <code>{guest_email}</code></p>
										<p class="mb-1 f-14">Total Night : <code>{total_night}</code></p>
										<p class="mb-1 f-14">User First Name : <code>{user_first_name}</code></p>
									</div>
									<div class="col-md-6">
										<p class="mb-1 f-14">Number of Guest : <code>{total_guest}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Check-in Time : <code>{start_date}</code></p>
										<p class="mb-1 f-14">Total amount : <code>{total_amount}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 14)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Admin name : <code>{admin_first_name}</code></p>
										<p class="mb-1 f-14">Guest full name : <code>{guest_name}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Payment method : <code>{payment_method}</code></p>
										<p class="mb-1 f-14">Payment amount : <code>{payment_amount}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 15)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Admin name : <code>{admin_first_name}</code></p>
										<p class="mb-1 f-14">Requestor's full name : <code>{user_name}</code></p>
										<p class="mb-1 f-14">Requestor's email : <code>{user_email}</code></p>
										<p class="mb-1 f-14">Payment method : <code>{payment_method}</code></p>
									</div>
									<div class="col-md-6">
										<p class="mb-1 f-14">Requested amount : <code>{requested_amount}</code></p>
										<p class="mb-1 f-14">Requested date : <code>{requested_date}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 16)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">Admin name : <code>{admin_first_name}</code></p>
										<p class="mb-1 f-14">Host name : <code>{host_name}</code></p>
										<p class="mb-1 f-14">Property Name : <code>{property_name}</code></p>
										<p class="mb-1 f-14">Property address : <code>{property_address}</code></p>
										<p class="mb-1 f-14">Listed date : <code>{listed_date}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@elseif ($tempId == 17)
								<div class="row">
									<div class="col-md-6">
										<p class="mb-1 f-14">User name : <code>{user_name}</code></p>
										<p class="mb-1 f-14">Total Amount : <code>{total_amount}</code></p>
										<p class="mb-1 f-14">Payment method : <code>{payment_method}</code></p>
										<p class="mb-1 f-14">Accepteance Date : <code>{accepted_date}</code></p>
										<p class="mb-1 f-14">Company Name : <code>{company_name}</code></p>
									</div>
								</div>
							@endif
						</div>

						<form action='{{ url("admin/email-template/" . $tempId) }}' method="post" id="myform">
							{!! csrf_field() !!}
							<div class="card-body">
								<div class="row mb-3">
									<label for="exampleInputEmail1" class="col-md-3 col-form-label text-md-end fw-bold">Subject</label>
									<div class="col-md-8">
										<input class="form-control f-14" name="en[subject]" type="text" value="{{ $temp_Data[0]->subject }}">
										<input type="hidden" name="en[id]" value="1">
									</div>
								</div>

								<div class="row mb-3">
									<label class="col-md-3 col-form-label text-md-end fw-bold">Body</label>
									<div class="col-md-8">
										<textarea id="compose-textarea" name="en[body]" class="form-control f-14 editor" style="height: 300px">
											{{ $temp_Data[0]->body }}
										</textarea>
									</div>
								</div>

								<div class="accordion" id="accordion">
									@foreach ($languages as $key => $language)
										@php if ($language->short_name == 'en') {continue;} @endphp

										<div class="accordion-item">
											<h2 class="accordion-header">
												<button class="accordion-button collapsed f-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $language->short_name }}" aria-expanded="false" aria-controls="collapse{{ $language->short_name }}">
													{{ $language->name }}
												</button>
											</h2>
											<div id="collapse{{ $language->short_name }}" class="accordion-collapse collapse" data-bs-parent="#accordion">
												<div class="accordion-body">
													<div class="row mb-3">
														<label class="col-md-3 col-form-label text-md-end fw-bold">Subject</label>
														<div class="col-md-8">
															<input class="form-control f-14" name="{{ $language->short_name }}[subject]" type="text" value="{{ isset($temp_Data[$key]->subject) ? $temp_Data[$key]->subject : 'Subject' }}">
															<input type="hidden" name="{{ $language->short_name }}[id]" value="{{ $language->id }}">
														</div>
													</div>

													<div class="row mb-3">
														<label class="col-md-3 col-form-label text-md-end fw-bold">Body</label>
														<div class="col-md-8">
															<textarea id="compose-textarea" name="{{ $language->short_name }}[body]" class="form-control f-14 editor" style="height: 300px">
																{{ isset($temp_Data[$key]->body) ? $temp_Data[$key]->body : 'Body' }}
															</textarea>
														</div>
													</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>

							<div class="card-footer text-end">
								<button type="submit" class="btn btn-info text-white f-14">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/js/backend.min.js') }}"></script>
@endsection
