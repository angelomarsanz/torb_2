<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Addons/Resources/assets/css/addon.css') }}">

@php
    $addons = \Modules\Addons\Entities\Addon::all();
    $numberOfAddons = count(
        array_filter($addons, function ($addon) {
            return !$addon->get('core');
        }),
    );
@endphp

{{-- Flash Alert --}}
@if (session('AddonMessage'))
    <div class="alert alert-{{ session('AddonStatus') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-4"
        role="alert">
        <i class="fa fa-{{ session('AddonStatus') == 'success' ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
        <strong>{{ session('AddonMessage') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div id="addons-normal-view">
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">

        {{-- Card Header --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom"
            style="background: #fff;">
            <div>
                <h5 class="mb-0 fw-semibold" style="font-size: 1rem; color: #1e293b;">{{ __('Addons') }}</h5>
                <small class="text-muted"
                    style="font-size: 0.8rem;">{{ __('Manage your installed and available addons') }}</small>
            </div>
            <div id="addon-upload-trigger-wrap">
                <button id="addon-install-btn" class="btn settings-btn-save h-42 px-4">
                    {{ __('Upload') }}
                </button>
            </div>
        </div>

        {{-- Upload Form Panel --}}
        <div id="addon-upload-panel">
            <div class="p-4">
                <div class="addon-wb-compact-form ms-auto" style="max-width: 560px;">
                    <form id="addons-form-container" action="{{ route('addon.upload') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Purchase Code --}}
                        <div class="settings-field-row mb-3">
                            <label class="settings-field-label d-block mb-2">{{ __('Purchase Code') }} <span
                                    class="text-danger">*</span></label>
                            <div class="settings-field-input">
                                <input type="text" class="form-control settings-input"
                                    placeholder="{{ __('Enter your purchase code') }}" name="purchase_code" required>
                            </div>
                        </div>

                        {{-- Upload Zip File --}}
                        <div class="settings-field-row mb-3">
                            <label class="settings-field-label d-block mb-2">{{ __('Addon File') }} <span
                                    class="text-danger">*</span></label>
                            <div class="settings-field-input">
                                <div class="addon-upload-area"
                                    onclick="document.getElementById('addon-module').click();">
                                    <div id="addon-file-placeholder" class="addon-upload-placeholder">
                                        <i class="fa fa-cloud-upload addon-upload-icon"></i>
                                        <span id="addon-file-text">{{ __('Click to select .zip file') }}</span>
                                    </div>
                                    <input id="addon-module" type="file" name="attachment" accept=".zip,.rar,.7zip"
                                        required style="display: none;">
                                </div>
                                <p class="text-muted mt-2 mb-0 d-flex align-items-center" style="font-size: 0.8rem;">
                                    <i
                                        class="fa fa-info-circle me-1 text-primary"></i>{{ __('Ensure you are uploading a valid addon zip file.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button id="cancel-addform" class="btn settings-btn-cancel"
                                type="button">{{ __('Cancel') }}</button>
                            <button class="btn settings-btn-save" type="submit">{{ __('Upload Now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- List Content --}}
        <div id="addon-list-content">

            {{-- Tab Bar + Search --}}
            <div id="a-tab-container" class="addon-tab-bar">
                <div class="addon-tab-nav">
                    <div id="ins-addon-tab" class="addon-tab-btn active">{{ __('Installed') }}</div>
                    <div id="avl-addon-tab" class="addon-tab-btn">{{ __('Available') }}</div>
                </div>
                <div class="addon-search-wrap position-relative">
                    <i class="fa fa-search addon-search-icon"></i>
                    <input type="text" class="addon-search-input search-box" placeholder="{{ __('Search addon...') }}">
                </div>
            </div>

            {{-- Installed Addons Table --}}
            <div id="addons-ins-table-container">
                @if ($numberOfAddons > 0)
                    <table class="addon-table">
                        <colgroup>
                            <col style="width: auto;">
                            <col style="width: 140px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('Addon') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addons as $addon)
                                @if ($addon->get('core'))
                                    @continue
                                @endif
                                <tr>
                                    <td class="ps-4">
                                        <div class="addon-cell">
                                            <div class="addon-logo-box">
                                                <img src="{{ addonThumbnail($addon->getName()) }}"
                                                    alt="{{ $addon->getName() }}">
                                            </div>
                                            <div>
                                                <div class="addon-info-name">
                                                    {{ Modules\Gateway\Entities\Gateway::where('alias', $addon->getLowerName())->first('name')->name ?? $addon->getName() }}
                                                    <span class="addon-version-inline">v1.0</span>
                                                </div>
                                                <div class="addon-info-actions">
                                                    <a href="{{ route('addon.switch-status', $addon->getAlias()) }}"
                                                        class="addon-action-link">
                                                        {{ $addon->isEnabled() ? __('Deactivate') : __('Activate') }}
                                                    </a>
                                                    @if (Config($addon->getLowerName() . '.options'))
                                                        @foreach (Config($addon->getLowerName() . '.options') as $option)
                                                            @php
                                                                $link = settingsModalLink($option);
                                                                $modal = settingModalStatus($option);
                                                            @endphp
                                                            <span class="addon-action-sep">|</span>
                                                            <a href="{{ $modal ? 'javascript:void(0)' : $link }}"
                                                                class="addon-action-link addon-modal-trigger"
                                                                data-name="{{ $addon->getName() }}" data-url="{{ $link }}"
                                                                target="{{ isset($option['target']) ? $option['target'] : '' }}">
                                                                {{ isset($option['label']) ? __($option['label']) : '' }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($addon->isEnabled())
                                            <span class="addon-badge addon-badge-active">{{ __('Active') }}</span>
                                        @else
                                            <span class="addon-badge addon-badge-inactive">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="addon-empty">
                        <div class="addon-empty-icon"><i class="fa fa-puzzle-piece"></i></div>
                        <h5>{{ __('No Addons Installed') }}</h5>
                        <p>{{ __('Click "Upload" to install your first addon.') }}</p>
                    </div>
                @endif
            </div>

            {{-- Available Addons Table --}}
            <div id="addons-avl-table-container" class="addons-hide">
                @if (count($available->addons))
                    <table class="addon-table" id="available-addon">
                        <colgroup>
                            <col style="width: auto;">
                            <col style="width: 110px;">
                            <col style="width: 160px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('Addon') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($available->addons as $addon)
                                @php $addon = miniCollection($addon) @endphp
                                @php $isInstalled = \Modules\Addons\Entities\Addon::find($addon->name); @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="addon-cell">
                                            <div class="addon-logo-box">
                                                <img src="{{ asset($addon->image[0]) }}" alt="{{ $addon->name }}">
                                            </div>
                                            <div>
                                                <div class="addon-info-name">{{ $addon->name }} <span
                                                        class="addon-version-inline">v1.0</span></div>
                                                <div class="mt-1">
                                                    <small class="text-muted"
                                                        style="font-size: 0.775rem;">{{ __('Requires v:x', ['x' => $addon->require_version]) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"
                                            style="color: #1e293b; font-size: 0.875rem;">{{ $addon->price }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($isInstalled)
                                            <span class="addon-badge addon-badge-installed">{{ __('Installed') }}</span>
                                        @else
                                            <a target="_blank" href="{{ $addon->url }}" class="btn settings-btn-save px-4"
                                                style="height: 36px; font-size: 0.8125rem; display: inline-flex; align-items: center;">
                                                {{ $addon->price_type == 'free' ? __('Download') : __('Buy Now') }}
                                            </a>
                                        @endif
                                    </td>
                                    {{-- Version moved inline --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="addon-empty">
                        <div class="addon-empty-icon"><i class="fa fa-th-large"></i></div>
                        <h5>{{ __('No Available Addons') }}</h5>
                        <p>{{ __('Check back later for new addons.') }}</p>
                    </div>
                @endif
            </div>

        </div>{{-- end #addon-list-content --}}
    </div>
</div>

{{-- Settings Modal --}}
<div class="addon-modal-window addon-modal-hidden">
    <div class="addon-modal-container">
        <div class="addon-modal-head">
            <h5 class="addon-modal-title"></h5>
            <button type="button" class="addon-modal-close"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-form-data">
            <div class="form"></div>
            <ul class="addon-form-loading addon-modal-dnone">
                <div id="addon-res-loader" class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
            </ul>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('Modules/Addons/Resources/assets/js/addons.js') }}"></script>