<div class="card card-outline card-info shadow-sm">
	<div class="card-header">
		<h3 class="card-title"><i class="fa fa-home me-2"></i>Property Settings</h3>
	</div>
	<div class="card-body p-0">
		<?php $requestUri = request()->segment(4); ?>
		<div class="list-group list-group-flush f-14">
			<a href='{{ url("admin/listing/$result->id/basics") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'basics') ? 'active' : '' }}">
				<i class="fa fa-list-ul me-2"></i>Basics
			</a>
			<a href='{{ url("admin/listing/$result->id/description") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'description' || $requestUri == 'details') ? 'active' : '' }}">
				<i class="fa fa-file-text-o me-2"></i>Description
			</a>
			<a href='{{ url("admin/listing/$result->id/location") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'location') ? 'active' : '' }}">
				<i class="fa fa-map-marker me-2"></i>Location
			</a>
			<a href='{{ url("admin/listing/$result->id/amenities") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'amenities') ? 'active' : '' }}">
				<i class="fa fa-bullseye me-2"></i>Amenities
			</a>
			<a href='{{ url("admin/listing/$result->id/photos") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'photos') ? 'active' : '' }}">
				<i class="fa fa-camera me-2"></i>Photos
			</a>
			<a href='{{ url("admin/listing/$result->id/pricing") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'pricing') ? 'active' : '' }}">
				<i class="fa fa-money me-2"></i>Pricing
			</a>
			<a href='{{ url("admin/listing/$result->id/booking") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'booking') ? 'active' : '' }}">
				<i class="fa fa-shopping-cart me-2"></i>Booking
			</a>
			<a href='{{ url("admin/listing/$result->id/calender") }}' class="list-group-item list-group-item-action {{ ($requestUri == 'calender') ? 'active' : '' }}">
				<i class="fa fa-calendar me-2"></i>Calendar
			</a>
		</div>
	</div>
</div>
