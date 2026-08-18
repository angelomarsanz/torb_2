@php 
	$breadcrumb = Route::current()->uri();
	$breadcrumbs = array(
						'admin/dashboard' => array( 'admin/dashboard' => 'Dashboard' ),
						'admin/profile' => array( 'admin/profile' => 'Profile' ),
						'admin/users' => array( 'admin/users' => 'Users' ),
						'admin/add_user' => array( 'admin/users' => 'Users', 'admin/add_user' => 'Add User'),
						'admin/edit_user' => array( 'admin/users' => 'Users', 'admin/edit_user' => 'Edit User'),
						'admin/addons' => array( 'admin/addons' => 'Addons'),
						'admin/payouts' => array( 'admin/payouts' => 'Payouts' ),
						'admin/payouts/edit/{id}' => array( 'admin/payouts' => 'Payouts', 'admin/payouts/edit/{id}' => 'Edit Payout' ),
						'admin/payouts/details/{id}' => array( 'admin/payouts' => 'Payouts', 'admin/payouts/details/{id}' => 'Payout Details' ),
						'admin/testimonials' => array( 'admin/testimonials' => 'Testimonials' ),
						'admin/add-testimonials' => array( 'admin/testimonials' => 'Testimonials', 'admin/add-testimonials' => 'Add Testimonial' ),
						'admin/edit-testimonials/{id}' => array( 'admin/testimonials' => 'Testimonials', 'admin/edit-testimonials/{id}' => 'Edit Testimonial' ),
						'admin/messages' => array( 'admin/messages' => 'Messages' ),
						'admin/sales-report' => array( 'admin/sales-report' => 'Sales Report' ),
						'admin/sales-analysis' => array( 'admin/sales-analysis' => 'Sales Analysis' ),
						'admin/overview-stats' => array( 'admin/overview-stats' => 'Overview & Statistics' ),
						'admin/display_report/{id}' => array( 'admin/display_report/{id}' => 'Report Details' ),
					);
	
	$breadcrumb = isset($breadcrumbs[$breadcrumb]) ? $breadcrumbs[$breadcrumb] : '';
@endphp

<div class="app-content-header pb-1" style="padding-top: 1.5rem;">
	<div class="container-fluid">
		<div class="row align-items-center">
			<div class="col-sm-12">
				<ol class="breadcrumb m-0" style="background: transparent; padding: 0; font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">
					<li class="breadcrumb-item">
						<a href="{{ url('admin/dashboard') }}" style="color: #3b82f6; text-decoration: none;">DASHBOARD</a>
					</li>
					@if (is_array($breadcrumb))
						@php $i = 1; $cnt = count($breadcrumb); @endphp
						@foreach ($breadcrumb as $key => $value)
							@if ($cnt == $i)
								<li class="breadcrumb-item active" aria-current="page" style="color: #6c757d; text-transform: uppercase;">{{ strtoupper(str_replace('-', ' ', $value)) }}</li>
							@else
								<li class="breadcrumb-item"><a href="{{ url($key) }}" style="color: #3b82f6; text-decoration: none; text-transform: uppercase;">{{ strtoupper(str_replace('-', ' ', $value)) }}</a></li>
							@endif
							@php $i++; @endphp
						@endforeach
					@endif
				</ol>
			</div>
		</div>
	</div>
</div>
