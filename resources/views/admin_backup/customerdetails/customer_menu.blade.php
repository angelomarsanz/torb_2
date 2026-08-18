<div class="card shadow-sm mb-3">
	<div class="card-body py-2 px-3">
		<ul class="nav nav-tabs flex-nowrap overflow-auto f-14 border-0" role="tablist">
			<li class="nav-item">
				<a class="nav-link {{ isset($customer_edit_tab) && $customer_edit_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/edit-customer/" . $user?->id) }}'>Edit Customer</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {{ isset($properties_tab) && $properties_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/properties/" . $user?->id) }}'>Properties</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {{ isset($bookings_tab) && $bookings_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/bookings/" . $user?->id) }}'>Bookings</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {{ isset($payouts_tab) && $payouts_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/payouts/" . $user?->id) }}'>Payouts</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {{ isset($payment_methods_tab) && $payment_methods_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/payment-methods/" . $user?->id) }}'>Payment Methods</a>
			</li>
			<li class="nav-item">
				<a class="nav-link {{ isset($wallet) && $wallet == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/wallet/" . $user?->id) }}'>Wallet</a>
			</li>
		</ul>
	</div>
</div>
<h4 class="mb-3">{{ $user?->first_name . " " . $user?->last_name }}</h4>
