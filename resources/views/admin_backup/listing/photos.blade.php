@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Photos</h1>
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
				<div class="col-lg-3 col-12 settings_bar_gap">
					@include('admin.common.property_bar')
				</div>
				<div class="col-lg-9 col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header">
							<h3 class="card-title">Property Photos</h3>
						</div>
						<div class="card-body">
							<form id="img_form" enctype="multipart/form-data" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="signup-form login-form" accept-charset="UTF-8">
								{{ csrf_field() }}
								<div class="card mb-3">
									<div class="card-body">
										@if(session('success'))
											<div class="alert alert-success alert-dismissible fade show" role="alert">
												{{ session('success') }}
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										@endif
										<div class="row align-items-center">
											<div class="col-md-6">
												<input class="form-control f-14" name="file" id="photo_file" type="file" value="">
												<input type="hidden" id="photo" type="text" name="photos">
												<input type="hidden" name="img_name" id="img_name">
												<input type="hidden" name="crop" id="type" value="crop">
												<p class="text-muted f-12 mt-1">(Width 640px and Height 360px)</p>
												<div id="result" class="hide">
													<img src="#" alt="">
												</div>
												@if ($errors->any('file'))
													<span class="text-danger f-12">{{ $errors->first() }}</span>
												@endif
											</div>
											<div class="col-md-6">
												<button type="submit" class="btn btn-info text-white f-14" id="submit">
													<i class="fa fa-upload me-1"></i> Upload
												</button>
											</div>
										</div>
									</div>
								</div>
							</form>

							<div class="row">
								<div id="photo-list-div" class="ps-4 min-height-div row">
									<?php $serial = 0; ?>
									@foreach($photos as $photo)
										<?php $serial++; ?>
										<div class="col-md-4 mb-3" id="photo-div-{{ $photo->id }}">
											<div class="room-image-container200" style="background-image:url('{{ url('public/images/property/' . $photo->property_id. '/' . $photo->photo) }}');">
												@if($photo->cover_photo == 0)
													<a class="photo-delete" href="javascript:void(0)" data-rel="{{ $photo->id }}">
														<p class="photo-delete-icon"><i class="fa fa-trash-o"></i></p>
													</a>
												@endif
											</div>
											<div class="mt-2">
												<textarea data-rel="{{ $photo->id }}" class="form-control f-14 photo-highlights" placeholder="What are the highlights of this photo?">{{ $photo->message }}</textarea>
											</div>
											<div class="row mt-2">
												<div class="col-md-6">
													<label class="fw-bold f-14 mb-1">Serial</label>
													<input type="text" image_id="{{ $photo->id }}" property_id="{{ $result->id }}" id="serial-{{ $photo->id }}" class="form-control f-14 serial" name="serial" value="{{ $photo->serial }}">
												</div>
												<div class="col-md-6">
													@if($photo->cover_photo == 0)
														<label class="fw-bold f-14 mb-1">Cover Photo</label>
														<select class="form-select f-14 photoId" id="photoId">
															<option value="Yes" <?= ($photo->cover_photo == 1) ? 'selected' : '' ?> image_id="{{ $photo->id }}" property_id="{{ $result->id }}">Yes</option>
															<option value="No" <?= ($photo->cover_photo == 0) ? 'selected' : '' ?> image_id="{{ $photo->id }}" property_id="{{ $result->id }}">No</option>
														</select>
													@endif
												</div>
											</div>
											@if($serial % 3 == 0)
												<div class="clearfix">&nbsp;</div>
											@endif
										</div>
									@endforeach
								</div>
								<div class="col-md-12">
									<span class="text-danger display-off" id="photo">This field is required</span>
								</div>
							</div>
						</div>
						<div class="card-footer d-flex justify-content-between">
							<a data-prevent-default="" href="{{ url('admin/listing/' . $result->id . '/amenities') }}" class="btn btn-outline-secondary f-14">
								<i class="fa fa-arrow-left me-1"></i> Back
							</a>
							<a href="{{ url('admin/listing/' . $result->id . '/pricing') }}" class="btn btn-info text-white f-14">
								<i class="fa fa-arrow-right me-1"></i> Next
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade dis-none z-index-high" id="crop-modal" role="dialog">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title f-18">Edit Image</h4>
				<button type="button" class="btn-close cls-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div>
				<canvas id="canvas">
					Your browser does not HTML5 canvas element.
				</canvas>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info text-white f-14" id="crop" type="submit" name="submit">Crop</button>
				<button type="button" id="restore" class="btn btn-outline-secondary f-14">Skip</button>
			</div>
		</div>
	</div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('public/css/cropper.css') }}"/>
<link rel="stylesheet" href="{{ asset('public/css/photo-listing.min.css') }}"/>
@endpush

@section('validate_script')
<script src="{{ asset('public/js/cropper.min.js') }}"></script>
<script type="text/javascript">
	let photoUploadURL = '{{ url("add_photos/$result->id") }}';
	var photoRoomURl = '{{ url("images/rooms/" . $result->id) }}';
	var photoMessageURL = '{{url("admin/listing/$result->id/photo_message")}}';
	let photoDeleteURL = '{{ url("admin/listing/$result->id/photo_delete") }}';
	let makeDefaultPhotoURL = '{{ url("admin/listing/photo/make_default_photo") }}';
	var makePhotoSerialURL = '{{ url("admin/listing/photo/make_photo_serial") }}';
	let highlightsPhotoText = "{{ __('What are the highlights of this photo?') }}";
	let networkErrorText = "{{ __('Network error! Please try again.') }}";
	var token = '{{ csrf_token() }}';
	let areYouSureText = "{{ __('Are you sure you want to delete this?') }}";
	let deleteForeverText = "{{ __('If you delete this, it will be gone forever.') }}";
	let invalidImagetypeText = "{{ __('Invalid file type! Please select an image file.') }}";
	let noFileSelectedText = "{{ __('No file(s) selected.') }}";
	let page = 'photos';
	var message = "{{ __('The file must be an image (jpg, jpeg, png or gif)') }}";
	let gl_photo_id = 0;
</script>
<script src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
<script src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
<script src="{{ asset('public/backend/js/listing-photo.min.js') }}"></script>
@endsection
