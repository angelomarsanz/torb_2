@extends('admin.template')

@section('main')
<div class="content-wrapper">
    {{-- Header --}}
    <section class="content-header pb-0">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 text-start">
                    <h1 class="m-0 fw-bold" style="font-size: 1.5rem; color: #1e293b; letter-spacing: -0.02em;">{{ __('ELITE PROFILE') }}</h1>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ __('Manage your administrative account and security settings') }}</p>
                </div>
                <div class="col-sm-6 text-end d-none d-md-block">
                    @include('admin.common.breadcrumb')
                </div>
            </div>
        </div>
    </section>

    <section class="content mt-4">
        <div class="container-fluid">

            @if (Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #fee2e2; color: #991b1b;">
                    <i class="fa fa-exclamation-circle me-2"></i>
                    <strong>{{ __('Warning!') }}</strong> {{ __('Whoops, there was an error. Please verify your information below.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                {{-- Left Side: Profile Preview --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body text-center py-5" style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);">
                            <div class="position-relative d-inline-block">
                                <div class="profile-avatar-shadow rounded-circle" style="padding: 10px; background: rgba(32, 107, 196, 0.05);">
                                    <div class="bg-white rounded-circle p-1">
                                        <img src="{{ Auth::guard('admin')->user()->profile_src }}" 
                                             alt="{{ $result->username }}" 
                                             class="rounded-circle"
                                             style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                                    </div>
                                </div>
                                <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle border border-4 border-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; transform: translate(-10%, -10%);">
                                    <i class="fa fa-shield-alt" style="font-size: 16px;"></i>
                                </span>
                            </div>
                            
                            <h4 class="mt-4 mb-1 fw-bold text-dark" style="letter-spacing: -0.01em;">{{ $result->username }}</h4>
                            <p class="text-muted mb-4" style="font-size: 0.9rem;">{{ $result->email }}</p>
                            
                            <div class="d-flex flex-column align-items-center gap-3">
                                <div class="d-flex gap-2">
                                    <span class="badge px-3 py-2 bg-primary-subtle text-primary rounded-pill fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('ADMINISTRATOR') }}</span>
                                    <span class="badge px-3 py-2 bg-success-subtle text-success rounded-pill fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('ACTIVE') }}</span>
                                </div>
                                
                                <div class="pt-3 w-100 border-top mt-2">
                                    <div class="d-flex justify-content-between px-4 mb-2">
                                        <span class="text-muted f-12">{{ __('Member Since') }}</span>
                                        <span class="fw-bold text-dark f-12">{{ $result->created_at ? $result->created_at->format('M Y') : __('N/A') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between px-4">
                                        <span class="text-muted f-12">{{ __('Account Type') }}</span>
                                        <span class="fw-bold text-dark f-12">{{ __('Full Access') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Edit Form --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0 !important;">

                        <form id="profile_edit" method="post" action="{{ url('admin/profile') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body p-4">
                                
                                {{-- General Info Section --}}
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                            <i class="fa fa-id-card" style="font-size: 14px;"></i>
                                        </div>
                                        <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ __('Personal Information') }}</h6>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-bold text-muted f-12 mb-1 text-uppercase">{{ __('Display Name') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 border-light"><i class="fa fa-user text-muted opacity-50"></i></span>
                                                <input type="text" name="name" class="form-control border-start-0 border-light bg-light" id="name"
                                                    placeholder="{{ __('Enter your full name') }}" value="{{ $result->username }}" required>
                                            </div>
                                            @if ($errors->has('name'))
                                                <div class="text-danger f-11 mt-1">{{ $errors->first('name') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-bold text-muted f-12 mb-1 text-uppercase">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 border-light"><i class="fa fa-envelope text-muted opacity-50"></i></span>
                                                <input type="email" name="email" class="form-control border-start-0 border-light bg-light" id="email"
                                                    placeholder="{{ __('Email') }}" value="{{ $result->email }}" required>
                                            </div>
                                            @if ($errors->has('email'))
                                                <div class="text-danger f-11 mt-1">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Security Section --}}
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                            <i class="fa fa-lock" style="font-size: 14px;"></i>
                                        </div>
                                        <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ __('Security & Authentication') }}</h6>
                                    </div>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label fw-bold text-muted f-12 mb-1 text-uppercase">{{ __('New Password') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 border-light"><i class="fa fa-key text-muted opacity-50"></i></span>
                                                <input type="password" name="password" class="form-control border-start-0 border-light bg-light new_password" id="password" 
                                                    placeholder="{{ __('••••••••') }}">
                                            </div>
                                            <div class="form-text text-muted mt-2 f-11">
                                                <i class="fa fa-info-circle me-1"></i>{{ __('Leave blank to keep your current password.') }}
                                            </div>
                                            @if ($errors->has('password'))
                                                <div class="text-danger f-11 mt-1">{{ $errors->first('password') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label fw-bold text-muted f-12 mb-1 text-uppercase">{{ __('Confirm New Password') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0 border-light"><i class="fa fa-key text-muted opacity-50"></i></span>
                                                <input type="password" name="password_confirmation" class="form-control border-start-0 border-light bg-light" 
                                                    id="password_confirmation" placeholder="{{ __('••••••••') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Profile Photo Section --}}
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                            <i class="fa fa-image" style="font-size: 14px;"></i>
                                        </div>
                                        <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.05em;">{{ __('Profile Branding') }}</h6>
                                    </div>
                                    
                                    <div class="profile-upload-zone p-4 text-center border-dashed rounded-3 bg-light-subtle position-relative overflow-hidden transition-all" style="border: 2px dashed #e2e8f0; cursor: pointer;" onclick="document.getElementById('profile_pic').click()">
                                        <div class="upload-content">
                                            <div class="mb-3">
                                                <span class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                                    <i class="fa fa-cloud-upload-alt fs-4"></i>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">{{ __('Update your profile picture') }}</h6>
                                            <p class="text-muted f-12 mb-0">{{ __('Drag and drop or click to browse') }}</p>
                                        </div>
                                        <input id="profile_pic" type="file" name="profile_pic" accept="image/*" class="d-none">
                                        <div id="file-name-preview" class="mt-3 text-primary fw-bold f-12"></div>
                                    </div>
                                    <div class="mt-3 d-flex align-items-center justify-content-between">
                                        <span class="text-muted f-11"><i class="fa fa-info-circle me-1 opacity-50"></i> JPG, PNG, GIF up to 2MB</span>
                                    </div>
                                    @if ($errors->has('profile_pic'))
                                        <div class="text-danger f-11 mt-1">{{ $errors->first('profile_pic') }}</div>
                                    @endif
                                </div>

                            </div>

                            <div class="card-footer px-4 py-3 bg-light-subtle border-top d-flex align-items-center justify-content-between" style="border-top-color: #f1f5f9 !important;">
                                <div class="d-flex align-items-center text-muted f-11">
                                    <i class="fa fa-shield-alt me-2 text-success"></i>
                                    <span>{{ __('Encrypted data transmission') }}</span>
                                </div>
                                <div class="d-flex gap-3">
                                    <a href="{{ url('admin/admin-users') }}" class="btn px-4 py-2 text-muted border-0 bg-transparent fw-bold f-13">{{ __('Cancel') }}</a>
                                    <button type="submit" class="btn px-5 py-2 fw-bold text-uppercase shadow-sm" style="background: rgba(32, 107, 196, 0.08); border: 1px solid rgba(32, 107, 196, 0.2); color: #206bc4; border-radius: 4px; letter-spacing: 0.5px; font-size: 13px;">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
    .profile-upload-zone:hover {
        background-color: rgba(32, 107, 196, 0.02) !important;
        border-color: #206bc4 !important;
    }
    .profile-avatar-shadow {
        transition: transform 0.3s ease;
    }
    .profile-avatar-shadow:hover {
        transform: scale(1.02);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .bg-primary-subtle {
        background-color: rgba(32, 107, 196, 0.1) !important;
    }
    .bg-success-subtle {
        background-color: rgba(43, 176, 116, 0.1) !important;
    }
    .border-dashed {
        border-style: dashed !important;
    }
    .f-11 { font-size: 11px !important; }
    .f-13 { font-size: 13px !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_pic');
        const previewText = document.getElementById('file-name-preview');
        
        if (fileInput && previewText) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    previewText.innerHTML = '<i class="fa fa-file-image me-1"></i> Selected: ' + this.files[0].name;
                }
            });
        }
    });
</script>
@endsection

@section('validate_script')
<script type="text/javascript">
	'use strict'
	var message = "{{ __('The file must be an image (jpg, gif or png)') }}";
</script>
<script type="text/javascript" src="{{ asset('public/backend/js/additional-method.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/dist/js/validate.min.js') }}"></script>
@endsection
