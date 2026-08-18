@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner">
				<h1 class="dashboard-page-title">TESTIMONIALS</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						<div class="settings-form-card mb-5">
							<div class="settings-form-header">
								<div class="d-flex justify-content-between align-items-center">
									<div class="d-flex align-items-center gap-3">
										<div class="settings-form-icon">
											<i class="fa fa-quote-left"></i>
										</div>
										<div>
											<h4 class="settings-form-title">Testimonials Management</h4>
											<p class="settings-form-subtitle">Review and manage customer stories and social proof displayed on the homepage</p>
										</div>
									</div>
									@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'add_testimonial'))
										<a class="btn settings-btn-save" href="{{ url('admin/add-testimonials') }}">
											<i class="fa fa-plus me-2"></i>Add Testimonial
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

@push('scripts')
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush
