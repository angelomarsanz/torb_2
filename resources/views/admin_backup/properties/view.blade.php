@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header p-0">
		<div class="stunning-header-container">
			<div class="stunning-header-title-wrap">
				<div class="stunning-header-icon">
					<i class="fa fa-home"></i>
				</div>
				<h1 class="stunning-page-title">
					Properties
					<small>Control panel & property management</small>
				</h1>
			</div>
			<div class="stunning-header-actions">
				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'add_properties'))
					<a class="stunning-btn-primary" href="{{ url('admin/add-properties') }}">
						<i class="fa fa-plus"></i>
						<span>Add Property</span>
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
							<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/properties') }}" method="GET" accept-charset="UTF-8">
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
											<option value="Listed" {{ $allstatus == "Listed" ? 'selected' : '' }}>Listed</option>
											<option value="Unlisted" {{ $allstatus == "Unlisted" ? 'selected' : '' }}>Unlisted</option>
										</select>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-6 col-12">
										<label class="form-label" for="space_type">Space Type</label>
										<select class="form-select select2-no-search" name="space_type" id="space_type">
											<option value="">All Space Types</option>
											@if ($space_type_all)
												@foreach($space_type_all as $data)
													<option value="{{ $data->id }}" {{ $data->id == $allSpaceType ? 'selected' : '' }}>{{ $data->name }}</option>
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
								<h3 class="card-title mb-0">Properties Management</h3>
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
    var page         = "properties";

</script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>

@endsection
