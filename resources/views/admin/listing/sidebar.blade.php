@php
	$steps = [
		['key' => 'basics',      'label' => __('Basics'),      'icon' => 'fa-home'],
		['key' => 'description', 'label' => __('Description'), 'icon' => 'fa-pencil'],
		['key' => 'location',    'label' => __('Location'),    'icon' => 'fa-map-marker'],
		['key' => 'amenities',   'label' => __('Amenities'),   'icon' => 'fa-wifi'],
		['key' => 'photos',      'label' => __('Photos'),      'icon' => 'fa-camera'],
		['key' => 'pricing',     'label' => __('Pricing'),     'icon' => 'fa-money'],
		['key' => 'booking',     'label' => __('Booking'),     'icon' => 'fa-calendar-check-o'],
		['key' => 'calendar',    'label' => __('Calender'),    'icon' => 'fa-calendar'],
	];
	$currentStep = Request::segment(3);
	$currentIndex = collect($steps)->search(fn($s) => $s['key'] === $currentStep);
	$hasStatus = $result->status != "";
@endphp

<div class="card listing-steps-card shadow-sm">
	<div class="card-header listing-steps-header">
		<div class="d-flex align-items-center gap-2">
			<div class="listing-steps-icon-wrap">
				<i class="fa fa-list-ol"></i>
			</div>
			<div>
				<h3 class="card-title mb-0 fw-bold">Listing Steps</h3>
				<small class="text-white-50">{{ $currentIndex !== false ? ($currentIndex + 1) : 0 }} of {{ count($steps) }} steps</small>
			</div>
		</div>
	</div>

	<div class="card-body p-0">
		<div class="listing-steps-progress">
			<div class="listing-steps-progress-bar" style="width: {{ $currentIndex !== false ? round((($currentIndex + 1) / count($steps)) * 100) : 0 }}%"></div>
		</div>

		<nav class="listing-steps-nav">
			@foreach ($steps as $i => $step)
				@php
					$isActive  = $currentStep === $step['key'];
					$isDone    = $currentIndex !== false && $i < $currentIndex;
					$href      = $hasStatus ? url("listing/" . $result->id . "/" . $step['key']) : '#';
					$stateClass = $isActive ? 'is-active' : ($isDone ? 'is-done' : 'is-pending');
				@endphp
				<a href="{{ $href }}" class="listing-step {{ $stateClass }}">
					<span class="listing-step-indicator">
						@if ($isDone)
							<i class="fa fa-check"></i>
						@else
							<span>{{ $i + 1 }}</span>
						@endif
					</span>
					<span class="listing-step-content">
						<span class="listing-step-label">{{ $step['label'] }}</span>
					</span>
					<i class="fa {{ $step['icon'] }} listing-step-trail-icon"></i>
				</a>
			@endforeach
		</nav>
	</div>
</div>
