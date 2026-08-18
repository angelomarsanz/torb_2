@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Update Message</h1>
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
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title">Update Message Form</h3>
						</div>

						<form class="form-horizontal" action="{{ url('admin/send-message-email/' . $messages->id) }}" id="send_email" method="post" name="add_customer" accept-charset='UTF-8'>
							{{ csrf_field() }}
							<input type="hidden" name="message_id" value="{{ $messages->id }}">
							<input type="hidden" name="receiver_id" value="{{ $messages->receiver_id }}">
							<input type="hidden" name="admin_email" value="{{ $messages->type_id }}">
							<input type="hidden" name="admin_email" value="{{ $messages->sender->email }}">

							<div class="card-body">
								<div class="row mb-3">
									<label for="content" class="col-md-3 col-form-label text-md-end fw-bold">
										Message <span class="text-danger">*</span>
									</label>
									<div class="col-md-6">
										<textarea id="content" name="content" rows="4" class="form-control f-14" placeholder="Enter your message...">{{ $messages->message }}</textarea>
										<span id="content-validation-error" class="text-danger f-12"></span>
									</div>
								</div>
							</div>

							<div class="card-footer text-end">
								<a href="{{ url('admin/messages') }}" class="btn btn-outline-secondary f-14 me-2">Cancel</a>
								<button type="submit" class="btn btn-info text-white f-14" id="submitBtn">Update</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
	<script src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endpush
