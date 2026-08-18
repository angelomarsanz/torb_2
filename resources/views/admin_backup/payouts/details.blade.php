@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Payout Details</h1>
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
				<div class="col-md-8 offset-md-2">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
							<h3 class="card-title mb-0">Payout Details</h3>
						</div>
						<div class="card-body">
							<table class="table table-borderless table-sm f-14 mb-0">
								<tbody>
									<tr>
										<th class="text-muted" style="width: 40%;">User name</th>
										<td>{{ $withDrawal->user->full_name }}</td>
									</tr>
									<tr>
										<th class="text-muted">Payment Method</th>
										<td>{{ $withDrawal->payment_methods->name }}</td>
									</tr>
									<tr>
										<th class="text-muted">Payout Amount</th>
										<td>{!! $withDrawal->currency->symbol !!} {{ $withDrawal->amount }}</td>
									</tr>
									<tr>
										<th class="text-muted">Status</th>
										<td><span class="badge bg-{{ $withDrawal->status == 'Success' ? 'success' : 'warning' }}">{{ $withDrawal->status }}</span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="card-footer">
							<a class="btn btn-outline-secondary f-14" href="{{ url('admin/payouts') }}"><i class="fa fa-arrow-left me-1"></i> Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
