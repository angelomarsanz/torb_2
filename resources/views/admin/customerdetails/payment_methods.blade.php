@extends('admin.template')

@section('main')
<div class="content-wrapper dashboard-page-wrapper" style="overflow-x:hidden;">
	<div class="dashboard-content-inner">
		<section class="content mb-5">
			<div class="container-fluid px-0">
				<div class="row">
					<div class="col-12 mt-0">
						@include('admin.customerdetails.customer_menu')
						<div class="settings-form-card">
							<div class="settings-form-header">
								<div class="d-flex align-items-center gap-3">
									<div class="settings-form-icon">
										<i class="fa fa-credit-card"></i>
									</div>
									<div>
										<h4 class="settings-form-title">Payment Methods</h4>
										<p class="settings-form-subtitle">Manage payout methods and bank account details for this customer</p>
									</div>
								</div>
							</div>
							<div class="settings-form-body p-0">
								<div class="table-responsive parent-table f-14 p-4">
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
															<span class="fw-bold">{{ $row->payment_methods->name }}</span>
															@if ($row->selected == 'Yes')
																<span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-2 px-2 py-1">Default</span>
															@endif
														</td>
														<td class="text-muted">{{ $row->account }} <span class="badge bg-light text-dark border ms-1">{{ $row->currency_code }}</span></td>
														<td><span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span></td>
													</tr>
												@endforeach
											</tbody>
										@else
											<tbody>
												<tr>
													<td colspan="3" class="text-center text-muted py-5">
														<i class="fa fa-info-circle d-block mb-2 f-24 opacity-25"></i>
														No payment methods found for this customer
													</td>
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
</div>

@endsection
