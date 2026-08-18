<footer class="app-footer f-14">
	<div class="float-end d-none d-md-inline-block">
		{{ __(appVersion()) }}
	</div>
	<strong>Copyright &copy; 2016-{{ date('Y') }} <a href="javascript:void(0)">{{ siteName() }}</a>.</strong> All rights reserved.
</footer>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete-warning-modal" tabindex="-1" aria-labelledby="deleteWarningLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title f-18" id="deleteWarningLabel">Confirm Delete</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body f-14">
				<p>You are about to delete one track, this procedure is irreversible.</p>
				<p>Do you want to proceed?</p>
			</div>
			<div class="modal-footer">
				<a class="btn btn-danger f-14" id="delete-modal-yes" href="javascript:void(0)">Yes</a>
				<button type="button" class="btn btn-outline-secondary f-14" data-bs-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script src="{{ asset('public/backend/js/admin-footer.min.js') }}"></script>
@endpush
