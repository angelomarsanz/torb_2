<div class="card card-outline card-info shadow-sm">
	<div class="card-header">
		<h3 class="card-title"><i class="fa fa-envelope me-2"></i>Email Templates</h3>
	</div>
	<div class="card-body p-0">
		<div class="list-group list-group-flush f-14">
			@if(Helpers::has_permission(Auth::guard('admin')->user()->id, 'manage_email_template'))
				<a href="{{ url('admin/email-template/1') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-1' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Account Info Default Update
				</a>
				<a href="{{ url('admin/email-template/2') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-2' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Account Info Update
				</a>
				<a href="{{ url('admin/email-template/3') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-3' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Account Info Delete
				</a>
				<a href="{{ url('admin/email-template/4') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-4' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Booking
				</a>
				<a href="{{ url('admin/email-template/5') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-5' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Email Confirm
				</a>
				<a href="{{ url('admin/email-template/6') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-6' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Forget Password
				</a>
				<a href="{{ url('admin/email-template/7') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-7' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Need Payment Account
				</a>
				<a href="{{ url('admin/email-template/8') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-8' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Payout Sent
				</a>
				<a href="{{ url('admin/email-template/9') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-9' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Booking Cancelled
				</a>
				<a href="{{ url('admin/email-template/10') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-10' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Booking Accepted/Declined
				</a>
				<a href="{{ url('admin/email-template/11') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-11' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Booking Request Send
				</a>
				<a href="{{ url('admin/email-template/12') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-12' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Booking Confirmation
				</a>
				<a href="{{ url('admin/email-template/13') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-13' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Property Booking Notify
				</a>
				<a href="{{ url('admin/email-template/14') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-14' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Property Booking Payment
				</a>
				<a href="{{ url('admin/email-template/15') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-15' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Payout Request Received
				</a>
				<a href="{{ url('admin/email-template/16') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-16' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Property Listing Approve
				</a>
				<a href="{{ url('admin/email-template/17') }}" class="list-group-item list-group-item-action {{ isset($list_menu) && $list_menu == 'menu-17' ? 'active' : '' }}">
					<i class="fa fa-file-text me-2"></i>Payout Request Approved
				</a>
			@endif
		</div>
	</div>
</div>
