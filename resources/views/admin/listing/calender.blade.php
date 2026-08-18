@extends('admin.template')
@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			{{-- Space to top of the page content --}}
			<div class="pt-0"></div>

			<section class="content mb-0">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-lg-3 col-12 settings_bar_gap">
							@include('admin.common.property_bar')
						</div>

						<div class="col-lg-9 col-12">
							<div class="card stunning-table-card border-0 shadow-sm mb-0">
								<div class="card-body p-0">

									{{-- Modern Workbench Form Card --}}
									<div class="settings-form-card">
										<style>
											.calender_box *,
											#calender-dv a,
											.wkText {
												text-decoration: none !important;
											}

											.calenBox .margin-top10,
											.calenBox .margin-top10:last-child .calender_box {
												border-bottom: none !important;
											}

											.calendar-blue-btn {
												height: 38px !important;
												padding: 0 20px !important;
												font-size: 13.5px !important;
												font-weight: 700 !important;
												border-radius: 4px !important;
												transition: all 0.2s ease !important;
												white-space: nowrap !important;
												display: inline-flex !important;
												align-items: center !important;
												justify-content: center !important;
											}

											.calendar-blue-btn:focus {
												box-shadow: 0 0 0 3px rgba(29, 95, 177, 0.25) !important;
												outline: none !important;
											}

											.today-label:empty {
												display: none !important;
												padding: 0 !important;
												background: transparent !important;
											}

											/* GLOBAL STRUCTURAL GRID */
											.col-md-02 {
												flex: 0 0 14.285% !important;
												max-width: 14.285% !important;
												width: 14.285% !important;
												border: 0.5px solid #ddd !important;
												margin: -0.25px !important;
											}

											/* GLOBAL ACTION TOOLBAR GRID */
											.calendar-action-container {
												display: grid !important;
												grid-template-columns: repeat(3, 1fr) !important;
												background: #fff !important;
												border: none !important;
												border-radius: 0 !important;
												box-shadow: none !important;
												margin: 0 !important;
											}

											.calendar-action-container .btn,
											.calendar-action-container a {
												width: 100% !important;
												display: flex !important;
												justify-content: center !important;
												align-items: center !important;
												text-align: center !important;
											}

											/* DESKTOP REFINEMENTS (768px and up) */
											@media (min-width: 768px) {
												.calender_box {
													min-height: 110px !important;
													padding: 12px 10px !important;
													display: flex !important;
													flex-direction: column !important;
													justify-content: space-between !important;
													align-items: flex-start !important;
													background: #fff !important;
												}

												.calender_box .wkText.final_day {
													font-size: 14px !important;
													font-weight: 700 !important;
													line-height: 1.4 !important;
													display: flex !important;
													flex-direction: column !important;
												}

												.day-number {
													font-size: 16px !important;
													margin-bottom: 3px !important;
													color: #1e293b !important;
												}

												.today-label {
													font-size: 11px !important;
													padding: 2px 6px !important;
													background: #1e293b !important;
													color: #fff !important;
													border-radius: 4px !important;
													display: inline-block !important;
													font-weight: 700 !important;
													text-transform: uppercase !important;
												}

												.today-label:empty {
													display: none !important;
													padding: 0 !important;
													background: transparent !important;
												}

												.calender_box .dTfont {
													font-size: 13.5px !important;
													font-weight: 800 !important;
													color: #1e293b !important;
													text-align: left !important;
													margin-top: auto !important;
												}

												.calendar-action-container {
													padding: 2.5rem 2rem !important;
													gap: 24px !important;
													max-width: 1000px !important;
													margin: 0 auto !important;
												}

												.calendar-action-container .btn,
												.calendar-action-container a {
													height: 44px !important;
													font-size: 14px !important;
													padding: 0 25px !important;
												}
											}

											/* MOBILE COMPACTNESS (767px and below) */
											@media (max-width: 767px) {
												.settings-form-body {
													padding: 0 !important;
												}

												.calenBox .margin-top10 {
													display: flex !important;
													width: 100% !important;
													background: #f8fafc !important;
												}

												.calenBox .margin-top10 .col-md-02 {
													text-align: center !important;
													padding: 10px 0 !important;
													border: none !important;
													margin: 0 !important;
												}

												.calenBox .margin-top10 .wkText {
													font-size: 11px !important;
													font-weight: 700 !important;
													text-transform: capitalize !important;
													color: #475569 !important;
												}

												.calender_box {
													height: 65px !important;
													min-height: 65px !important;
													padding: 0 5px !important;
													display: flex !important;
													flex-direction: column !important;
													justify-content: flex-start !important;
													align-items: flex-start !important;
													background: #fff !important;
													border-radius: 0 !important;
													gap: 1px !important;
												}

												.calender_box.dt-not-available {
													background: #f1f5f9 !important;
													color: #94a3b8 !important;
												}

												.calender_box .wkText.final_day {
													font-size: 12px !important;
													font-weight: 700 !important;
													width: 100% !important;
													text-align: left !important;
													display: flex !important;
													flex-direction: column !important;
													line-height: 1 !important;
													margin: 0 !important;
												}

												.day-number {
													font-size: 13px !important;
													color: inherit !important;
													line-height: 1.1 !important;
													margin: 0 !important;
												}

												.today-label {
													font-size: 10.5px !important;
													color: #1e293b !important;
													font-weight: 800 !important;
													display: block !important;
													margin-top: 1px !important;
													margin-left: 0 !important;
													background: transparent !important;
													padding: 0 !important;
													text-transform: none !important;
													line-height: 1 !important;
													margin: 0 !important;
												}

												.calender_box .dTfont {
													font-size: 10.5px !important;
													font-weight: 500 !important;
													color: #475569 !important;
													width: 100% !important;
													text-align: left !important;
													margin-top: auto !important;
													background: transparent !important;
													border: none !important;
													padding: 0 !important;
													min-width: 0 !important;
													line-height: 1 !important;
													margin: 0 !important;
												}

												.calendar-action-container {
													padding: 1.25rem 1rem !important;
													gap: 8px !important;
												}

												.calendar-action-container .btn,
												.calendar-action-container a {
													height: 38px !important;
													font-size: 11px !important;
													padding: 0 4px !important;
												}

												.calendar-action-container .btn i,
												.calendar-action-container a i {
													font-size: 12px !important;
													margin-right: 4px !important;
												}

												/* NUCLEAR VISIBILITY OVERRIDE: FORCE GRID TO FULL HEIGHT */
												#calender-dv,
												#calender-dv>div,
												.calenBox {
													height: auto !important;
													min-height: auto !important;
													max-height: none !important;
													overflow: visible !important;
													display: block !important;
													width: 100% !important;
													clear: both !important;
												}
											}
										</style>
										<div class="settings-form-header py-3 px-4 border-bottom">
											<div
												class="calendar-nav-container d-flex justify-content-between align-items-center">
												<a href="{{ url('admin/listing/' . $result->id . '/booking') }}"
													class="btn calendar-blue-btn">
													<i class="fa fa-arrow-left me-2"></i> Back
												</a>
												<a href="{{ url('admin/properties') }}" class="btn calendar-blue-btn">
													Your Listings
												</a>
											</div>
										</div>
										<div class="settings-form-body">
											{{-- Section: Calendar Interface --}}
											<div class="settings-section">
												<div class="p-0 border bg-white shadow-sm mb-0"
													style="overflow: visible !important;">
													<form method="post"
														action="admin/property-save/{{ $result->id }}/pricing">
														{{ csrf_field() }}
														<input type="hidden" id="dtpc_property_id"
															value="{{ $result->id }}">
														<div id="calender-dv">
															{!! $calendar !!}
														</div>
													</form>

													{{-- Integrated Synchronization Actions (Centered & Compact) --}}
													<div class="p-0 border-top">
														<div class="calendar-action-container d-grid">
															<button class="btn calendar-blue-btn imporpt_calendar"
																data-bs-toggle="modal"
																data-bs-target="#import_calendar_package">
																<i class="fa fa-cloud-download"></i>
																<span class="d-none d-md-inline">Import Calendar</span>
																<span class="d-md-none">Import</span>
															</button>
															<a class="js-calendar-sync btn calendar-blue-btn"
																data-prevent-default="true"
																href="{{ url('admin/icalendar/synchronization/' . $result->id) }}">
																<i class="fa fa-refresh"></i>
																<span class="d-none d-md-inline">Sync Calendars</span>
																<span class="d-md-none">Sync</span>
															</a>
															<button class="btn calendar-blue-btn" id="export_icalendar"
																data-bs-toggle="modal"
																data-bs-target="#calendar_export_package">
																<i class="fa fa-cloud-upload"></i>
																<span class="d-none d-md-inline">Export Calendar</span>
																<span class="d-md-none">Export</span>
															</button>
														</div>
													</div>
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

	{{-- Set Price Modal --}}
	<div class="modal fade" id="hotel_date_package_admin" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow-lg">
				<div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
					<h4 class="modal-title f-18 fw-bold">Set Custom Date Pricing</h4>
					<button type="button" class="btn-close cls-reload" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form method="post" action="admin/hotel_date_package/" class="form-horizontal" id="dtpc_form">
					{{ csrf_field() }}
					<div class="modal-body p-4">
						<p class="alert alert-success text-center py-2 f-13 display-off" id="model-message"></p>
						<input type="hidden" value="{{ $result->id }}" name="property_id" id="dtpc_property_id">

						<div class="mb-4">
							<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Date Range</label>
							<div class="row g-2">
								<div class="col-6">
									<input type="text" class="form-control settings-input" name="start_date"
										id="dtpc_start_admin" placeholder="Start Date" autocomplete="off">
								</div>
								<div class="col-6">
									<input type="text" class="form-control settings-input" name="end_date"
										id="dtpc_end_admin" placeholder="End Date" autocomplete="off">
								</div>
							</div>
							<div class="d-flex justify-content-between mt-1">
								<span class="text-danger f-11" id="error-dtpc-start_date"></span>
								<span class="text-danger f-11" id="error-dtpc-end_date"></span>
							</div>
						</div>

						<div class="row g-3 mb-4">
							<div class="col-6">
								<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Nightly Price</label>
								<input type="text" class="form-control settings-input" name="price" id="dtpc_price"
									placeholder="0.00">
								<span class="text-danger f-11 d-block mt-1" id="error-dtpc-price"></span>
							</div>
							<div class="col-6">
								<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Min. Stay</label>
								<input type="text" class="form-control settings-input" name="min_stay"
									id="dtpc_minstay_admin" placeholder="1 Night">
								<span class="text-danger f-11 d-block mt-1" id="error-dtpc-minstay"></span>
							</div>
						</div>

						<div class="mb-0">
							<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Availability Status</label>
							<select class="form-select settings-input" name="status" id="dtpc_status">
								<option value="">-- Select Status --</option>
								<option value="Available">Available</option>
								<option value="Not available">Not Available</option>
							</select>
							<span class="text-danger f-11 d-block mt-1" id="error-dtpc-status"></span>
						</div>
					</div>
					<div class="modal-footer border-top-0 p-4 pt-0 d-flex gap-2">
						<button type="button" class="btn settings-btn-cancel px-4 flex-grow-1"
							data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn settings-btn-save px-4 flex-grow-1">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- Import Calendar Modal --}}
	<div class="modal fade" id="import_calendar_package" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow-lg">
				<div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
					<h4 class="modal-title f-18 fw-bold">Import External Calendar</h4>
					<button type="button" class="btn-close cls-reload" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form class="form-horizontal" id="icalendar_form">
					<div class="modal-body p-4">
						<p class="alert alert-info text-center py-2 f-13 display-off i-cal-m-msg"
							id="icalendar-model-message"></p>
						<input type="hidden" value="{{ $result->id }}" name="property_id" id="icalendar_property_id">

						<div class="mb-4">
							<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">iCal URL</label>
							<input type="text" class="form-control settings-input" name="url" id="icalendar_url"
								placeholder="Paste .ics URL (Airbnb, VRBO, etc.)" autocomplete="off">
							<span class="text-danger f-11 d-block mt-1" id="error-icalendar-url"></span>
						</div>

						<div class="mb-4">
							<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Calendar Name</label>
							<input type="text" class="form-control settings-input" name="name" id="icalendar_name"
								placeholder="e.g. My Airbnb Calendar" autocomplete="off">
							<span class="text-danger f-11 d-block mt-1" id="error-icalendar-name"></span>
						</div>

						<div class="mb-0">
							<label class="f-13 fw-bold text-slate-500 text-uppercase mb-2">Display Color</label>
							<div class="row g-2 colorSelect">
								<div class="col-12">
									<select class="form-select settings-input" name="color" id="color">
										<option value="">-- Choose Color --</option>
										<option value="#7FFFD4">Aquamarine</option>
										<option value="#0000FF">Blue</option>
										<option value="#000080">Navy</option>
										<option value="#800080">Purple</option>
										<option value="#FF1493">DeepPink</option>
										<option value="#EE82EE">Violet</option>
										<option value="#FFC0CB">Pink</option>
										<option value="#006400">DarkGreen</option>
										<option value="#008000">Green</option>
										<option value="#9ACD32">YellowGreen</option>
										<option value="#FFFF00">Yellow</option>
										<option value="#FFA500">Orange</option>
										<option value="#FF0000">Red</option>
										<option value="#A52A2A">Brown</option>
										<option value="#DEB887">BurlyWood</option>
										<option value="custom">Custom Hex Code</option>
									</select>
								</div>
							</div>
							<div class="mt-3 colorCustom d-none">
								<input type="text" class="form-control settings-input" name="customcolor" id="customcolor"
									placeholder="#HEXCOLOR">
								<small class="text-muted mt-1 d-block"><a href="http://htmlcolorcodes.com/" target="_blank"
										class="text-primary">Visit Color Picker</a></small>
							</div>
						</div>
					</div>
					<div class="modal-footer border-top-0 p-4 pt-0">
						<button type="submit" class="btn calendar-blue-btn w-100" id="import_btn">
							<i class="fa fa-cloud-upload me-2"></i> Import & Sync
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- Export Calendar Modal --}}
	<div class="modal fade" id="calendar_export_package" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow-lg">
				<div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
					<h4 class="modal-title f-18 fw-bold">Export Your Calendar</h4>
					<button type="button" class="btn-close cls-reload" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body p-4">
					<p class="text-muted f-14 mb-3">Copy and paste this link into other iCal-compatible applications to keep
						them synced with your VRent calendar.</p>
					<div class="position-relative">
						<input type="text" class="form-control settings-input pe-5"
							value="{{ url('icalender/export/' . $result->id . '.ics') }}" readonly="">
						<i class="fa fa-link position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
					</div>
				</div>
				<div class="modal-footer border-top-0 p-4 pt-0">
					<button type="button" class="btn calendar-outline-btn w-100" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('validate_script')
	<script type="text/javascript">
		'use strict'
		var message = "{{ __('Please enter at least 6 characters.') }}";
	</script>
	<script type="text/javascript" src="{{ asset('public/js/jquery.validate.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/backend/js/backend.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('public/js/jquery-ui.js') }}"></script>
@endsection