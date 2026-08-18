<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
	<div class="sidebar-brand">
		<a href="{{ url('admin/dashboard') }}" class="brand-link">
			<span class="brand-text fw-light">{{ siteName() }}</span>
		</a>
	</div>
	<div class="sidebar-wrapper">
		<nav class="mt-2">
			<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="{{ url('admin/dashboard') }}" class="nav-link {{ (Route::current()->uri() == 'admin/dashboard') ? 'active' : '' }}">
						<i class="nav-icon fa fa-tachometer"></i>
						<p>Dashboard</p>
					</a>
				</li>
				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'customers'))
				<li class="nav-item">
					<a href="{{ url('admin/customers') }}" class="nav-link {{ (Route::current()->uri() == 'admin/customers') || (Route::current()->uri() == 'admin/add-customer') || (Route::current()->uri() == 'admin/edit-customer/{id}') || (Route::current()->uri() == 'admin/customer/properties/{id}')  || (Route::current()->uri() == 'admin/customer/bookings/{id}') || (Route::current()->uri() == 'admin/customer/payouts/{id}')  || (Route::current()->uri() == 'admin/customer/payment-methods/{id}') || (Route::current()->uri() == 'admin/customer/wallet/{id}')  ? 'active' : '' }}">
						<i class="nav-icon fa fa-users"></i>
						<p>Customers</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'properties'))
				<li class="nav-item">
					<a href="{{ url('admin/properties') }}" class="nav-link {{ (Route::current()->uri() == 'admin/properties') || (Route::current()->uri() == 'admin/add-properties') || (Route::current()->uri() == 'admin/listing/{id}/{step}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-home"></i>
						<p>Properties</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_bookings'))
				<li class="nav-item">
					<a href="{{ url('admin/bookings') }}" class="nav-link {{ (Route::current()->uri() == 'admin/bookings') || (Route::current()->uri() == 'admin/bookings/detail/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-shopping-cart"></i>
						<p>Bookings</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'view_payouts'))
				<li class="nav-item">
					<a href="{{ url('admin/payouts') }}" class="nav-link {{ (Route::current()->uri() == 'admin/payouts') || (Route::current()->uri() == 'admin/payouts/details/{id}') || (Route::current()->uri() == 'admin/payouts/edit/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-paypal"></i>
						<p>Payouts</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_amenities'))
				<li class="nav-item">
					<a href="{{ url('admin/amenities') }}" class="nav-link {{ (Route::current()->uri() == 'admin/amenities') || (Route::current()->uri() == 'admin/add-amenities') || (Route::current()->uri() == 'admin/edit-amenities/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-bullseye"></i>
						<p>Amenities</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_pages'))
				<li class="nav-item">
					<a href="{{ url('admin/pages') }}" class="nav-link {{ (Route::current()->uri() == 'admin/pages') || (Route::current()->uri() == 'admin/add-page') || (Route::current()->uri() == 'admin/edit-page/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-newspaper-o"></i>
						<p>Static Pages</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'addons'))
				<li class="nav-item">
					<a href="{{ url('admin/addons') }}" class="nav-link {{ (Route::current()->uri() == 'admin/addons') ? 'active' : '' }}">
						<i class="nav-icon fa fa-puzzle-piece"></i>
						<p>Addons</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_reviews'))
				<li class="nav-item">
					<a href="{{ url('admin/reviews') }}" class="nav-link {{ (Route::current()->uri() == 'admin/reviews') || (Route::current()->uri() == 'admin/edit_review/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-eye"></i>
						<p>Manage Reviews</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_testimonial'))
				<li class="nav-item">
					<a href="{{ url('admin/testimonials') }}" class="nav-link {{ (Route::current()->uri() == 'admin/testimonials') || (Route::current()->uri() == 'admin/edit-testimonials/{id}') || (Route::current()->uri() == 'admin/add-testimonials') ? 'active' : '' }}">
						<i class="nav-icon fa fa-quote-left"></i>
						<p>Testimonials</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_admin'))
				<li class="nav-item">
					<a href="{{ url('admin/admin-users') }}" class="nav-link {{ (Route::current()->uri() == 'admin/admin-users') || (Route::current()->uri() == 'admin/add-admin') || (Route::current()->uri() == 'admin/edit-admin/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-user-plus"></i>
						<p>Users</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_messages'))
				<li class="nav-item">
					<a href="{{ url('admin/messages') }}" class="nav-link {{ (Route::current()->uri() == 'admin/messages') || (Route::current()->uri() == 'admin/messaging/host/{id}') || (Route::current()->uri() == 'admin/send-message-email/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-comments"></i>
						<p>Messages</p>
					</a>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'view_reports'))
				<li class="nav-item {{ (Route::current()->uri() == 'admin/sales-report' || Route::current()->uri() == 'admin/sales-analysis' || Route::current()->uri() == 'admin/overview-stats') ? 'menu-open' : '' }}">
					<a href="javascript:void(0)" class="nav-link {{ (Route::current()->uri() == 'admin/sales-report' || Route::current()->uri() == 'admin/sales-analysis' || Route::current()->uri() == 'admin/overview-stats') ? 'active' : '' }}">
						<i class="nav-icon fa fa-bar-chart"></i>
					<p>
						Reports
						<i class="nav-arrow bi bi-chevron-right"></i>
					</p>
					</a>
					<ul class="nav nav-treeview">
						<li class="nav-item">
							<a href="{{ url('admin/overview-stats') }}" class="nav-link {{ (Route::current()->uri() == 'admin/overview-stats') ? 'active' : '' }}">
							<i class="nav-icon bi bi-circle"></i>
							<p>Overview & Stats</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('admin/sales-report') }}" class="nav-link {{ (Route::current()->uri() == 'admin/sales-report') ? 'active' : '' }}">
							<i class="nav-icon bi bi-circle"></i>
							<p>Sales Report</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{ url('admin/sales-analysis') }}" class="nav-link {{ (Route::current()->uri() == 'admin/sales-analysis') ? 'active' : '' }}">
							<i class="nav-icon bi bi-circle"></i>
							<p>Sales Analysis</p>
							</a>
						</li>
					</ul>
				</li>
				@endif

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_email_template'))
				<li class="nav-item">
					<a href="{{ url('admin/email-template/1') }}" class="nav-link {{ (Route::current()->uri() == 'admin/email-template/{id}') ? 'active' : '' }}">
						<i class="nav-icon fa fa-envelope-o"></i>
						<p>Email Templates</p>
					</a>
				</li>
				@endif

				<li class="nav-item">
					<a href="{{ url('admin/cache-clear') }}" class="nav-link {{ (Route::current()->uri() == 'admin/cache-clear') ? 'active' : '' }}">
						<i class="nav-icon fa fa-trash-o"></i>
						<p>Cache Clear</p>
					</a>
				</li>

				@if (Helpers::has_permission(Auth::guard('admin')->user()->id, 'general_setting'))
				<li class="nav-item">
					<a href="{{ url('admin/settings') }}" class="nav-link {{ (Request::segment(2) == 'settings') ? 'active' : '' }}">
						<i class="nav-icon fa fa-cog"></i>
						<p>Settings</p>
					</a>
				</li>
				@endif
			</ul>
		</nav>
	</div>
</aside>
