<div class="card card-outline card-info shadow-sm">
	<div class="card-header">
		<h3 class="card-title"><i class="fa fa-cogs me-2"></i>Manage Settings</h3>
	</div>
	<div class="card-body p-0">
		<div class="list-group list-group-flush f-14">
			@if (Permission::has_permission(Auth::guard('admin')->user()->id, 'general_setting'))
				<a href="{{ url('admin/settings') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings') ? 'active' : '' }}">
					<i class="fa fa-cog me-2"></i>General
				</a>
			@endif

			@if (Permission::has_permission(Auth::guard('admin')->user()->id, 'preference'))
				<a href="{{ url('admin/settings/preferences') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/preferences') ? 'active' : '' }}">
					<i class="fa fa-sliders me-2"></i>Preferences
				</a>
			@endif

			@if (Permission::has_permission(Auth::guard('admin')->user()->id, 'manage_sms'))
				<a href="{{ url('admin/settings/sms') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/sms') ? 'active' : '' }}">
					<i class="fa fa-comment me-2"></i>SMS Settings
				</a>
			@endif

			@if (Permission::has_permission(Auth::guard('admin')->user()->id, 'google_recaptcha'))
				<a href="{{ url('admin/settings/google-recaptcha-api-information') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/google-recaptcha-api-information') ? 'active' : '' }}">
					<i class="fa fa-shield me-2"></i>Google reCaptcha
				</a>
			@endif

			@if (Permission::has_permission(Auth::guard('admin')->user()->id, 'manage_banners'))
				<a href="{{ url('admin/settings/banners') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/banners') || (Route::current()->uri() == 'admin/settings/add-banners') || (Route::current()->uri() == 'admin/settings/edit-banners/{id}') ? 'active' : '' }}">
					<i class="fa fa-image me-2"></i>Banners
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'starting_cities_settings'))
				<a href="{{ url('admin/settings/starting-cities') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/starting-cities') || (Route::current()->uri() == 'admin/settings/add-starting-cities') || (Route::current()->uri() == 'admin/settings/edit-starting-cities/{id}') ? 'active' : '' }}">
					<i class="fa fa-building me-2"></i>Starting Cities
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_property_type'))
				<a href="{{ url('admin/settings/property-type') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/property-type' || Route::current()->uri() == 'admin/settings/add-property-type' || Route::current()->uri() == 'admin/settings/edit-property-type/{id}') ? 'active' : '' }}">
					<i class="fa fa-home me-2"></i>Property Type
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'space_type_setting'))
				<a href="{{ url('admin/settings/space-type') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/space-type' || Route::current()->uri() == 'admin/settings/add-space-type' || Route::current()->uri() == 'admin/settings/edit-space-type/{id}') ? 'active' : '' }}">
					<i class="fa fa-th-large me-2"></i>Space Type
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_bed_type'))
				<a href="{{ url('admin/settings/bed-type') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/bed-type' || Route::current()->uri() == 'admin/settings/add-bed-type' || Route::current()->uri() == 'admin/settings/edit-bed-type/{id}') ? 'active' : '' }}">
					<i class="fa fa-bed me-2"></i>Bed Type
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_currency'))
				<a href="{{ url('admin/settings/currency') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/currency' || Route::current()->uri() == 'admin/settings/add-currency' || Route::current()->uri() == 'admin/settings/edit-currency/{id}') ? 'active' : '' }}">
					<i class="fa fa-money me-2"></i>Currency
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_country'))
				<a href="{{ url('admin/settings/country') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/country' || Route::current()->uri() == 'admin/settings/add-country' || Route::current()->uri() == 'admin/settings/edit-country/{id}') ? 'active' : '' }}">
					<i class="fa fa-globe me-2"></i>Country
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_amenities_type'))
				<a href="{{ url('admin/settings/amenities-type') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/amenities-type' || Route::current()->uri() == 'admin/settings/add-amenities-type' || Route::current()->uri() == 'admin/settings/edit-amenities-type/{id}') ? 'active' : '' }}">
					<i class="fa fa-list me-2"></i>Amenities Type
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'email_settings'))
				<a href="{{ url('admin/settings/email') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/email') ? 'active' : '' }}">
					<i class="fa fa-envelope me-2"></i>Email Settings
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_fees'))
				<a href="{{ url('admin/settings/fees') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/fees') ? 'active' : '' }}">
					<i class="fa fa-percent me-2"></i>Fees
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_language'))
				<a href="{{ url('admin/settings/language') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/language' || Route::current()->uri() == 'admin/settings/add-language' || Route::current()->uri() == 'admin/settings/edit-language/{id}') ? 'active' : '' }}">
					<i class="fa fa-language me-2"></i>Language
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_metas'))
				<a href="{{ url('admin/settings/metas') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/metas' || Route::current()->uri() == 'admin/settings/edit_meta/{id}') ? 'active' : '' }}">
					<i class="fa fa-tags me-2"></i>Metas
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'api_informations'))
				<a href="{{ url('admin/settings/api-informations') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/api-informations') ? 'active' : '' }}">
					<i class="fa fa-key me-2"></i>Api Credentials
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'social_links'))
				<a href="{{ url('admin/settings/social-links') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/social-links') ? 'active' : '' }}">
					<i class="fa fa-share-alt me-2"></i>Social Links
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'social_logins'))
				<a href="{{ url('admin/settings/social-logins') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/social-logins') ? 'active' : '' }}">
					<i class="fa fa-sign-in me-2"></i>Social Logins
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_roles'))
				<a href="{{ url('admin/settings/roles') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/roles' || Route::current()->uri() == 'admin/permissions' || Route::current()->uri() == 'admin/settings/add-role' || Route::current()->uri() == 'admin/settings/edit-role/{id}') ? 'active' : '' }}">
					<i class="fa fa-users me-2"></i>Roles & Permissions
				</a>
			@endif

			@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'database_backup'))
				<a href="{{ url('admin/settings/backup') }}" class="list-group-item list-group-item-action {{ (Route::current()->uri() == 'admin/settings/backup') ? 'active' : '' }}">
					<i class="fa fa-database me-2"></i>Database Backups
				</a>
			@endif
		</div>
	</div>
</div>
