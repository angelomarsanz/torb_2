@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Sales Report <small class="text-muted fw-normal">Past 12 months</small></h1>
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
				<div class="col-lg-4 col-6">
					<div class="small-box bg-gradient-warning text-white">
						<div class="inner">
							<h3>{!! moneyFormat($default_cur_code->org_symbol, $totalIncome ?? '') !!}</h3>
							<p>Total Income</p>
						</div>
						<div class="icon">
							<i class="fa fa-money"></i>
						</div>
						<div class="small-box-footer">Income from Past 12 Months</div>
					</div>
				</div>
				<div class="col-lg-4 col-6">
					<div class="small-box bg-gradient-success text-white">
						<div class="inner">
							<h3>{{ $totalNights ?? '' }}</h3>
							<p>Total Nights</p>
						</div>
						<div class="icon">
							<i class="fa fa-building"></i>
						</div>
						<div class="small-box-footer">Reserved Nights from Past 12 Months</div>
					</div>
				</div>
				<div class="col-lg-4 col-6">
					<div class="small-box bg-gradient-info text-white">
						<div class="inner">
							<h3>{{ $totalReservations ?? '' }}</h3>
							<p>Total Reservations</p>
						</div>
						<div class="icon">
							<i class="fa fa-plane"></i>
						</div>
						<div class="small-box-footer">Reservations from Past 12 Months</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-secondary shadow-sm">
						<div class="card-header bg-secondary text-white">
							<h3 class="card-title mb-0">Sales Chart</h3>
						</div>
						<div class="card-body">
							<div id="container" class="sale-container"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/plugins/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('public/backend/plugins/highcharts/exporting.js') }}"></script>
<script type="text/javascript">
  'use strict'
  let currencyCode = "{{ $default_cur_code->code }}";
  let totalIncome = "{{ $totalIncome }}";
  let totalNight = "{{ $totalNights }}";
  let months = '{!! $months !!}';
  let monthlyNights = '{!! $monthlyNights !!}';
</script>
<script src="{{ asset('public/backend/js/sales-report.min.js') }}"></script>
@endsection
