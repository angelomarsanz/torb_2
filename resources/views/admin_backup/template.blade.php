@include('admin.common.head')
<div class="app-wrapper">
	@include('admin.common.header')
	@include('admin.common.left_sidebar')
	<main class="app-main">
		<div class="flash-toast-wrapper" id="flashToastWrapper">
			@if (Session::has('message'))
				<div class="flash-toast flash-toast-{{ str_contains(Session::get('alert-class', ''), 'success') ? 'success' : (str_contains(Session::get('alert-class', ''), 'danger') ? 'danger' : (str_contains(Session::get('alert-class', ''), 'warning') ? 'warning' : 'info')) }}" role="alert">
					<div class="flash-toast-icon">
						@if(str_contains(Session::get('alert-class', ''), 'success'))
							<i class="fa fa-check-circle"></i>
						@elseif(str_contains(Session::get('alert-class', ''), 'danger'))
							<i class="fa fa-times-circle"></i>
						@elseif(str_contains(Session::get('alert-class', ''), 'warning'))
							<i class="fa fa-exclamation-triangle"></i>
						@else
							<i class="fa fa-info-circle"></i>
						@endif
					</div>
					<div class="flash-toast-body">{{ Session::get('message') }}</div>
					<button type="button" class="flash-toast-close" onclick="this.closest('.flash-toast').remove()">&times;</button>
					<div class="flash-toast-progress"></div>
				</div>
			@endif

			<div class="flash-toast flash-toast-success d-none" id="success_message_div" role="alert">
				<div class="flash-toast-icon"><i class="fa fa-check-circle"></i></div>
				<div class="flash-toast-body" id="success_message"></div>
				<button type="button" class="flash-toast-close" onclick="this.closest('.flash-toast').classList.add('d-none')">&times;</button>
				<div class="flash-toast-progress"></div>
			</div>

			<div class="flash-toast flash-toast-danger d-none" id="error_message_div" role="alert">
				<div class="flash-toast-icon"><i class="fa fa-times-circle"></i></div>
				<div class="flash-toast-body" id="error_message"></div>
				<button type="button" class="flash-toast-close" onclick="this.closest('.flash-toast').classList.add('d-none')">&times;</button>
				<div class="flash-toast-progress"></div>
			</div>
		</div>
		@yield('main')
		@include('admin.common.footer')
	</main>
</div>
@include('admin.common.foot')
@yield('validate_script')
