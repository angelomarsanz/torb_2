<footer class="app-footer">
	<div class="container-fluid">
		<div class="row align-items-center">
			<div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
				<span>Copyright &copy; 2016-{{ date('Y') }} <a href="javascript:void(0)">{{ siteName() }}</a>. All rights reserved.</span>
			</div>
			<div class="col-md-6 text-center text-md-end">
				<span class="footer-v">
					<i class="fa fa-code-fork me-1"></i> {{ __(appVersion()) }}
				</span>
			</div>
		</div>
	</div>
</footer>

<!-- Delete Confirmation Modal (Workbench Elite) -->
<div class="modal fade" id="delete-warning-modal" tabindex="-1" aria-labelledby="deleteWarningLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);">
            <div class="modal-body text-center p-5 pb-4">
                <div class="mb-4 d-flex justify-content-center">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 28px;">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2" style="font-size: 1.15rem; color: #1e293b;" id="deleteWarningLabel">Confirm Deletion</h4>
                <p class="text-muted mb-0" style="font-size: 0.875rem; line-height: 1.5;">Are you sure you want to delete this record?<br>This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-center gap-2" style="background: transparent;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f1f5f9; color: #475569; border-radius: 8px; padding: 0.5rem 1.25rem; font-size: 0.875rem; font-weight: 500; height: 42px; border: none; transition: all 0.2s;">{{ __('Cancel') }}</button>
                <a class="btn btn-danger" id="delete-modal-yes" href="javascript:void(0)" style="background: #ef4444; color: #fff; border-radius: 8px; padding: 0.5rem 1.25rem; font-size: 0.875rem; font-weight: 600; height: 42px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2); border: none; transition: all 0.2s;">{{ __('Yes, Delete') }}</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('public/backend/js/admin-footer.min.js') }}"></script>
@endpush
