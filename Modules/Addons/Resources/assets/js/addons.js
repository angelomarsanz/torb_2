"use strict";

$(document).ready(function () {

    let httpRequest;

    const insTable = document.getElementById('addons-ins-table-container');
    const avlTable = document.getElementById('addons-avl-table-container');
    const uploadPanel = document.getElementById('addon-upload-panel');
    const installBtn = document.getElementById('addon-install-btn');
    const listContent = document.getElementById('addon-list-content');
    const cancelBtn = document.getElementById('cancel-addform');

    // 1. Upload Form Toggle
    if (installBtn) {
        installBtn.onclick = function () {
            const isVisible = $(uploadPanel).is(':visible');
            if (isVisible) {
                $(uploadPanel).slideUp(350);
            } else {
                $(uploadPanel).slideDown(350);
            }
        };
    }

    if (cancelBtn) {
        cancelBtn.onclick = function () {
            $(uploadPanel).slideUp(350);
        };
    }

    // 2. Tab Switching Logic
    $('.addon-tab-btn').on('click', function () {
        $('.addon-tab-btn').removeClass('active');
        $(this).addClass('active');

        if (this.id === 'ins-addon-tab') {
            $(insTable).removeClass('addons-hide');
            $(avlTable).addClass('addons-hide');
        } else {
            $(avlTable).removeClass('addons-hide');
            $(insTable).addClass('addons-hide');
        }
    });

    // 3. Search Logic
    $('.search-box').on('keyup', function () {
        var filter = $(this).val().toUpperCase();
        var activeTable = $('#addons-avl-table-container').hasClass('addons-hide') ? insTable : avlTable;
        var tr = $(activeTable).find("tbody tr");

        tr.each(function () {
            var nameTd = $(this).find(".addon-info-name");
            if (nameTd.length) {
                var txtValue = nameTd.text();
                $(this).toggle(txtValue.toUpperCase().indexOf(filter) > -1);
            }
        });
    });

    // 4. Modal Logic
    if (window.XMLHttpRequest) {
        httpRequest = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // Bind modal triggers
    function bindModalTriggers() {
        $(document).on('click', '.addon-modal-trigger', function () {
            clearForm();
            addonModalToggle();
            setFormName(this.dataset.name);
            getAddonFormData(this.dataset.url);
        });
    }
    bindModalTriggers();

    // Modal close (Head Close Button)
    $(document).on('click', '.addon-modal-close', function () {
        addonModalToggle();
    });

    // Modal close (Footer Cancel Button - Dynamically Loaded)
    $(document).on('click', '.addon-modal-cancel', function () {
        addonModalToggle();
    });

    // Close modal on backdrop click
    $(document).on('click', '.addon-modal-window', function (e) {
        if ($(e.target).is('.addon-modal-window')) {
            addonModalToggle();
        }
    });

    function getAddonFormData(url) {
        if (!url || url === '#') return;
        toggleAddonLoading();
        httpRequest.onreadystatechange = handleResponse;
        httpRequest.open('GET', url, true);
        httpRequest.send();
    }

    function addonModalToggle() {
        const modal = document.querySelector('.addon-modal-window');
        if (modal) modal.classList.toggle('addon-modal-hidden');
    }

    function handleResponse() {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            toggleAddonLoading();
            if (httpRequest.status === 200) {
                let response = JSON.parse(httpRequest.responseText);
                if (response.status) {
                    generateForm(response.html);
                }
            } else {
                alert('There was a problem with the request.');
                addonModalToggle();
            }
        }
    }

    function generateForm(html) {
        var formDiv = document.querySelector('.modal-form-data .form');
        if (formDiv) formDiv.innerHTML = html;
    }

    function setFormName(name) {
        const title = document.querySelector('.addon-modal-title');
        if (title) title.innerHTML = name;
    }

    function toggleAddonLoading() {
        const loader = document.querySelector('.addon-form-loading');
        if (loader) loader.classList.toggle('addon-modal-dnone');
    }

    function clearForm() {
        const formDiv = document.querySelector('.modal-form-data .form');
        if (formDiv) formDiv.innerHTML = '';
        setFormName('');
    }

    // 5. Custom File Upload UI Interaction
    const addonInput = document.getElementById('addon-module');
    const uploadArea = document.querySelector('.addon-upload-area');
    const fileText = document.getElementById('addon-file-text');

    if (addonInput && uploadArea) {
        addonInput.onchange = function () {
            if (this.files && this.files.length > 0) {
                const fileName = this.files[0].name;
                if (fileText) fileText.innerHTML = fileName;
                uploadArea.classList.add('file-selected');
            } else {
                if (fileText) fileText.innerHTML = "Click to select .zip file";
                uploadArea.classList.remove('file-selected');
            }
        };
    }

}); // end document.ready
