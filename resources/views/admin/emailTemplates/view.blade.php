@extends('admin.template')

@section('main')
<style>
	/* Workbench Elite: Modernization Overhaul */
	:root {
		--wb-primary: #2563eb;
		--wb-bg: #f8fafc;
		--wb-card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
		--wb-border: #e2e8f0;
	}

	.dashboard-page-wrapper {
		background-color: var(--wb-bg) !important;
	}

	.stunning-table-card {
		border: none !important;
		box-shadow: var(--wb-card-shadow) !important;
		border-radius: 1rem !important;
		background: #fff !important;
	}

	.workbench-container {
		display: flex;
		gap: 2rem;
		align-items: flex-start;
	}

	/* Sidebar Menu Refinement */
	.workbench-sidebar {
		flex: 0 0 260px;
		background: #fff;
		border-right: 1px solid var(--wb-border);
		padding-right: 1rem;
	}

	.workbench-menu-item {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		padding: 0.875rem 1rem;
		border-radius: 0.5rem;
		color: #64748b;
		font-weight: 500;
		font-size: 0.875rem;
		transition: all 0.2s ease;
		margin-bottom: 0.25rem;
		text-decoration: none !important;
	}

	.workbench-menu-item i {
		font-size: 1rem;
		width: 20px;
		text-align: center;
	}

	.workbench-menu-item:hover {
		background: #f1f5f9;
		color: var(--wb-primary);
	}

	.workbench-menu-item.active {
		background: #eff6ff;
		color: var(--wb-primary);
		font-weight: 600;
		box-shadow: inset 3px 0 0 var(--wb-primary);
	}

	/* Main Content Area */
	.workbench-main {
		flex: 1;
		min-width: 0;
	}

	.settings-form-card {
		border: 1px solid var(--wb-border) !important;
		border-radius: 0.75rem !important;
	}

	.settings-field-label {
		color: #1e293b;
		font-weight: 600;
		font-size: 0.875rem;
	}

	.settings-input {
		border: 1px solid var(--wb-border) !important;
		padding: 0.75rem 1rem !important;
		border-radius: 0.5rem !important;
		font-size: 0.9375rem !important;
		transition: border-color 0.2s, box-shadow 0.2s;
	}

	.settings-input:focus {
		border-color: var(--wb-primary) !important;
		box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
		outline: none;
	}

	/* Custom Accordion Styling */
	.accordion-item {
		border: 1px solid var(--wb-border) !important;
		border-radius: 0.5rem !important;
		margin-bottom: 0.75rem !important;
		overflow: hidden;
	}

	.accordion-button {
		background: #fff !important;
		padding: 1rem 1.25rem !important;
		font-weight: 600 !important;
		color: #334155 !important;
	}

	.accordion-button:not(.collapsed) {
		background: #f8fafc !important;
		color: var(--wb-primary) !important;
		box-shadow: none !important;
	}

	.accordion-button::after {
		background-size: 1rem !important;
	}

	/* Variable Chips Refinement */
	.workbench-right {
		flex: 0 0 280px;
		position: sticky;
		top: 2rem;
	}

	.workbench-ref-card {
		background: #fff;
		border: 1px solid var(--wb-border);
		border-radius: 0.75rem;
		padding: 1.25rem;
	}

	.workbench-ref-header h5 {
		font-size: 0.8125rem;
		font-weight: 700;
		color: #94a3b8;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		margin-bottom: 1rem;
	}

	.ref-badge {
		background: #f1f5f9;
		border: 1px solid #e2e8f0;
		padding: 0.5rem 0.75rem;
		border-radius: 0.375rem;
		margin-bottom: 0.625rem;
		display: flex;
		flex-direction: column;
		gap: 0.125rem;
		transition: all 0.2s;
		cursor: default;
	}

	.ref-badge:hover {
		border-color: #cbd5e1;
		background: #f8fafc;
	}

	.ref-badge code {
		color: var(--wb-primary);
		font-weight: 700;
		font-size: 0.875rem;
		font-family: 'Fira Code', 'Monaco', monospace;
	}

	.ref-badge-label {
		font-size: 0.6875rem;
		color: #64748b;
		font-weight: 500;
	}

	/* CKEditor Wrap Overrides */
	.cke_chrome {
		border: 1px solid var(--wb-border) !important;
		box-shadow: none !important;
		border-radius: 0 0 0.5rem 0.5rem !important;
	}

	.cke_top {
		background: #f8fafc !important;
		border-bottom: 1px solid var(--wb-border) !important;
	}

	.editor-wrap {
		border-radius: 0.5rem !important;
	}

	.settings-btn-save {
		background: var(--wb-primary) !important;
		border: none !important;
		font-weight: 600 !important;
		border-radius: 0.5rem !important;
		box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2) !important;
		transition: all 0.2s;
	}

	.settings-btn-save:hover {
		transform: translateY(-1px);
		box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3) !important;
	}

	/* ═══════════════════════════════════════════
	   Mobile & Responsive Enhancements
	   ═══════════════════════════════════════════ */

	@media (max-width: 1199px) {
		.workbench-container {
			flex-direction: column;
		}

		.workbench-sidebar, .workbench-main, .workbench-right {
			width: 100% !important;
			flex: none !important;
			max-width: 100% !important;
		}

		.workbench-sidebar {
			border-right: none;
			border-bottom: 1px solid var(--wb-border);
			padding-right: 0;
			padding-bottom: 1rem;
		}

		.workbench-right {
			position: static;
		}
	}

	@media (max-width: 991px) {
		/* Horizontal scrollable nav for templates on mobile */
		.workbench-sidebar {
			background: transparent;
			padding-bottom: 0.5rem;
		}

		.workbench-sidebar .text-muted {
			display: none; /* Hide 'Templates' label on small screens */
		}

		.workbench-menu {
			display: flex !important;
			overflow-x: auto;
			padding-bottom: 10px;
			gap: 0.5rem;
			-webkit-overflow-scrolling: touch;
			scrollbar-width: none; /* Hide scrollbar Firefox */
		}

		.workbench-menu::-webkit-scrollbar {
			display: none; /* Hide scrollbar Chrome/Safari */
		}

		.workbench-menu-item {
			white-space: nowrap;
			margin-bottom: 0;
			flex: 0 0 auto;
		}

		.dashboard-page-header {
			padding: 1.5rem 0.5rem !important;
		}

		.stunning-table-card .card-body {
			padding: 0.75rem !important;
		}
	}

	@media (max-width: 767px) {
		.card-body {
			padding: 1.25rem !important;
		}

		.accordion-button {
			padding: 0.75rem 1rem !important;
		}

		.settings-btn-save {
			width: 100%;
			justify-content: center;
		}

		.workbench-ref-card {
			margin-top: 1rem;
		}
	}
