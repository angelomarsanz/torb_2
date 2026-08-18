@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content-header dashboard-page-header">
			<div class="dashboard-header-inner w-100 d-flex justify-content-between align-items-center">
				<h1 class="dashboard-page-title m-0">EDIT ROLE</h1>
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

									<form id="edit_role" method="post" action="{{ url('admin/settings/edit-role/' . $result->id) }}" class="form-horizontal">
										{{ csrf_field() }}
										<div class="settings-form-card">
											
											{{-- Form Header --}}
											<div class="settings-form-header">
												<div class="d-flex align-items-center gap-3">
													<div class="settings-form-icon">
														<i class="fa fa-users"></i>
													</div>
													<div>
														<h4 class="settings-form-title">Edit Role</h4>
														<p class="settings-form-subtitle">Modify the identity and access permissions for the {{ $result->display_name }} role</p>
													</div>
												</div>
											</div>

											<div class="settings-form-body">

												{{-- Section: Role Identity --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-id-badge"></i>
														<span>Role Details</span>
													</div>

													{{-- Name --}}
													<div class="settings-field-row">
														<label for="name" class="settings-field-label">Name <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="name" class="form-control settings-input" id="name" placeholder="role_name" value="{{ $result->name }}">
															<span class="text-danger f-12">{{ $errors->first("name") }}</span>
														</div>
													</div>

													{{-- Display Name --}}
													<div class="settings-field-row">
														<label for="display_name" class="settings-field-label">Display Name <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<input type="text" name="display_name" class="form-control settings-input" id="display_name" placeholder="User Friendly Name" value="{{ $result->display_name }}">
															<span class="text-danger f-12">{{ $errors->first("display_name") }}</span>
														</div>
													</div>

													{{-- Description --}}
													<div class="settings-field-row">
														<label for="description" class="settings-field-label">Description <span class="text-danger">*</span></label>
														<div class="settings-field-input">
															<textarea name="description" placeholder="What this role can do..." rows="3" class="form-control settings-input" id="description" style="height: auto !important;">{{ $result->description }}</textarea>
															<span class="text-danger f-12">{{ $errors->first('description') }}</span>
														</div>
													</div>
												</div>

												{{-- Section: Permissions --}}
												<div class="settings-section">
													<div class="settings-section-label">
														<i class="fa fa-key"></i>
														<span>Permissions & Access Control</span>
													</div>

													<div class="settings-field-row d-block">
														@php
															$hierarchy = [
																'Admin Panel' => [
																	'Web' => [
																		'User Management' => [
																			'permissions' => ['Manage Admin', 'View Customers', 'Add Customer', 'Edit Customer', 'Delete Customer', 'Manage Roles']
																		],
																		'Property Management' => [
																			'permissions' => ['View Properties', 'Add Properties', 'Edit Properties', 'Delete Property', 'Manage Property Type', 'Space Type Setting', 'Manage Amenities', 'Manage Amenities Type', 'Manage Bed Type']
																		],
																		'Bookings & Finance' => [
																			'permissions' => ['Manage Bookings', 'View Payouts', 'Payment Settings', 'Manage Fees', 'Manage Currency']
																		],
																		'Site Content' => [
																			'permissions' => ['Manage Pages', 'Manage Banners', 'Manage Testimonial', 'Add Testimonial', 'Edit Testimonial', 'Delete Testimonial', 'Manage Metas', 'Starting Cities Settings']
																		],
																		'Reports & Analytics' => [
																			'permissions' => ['View Reports']
																		],
																		'System Settings' => [
																			'permissions' => ['Settings', 'Preference', 'Email Settings', 'Manage Email Template', 'Api Credentials', 'Social Links', 'Manage SMS', 'Manage Messages', 'Edit Messages', 'Google Recaptcha', 'Social Logins', 'Database Backup', 'Addons', 'Manage Language']
																		]
																	]
																]
															];

															// Flatten the permissions into IDs for easy lookup
															$allPerms = $permissions; 
														@endphp

														<div class="hierarchical-permissions shadow-sm rounded-3 overflow-hidden border bg-white">
															{{-- Level 1: Top Level (Admin Panel) --}}
															@foreach ($hierarchy as $l1Name => $l2Groups)
																<div class="perm-node level-1 border-bottom last-border-0">
																	<div class="perm-header bg-light d-flex align-items-center justify-content-between p-3 clickable-node" style="cursor: pointer;">
																		<div class="d-flex align-items-center gap-3">
																			<i class="fa fa-caret-down toggle-icon f-18 text-muted-400" style="transition: transform 0.2s;"></i>
																			<span class="fw-bold text-dark f-15">{{ $l1Name }}</span>
																			<span class="text-muted f-12 ms-2 selection-count">Selected 0 out of 0</span>
																		</div>
																		<div class="form-check mb-0">
																			<input class="form-check-input group-checkbox l1-check" type="checkbox" style="width: 18px; height: 18px;" onclick="event.stopPropagation();">
																		</div>
																	</div>
																	<div class="perm-content ps-4 bg-white border-top">
																		{{-- Level 2: Sub-Level (Web) --}}
																		@foreach ($l2Groups as $l2Name => $l3Groups)
																			<div class="perm-node level-2 border-start ms-2">
																				<div class="perm-header d-flex align-items-center justify-content-between p-3 border-bottom bg-white hover-bg-light transition-all clickable-node" style="cursor: pointer;">
																					<div class="d-flex align-items-center gap-3">
																						<i class="fa fa-caret-down toggle-icon f-16 text-muted-400"></i>
																						<span class="fw-600 text-dark f-14">{{ $l2Name }}</span>
																						<span class="text-muted f-11 ms-2 selection-count">Selected 0 out of 0</span>
																					</div>
																					<div class="form-check mb-0">
																						<input class="form-check-input group-checkbox l2-check" type="checkbox" style="width: 16px; height: 16px;" onclick="event.stopPropagation();">
																					</div>
																				</div>
																				<div class="perm-content ps-4">
																					{{-- Level 3: Modules (User Management, etc.) --}}
																					@foreach ($l3Groups as $l3Name => $config)
																						<div class="perm-node level-3 border-start border-primary ms-2 my-3 rounded-2 border shadow-xs overflow-hidden">
																							<div class="perm-header d-flex align-items-center justify-content-between p-3 bg-primary-light-5 transition-all clickable-node" style="cursor: pointer;">
																								<div class="d-flex align-items-center gap-3">
																									<i class="fa fa-caret-down toggle-icon f-15 text-primary opacity-75"></i>
																									<span class="fw-600 text-dark f-13">{{ $l3Name }}</span>
																									<span class="text-primary f-11 ms-2 selection-count fw-500">Selected 0 out of 0</span>
																								</div>
																								<div class="form-check mb-0">
																									<input class="form-check-input group-checkbox l3-check" type="checkbox" style="width: 15px; height: 15px;" onclick="event.stopPropagation();">
																								</div>
																							</div>
																							<div class="perm-content p-3 bg-white border-top" style="border-top-color: rgba(0,0,0,0.05) !important;">
																								{{-- Level 4: Leaf Permissions --}}
																								<div class="row g-3 pb-1">
																									@foreach ($config['permissions'] as $permName)
																										@php
																											$permId = array_search($permName, $allPerms);
																										@endphp
																										@if ($permId !== false)
																											<div class="col-xl-3 col-lg-4 col-md-6">
																												<div class="form-check custom-permission-checkbox mb-0">
																													<input type="checkbox" name="permission[]" value="{{ $permId }}" class="form-check-input leaf-checkbox" id="perm_{{ $permId }}"
																														{{ isset($stored_permissions) && in_array($permId, $stored_permissions) ? 'checked' : '' }}>
																													<label class="form-check-label f-13 mb-0 w-100" for="perm_{{ $permId }}" style="cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
																														{{ $permName }}
																													</label>
																												</div>
																											</div>
																										@endif
																									@endforeach
																								</div>
																							</div>
																						</div>
																					@endforeach
																				</div>
																			</div>
																		@endforeach
																	</div>
																</div>
															@endforeach
														</div>
													</div>
												</div>
											</div>

											{{-- Form Footer --}}
											<div class="settings-form-footer">
												<div class="d-flex align-items-center gap-2">
													<button type="submit" class="btn settings-btn-save">
														<i class="fa fa-check me-2"></i>Save Changes
													</button>
													<a class="btn settings-btn-cancel" href="{{ url('admin/settings/roles') }}">Cancel</a>
												</div>
												</div>
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
<script type="text/javascript">
	'use strict'
	var message = "{{ __('Please select at least one checkbox!') }}";

	$(document).ready(function() {
		// Toggle nodes (Full Header Click)
		$('.clickable-node').on('click', function(e) {
			if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) return;
			
			var node = $(this).closest('.perm-node');
			var content = node.children('.perm-content');
			content.slideToggle(200);
			$(this).find('.toggle-icon').first().toggleClass('fa-caret-down fa-caret-right');
		});

		// Group checkbox change (Select all children)
		$('.group-checkbox').on('change', function() {
			var isChecked = $(this).prop('checked');
			var node = $(this).closest('.perm-node');
			node.find('input[type="checkbox"]').prop('checked', isChecked);
			updateAllCounters();
		});

		// Leaf checkbox change
		$('.leaf-checkbox').on('change', function() {
			updateAllCounters();
		});

		function updateAllCounters() {
			// Iterate Level 3 first (Bottom-up logic)
			$('.perm-node.level-3').each(function() {
				var leaves = $(this).find('.leaf-checkbox');
				var total = leaves.length;
				var selected = leaves.filter(':checked').length;
				$(this).find('.selection-count').first().text('Selected ' + selected + ' out of ' + total);
				
				// Update Level 3 Group Checkbox state
				var allChecked = total > 0 && selected === total;
				$(this).find('.group-checkbox').first().prop('checked', allChecked);
			});

			// Update Level 2
			$('.perm-node.level-2').each(function() {
				var leaves = $(this).find('.leaf-checkbox');
				var total = leaves.length;
				var selected = leaves.filter(':checked').length;
				$(this).find('.selection-count').first().text('Selected ' + selected + ' out of ' + total);
				
				var allChecked = total > 0 && selected === total;
				$(this).find('.group-checkbox').first().prop('checked', allChecked);
			});

			// Update Level 1
			$('.perm-node.level-1').each(function() {
				var leaves = $(this).find('.leaf-checkbox');
				var total = leaves.length;
				var selected = leaves.filter(':checked').length;
				$(this).find('.selection-count').first().text('Selected ' + selected + ' out of ' + total);
				
				var allChecked = total > 0 && selected === total;
				$(this).find('.group-checkbox').first().prop('checked', allChecked);
			});
		}

		// Initial setup
		updateAllCounters();
	});
</script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
