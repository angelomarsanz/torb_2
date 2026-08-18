@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">COUNTRY MANAGEMENT</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<div class="workbench-container">
									
									<div class="workbench-sidebar">
										<div class="text-muted mb-3 f-12 fw-bold text-uppercase letter-spacing-1 ps-2">Settings Menu</div>
										@include('admin.common.settings_bar')
									</div>

									<div class="workbench-main">
										<div class="settings-form-card">
											<div class="settings-form-header">
												<div class="d-flex justify-content-between align-items-center">
													<div class="d-flex align-items-center gap-3">
														<div class="settings-form-icon">
															<i class="fa fa-globe"></i>
														</div>
														<div>
															<h4 class="settings-form-title">Countries</h4>
															<p class="settings-form-subtitle">Configure supported countries and regions for user registration and listings</p>
														</div>
													</div>
													<a class="btn settings-btn-save" href="{{ url('admin/settings/add-country') }}">
														<i class="fa fa-plus me-2"></i>Add Country
													</a>
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
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush
