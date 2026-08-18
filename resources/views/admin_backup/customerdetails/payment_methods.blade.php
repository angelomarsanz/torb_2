@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Payment Methods</h1>
				</div>
				<div class="col-sm-6">
					@include('admin.common.breadcrumb')
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="container-fluid">
			@include('admin.customerdetails.customer_menu')

			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-info shadow-sm">
						<div class="card-header bg-info text-white">
							<h3 class="card-title mb-0">Payment Methods</h3>
						</div>
						<div class="card-body p-0">
							<div class="table-responsive parent-table f-14 p-3">
								<table class="table table-striped table-hover mb-0 w-100" id="payout_methods">
									@if (count($payouts))
										<thead>
											<tr>
												<th>Methods</th>
												<th>Details/Account</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($payouts as $row)
												<tr>
													<td>
														{{ $row->payment_methods->name }}
														@if ($row->selected == 'Yes')
															<span class="badge bg-info ms-1">Default</span>
														@endif
													</td>
													<td>{{ $row->account }} ({{ $row->currency_code }})</td>
													<td><span class="badge bg-success">Ready</span></td>
												</tr>
											@endforeach
										</tbody>
									@else
										<tbody>
											<tr>
												<td colspan="3" class="text-center text-muted py-4">No data available</td>
											</tr>
										</tbody>
									@endif
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection