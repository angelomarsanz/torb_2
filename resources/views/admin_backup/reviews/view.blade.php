@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Reviews <small class="text-muted fw-normal">Control panel</small></h1>
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
							<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/reviews') }}" method="GET" accept-charset="UTF-8">
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
										<label class="form-label" for="property">Property</label>
										<select class="form-select select2" name="property" id="property">
											<option value="">All</option>
											@if (!empty($property))
												@foreach ($property as $properties)
													<option value="{{ $properties->id }}" {{ $properties->id == $allproperties ? 'selected' : '' }}>{{ $properties->name }}</option>
												@endforeach
											@endif
										</select>
									</div>

									<div class="col-xl-2 col-lg-4 col-md-6 col-12">
										<label class="form-label" for="reviewer">Reviewer</label>
										<select class="form-select" name="reviewer" id="reviewer">
											<option value="">All</option>
											<option value="guest" {{ $allreviewer == "guest" ? 'selected' : '' }}>Guest</option>
											<option value="host" {{ $allreviewer == "host" ? 'selected' : '' }}>Host</option>
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
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
							<h3 class="card-title mb-0">Reviews Management</h3>
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
	var sessionDate = '{{ strtoupper(Session::get('date_format_type')) }}';
	var page = 'review'
</script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
