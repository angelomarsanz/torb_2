@extends('admin.template')
@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">PHOTOS</h1>
				<div class="dashboard-header-right">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</section>

		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-lg-3 col-12 settings_bar_gap">
						@include('admin.common.property_bar')
					</div>

					<div class="col-lg-9 col-12">
						<div class="card stunning-table-card rounded-4 border-0 shadow-sm mb-0">
							<div class="card-body p-4 pt-3">
								<div class="settings-form-card">
									
									{{-- Form Header --}}
									<div class="settings-form-header">
										<div class="d-flex align-items-center gap-3">
											<div class="settings-form-icon">
												<i class="fa fa-camera-retro"></i>
											</div>
											<div>
												<h4 class="settings-form-title">Property Photos</h4>
												<p class="settings-form-subtitle">Upload and manage high-quality photos of your space</p>
											</div>
										</div>
									</div>

									<div class="settings-form-body">
										@if(session('success'))
											<div class="alert alert-success alert-dismissible fade show rounded-5 border-0 shadow-sm mb-4 p-2 d-flex align-items-center justify-content-between" role="alert" style="background: #e6fffa;">
												<div class="d-flex align-items-center">
													<div class="bg-teal-500 text-white rounded-circle d-flex align-items-center justify-content-center ms-1" style="width: 32px; height: 32px; background: #38b2ac;">
														<i class="fa fa-check f-14"></i>
													</div>
												</div>
												<div class="text-center flex-grow-1">
													<h6 class="mb-0 fw-bold f-14 text-teal-900" style="letter-spacing: 0.5px; color: #234e52;">Success!</h6>
													<p class="mb-0 f-12 fw-500 text-teal-800" style="color: #2c7a7b;">{{ session('success') }}</p>
												</div>
												<button type="button" class="btn-close shadow-none me-1" data-bs-dismiss="alert" aria-label="Close" style="padding: 1.25rem;"></button>
											</div>
										@endif

										{{-- Upload Section --}}
										<div class="settings-section">
											<div class="settings-section-label">
												<i class="fa fa-upload"></i>
												<span>Upload New Photos</span>
											</div>

											<form id="img_form" enctype="multipart/form-data" method="post" action="{{ url('admin/listing/' . $result->id . '/' . $step) }}" class="form-horizontal" accept-charset="UTF-8">
												{{ csrf_field() }}
												
												<div class="settings-field-row mb-0">
													<div class="settings-field-label">
														<label class="f-14 fw-600 mb-0">Select Image</label>
														<p class="text-muted f-11 mt-1 mb-0">Recommended: 640x360px or larger</p>
													</div>
													<div class="settings-field-input">
														<style>
															#photo_file::-webkit-file-upload-button {
																background: #f8fafc;
																border: none;
																border-right: 1px solid #e2e8f0;
																margin: -5px 12px -5px -12px;
																padding: 0 15px;
																height: 38px;
																cursor: pointer;
																font-weight: 600;
																color: #475569;
																transition: all 0.2s;
															}
															#photo_file::-webkit-file-upload-button:hover {
																background: #f1f5f9;
																color: #1e293b;
															}
															#photo_file::file-selector-button {
																background: #f8fafc;
																border: none;
																border-right: 1px solid #e2e8f0;
																margin: -5px 12px -5px -12px;
																padding: 0 15px;
																height: 38px;
																cursor: pointer;
																font-weight: 600;
																color: #475569;
															}
														</style>
														<div class="input-group dashboard-filter-group flex-nowrap align-items-stretch">
															<input class="form-control border-end-0" name="file" id="photo_file" type="file" style="border-radius: 4px 0 0 4px; height: 38px; padding: 2px 12px; overflow: hidden;">
															<button type="submit" class="btn settings-btn-save px-4" id="submit" style="border-radius: 0 4px 4px 0 !important; height: 38px !important; min-width: 130px !important;">
																<i class="fa fa-cloud-upload me-2 f-16"></i> Upload Photo
															</button>
														</div>
														
														<input type="hidden" id="photo" name="photos">
														<input type="hidden" name="img_name" id="img_name">
														<input type="hidden" name="crop" id="type" value="crop">
														
														<div id="result" class="hide mt-3">
															<img src="#" alt="" class="rounded shadow-sm border" style="max-height: 200px;">
														</div>
														@if ($errors->any('file'))
															<span class="text-danger f-12 mt-2 d-block px-2"><i class="fa fa-exclamation-triangle me-1"></i> {{ $errors->first() }}</span>
														@endif
													</div>
												</div>
											</form>
										</div>

										<div class="settings-section">
											<div class="settings-section-label mb-4">
												<i class="fa fa-th text-slate-400"></i>
												<span class="text-uppercase fw-700 ls-1 f-13 text-slate-500">Manage Photos</span>
											</div>

											<div class="row g-4" id="photo-list-div">
												<?php $serial = 0; ?>
												@foreach($photos as $photo)
													<?php $serial++; ?>
													<div class="col-md-6 col-xl-4 mb-3" id="photo-div-{{ $photo->id }}">
														<div class="card border border-light shadow-sm rounded-4 overflow-hidden h-100 bg-white hover-shadow-card transition-all">
															<div class="position-relative">
																<div class="room-image-container200" style="background-image:url('{{ url('public/images/property/' . $photo->property_id. '/' . $photo->photo) }}'); height: 190px;">
																	@if($photo->cover_photo == 0)
																		<a class="photo-delete position-absolute top-0 end-0 m-3 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg border-0" href="javascript:void(0)" data-rel="{{ $photo->id }}" style="width: 36px; height: 36px; font-size: 16px; opacity: 1; transform: scale(1); transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
																			<i class="fa fa-trash-o"></i>
																		</a>
																	@else
																		<div class="position-absolute top-0 start-0 m-3 h-100 w-100 pointer-events-none" style="background: linear-gradient(to bottom right, rgba(28, 100, 242, 0.1), transparent);"></div>
																		<span class="position-absolute top-0 start-0 m-3 badge bg-primary shadow-sm px-3 py-1 rounded-pill f-10 fw-700">COVER PHOTO</span>
																	@endif
																</div>
															</div>
															<div class="card-body p-3">
																<textarea data-rel="{{ $photo->id }}" class="form-control settings-input f-13 photo-highlights mb-3 shadow-none border-light focus-border-primary" rows="3" placeholder="What are the highlights of this photo?" style="background: #f8fafc; border-radius: 10px;">{{ $photo->message }}</textarea>
																
																<div class="row g-2">
																	<div class="col-6">
																		<label class="f-13 fw-bold text-dark mb-1">Serial</label>
																		<input type="text" image_id="{{ $photo->id }}" property_id="{{ $result->id }}" id="serial-{{ $photo->id }}" class="form-control settings-input f-13 serial py-2 px-3 shadow-none border-light" value="{{ $photo->serial }}">
																	</div>
																	<div class="col-6">
																		<label class="f-13 fw-bold text-dark mb-1">Cover Photo</label>
																		<select class="form-select settings-input f-13 photoId py-2 px-3 shadow-none border-light" id="photoId">
																			<option value="No" {{ ($photo->cover_photo == 0) ? 'selected' : '' }} image_id="{{ $photo->id }}" property_id="{{ $result->id }}">No</option>
																			<option value="Yes" {{ ($photo->cover_photo == 1) ? 'selected' : '' }} image_id="{{ $photo->id }}" property_id="{{ $result->id }}">Yes</option>
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
												@endforeach
											</div>
											<div class="mt-3">
												<span class="text-danger display-off" id="photo">This field is required</span>
											</div>
										</div>
									</div>

									{{-- Form Footer --}}
									<div class="settings-form-footer">
										<div class="d-flex align-items-center gap-2">
											<a href="{{ url('admin/listing/' . $result->id . '/amenities') }}" class="btn settings-btn-cancel">
												<i class="fa fa-arrow-left me-1 f-12"></i> Back
											</a>
											<a href="{{ url('admin/listing/' . $result->id . '/pricing') }}" class="btn settings-btn-save">
												Next <i class="fa fa-arrow-right ms-1 f-12"></i>
											</a>
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

{{-- Crop Modal --}}
<div class="modal fade" id="crop-modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content rounded-4 border-0 shadow-lg">
			<div class="modal-header border-bottom-0 pb-0">
				<h4 class="modal-title f-18 fw-bold">Edit Image</h4>
				<button type="button" class="btn-close cls-reload" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<div class="rounded-3 overflow-hidden border">
					<canvas id="canvas" style="max-width: 100%;"></canvas>
				</div>
			</div>
			<div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
				<button type="button" id="restore" class="btn settings-btn-cancel px-4">Skip</button>
				<button class="btn settings-btn-save px-4" id="crop" type="submit">Crop & Save</button>
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
