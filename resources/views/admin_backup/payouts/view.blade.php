@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Payouts <small class="text-muted fw-normal">Control panel</small></h1>
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
					<div class="card shadow-sm">
						<div class="card-body">
							<form class="form-horizontal" enctype='multipart/form-data' action="{{ url('admin/payouts') }}" method="GET" accept-charset="UTF-8">
								{{ csrf_field() }}
								<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
								<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

								<div class="row g-3 align-items-end date-parent">
									<div class="col-xl-3 col-lg-4 col-md-6 col-12">
										<label class="form-label">Date Range</label>
										<div class="input-group">
											<button type="button" class="form-control text-start d-flex align-items-center justify-content-between" id="daterange-btn">
												<span><i class="fa fa-calendar me-1"></i> Pick a date range</span>
												<i class="fa fa-caret-down"></i>
											</button>
										</div>
									</div>

									<div class="col-xl-3 col-lg-4 col-md-6 col-12">
										<label class="form-label" for="status">Status</label>
										<select class="form-select" name="status" id="status">
											<option value="">All</option>
											<option value="Success" {{ $allstatus == "Success" ? 'selected' : '' }}>Success</option>
											<option value="Pending" {{ $allstatus == "Pending" ? 'selected' : '' }}>Pending</option>
										</select>
									</div>

									<div class="col-xl-2 col-lg-12 col-md-6 col-12">
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
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="row g-2">
								<div class="col-xl-2 col-md-4 py-2 py-md-0">
									<div class="card bg-success text-white rounded shadow-sm mb-0">
										<div class="card-body text-center py-3">
											<span class="text-20 d-block">{{ $totalPayouts }}</span>
											<span class="fw-bold f-14">Total Payouts</span>
										</div>
									</div>
								</div>
								<div class="col-xl-2 col-md-4 py-2 py-md-0">
									<div class="card bg-info text-white rounded shadow-sm mb-0">
										<div class="card-body text-center py-3">
											<span class="text-20 d-block">{{ $totalPayoutsAmount }}</span>
											<span class="fw-bold f-14">Total amount</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
							<h3 class="card-title mb-0">Payouts Management</h3>
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

@section('validate_script')
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
<script type="text/javascript">
	'use strict'
	var sessionDate  = '{{ strtoupper(Session::get('date_format_type')) }}';
	var user_id      = '{{ $user->id ?? '' }}';
	var page         = 'payout';
</script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
