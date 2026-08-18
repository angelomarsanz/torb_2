@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content">
			<div class="container-fluid px-0">
				<div class="row justify-content-center">
					<div class="col-lg-10 col-xl-8 mt-0">
						<div class="settings-form-card">
							<div class="settings-form-header">
								<div class="d-flex align-items-center gap-3">
									<div class="settings-form-icon">
										<i class="fa fa-pencil-square-o"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Edit Payout</h4>
										<p class="settings-form-subtitle">Update withdrawal status for Reference #{{ $withDrawal->id }}</p>
									</div>
								</div>
							</div>

							<div class="settings-form-body p-4 pt-0">
								<form class="form-horizontal" action="{{ url('admin/payouts/edit/' . $withDrawal->id) }}" id="edit_payout" method="post" name="add_customer" accept-charset="UTF-8">
									{{ csrf_field() }}
									<input type="hidden" name="id" value="{{ $withDrawal->id }}">

									<div class="settings-section mt-4">
										<div class="settings-section-label">
											<i class="fa fa-info-circle"></i>
											<span>Update Status</span>
										</div>

										<div class="settings-field-row">
											<label class="settings-field-label">Payout Amount</label>
											<div class="settings-field-input pt-2 fw-bold text-dark">
												<input type="hidden" name="amount" value="{{ $withDrawal->subtotal }}">
												{!! $withDrawal->currency->org_symbol !!} {{ $withDrawal->subtotal }}
											</div>
										</div>

										<div class="settings-field-row">
											<label for="status" class="settings-field-label">Withdrawal Status</label>
											<div class="settings-field-input">
												<select class="form-select settings-input" name="status" id="status">
													<option value="Pending" {{ $withDrawal->status == 'Pending' ? 'selected' : '' }}>Pending</option>
													<option value="Success" {{ $withDrawal->status == 'Success' ? 'selected' : '' }}>Success</option>
												</select>
												@if ($errors->has('status'))
													<p class="text-danger f-12 mt-1 mb-0">{{ $errors->first('status') }}</p>
												@endif
											</div>
										</div>
									</div>

									<div class="settings-form-footer border-0 pt-4 mt-2">
										<div class="d-flex align-items-center gap-2">
											<button type="submit" class="btn settings-btn-save px-4" id="submitBtn">
												<i class="fa fa-check me-2"></i>Apply Changes
											</button>
											<a class="btn settings-btn-cancel px-4" href="{{ url('admin/payouts') }}">
												Cancel
											</a>
										</div>
									</div>
								</form>
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
