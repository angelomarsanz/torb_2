@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">SALES ANALYSIS</h1>
			</div>
		</section>

		<section class="content mb-5">
			{{-- Filter Card --}}
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-4 mt-0">
							<div class="settings-form-body p-4">
								<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/sales-analysis') }}" method="GET" accept-charset="UTF-8">
									{{ csrf_field() }}
									<div class="row g-4 align-items-end">
										<div class="col-xl-4 col-lg-5 col-md-6">
											<label class="settings-field-label text-start mb-2" for="year">Pick a Year</label>
											<select class="form-select settings-input select2" name="year" id="year">
												<option value="">Last 12 Months</option>
												@if (!empty($yearLists))
													@foreach ($yearLists as $yearList)
														<option value="{{ $yearList->year }}" {{ $yearList->year == $year ? 'selected' : '' }}>{{ $yearList->year }}</option>
													@endforeach
												@endif
											</select>
										</div>
										<div class="col-xl-5 col-lg-5 col-md-6">
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
										<div class="col-xl-3 col-lg-2 col-md-12">
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

			{{-- Stats Table --}}
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12">
						<div class="card settings-form-card border-0 shadow-sm mb-0 overflow-hidden">
							<div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f4f9!important;">
								<div class="d-flex align-items-center gap-3">
									<div class="bg-primary-light p-2 rounded-3 text-primary">
										<i class="fa fa-area-chart"></i>
									</div>
									<div>
										<h3 class="card-title mb-0 fw-bold f-16 text-dark tracking-tight">Rates of Reservations & Average Sales</h3>
										<p class="text-muted f-12 mb-0">Monthly analysis and performance trends</p>
									</div>
								</div>
							</div>
							<div class="card-body p-0">
								<div class="table-responsive stunning-table-wrapper">
									<table class="table mb-0 stunning-dashboard-table workbench-table">
										<caption class="px-4 py-3 text-muted f-13 bg-light border-top">Monthly Average sales and Reservation Rates for <strong>{{ $propertyName }}</strong></caption>
										<thead>
											<tr>
												<th class="ps-4">MONTH</th>
												<th>AVERAGE SALES</th>
												<th>TREND</th>
												<th>RESERVATION RATE</th>
												<th class="pe-4">TREND</th>
											</tr>
										</thead>
										<tbody>
											@for ($i=1; $i < count($monthYears); $i++)
												<tr>
													<td class="ps-4 py-3 fw-600 text-dark">{{ $monthYears[$i] }}</td>
													
													{{-- Average Sales --}}
													<td class="py-3">
														<span class="fw-bold f-15 @if($monthlyAvgDiff[$i] > 0) text-success @elseif($monthlyAvgDiff[$i] < 0) text-danger @else text-primary @endif">
															{!! moneyFormat($default_cur_code->org_symbol, $monthlyAvg[$i]) !!}
														</span>
													</td>
													<td class="py-3">
														@if ($monthlyAvgDiff[$i] > 0)
															<span class="badge bg-success-light text-success rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-up me-1"></i> +{{ $monthlyAvgDiff[$i] }}%
															</span>
														@elseif ($monthlyAvgDiff[$i] == 0)
															<span class="badge bg-primary-light text-primary rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-right me-1"></i> {{ $monthlyAvgDiff[$i] }}%
															</span>
														@else
															<span class="badge bg-danger-light text-danger rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-down me-1"></i> {{ $monthlyAvgDiff[$i] }}%
															</span>
														@endif
													</td>

													{{-- Rates of Reservations --}}
													<td class="py-3">
														<span class="fw-bold f-15 text-dark">{{ $reservationRates[$i] }}%</span>
													</td>
													<td class="pe-4 py-3">
														@if ($reservationRateDiff[$i] > 0)
															<span class="badge bg-success-light text-success rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-up me-1"></i> +{{ $reservationRateDiff[$i] }}%
															</span>
														@elseif ($reservationRateDiff[$i] == 0)
															<span class="badge bg-primary-light text-primary rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-right me-1"></i> {{ $reservationRateDiff[$i] }}%
															</span>
														@else
															<span class="badge bg-danger-light text-danger rounded-pill px-3 py-2 fw-bold">
																<i class="fa fa-arrow-down me-1"></i> {{ $reservationRateDiff[$i] }}%
															</span>
														@endif
													</td>
												</tr>
											@endfor
										</tbody>
									</table>
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
<script src="{{ asset('public/backend/js/report.min.js') }}?v={{ time() }}"></script>
@endsection
