@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header p-0">
		<div class="stunning-header-container">
			<div class="stunning-header-title-wrap">
				<div class="stunning-header-icon">
					<i class="fa fa-users"></i>
				</div>
				<h1 class="stunning-page-title">
					Customers
					<small>Control panel & customer management</small>
				</h1>
			</div>
			<div class="stunning-header-actions">
				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'add_customer'))
					<a class="stunning-btn-primary" href="{{ url('admin/add-customer') }}">
						<i class="fa fa-plus"></i>
						<span>Add Customer</span>
					</a>
				@endif
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card stunning-table-card mb-4">
						<div class="card-header">
							<h3 class="card-title mb-0">Filter</h3>
						</div>
						<div class="card-body p-4">
							<form action="{{ url('admin/customers') }}" method="GET" accept-charset="UTF-8">
								{{ csrf_field() }}

								<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
								<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

								<div class="row g-3 align-items-end date-parent">
									<div class="col-xl-3 col-lg-3 col-md-6 col-12">
										<label class="form-label" for="daterange-btn">Date Range</label>
										<div class="input-group">
											<button type="button" class="form-control text-start d-flex align-items-center justify-content-between h-42" id="daterange-btn">
												<span><i class="fa fa-calendar me-2 text-success"></i> Pick a date range</span>
												<i class="fa fa-caret-down opacity-50"></i>
											</button>
										</div>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-6 col-12">
										<label class="form-label" for="status">Status</label>
										<select class="form-select select2-no-search" name="status" id="status">
											<option value="">All Status</option>
											<option value="Active" {{ $allstatus == "Active" ? 'selected' : '' }}>Active</option>
											<option value="Inactive" {{ $allstatus == "Inactive" ? 'selected' : '' }}>Inactive</option>
										</select>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-6 col-12">
										<label class="form-label" for="customer">Customer</label>
										<select class="form-select select2" name="customer" id="customer">
											<option value="">All Customers</option>
											@if (!empty($customers))
												@foreach ($customers as $customer)
													<option value="{{ $customer->id }}" {{ $customer->id == $allcustomers ? 'selected' : '' }}>{{ $customer->first_name . ' ' . $customer->last_name }}</option>
												@endforeach
											@endif
										</select>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-6 col-12">
										<div class="d-flex gap-2">
											<button type="submit" name="btn" class="btn stunning-btn-primary f-14 px-4 h-42">
												<i class="fa fa-filter"></i> Filter
											</button>
											<button type="button" name="reset_btn" id="reset_btn" class="btn btn-outline-neutral f-14 px-4 btn-sharp h-42">
												<i class="fa fa-refresh"></i> Reset
											</button>
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
					<div class="card stunning-table-card">
						<div class="card-header">
							<div class="d-flex justify-content-between align-items-center">
								<h3 class="card-title mb-0">Customers Management</h3>
							</div>
						</div>
						<div class="card-body pt-3 p-0">
							<div class="stunning-table-wrapper">
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

@section('validate_script')
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
<script type="text/javascript">
    'use strict'
    var sessionDate  = '{{ strtoupper(Session::get('date_format_type')) }}';
    var user_id      = '{{ $user->id ?? '' }}';
    var page         = "customer";
</script>
<script src="{{ asset('public/backend/js/property_customer_dropdown.min.js') }}"></script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
