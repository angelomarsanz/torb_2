@include('admin.common.head')
<style>
    /* =====================================================
       ULTRASONIC HARD OVERRIDE: MOBILE FULL-WIDTH Bleed
       Targeting all admin forms and tables for edge-to-edge docking
       ===================================================== */
    @media (max-width: 768px) {
        body.layout-fixed .content-wrapper.dashboard-page-wrapper,

        body.layout-fixed .dashboard-content-inner,
        body.layout-fixed .content,
        body.layout-fixed .container-fluid,
        body.layout-fixed .row,
        body.layout-fixed .col-12,
        body.layout-fixed .workbench-container,
        body.layout-fixed .workbench-main,
        body.layout-fixed .workbench-sidebar {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        body.layout-fixed .settings-form-card,
        body.layout-fixed .stunning-table-card,
        body.layout-fixed .card.stunning-table-card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: 1px solid #e8ecf1 !important;
            box-shadow: none !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            /* Force internal bleed */
        }

        body.layout-fixed .workbench-container {
            flex-direction: column !important;
            gap: 0 !important;
            display: flex !important;
        }

        body.layout-fixed .settings-field-row {
            flex-direction: column !important;
            display: flex !important;
            gap: 0.15rem !important;
            /* Extremely compact vertical gap */
            padding: 0.45rem 0.75rem !important;
            /* Narrower horizontal padding */
            border-bottom: none !important;
        }



        body.layout-fixed .settings-field-label {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            text-align: left !important;
            font-weight: 700 !important;
            font-size: 0.8125rem !important;
            /* Reduced font size for labels */
            color: #1e293b !important;
            margin-bottom: 0 !important;
            padding-top: 0 !important;
        }

        body.layout-fixed .settings-field-input {
            width: 100% !important;
            flex: 0 0 100% !important;
        }

        body.layout-fixed .settings-form-header,
        body.layout-fixed .settings-form-footer {
            padding-left: 0.75rem !important;
            /* Narrower horizontal padding */
            padding-right: 0.75rem !important;
        }


        /* Standardize 4px border-radius for all inputs on mobile */
        body.layout-fixed .settings-input,
        body.layout-fixed .settings-phone-input,
        body.layout-fixed .form-control,
        body.layout-fixed .form-select,
        body.layout-fixed .select2-container--default .select2-selection--single,
        body.layout-fixed .select2-container--default .select2-selection--multiple {
            border-radius: 4px !important;
        }

        /* Remove spacing gaps for sidebars on mobile */
        .settings_bar_gap {
            margin-bottom: 0 !important;
        }

    }

    /* =====================================================
       HARD OVERRIDE: DESKTOP/TABLET ELITE DESIGN
       Targeting screens above 575px for horizontal alignment
       ===================================================== */
    @media (min-width: 769px) {
        body.layout-fixed .settings-field-row {
            display: flex !important;
            flex-direction: row !important;
            align-items: flex-start !important;
            padding: 0.875rem 0 !important;
            gap: 1.5rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        body.layout-fixed .settings-field-row:last-child {
            border-bottom: none !important;
        }

        body.layout-fixed .settings-field-label {
            flex: 0 0 180px !important;
            max-width: 180px !important;
            text-align: right !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            padding-top: 0.65rem !important;
            line-height: 1.4 !important;
        }

        body.layout-fixed .settings-field-input {
            flex: 1 !important;
            min-width: 0 !important;
        }

        /* Desktop specific border radius ensuring 4px is "Hard Applied" */
        body.layout-fixed .settings-input,
        body.layout-fixed .settings-phone-input,
        body.layout-fixed .form-control,
        body.layout-fixed .form-select,
        body.layout-fixed .select2-container--default .select2-selection--single,
        body.layout-fixed .select2-container--default .select2-selection--multiple {
            border-radius: 4px !important;
        }
    }

    /* Strict Alignment: Form Headers should always stay left-aligned */
    .settings-form-header,
    .settings-form-header .d-flex,
    .settings-form-header .settings-form-title,
    .settings-form-header .settings-form-subtitle {
        text-align: left !important;
        justify-content: flex-start !important;
    }

    /* Page headers with actions (e.g. Add Button) maintain their spacing */
    .settings-form-header .d-flex.justify-content-between {
        justify-content: space-between !important;
    }

    /* Global Spacing: Small consistent space for form containers in ALL modes */
    body.layout-fixed .card-body,
    body.layout-fixed .settings-form-body,
    body.layout-fixed .accordion-body {
        padding: 0.5rem !important;
    }
</style>
<div class="app-wrapper">

    @include('admin.common.header')
    @include('admin.common.left_sidebar')
    <main class="app-main">
        <div class="flash-toast-wrapper" id="flashToastWrapper">
            @if (Session::has('message'))
                @php
                    $alertClass = 'info';
                    $bgClass = 'bg-info';
                    $iconClass = 'fa-info-circle';
                    if (str_contains(Session::get('alert-class', ''), 'success')) {
                        $alertClass = 'success';
                        $bgClass = 'bg-success';
                        $iconClass = 'fa-check-circle';
                    } elseif (str_contains(Session::get('alert-class', ''), 'danger')) {
                        $alertClass = 'danger';
                        $bgClass = 'bg-danger';
                        $iconClass = 'fa-times-circle';
                    } elseif (str_contains(Session::get('alert-class', ''), 'warning')) {
                        $alertClass = 'warning';
                        $bgClass = 'bg-warning text-dark';
                        $iconClass = 'fa-exclamation-triangle';
                    }
                @endphp
                <div class="alert {{ $bgClass }} flash-toast text-white alert-dismissible fade show d-flex align-items-center m-0"
                    role="alert">
                    <i class="fa {{ $iconClass }} me-2" style="font-size: 1.1rem;"></i>
                    <div style="font-size: 0.95rem; font-weight: 500;">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"
                        onclick="this.closest('.alert').remove()"
                        style="font-size: 0.65rem; top: 50%; transform: translateY(-50%); padding: 0.5rem 1rem; margin-top: 0;"></button>
                </div>
            @endif

            <div class="alert bg-success flash-toast text-white alert-dismissible fade show d-flex align-items-center m-0 d-none"
                id="success_message_div" role="alert">
                <i class="fa fa-check-circle me-2" style="font-size: 1.1rem;"></i>
                <div id="success_message" style="font-size: 0.95rem; font-weight: 500;"></div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"
                    onclick="this.closest('.alert').classList.add('d-none')"
                    style="font-size: 0.65rem; top: 50%; transform: translateY(-50%); padding: 0.5rem 1rem; margin-top: 0;"></button>
            </div>

            <div class="alert bg-danger flash-toast text-white alert-dismissible fade show d-flex align-items-center m-0 d-none"
                id="error_message_div" role="alert">
                <i class="fa fa-times-circle me-2" style="font-size: 1.1rem;"></i>
                <div id="error_message" style="font-size: 0.95rem; font-weight: 500;"></div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"
                    onclick="this.closest('.alert').classList.add('d-none')"
                    style="font-size: 0.65rem; top: 50%; transform: translateY(-50%); padding: 0.5rem 1rem; margin-top: 0;"></button>
            </div>
        </div>
        @yield('main')
        @include('admin.common.footer')
    </main>
</div>
@include('admin.common.foot')
@yield('validate_script')