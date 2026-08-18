@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">MESSAGES</h1>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-lg-8 mx-auto col-12">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<div class="workbench-main w-100 ps-0">
									@if (Session::has('error'))
										<div class="mb-4">
											<div class="alert alert-warning alert-dismissible fade show shadow-sm" style="border-radius: 8px;" role="alert">
												<strong>Warning!</strong> Whoops there was an error. Please verify your below information.
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										</div>
									@endif

									<form id="send_email" method="post" action="{{ url('admin/send-message-email/' . $messages->id) }}" class="form-horizontal">
										{{ csrf_field() }}
										<input type="hidden" name="message_id" value="{{ $messages->id }}">
										<input type="hidden" name="receiver_id" value="{{ $messages->receiver_id }}">
										<input type="hidden" name="admin_email" value="{{ $messages->type_id }}">
										<input type="hidden" name="admin_email" value="{{ $messages->sender->email }}">

										<div class="settings-form-card">
											
											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-envelope-o"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Update Message</h4>
														<p class="settings-form-subtitle">Modify the message content for this communication</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Message Content --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-commenting-o"></i>
														<span>Message Content</span>
													</div>

													{{-- Content --}}
													<div class="settings-field-row align-items-start border-bottom-0 pb-0 mb-0">
														<label for="content" class="settings-field-label pt-2">Message <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<textarea name="content" id="content" class="form-control settings-input" rows="6" placeholder="Enter your message...">{{ $messages->message }}</textarea>
															<span id="content-validation-error" class="text-danger f-12"></span>
															<span class="text-danger f-12">{{ $errors->first("content") }}</span>
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save" id="submitBtn">
														<i class="fa fa-check me-2"></i>Update
													</button>
													<a class="btn settings-btn-cancel" href="{{ url('admin/messages') }}">Cancel</a>
												</div>
												<small class="text-muted d-none d-md-block" style="font-size: 11.5px; opacity: 0.7;">Fields marked with <span class="text-danger">*</span> are required</small>
											</div>
										</div>
									</form>
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
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endpush
