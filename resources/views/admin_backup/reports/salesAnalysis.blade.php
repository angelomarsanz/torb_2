@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Analysis of Data <small class="text-muted fw-normal">Sales & reservations</small></h1>
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
							<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/sales-analysis') }}" method="GET" accept-charset="UTF-8">
								{{ csrf_field() }}
								<div class="row g-3 align-items-end">
									<div class="col-md-3 col-sm-6 col-12">
										<label class="form-label">Pick a Year</label>
										<select class="form-select" name="year" id="year">
											<option value="">Last 12 Months</option>
											@if (!empty($yearLists))
												@foreach ($yearLists as $yearList)
													<option value="{{ $yearList->year }}" {{ $yearList->year == $year ? 'selected' : '' }}>{{ $yearList->year }}</option>
												@endforeach
											@endif
										</select>
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
											<button type="submit" name="reset_btn" class="btn btn-outline-secondary f-14 rounded">Reset</button>
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
							<h3 class="card-title mb-0">Rates of Reservations & Average Sales</h3>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive parent-table f-14 p-3">
								<table class="table table-bordered table-hover table-striped mb-0">
									<caption class="px-3 pt-2">Monthly Average sales and Reservation Rates for <strong>{{ $propertyName }}</strong></caption>
									<thead>
										<tr>
											<th scope="col">Months</th>
											<th scope="col">{{ $propertyName }}</th>
										</tr>
										<tr>
											<th scope="col"></th>
											<th scope="col">Average Sales</th>
											<th></th>
											<th scope="col">Rates of Reservations</th>
										</tr>
									</thead>
									<tbody>
										@for ($i=1; $i < count($monthYears); $i++)
											<tr>
												<td>{{ $monthYears[$i] }}</td>
												@if ($monthlyAvgDiff[$i] > 0)
													<td class="bg-success text-white">{!! moneyFormat($default_cur_code->org_symbol, $monthlyAvg[$i]) !!}</td>
													<td class="bg-success text-white"><i class="fa fa-arrow-up"></i> +{{ $monthlyAvgDiff[$i] }}%</td>
												@elseif ($monthlyAvgDiff[$i] == 0)
													<td class="bg-primary text-white">{!! moneyFormat($default_cur_code->org_symbol, $monthlyAvg[$i]) !!}</td>
													<td class="bg-primary text-white"><i class="fa fa-arrow-right"></i> {{ $monthlyAvgDiff[$i] }}%</td>
												@else
													<td class="bg-danger text-white">{!! moneyFormat($default_cur_code->org_symbol, $monthlyAvg[$i]) !!}</td>
													<td class="bg-danger text-white"><i class="fa fa-arrow-down"></i> {{ $monthlyAvgDiff[$i] }}%</td>
												@endif
												@if ($reservationRateDiff[$i] > 0)
													<td class="bg-success text-white">{{ $reservationRates[$i] }}%</td>
													<td class="bg-success text-white"><i class="fa fa-arrow-up"></i> +{{ $reservationRateDiff[$i] }}%</td>
												@elseif ($reservationRateDiff[$i] == 0)
													<td class="bg-primary text-white">{{ $reservationRates[$i] }}%</td>
													<td class="bg-primary text-white"><i class="fa fa-arrow-up"></i> {{ $reservationRateDiff[$i] }}%</td>
												@else
													<td class="bg-danger text-white">{{ $reservationRates[$i] }}%</td>
													<td class="bg-danger text-white"><i class="fa fa-arrow-down"></i> {{ $reservationRateDiff[$i] }}%</td>
												@endif
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
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/js/report.min.js') }}"></script>
@endsection
