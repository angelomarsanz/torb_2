@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">REPORTS OVERVIEW</h1>
			</div>
		</section>

		<section class="content mb-5">
			{{-- Filter Card --}}
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-4">
							<div class="settings-form-body p-4">
								<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/overview-stats') }}" method="GET" accept-charset="UTF-8">
									{{ csrf_field() }}
									<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
									<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">
									<div class="row g-4 align-items-end date-parent">
										<div class="col-xl-3 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-2" for="daterange-btn">Date Range</label>
											<div class="input-group">
												<button type="button" class="form-control settings-input text-start d-flex align-items-center justify-content-between" id="daterange-btn">
													<span><i class="fa fa-calendar me-2 text-primary f-14"></i> <span id="daterange-label" class="f-13 fw-500">Pick a date range</span></span>
													<i class="fa fa-caret-down text-muted f-12"></i>
												</button>
											</div>
										</div>
										<div class="col-xl-4 col-lg-4 col-md-6">
											<label class="settings-field-label text-start mb-2" for="property">Property</label>
											<select class="form-select settings-input select2" name="property" id="property">
												<option value="">All Properties</option>
												@if (!empty($properties))
													@foreach ($properties as $property)
														<option value="{{ $property->id }}" {{ $property->id == $allproperties ? 'selected' : '' }}>{{ $property->name }}</option>
													@endforeach
												@endif
											</select>
										</div>
										<div class="col-xl-3 col-lg-4 col-md-6">
											<div class="d-flex gap-2">
												<button type="submit" name="btn" class="btn settings-btn-save h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-filter f-14"></i> Filter
												</button>
												<button type="button" name="reset_btn" id="reset_btn" class="btn settings-btn-cancel h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-refresh f-14"></i> Reset
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			{{-- Charts and Data --}}
			<div class="row">
				<div class="col-lg-8 col-12">
					<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-4 mb-lg-0">
						<div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #f1f4f9!important;">
							<div class="d-flex align-items-center gap-2">
								<div class="bg-primary-light p-2 rounded-3 text-primary">
									<i class="fa fa-line-chart"></i>
								</div>
								<h3 class="card-title mb-0 fw-bold f-16">Overview Chart</h3>
							</div>
						</div>
						<div class="card-body p-4" style="min-height: 450px;">
							<div id="main" class="w-100-p h-100-p" style="min-height: 400px;"></div>
						</div>
					</div>
				</div>
				<input type="hidden" value="{{ $collections }}" id="collections" name="collections[]">
				<input type="hidden" value="{{ $totalReservations }}" id="totalReservations">
				<div class="col-lg-4 col-12">
					<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
						<div class="card-header bg-white py-3 px-4 border-bottom" style="border-color: #f1f4f9!important;">
							<div class="d-flex align-items-center gap-2">
								<div class="bg-success-light p-2 rounded-3 text-success">
									<i class="fa fa-globe"></i>
								</div>
								<h3 class="card-title mb-0 fw-bold f-16">Reservations by Country</h3>
							</div>
						</div>
						<div class="card-body p-4">
							@if ($countryCodes != null && count($countryCodes) > 0)
								<div class="table-responsive stunning-table-wrapper">
									<table class="table table-sm table-borderless mb-0 stunning-dashboard-table">
										<tbody>
											@foreach ($countryCodes as $countryCode)
												<tr class="border-bottom" style="border-color: #f8fafc!important;">
													<td class="align-middle py-3" width="45">
														<div class="flag-wrapper shadow-sm rounded-1 overflow-hidden" style="width:35px; height:22px;">
															<img src="{{ asset('public/images/flags/flags-medium/' . strtolower($countryCode->code) . '.png') }}" class="w-100 h-100 object-fit-cover" alt="{{ $countryCode->name }}">
														</div>
													</td>
													<td class="align-middle py-3">
														<div class="d-flex justify-content-between align-items-center">
															<div>
																<div class="fw-bold text-dark f-14">{{ $countryCode->name }}</div>
																<div class="text-muted f-12">{{ $countryCode->value }} Reservations</div>
															</div>
															<div class="text-end">
																@php
																	$percentage = ($countryCode->value / $totalReservations) * 100;
																@endphp
																<div class="fw-bold text-primary f-14">{{ round($percentage) }}%</div>
																<div class="progress mt-1" style="height: 4px; width: 60px;">
																	<div class="progress-bar bg-primary" role="progressbar" style="width: {{ round($percentage) }}%" aria-valuenow="{{ round($percentage) }}" aria-valuemin="0" aria-valuemax="100"></div>
																</div>
															</div>
														</div>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<div class="text-center py-5">
									<div class="text-muted opacity-50 mb-3">
										<i class="fa fa-database f-40"></i>
									</div>
									<p class="text-muted mb-0">No data available for the selected range.</p>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/echarts.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/echarts-gl.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/ecStat.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/dataTool.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/china.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/world.js') }}"></script>
<script src="{{ asset('public/backend/plugins/ECharts/simplex.js') }}"></script>
<script src="{{ asset('public/backend/js/report.min.js') }}?v={{ time() }}" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		// Update date range label if values exist
		var from = $('#startDate').val();
		var to = $('#endDate').val();
		if(from && to) {
			$('#daterange-label').text(from + ' - ' + to);
		}
	});
</script>
@endsection
