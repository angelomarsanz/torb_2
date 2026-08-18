@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner">
				<h1 class="dashboard-page-title">CUSTOMERS</h1>
			</div>
		</section>

		<section class="content">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-4">
							<div class="settings-form-body py-3 px-4">
								<form action="{{ url('admin/customers') }}" method="GET" accept-charset="UTF-8">
									{{ csrf_field() }}

									<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
									<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

									<div class="row g-3 align-items-end date-parent">
										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-1" for="daterange-btn">Date Range</label>
											<div class="input-group">
												<button type="button" class="form-control settings-input text-start d-flex align-items-center justify-content-between" id="daterange-btn">
													<span><i class="fa fa-calendar me-2"></i> Pick a date range</span>
													<i class="fa fa-caret-down text-muted"></i>
												</button>
											</div>
										</div>
										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-1" for="status">Status</label>
											<select class="form-select settings-input select2-no-search" name="status" id="status">
												<option value="">All Statuses</option>
												<option value="Active" {{ $allstatus == "Active" ? 'selected' : '' }}>Active</option>
												<option value="Inactive" {{ $allstatus == "Inactive" ? 'selected' : '' }}>Inactive</option>
											</select>
										</div>
										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-1" for="customer">Customer</label>
											<select class="form-select settings-input select2" name="customer" id="customer">
												<option value="">All Customers</option>
												@if (!empty($customers))
													@foreach ($customers as $customer)
														<option value="{{ $customer->id }}" {{ $customer->id == $allcustomers ? 'selected' : '' }}>{{ $customer->first_name . ' ' . $customer->last_name }}</option>
													@endforeach
												@endif
											</select>
										</div>
										<div class="col-xl-3 col-lg-3 col-md-6">
											<div class="d-flex gap-2 flex-wrap">
												<button type="submit" name="btn" class="btn settings-btn-save h-42 px-4 d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center">
													<i class="fa fa-filter"></i> Filter
												</button>
												<button type="button" name="reset_btn" id="reset_btn" class="btn settings-btn-cancel h-42 px-4 d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0 justify-content-center">
													<i class="fa fa-refresh"></i> Reset
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="settings-form-card mb-5">
							<div class="settings-form-header">
								<div class="d-flex flex-row justify-content-between align-items-center gap-2">
									<div class="d-flex align-items-center gap-3">
										<div class="settings-form-icon">
											<i class="fa fa-users"></i>
										</div>
										<div class="flex-grow-1">
											<h4 class="settings-form-title">Customers Management</h4>
											<p class="settings-form-subtitle mb-0">Manage all user accounts and profiles</p>
										</div>
									</div>
									@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'add_customer'))
										<a class="btn settings-btn-save flex-shrink-0" href="{{ url('admin/add-customer') }}">
											<i class="fa fa-plus me-2"></i>Add Customer
										</a>
									@endif
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
    var page         = "customer";
</script>
<script src="{{ asset('public/backend/js/property_customer_dropdown.min.js') }}"></script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
