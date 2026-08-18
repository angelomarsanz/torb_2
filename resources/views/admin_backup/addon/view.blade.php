@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Addons</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
							<h3 class="card-title mb-0">Addons Management</h3>
						</div>
						<div class="card-body">
							@include('addons::index')
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
