(function () {
    'use strict';

    function autoDismissToast(el, delay) {
        if (!el || el.classList.contains('d-none')) return;
        setTimeout(function () {
            el.classList.remove('show');
            setTimeout(function() {
                el.remove();
            }, 300); // Wait for Bootstrap fade out
        }, delay || 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var wrapper = document.getElementById('flashToastWrapper');
        if (!wrapper) return;

        var toasts = wrapper.querySelectorAll('.alert:not(.d-none)');
        toasts.forEach(function (toast) {
            autoDismissToast(toast, 5000);
        });
    });

    window.showFlashToast = function (type, message, delay) {
        var wrapper = document.getElementById('flashToastWrapper');
        if (!wrapper) return;

        var icons = {
            success: 'fa-check-circle',
            danger: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        var bgClass = 'info';
        if (type === 'success') bgClass = 'success';
        else if (type === 'danger') bgClass = 'danger';
        else if (type === 'warning') bgClass = 'warning text-dark';

        var toast = document.createElement('div');
        toast.className = 'alert bg-' + bgClass + ' text-white alert-dismissible fade show d-flex align-items-center m-0 bs-toast';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('style', 'pointer-events: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; border-radius: 6px; padding: 0.8rem 1.25rem;');
        toast.innerHTML =
            '<i class="fa ' + (icons[type] || icons.info) + ' me-2" style="font-size: 1.1rem;"></i>' +
            '<div style="font-size: 0.95rem; font-weight: 500;">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="this.closest(\'.alert\').remove()" style="font-size: 0.65rem; top: 50%; transform: translateY(-50%); padding: 0.5rem 1rem; margin-top: 0;"></button>';

        wrapper.appendChild(toast);
        autoDismissToast(toast, delay || 5000);
    };
})();
