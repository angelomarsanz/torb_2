<h1 class="customer-profile-name mt-0 mb-4">{{ $user?->first_name . " " . $user?->last_name }}</h1>

<div class="settings-nav-card shadow-none">
    <ul class="settings-nav-list" role="tablist">
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($customer_edit_tab) && $customer_edit_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/edit-customer/" . $user?->id) }}'>
                Edit Customer
            </a>
        </li>
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($properties_tab) && $properties_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/properties/" . $user?->id) }}'>
                Properties
            </a>
        </li>
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($bookings_tab) && $bookings_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/bookings/" . $user?->id) }}'>
                Bookings
            </a>
        </li>
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($payouts_tab) && $payouts_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/payouts/" . $user?->id) }}'>
                Payouts
            </a>
        </li>
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($payment_methods_tab) && $payment_methods_tab == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/payment-methods/" . $user?->id) }}'>
                Payment Methods
            </a>
        </li>
        <li class="settings-nav-item">
            <a class="settings-nav-link {{ isset($wallet) && $wallet == 'active' ? 'active' : '' }}" href='{{ url("admin/customer/wallet/" . $user?->id) }}'>
                Wallet
            </a>
        </li>
    </ul>
</div>

