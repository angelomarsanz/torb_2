@extends('admin.template')

<style>
	.stunning-stat-card {
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		border: 1px solid #f1f4f9!important;
	}
	.stunning-stat-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05)!important;
	}
	.bg-warning-gradient { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
	.bg-primary-gradient { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); }
	.bg-info-gradient { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); }
	.icon-container {
		width: 54px;
		height: 54px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 14px;
	}
</style>

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">SALES REPORT</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				{{-- Stat Cards --}}
				<div class="row g-4 mb-4">
					<div class="col-xl-4 col-md-4">
						<div class="card stunning-stat-card border-0 shadow-sm h-100" style="border-left: 4px solid #22c55e !important; border-radius: 12px;">
							<div class="card-body p-4 d-flex align-items-center gap-4">
								<div class="icon-container" style="background: #f0fdf4; color: #22c55e; width: 56px; height: 56px; border-radius: 12px; flex-shrink: 0;">
									<i class="fa fa-calendar-check-o" style="font-size: 22px;"></i>
								</div>
								<div>
									<div style="font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{!! moneyFormat($default_cur_code->org_symbol, $totalIncome ?? '0') !!}</div>
									<div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px;">Total Income</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-4">
						<div class="card stunning-stat-card border-0 shadow-sm h-100" style="border-left: 4px solid #206bc4 !important; border-radius: 12px;">
							<div class="card-body p-4 d-flex align-items-center gap-4">
								<div class="icon-container" style="background: #eff6ff; color: #206bc4; width: 56px; height: 56px; border-radius: 12px; flex-shrink: 0;">
									<i class="fa fa-moon-o" style="font-size: 22px;"></i>
								</div>
								<div>
									<div style="font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ $totalNights ?? '0' }}</div>
									<div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px;">Total Nights</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-4">
						<div class="card stunning-stat-card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b !important; border-radius: 12px;">
							<div class="card-body p-4 d-flex align-items-center gap-4">
								<div class="icon-container" style="background: #fffbeb; color: #f59e0b; width: 56px; height: 56px; border-radius: 12px; flex-shrink: 0;">
									<i class="fa fa-money" style="font-size: 22px;"></i>
								</div>
								<div>
									<div style="font-size: 1.6rem; font-weight: 800; color: #1e293b; line-height: 1.1;">{{ $totalReservations ?? '0' }}</div>
									<div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px;">Total Reservations</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				{{-- Chart Card --}}
				<div class="row">
					<div class="col-12">
						<div class="card settings-form-card border-0 shadow-sm mb-0 overflow-hidden">
							<div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f4f9!important;">
								<div class="d-flex align-items-center gap-3">
									<div class="bg-primary-light p-2 rounded-3 text-primary shadow-xs">
										<i class="fa fa-bar-chart"></i>
									</div>
									<div>
										<h3 class="card-title mb-0 fw-bold f-16 text-dark">Sales Performance Chart</h3>
										<p class="text-muted f-12 mb-0">Monthly breakdown of income and nights</p>
									</div>
								</div>
							</div>
							<div class="card-body p-4">
								<div id="container" class="sale-container" style="min-height: 450px;"></div>
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
<script src="{{ asset('public/backend/js/sales-report.min.js') }}?v={{ time() }}"></script>
@endsection
