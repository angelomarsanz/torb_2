<nav class="app-header navbar navbar-expand bg-body">
	<div class="container-fluid">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" data-lte-toggle="sidebar" href="javascript:void(0)" role="button"><i class="fa fa-bars"></i></a>
			</li>
		</ul>
		<ul class="navbar-nav ms-auto">
			<li class="nav-item dropdown user-menu">
				<a href="javascript:void(0)" class="nav-link dropdown-toggle user-menu-trigger" data-bs-toggle="dropdown" aria-expanded="false">
					<img src="{{ Auth::guard('admin')->user()->profile_src }}" class="user-image rounded-circle shadow" alt="User Image">
					<span class="user-menu-name d-none d-md-inline">{{ ucfirst(Auth::guard('admin')->user()->username) }}</span>
					<i class="fa fa-chevron-down user-menu-chevron d-none d-md-inline ms-1"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end user-dropdown-menu">
					<li class="user-dropdown-header">
						<div class="user-dropdown-header-inner">
							
							<div class="user-dropdown-info">
								<div class="user-dropdown-name">{{ ucfirst(Auth::guard('admin')->user()->username) }}</div>
								<div class="user-dropdown-email">{{ Auth::guard('admin')->user()->email }}</div>
							</div>
						</div>
					</li>
					<li><hr class="dropdown-divider user-dropdown-divider"></li>
					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item" href="{{ url('admin/profile') }}">
							<i class="fa fa-user"></i> Your Profile
						</a>
					</li>
					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item" href="{{ url('admin/settings') }}">
							<i class="fa fa-cog"></i> Settings
						</a>
					</li>
					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item user-dropdown-signout" href="{{ url('admin/logout') }}">
							<i class="fa fa-sign-out"></i> Sign out
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</nav>
