<div class="settings-nav-card">
	<div class="settings-section-label px-3 pt-3 mb-2">
		<i class="fa fa-home"></i>
		<span>Property Settings</span>
	</div>
	<ul class="settings-nav-list flex-column">
		<?php $requestUri = request()->segment(4); ?>
		
		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/basics") }}' class="settings-nav-link {{ ($requestUri == 'basics') ? 'active' : '' }}">
				<i class="fa fa-list-ul me-2"></i>Basics
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/description") }}' class="settings-nav-link {{ ($requestUri == 'description' || $requestUri == 'details') ? 'active' : '' }}">
				<i class="fa fa-file-text-o me-2"></i>Description
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/location") }}' class="settings-nav-link {{ ($requestUri == 'location') ? 'active' : '' }}">
				<i class="fa fa-map-marker me-2"></i>Location
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/amenities") }}' class="settings-nav-link {{ ($requestUri == 'amenities') ? 'active' : '' }}">
				<i class="fa fa-bullseye me-2"></i>Amenities
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/photos") }}' class="settings-nav-link {{ ($requestUri == 'photos') ? 'active' : '' }}">
				<i class="fa fa-camera me-2"></i>Photos
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/pricing") }}' class="settings-nav-link {{ ($requestUri == 'pricing') ? 'active' : '' }}">
				<i class="fa fa-money me-2"></i>Pricing
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/booking") }}' class="settings-nav-link {{ ($requestUri == 'booking') ? 'active' : '' }}">
				<i class="fa fa-shopping-cart me-2"></i>Booking
			</a>
		</li>

		<li class="settings-nav-item w-100">
			<a href='{{ url("admin/listing/$result->id/calender") }}' class="settings-nav-link {{ ($requestUri == 'calender') ? 'active' : '' }}">
				<i class="fa fa-calendar me-2"></i>Calendar
			</a>
		</li>
	</ul>
</div>
