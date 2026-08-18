@extends('admin.template')
@section('main')
<div class="content-wrapper" style="overflow-x:hidden;">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">{{ $page_title ?? 'Report' }} <small class="text-muted fw-normal">{{ $page_subtitle ?? '' }}</small></h1>
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
						<div class="card-header bg-info text-white">
							<h3 class="card-title mb-0">Report Details</h3>
						</div>
						<form id="report_form" method="post" action="{{ url('admin/display_report/' . $result->id) }}" class="form-horizontal">
							{{ csrf_field() }}
							<div class="card-body">
								<p class="mb-4">{{ $result->message }}</p>
								<div class="row mb-3">
									<label for="report_status" class="col-sm-2 col-form-label">Status</label>
									<div class="col-sm-6">
										<select class="form-select" id="report_status" name="status">
											<option value="unsolved" {{ $result->status == 'unsolved' ? 'selected' : '' }}>Unsolved</option>
											<option value="solved" {{ $result->status == 'solved' ? 'selected' : '' }}>Solved</option>
										</select>
										<span class="text-danger">{{ $errors->first('status') }}</span>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6 offset-sm-2">
										<button type="submit" class="btn btn-info">Submit</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
