@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Customer Bookings</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			@include('admin.customerdetails.customer_menu')

			<div class="row">
				<div class="col-12">
					<div class="card shadow-sm">
						<div class="card-body">
							<form action="{{ url('admin/customer/bookings/' . $user->id) }}" method="GET" accept-charset="UTF-8">
								{{ csrf_field() }}
								<input type="hidden" id="startfrom" name="from" value="{{ isset($from) ? $from : '' }}">
								<input type="hidden" id="endto" name="to" value="{{ isset($to) ? $to : '' }}">

								<div class="row g-3 align-items-end date-parent">
									<div class="col-xl-3 col-lg-4 col-md-6 col-12">
										<label class="form-label" for="daterange-btn">Date Range</label>
										<div class="input-group">
											<button type="button" class="form-control text-start d-flex align-items-center justify-content-between" id="daterange-btn">
												<span><i class="fa fa-calendar me-1"></i> Pick a date range</span>
												<i class="fa fa-caret-down"></i>
											</button>
										</div>
									</div>
									<div class="col-xl-3 col-lg-4 col-md-6 col-12">
										<label class="form-label" for="property">Property</label>
										<select class="form-select select2" name="property" id="property">
											<option value="">All</option>
											@if (!empty($properties))
												@foreach ($properties as $property)
													<option value="{{ $property->id }}" {{ $property->id == $allproperties ? 'selected' : '' }}>{{ $property->name }}</option>
												@endforeach
											@endif
										</select>
									</div>
									<div class="col-xl-3 col-lg-4 col-md-6 col-12">
										<label class="form-label" for="status">Status</label>
										<select class="form-select" name="status" id="status">
											<option value="">All</option>
											<option value="Accepted" {{ $allstatus == "Accepted" ? 'selected' : '' }}>Accepted</option>
											<option value="Cancelled" {{ $allstatus == "Cancelled" ? 'selected' : '' }}>Cancelled</option>
											<option value="Declined" {{ $allstatus == "Declined" ? 'selected' : '' }}>Declined</option>
											<option value="Expired" {{ $allstatus == "Expired" ? 'selected' : '' }}>Expired</option>
											<option value="Pending" {{ $allstatus == "Pending" ? 'selected' : '' }}>Pending</option>
										</select>
									</div>
									<div class="col-xl-3 col-lg-12 col-md-6 col-12">
										<div class="d-flex gap-2">
											<button type="submit" name="btn" class="btn btn-primary f-14 rounded">Filter</button>
											<button type="button" name="reset_btn" id="reset_btn" class="btn btn-outline-secondary f-14 rounded">Reset</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header bg-info text-white">
							<h3 class="card-title mb-0">Bookings</h3>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive parent-table f-14 p-3">
								{!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive w-100', 'cellspacing' => '0']) !!}
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
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush

@section('validate_script')
<script type="text/javascript">
	'use strict'
	var sessionDate  = '{{ strtoupper(Session::get('date_format_type')) }}';
	var user_id      = '{{ $user?->id }}';
	var page         = 'customer_booking'
</script>
<script src="{{ asset('public/backend/js/property_customer_dropdown.min.js') }}"></script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection