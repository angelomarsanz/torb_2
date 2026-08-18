@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Overview & Statistics <small class="text-muted fw-normal">Reservations by country</small></h1>
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
					<div class="card card-outline card-primary shadow-sm">
						<div class="card-header bg-primary text-white">
							<h3 class="card-title mb-0">Filters</h3>
						</div>
						<div class="card-body">
							<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/overview-stats') }}" method="GET" accept-charset="UTF-8">
								{{ csrf_field() }}
								<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
								<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">
								<div class="row g-3 align-items-end">
									<div class="col-md-3 col-sm-6 col-12">
										<label class="form-label">Date Range</label>
										<div class="input-group">
											<button type="button" class="form-control text-start d-flex align-items-center justify-content-between" id="daterange-btn">
												<span><i class="fa fa-calendar me-1"></i> Pick a date range</span>
												<i class="fa fa-caret-down"></i>
											</button>
										</div>
									</div>
									<div class="col-md-4 col-sm-6 col-12">
										<label class="form-label">Property</label>
										<select class="form-select select2" name="property" id="property">
											<option value="">All</option>
											@if (!empty($properties))
												@foreach ($properties as $property)
													<option value="{{ $property->id }}" {{ $property->id == $allproperties ? 'selected' : '' }}>{{ $property->name }}</option>
												@endforeach
											@endif
										</select>
									</div>
									<div class="col-md-3 col-sm-6 col-12">
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
				<div class="col-lg-8 col-12">
					<div class="card card-outline card-secondary shadow-sm">
						<div class="card-header bg-secondary text-white">
							<h3 class="card-title mb-0">Overview Chart</h3>
						</div>
						<div class="card-body">
							<div id="main" class="w-100-p h-100-p"></div>
						</div>
					</div>
				</div>
				<input type="hidden" value="{{ $collections }}" id="collections" name="collections[]">
				<div class="col-lg-4 col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header bg-info text-white">
							<h3 class="card-title mb-0">Reservations by Country</h3>
						</div>
						<div class="card-body">
							@if ($countryCodes != null)
								<div class="table-responsive f-14">
									<table class="table table-sm table-borderless mb-0">
										<tbody>
											@foreach ($countryCodes as $countryCode)
												<tr>
													<td class="align-middle" width="40">
														<img src="{{ asset('public/images/flags/flags-medium/' . strtolower($countryCode->code) . '.png') }}" width="35" height="20" alt="">
													</td>
													<td class="align-middle">
														<strong>{{ $countryCode->name }}</strong><br>
														<small class="text-muted">
															{{ $countryCode->value }}
															@php
																$percentage = ($countryCode->value / $totalReservations) * 100;
															@endphp
															({{ round($percentage) }}%)
														</small>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<p class="text-muted mb-0">No data available.</p>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
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
<script src="{{ asset('public/backend/js/report.min.js') }}" type="text/javascript"></script>
@endsection
