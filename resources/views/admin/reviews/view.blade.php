@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner">
				<h1 class="dashboard-page-title">REVIEWS</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-4">
							<div class="settings-form-body p-4">
								<form class="form-horizontal" enctype="multipart/form-data" action="{{ url('admin/reviews') }}" method="GET" accept-charset="UTF-8">
									{{ csrf_field() }}
									<input type="hidden" id="startDate" name="from" value="{{ isset($from) ? $from : '' }}">
									<input type="hidden" id="endDate" name="to" value="{{ isset($to) ? $to : '' }}">

									<div class="row g-4 align-items-end date-parent">
										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-2">Date Range</label>
											<div class="input-group">
												<button type="button" class="form-control text-start d-flex align-items-center justify-content-between h-42 settings-input" id="daterange-btn">
													<span><i class="fa fa-calendar me-2"></i> Pick a date range</span>
													<i class="fa fa-caret-down opacity-50"></i>
												</button>
											</div>
										</div>

										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-2" for="property">Property</label>
											<select class="form-select select2 settings-input" name="property" id="property">
												<option value="">All Properties</option>
												@if (!empty($property))
													@foreach ($property as $properties)
														<option value="{{ $properties->id }}" {{ $properties->id == $allproperties ? 'selected' : '' }}>{{ $properties->name }}</option>
													@endforeach
												@endif
											</select>
										</div>

										<div class="col-xl-3 col-lg-3 col-md-6">
											<label class="settings-field-label text-start mb-2" for="reviewer">Reviewer Type</label>
											<select class="form-select select2-no-search settings-input" name="reviewer" id="reviewer">
												<option value="">All Reviewers</option>
												<option value="guest" {{ $allreviewer == "guest" ? 'selected' : '' }}>Guest</option>
												<option value="host" {{ $allreviewer == "host" ? 'selected' : '' }}>Host</option>
											</select>
										</div>

										<div class="col-xl-3 col-lg-3 col-md-6">
											<div class="d-flex gap-2">
												<button type="submit" name="btn" class="btn settings-btn-save h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-filter"></i> Filter
												</button>
												<button type="button" name="reset_btn" id="reset_btn" class="btn settings-btn-cancel h-42 px-4 d-flex align-items-center gap-2">
													<i class="fa fa-refresh"></i> Reset
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="settings-form-card mb-5">
							<div class="settings-form-header">
								<div class="d-flex align-items-center gap-3">
									<div class="settings-form-icon">
										<i class="fa fa-star"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Reviews Management</h4>
										<p class="settings-form-subtitle">Monitor and moderate property reviews submitted by guests and hosts</p>
									</div>
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
	var page = 'review'
</script>
<script src="{{ asset('public/backend/js/reset-btn.min.js') }}"></script>
<script src="{{ asset('public/backend/js/admin-date-range-picker.min.js') }}"></script>
@endsection
