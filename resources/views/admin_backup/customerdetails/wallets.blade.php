@extends('admin.template')

@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Customer Wallet</h1>
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
							<h3 class="card-title mb-0">Wallet Transactions</h3>
						</div>
					<div class="card-body">
						<div class="table-responsive parent-table filters-parent f-14">
							{!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive w-100', 'cellspacing' => '0']) !!}
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@section('validate_script')
<script src="{{ asset('public/backend/plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/backend/plugins/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection