@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Edit Payout</h1>
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
				<div class="col-md-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title">Edit Payout</h3>
						</div>

						<form class="form-horizontal" action="{{ url('admin/payouts/edit/' . $withDrawal->id) }}" id="edit_payout" method="post" name="add_customer" accept-charset="UTF-8">
							{{ csrf_field() }}
							<input type="hidden" name="id" value="{{ $withDrawal->id }}">

							<div class="card-body">
								<div class="row mb-3">
									<label class="col-md-3 col-form-label text-md-end fw-bold">Amount</label>
									<div class="col-md-5">
										<input type="hidden" name="amount" value="{{ $withDrawal->subtotal }}" class="form-control f-14">
										<p class="mb-0 f-14 mt-lg-2">{!! $withDrawal->currency->org_symbol !!} {{ $withDrawal->subtotal }}</p>
									</div>
								</div>

								<div class="row mb-3">
									<label for="status" class="col-md-3 col-form-label text-md-end fw-bold">Status</label>
									<div class="col-md-5">
										<select class="form-select f-14" name="status" id="status">
											<option value="Pending" {{ $withDrawal->status == 'Pending' ? 'selected' : '' }}>Pending</option>
											<option value="Success" {{ $withDrawal->status == 'Success' ? 'selected' : '' }}>Success</option>
										</select>
										@if ($errors->has('status'))
											<p class="text-danger f-12 mt-1 mb-0">{{ $errors->first('status') }}</p>
										@endif
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a href="{{ url('admin/payouts') }}" class="btn btn-outline-secondary f-14 me-2">Cancel</a>
								<button type="submit" class="btn btn-info text-white f-14" id="submitBtn">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
