<nav class="app-header navbar navbar-expand bg-body">
	<div class="container-fluid">
		<ul class="navbar-nav align-items-center">
			<li class="nav-item">
				<div class="header-toggle-bar">
					<a class="nav-link sidebar-toggle-link" data-lte-toggle="sidebar" href="javascript:void(0)" role="button" aria-label="Toggle sidebar"><i class="fa fa-bars"></i></a>
				</div>
			</li>
		</ul>
		<ul class="navbar-nav ms-auto">
			<li class="nav-item">
				<a href="{{ url('/') }}" target="_blank" class="nav-link visit-website-link" title="Visit Site">
					<i class="bi bi-globe"></i>
					<span class="d-none d-sm-inline">Visit Site</span>
				</a>
			</li>
			<li class="nav-item dropdown user-menu">
				<a href="javascript:void(0)" class="nav-link dropdown-toggle user-menu-trigger" data-bs-toggle="dropdown" aria-expanded="false">
					<img src="{{ Auth::guard('admin')->user()->profile_src }}" class="user-image rounded-circle shadow" alt="User Image">
					<div class="user-menu-info">
						<span class="user-menu-name">{{ ucfirst(Auth::guard('admin')->user()->username) }}</span>
						<span class="user-menu-email">{{ Auth::guard('admin')->user()->email }}</span>
					</div>
					<i class="fa fa-chevron-down user-menu-chevron d-none d-md-inline ms-1"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end user-dropdown-menu">

					{{-- Rich header with avatar --}}
					<li class="user-dropdown-header">
						<div class="user-dropdown-header-inner">
							<div class="user-dropdown-avatar-wrap">
								<img class="user-dropdown-avatar rounded-circle" src="{{ Auth::guard('admin')->user()->profile_src }}" alt="{{ Auth::guard('admin')->user()->username }}">
								<span class="user-dropdown-online-dot"></span>
							</div>
							<div class="user-dropdown-info">
								<div class="user-dropdown-name">{{ ucfirst(Auth::guard('admin')->user()->username) }}</div>
								<div class="user-dropdown-email">{{ Auth::guard('admin')->user()->email }}</div>
								<span class="user-dropdown-role-badge"><i class="fa fa-shield me-1"></i>Administrator</span>
							</div>
						</div>
					</li>

					<li><hr class="user-dropdown-divider"></li>

					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item" href="{{ url('admin/profile') }}">
							<span class="ud-icon-box ud-icon-box--blue"><i class="fa fa-user"></i></span>
							<span class="ud-label">Your Profile</span>
							<i class="fa fa-chevron-right ud-arrow"></i>
						</a>
					</li>
					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item" href="{{ url('admin/settings') }}">
							<span class="ud-icon-box ud-icon-box--slate"><i class="fa fa-cog"></i></span>
							<span class="ud-label">Settings</span>
							<i class="fa fa-chevron-right ud-arrow"></i>
						</a>
					</li>

					<li><hr class="user-dropdown-divider"></li>

					<li class="user-dropdown-item-wrap">
						<a class="dropdown-item user-dropdown-item user-dropdown-signout" href="{{ url('admin/logout') }}">
							<span class="ud-icon-box ud-icon-box--red"><i class="fa fa-sign-out"></i></span>
							<span class="ud-label">Sign out</span>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</nav>