</style>
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
		<div class="dashboard-header-inner">
			<h1 class="dashboard-page-title">EMAIL TEMPLATES</h1>
		</div>
	</section>

	<section class="content mb-5">
		<div class="container-fluid px-0">
			<div class="row">
				<div class="col-12">
					<div class="card stunning-table-card">
						<div class="card-body p-4 pt-3">
							<div class="workbench-container">
								
								<!-- Column 1: Navigation -->
								<div class="workbench-sidebar">
									<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">Templates</div>
									@include('admin.common.mail_menu')
								</div>

								<!-- Column 2: Main Editor -->
								<div class="workbench-main">
									<form action='{{ url("admin/email-template/" . $tempId) }}' method="post" id="myform">
										{!! csrf_field() !!}
										<div class="settings-form-card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
											
											<div class="card-body bg-light p-4 border-bottom">
												<div class="mb-0">
													<label class="settings-field-label text-start mb-2" for="subject_en">Primary Email Subject <span class="text-danger">*</span></label>
													<input class="form-control settings-input bg-white" id="subject_en" name="en[subject]" type="text" value="{{ $temp_Data[0]->subject }}" placeholder="Enter subject line...">
													<input type="hidden" name="en[id]" value="1">
												</div>
											</div>

											<div class="card-body p-4">
												<div class="mb-4">
													<label class="settings-field-label text-start mb-2" for="compose-textarea">Body Content <span class="text-danger">*</span></label>
													<div class="editor-wrap border rounded-3 overflow-hidden">
														<textarea id="compose-textarea" name="en[body]" class="form-control f-14 ckeditor-area" style="height: 300px; border: none;">
															{{ $temp_Data[0]->body }}
														</textarea>
													</div>
												</div>

												<div class="accordion mt-5 overflow-hidden rounded-4 border shadow-sm bg-white" id="accordion">
													<div class="p-4 bg-white border-bottom d-flex align-items-center justify-content-between">
														<div class="d-flex align-items-center gap-2">
															<i class="fa fa-language text-primary" style="font-size: 1.1rem;"></i>
															<span class="text-dark f-13 fw-700 text-uppercase tracking-wider">Available Translations</span>
														</div>
														<span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill f-11 fw-600 border border-primary border-opacity-10">{{ count($languages) - 1 }} Languages</span>
													</div>
													@foreach ($languages as $key => $language)
														@php if ($language->short_name == 'en') {continue;} @endphp

														<div class="accordion-item border-0 border-bottom">
															<h2 class="accordion-header">
																<button class="accordion-button collapsed f-14 fw-600 text-dark py-4 px-4 bg-white shadow-none d-flex align-items-center gap-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $language->short_name }}" aria-expanded="false" aria-controls="collapse{{ $language->short_name }}">
																	<div class="flag-wrapper shadow-sm rounded-1 overflow-hidden d-flex" style="border: 1px solid #eee;">
																		<img src="{{ url('public/images/flags/flags/' . strtolower($language->short_name) . '.png') }}" alt="{{ $language->name }}" style="width: 22px; height: 15px; object-fit: cover;" onerror="this.style.display='none'">
																	</div>
																	<span>{{ $language->name }}</span>
																</button>
															</h2>
															<div id="collapse{{ $language->short_name }}" class="accordion-collapse collapse" data-bs-parent="#accordion">
																<div class="accordion-body bg-white p-4 border-top">
																	<div class="mb-4">
																		<label class="settings-field-label text-start mb-2">Subject ({{ $language->short_name }})</label>
																		<input class="form-control settings-input bg-white" name="{{ $language->short_name }}[subject]" type="text" value="{{ isset($temp_Data[$key]->subject) ? $temp_Data[$key]->subject : 'Subject' }}" placeholder="Enter subject line...">
																		<input type="hidden" name="{{ $language->short_name }}[id]" value="{{ $language->id }}">
																	</div>

																	<div class="mb-0">
																		<label class="settings-field-label text-start mb-2">Body ({{ $language->short_name }})</label>
																		<div class="editor-wrap border rounded-3 overflow-hidden">
																			<textarea id="compose-textarea-{{ $language->short_name }}" name="{{ $language->short_name }}[body]" class="form-control f-14 ckeditor-area" style="height: 300px; border: none;">
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

											<div class="card-footer bg-white border-top py-4 px-4 d-flex justify-content-end align-items-center gap-3" style="border-color: #f1f4f9!important;">
												<button type="submit" class="btn settings-btn-save px-5 h-52 d-flex align-items-center gap-2">
													<i class="fa fa-save"></i> Save Template
												</button>
											</div>
										</div>
									</form>
								</div>

								<!-- Column 3: Variable Reference -->
								<div class="workbench-right">
									<div class="workbench-ref-card shadow-sm border-0">
										<div class="workbench-ref-header border-bottom pb-3 mb-4 d-flex align-items-center gap-2">
											<i class="fa fa-info-circle text-muted"></i>
											<h5 class="m-0">Variable Reference</h5>
										</div>
										<div class="workbench-ref-body">
											@if ($tempId == 1)
												<div class="ref-badge"><code>{site_name}</code><div class="ref-badge-label">Site Name</div></div>
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{date_time}</code><div class="ref-badge-label">Date Time</div></div>
											@elseif ($tempId == 2)
												<div class="ref-badge"><code>{site_name}</code><div class="ref-badge-label">Site Name</div></div>
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{date_time}</code><div class="ref-badge-label">Date Time</div></div>
											@elseif ($tempId == 3)
												<div class="ref-badge"><code>{site_name}</code><div class="ref-badge-label">Site Name</div></div>
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{date_time}</code><div class="ref-badge-label">Date Time</div></div>
											@elseif ($tempId == 4)
												<div class="ref-badge"><code>{start_date}</code><div class="ref-badge-label">Start Date</div></div>
												<div class="ref-badge"><code>{total_guest}</code><div class="ref-badge-label">Total Guest</div></div>
												<div class="ref-badge"><code>{messages_message}</code><div class="ref-badge-label">Messages</div></div>
												<div class="ref-badge"><code>{night/nights}</code><div class="ref-badge-label">Night</div></div>
												<div class="ref-badge"><code>{payment_method}</code><div class="ref-badge-label">Payment Method</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property Name</div></div>
												<div class="ref-badge"><code>{owner_first_name}</code><div class="ref-badge-label">Owner</div></div>
												<div class="ref-badge"><code>{user_first_name}</code><div class="ref-badge-label">User</div></div>
												<div class="ref-badge"><code>{total_night}</code><div class="ref-badge-label">Total Nights</div></div>
											@elseif ($tempId == 5)
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{site_name}</code><div class="ref-badge-label">Site Name</div></div>
											@elseif ($tempId == 6)
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
											@elseif ($tempId == 7)
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{currency_symbol}</code><div class="ref-badge-label">Currency</div></div>
												<div class="ref-badge"><code>{payout_amount}</code><div class="ref-badge-label">Payout</div></div>
											@elseif ($tempId == 8)
												<div class="ref-badge"><code>{site_name}</code><div class="ref-badge-label">Site Name</div></div>
												<div class="ref-badge"><code>{first_name}</code><div class="ref-badge-label">First Name</div></div>
												<div class="ref-badge"><code>{currency_symbol}</code><div class="ref-badge-label">Currency</div></div>
												<div class="ref-badge"><code>{payout_amount}</code><div class="ref-badge-label">Payout</div></div>
												<div class="ref-badge"><code>{payout_payment_method}</code><div class="ref-badge-label">Method</div></div>
											@elseif ($tempId == 9 || $tempId == 10)
												<div class="ref-badge"><code>{Accepted/Declined}</code><div class="ref-badge-label">Status</div></div>
												<div class="ref-badge"><code>{guest_first_name}</code><div class="ref-badge-label">Guest</div></div>
												<div class="ref-badge"><code>{host_first_name}</code><div class="ref-badge-label">Host</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
											@elseif ($tempId == 11)
												<div class="ref-badge"><code>{owner_first_name}</code><div class="ref-badge-label">Host</div></div>
												<div class="ref-badge"><code>{user_first_name}</code><div class="ref-badge-label">User</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
												<div class="ref-badge"><code>{total_night}</code><div class="ref-badge-label">Nights</div></div>
												<div class="ref-badge"><code>{total_guest}</code><div class="ref-badge-label">Guests</div></div>
												<div class="ref-badge"><code>{start_date}</code><div class="ref-badge-label">Check-in</div></div>
											@elseif ($tempId == 12)
												<div class="ref-badge"><code>{total_night}</code><div class="ref-badge-label">Nights</div></div>
												<div class="ref-badge"><code>{user_first_name}</code><div class="ref-badge-label">User</div></div>
												<div class="ref-badge"><code>{total_guest}</code><div class="ref-badge-label">Guests</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
												<div class="ref-badge"><code>{start_date}</code><div class="ref-badge-label">Check-in</div></div>
												<div class="ref-badge"><code>{total_amount}</code><div class="ref-badge-label">Total</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@elseif ($tempId == 13)
												<div class="ref-badge"><code>{owner_first_name}</code><div class="ref-badge-label">Host</div></div>
												<div class="ref-badge"><code>{guest_first_name}</code><div class="ref-badge-label">Guest First</div></div>
												<div class="ref-badge"><code>{guest_name}</code><div class="ref-badge-label">Guest Full</div></div>
												<div class="ref-badge"><code>{guest_email}</code><div class="ref-badge-label">Guest Email</div></div>
												<div class="ref-badge"><code>{total_night}</code><div class="ref-badge-label">Nights</div></div>
												<div class="ref-badge"><code>{user_first_name}</code><div class="ref-badge-label">User</div></div>
												<div class="ref-badge"><code>{total_guest}</code><div class="ref-badge-label">Guests</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
												<div class="ref-badge"><code>{start_date}</code><div class="ref-badge-label">Check-in</div></div>
												<div class="ref-badge"><code>{total_amount}</code><div class="ref-badge-label">Total</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@elseif ($tempId == 14)
												<div class="ref-badge"><code>{admin_first_name}</code><div class="ref-badge-label">Admin</div></div>
												<div class="ref-badge"><code>{guest_name}</code><div class="ref-badge-label">Guest</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
												<div class="ref-badge"><code>{payment_method}</code><div class="ref-badge-label">Method</div></div>
												<div class="ref-badge"><code>{payment_amount}</code><div class="ref-badge-label">Amount</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@elseif ($tempId == 15)
												<div class="ref-badge"><code>{admin_first_name}</code><div class="ref-badge-label">Admin</div></div>
												<div class="ref-badge"><code>{user_name}</code><div class="ref-badge-label">Name</div></div>
												<div class="ref-badge"><code>{user_email}</code><div class="ref-badge-label">Email</div></div>
												<div class="ref-badge"><code>{payment_method}</code><div class="ref-badge-label">Method</div></div>
												<div class="ref-badge"><code>{requested_amount}</code><div class="ref-badge-label">Amount</div></div>
												<div class="ref-badge"><code>{requested_date}</code><div class="ref-badge-label">Date</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@elseif ($tempId == 16)
												<div class="ref-badge"><code>{admin_first_name}</code><div class="ref-badge-label">Admin</div></div>
												<div class="ref-badge"><code>{host_name}</code><div class="ref-badge-label">Host</div></div>
												<div class="ref-badge"><code>{property_name}</code><div class="ref-badge-label">Property</div></div>
												<div class="ref-badge"><code>{property_address}</code><div class="ref-badge-label">Address</div></div>
												<div class="ref-badge"><code>{listed_date}</code><div class="ref-badge-label">Date</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@elseif ($tempId == 17)
												<div class="ref-badge"><code>{user_name}</code><div class="ref-badge-label">User</div></div>
												<div class="ref-badge"><code>{total_amount}</code><div class="ref-badge-label">Total</div></div>
												<div class="ref-badge"><code>{payment_method}</code><div class="ref-badge-label">Method</div></div>
												<div class="ref-badge"><code>{accepted_date}</code><div class="ref-badge-label">Date</div></div>
												<div class="ref-badge"><code>{company_name}</code><div class="ref-badge-label">Company</div></div>
											@endif
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
</div>
@endsection

@push('scripts')
	<script src="{{ asset('public/backend/plugins/ckeditor/ckeditor.js') }}"></script>
	<script type="text/javascript">
		$(function () {
			$('.ckeditor-area').each(function() {
				var id = $(this).attr('id');
				if (!CKEDITOR.instances[id]) {
					CKEDITOR.replace(id, { height: 500 });
				}
			});

			// Scroll active menu item into view on mobile
			var activeItem = $('.workbench-menu-item.active');
			if (activeItem.length && $(window).width() < 992) {
				var menu = $('.workbench-menu');
				var scrollPos = activeItem.offset().left - menu.offset().left + menu.scrollLeft() - (menu.width() / 2) + (activeItem.width() / 2);
				menu.animate({ scrollLeft: scrollPos }, 500);
			}
		});
	</script>
@endpush
