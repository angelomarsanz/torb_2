@extends('admin.template')

@section('main')
	<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
		<div class="dashboard-content-inner">
			<section class="content-header dashboard-page-header">
				<div class="dashboard-header-inner">
					<h1 class="dashboard-page-title">PROPERTIES</h1>
				</div>
			</section>

			<section class="content">
				<div class="container-fluid px-0">
					<div class="row">
						<div class="col-12 mt-0">
							<div class="settings-form-card mb-4">
								<div class="settings-form-body p-4">
									<form class="form-horizontal" action="{{ url('admin/properties') }}" method="GET"
										accept-charset="UTF-8">
										{{ csrf_field() }}

										<input type="hidden" id="startDate" name="from"
											value="{{ isset($from) ? $from : '' }}">
										<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

										<div class="row g-4 align-items-end date-parent">
											<div class="col-xl-3 col-lg-3 col-md-6">
												<label class="settings-field-label text-start mb-2" for="daterange-btn">Date
													Range</label>
												<div class="input-group">
													<button type="button"
														class="form-control settings-input text-start d-flex align-items-center justify-content-between"
														id="daterange-btn">
														<span><i class="fa fa-calendar me-2"></i> Pick a date range</span>
														<i class="fa fa-caret-down text-muted"></i>
													</button>
												</div>
											</div>
											<div class="col-xl-3 col-lg-3 col-md-6">
												<label class="settings-field-label text-start mb-2"
													for="status">Status</label>
												<select class="form-select settings-input" name="status" id="status">
													<option value="">All Statuses</option>
													<option value="Listed" {{ $allstatus == "Listed" ? 'selected' : '' }}>
														Listed</option>
													<option value="Unlisted" {{ $allstatus == "Unlisted" ? 'selected' : '' }}>
														Unlisted</option>
												</select>
											</div>
											<div class="col-xl-3 col-lg-3 col-md-6">
												<label class="settings-field-label text-start mb-2" for="space_type">Space
													Type</label>
												<select class="form-select settings-input" name="space_type"
													id="space_type">
													<option value="">All Space Types</option>
													@if ($space_type_all)
														@foreach($space_type_all as $data)
															<option value="{{ $data->id }}" {{ $data->id == $allSpaceType ? 'selected' : '' }}>{{ $data->name }}</option>
														@endforeach
													@endif
												</select>
											</div>
											<div class="col-xl-3 col-lg-3 col-md-6">
												<div class="d-flex gap-2">
													<button type="submit" name="btn"
														class="btn settings-btn-save h-42 px-4 d-flex align-items-center gap-2">
														<i class="fa fa-filter"></i> Filter
													</button>
													<button type="button" name="reset_btn" id="reset_btn"
														class="btn settings-btn-cancel h-42 px-4 d-flex align-items-center gap-2">
														<i class="fa fa-refresh"></i> Reset
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="settings-form-card">
								<div class="settings-form-header">
									<div class="d-flex justify-content-between align-items-center">
										<div class="d-flex align-items-center gap-3">
											<div class="settings-form-icon">
												<i class="fa fa-building-o"></i>
											</div>
											<div>
												<h4 class="settings-form-title">Properties Management</h4>
												<p class="settings-form-subtitle">Overview and management of all listed
													properties</p>
											</div>
										</div>
										@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'add_properties'))
											<a class="btn settings-btn-save" href="{{ url('admin/add-properties') }}">
												<i class="fa fa-plus me-2"></i>Add Property
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
		var sessionDate = '{{ strtoupper(Session::get('date_format_type')) }}';
		var user_id = '{{ $user->id ?? '' }}';
		var page = "properties";

	</script>
	<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
	<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>

@endsection