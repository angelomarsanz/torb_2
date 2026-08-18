@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner">
				<h1 class="dashboard-page-title">BOOKINGS</h1>
			</div>
		</section>

		<section class="content">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-3">
							<div class="settings-form-body py-3 px-4">
								<form class="form-horizontal" action="{{ url('admin/bookings') }}" method="GET" accept-charset="UTF-8">
									{{ csrf_field() }}
									<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
									<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

									<div class="row g-3 align-items-end date-parent">
										<div class="col-xl-3 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-1">Date Range</label>
											<div class="input-group">
												<button type="button" class="form-control settings-input text-start d-flex align-items-center justify-content-between" id="daterange-btn">
													<span><i class="fa fa-calendar me-2"></i> Pick a date range</span>
													<i class="fa fa-caret-down text-muted"></i>
												</button>
											</div>
										</div>

										<div class="col-xl-2 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-1" for="property">Property</label>
											<select class="form-select settings-input select2" name="property" id="property">
												<option value="">All Properties</option>
												@if (!empty($properties))
													@foreach ($properties as $property)
														<option value="{{ $property->id }}" {{ $property->id == $allproperties ? 'selected' : '' }}>{{ $property->name }}</option>
													@endforeach
												@endif
											</select>
										</div>

										<div class="col-xl-2 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-1" for="customer">Customer</label>
											<select class="form-select settings-input select2customer" name="customer" id="customer">
												<option value="">All Customers</option>
												@if (!empty($customers))
													@foreach ($customers as $customer)
														<option value="{{ $customer->id }}" {{ $customer->id == $allcustomers ? 'selected' : '' }}>{{ $customer->first_name . " " . $customer->last_name }}</option>
													@endforeach
												@endif
											</select>
										</div>

										<div class="col-xl-2 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-1" for="status">Status</label>
											<select class="form-select settings-input" name="status" id="status">
												<option value="">All Statuses</option>
												<option value="Accepted" {{ $allstatus == "Accepted" ? 'selected' : '' }}>Accepted</option>
												<option value="Cancelled" {{ $allstatus == "Cancelled" ? 'selected' : '' }}>Cancelled</option>
												<option value="Declined" {{ $allstatus == "Declined" ? 'selected' : '' }}>Declined</option>
												<option value="Expired" {{ $allstatus == "Expired" ? 'selected' : '' }}>Expired</option>
												<option value="Pending" {{ $allstatus == "Pending" ? 'selected' : '' }}>Pending</option>
												<option value="Processing" {{ $allstatus == "Processing" ? 'selected' : '' }}>Processing</option>
											</select>
										</div>

										<div class="col-xl-3 col-lg-4 col-md-6">
											<div class="d-flex gap-2">
												<button type="submit" name="btn" class="btn settings-btn-save h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-filter"></i> Filter
												</button>
												<button type="button" name="reset_btn" id="reset_btn" class="btn settings-btn-cancel h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-refresh"></i> Reset
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>

						{{-- Quick Stats --}}
						<div class="row g-3 mb-3">
							<div class="col-xl-3 col-md-4">
								<div class="settings-form-card stats-card-v2 h-100 p-3 border-start-accent-success">
									<div class="d-flex align-items-center gap-3">
										<div class="stats-icon-box bg-light-success text-success">
											<i class="fa fa-calendar-check-o"></i>
										</div>
										<div>
											<span class="d-block f-20 fw-bold text-dark">{{ $total_bookings }}</span>
											<span class="f-12 text-muted fw-600">Total Bookings</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-3 col-md-4">
								<div class="settings-form-card stats-card-v2 h-100 p-3 border-start-accent-primary">
									<div class="d-flex align-items-center gap-3">
										<div class="stats-icon-box bg-light-primary text-primary">
											<i class="fa fa-users"></i>
										</div>
										<div>
											<span class="d-block f-20 fw-bold text-dark">{{ $total_customers }}</span>
											<span class="f-12 text-muted fw-600">Total Customers</span>
										</div>
									</div>
								</div>
							</div>
							@if ($different_total_amounts)
								@foreach ($different_total_amounts as $total_amount)
								<div class="col-xl-3 col-md-4">
									<div class="settings-form-card stats-card-v2 h-100 p-3 border-start-accent-warning">
										<div class="d-flex align-items-center gap-3">
											<div class="stats-icon-box bg-light-warning text-warning">
												<i class="fa fa-money"></i>
											</div>
											<div>
												<span class="d-block f-20 fw-bold text-dark">{!! $total_amount['total'] !!}</span>
												<span class="f-12 text-muted fw-600">Total {{ $total_amount['currency_code'] }}</span>
											</div>
										</div>
									</div>
								</div>
								@endforeach
							@endif
						</div>

						<div class="settings-form-card mb-5">
							<div class="settings-form-header">
								<div class="d-flex align-items-center gap-3">
									<div class="settings-form-icon">
										<i class="fa fa-book"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Bookings Management</h4>
										<p class="settings-form-subtitle">Comprehensive list and management of all guest bookings</p>
									</div>
								</div>
							</div>
							<div class="settings-form-body p-0">
								<div class="table-responsive parent-table f-14 p-4">
									{!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive w-100 workbench-table', 'cellspacing' => '0']) !!}
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
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
<script type="text/javascript">
	'use strict'
	var sessionDate  = '{{ strtoupper(Session::get('date_format_type')) }}';
	var user_id      = '{{ $user->id ?? '' }}';
	var page         = 'booking'
</script>
<script src="{{ asset('public/backend/js/property_customer_dropdown.min.js') }}"></script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
