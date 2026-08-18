@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">EDIT SEO METAS</h1>
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
									@if (Session::has('error'))
										<div class="mb-4">
											<div class="alert alert-warning alert-dismissible fade show shadow-sm" style="border-radius: 8px;" role="alert">
												<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										</div>
									@endif

									<form id="edit_metas" method="post" action="{{ url('admin/settings/edit_meta/' . $result->id) }}" class="form-horizontal">
										{{ csrf_field() }}
										<div class="settings-form-card">
											
											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-tags"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Edit Meta Details</h4>
														<p class="settings-form-subtitle">Optimize search engine visibility for the {{ $result->url }} page</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Page Info --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-link"></i>
														<span>Target Page</span>
													</div>

													{{-- URL --}}
													<div class="settings-field-row">
														<label class="settings-field-label">Page URL</label>
														<div class="settings-field-input">
															<input type="text" class="form-control settings-input bg-light" value="{{ $result->url }}" readonly disabled>
															<small class="text-muted d-block mt-1" style="font-size: 11px;">The page path this meta data is associated with</small>
														</div>
													</div>
												</div>

												{{-- Section: Meta Data --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-search"></i>
														<span>SEO Configuration</span>
													</div>

													{{-- Title --}}
													<div class="settings-field-row">
														<label for="title" class="settings-field-label">Meta Title <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="title" class="form-control settings-input" id="title" placeholder="Page Title" value="{{ $result->title }}">
															<span class="text-danger f-12">{{ $errors->first("title") }}</span>
														</div>
													</div>

													{{-- Description --}}
													<div class="settings-field-row">
														<label for="description" class="settings-field-label">Meta Description <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<textarea name="description" placeholder="Briefly summarize page content for search results..." rows="4" class="form-control settings-input" id="description" style="height: auto !important;">{{ $result->description }}</textarea>
															<span class="text-danger f-12">{{ $errors->first('description') }}</span>
														</div>
													</div>

													{{-- Keywords --}}
													<div class="settings-field-row">
														<label for="keywords" class="settings-field-label">Keywords</label>
														<div class="settings-field-input">
															<textarea name="keywords" placeholder="keyword1, keyword2, keyword3..." rows="3" class="form-control settings-input" id="keywords" style="height: auto !important;">{{ $result->keywords }}</textarea>
															<span class="text-danger f-12">{{ $errors->first('keywords') }}</span>
															<small class="text-muted d-block mt-1" style="font-size: 11px;">Separate keywords with commas</small>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														<i class="fa fa-check me-2"></i>Save Metadata
													</button>
													<a class="btn settings-btn-cancel" href="{{ url('admin/settings/metas') }}">Cancel</a>
												</div>
												<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Optimizing metas helps improve organic search rankings</small>
											</div>
										</div>
									</form>
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

@section('validate_script')
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
