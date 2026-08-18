@extends('admin.login-layout.template')

@section('main')
    <h1 class="login-card__title text-center">{{ siteName() }}</h1>

    <form action="{{ url('admin/authenticate') }}" method="post" id="admin_login" class="login-form">
        @csrf
        <div class="login-form__group">
            <div class="login-form__input-wrapper">
                <i class="fa fa-envelope login-form__input-icon"></i>
                <input type="email" name="email" id="email" class="form-control login-form__input"
                    placeholder="{{ __('Email') }}" value="{{ old('email') }}" required>
            </div>
            @if ($errors->has('email'))
                <p class="login-form__error mt-1 text-danger f-12">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="login-form__group password-group">
            <div class="login-form__input-wrapper">
                <i class="fa fa-lock login-form__input-icon"></i>
                <input type="password" name="password" id="password" class="form-control login-form__input"
                    placeholder="{{ __('Password') }}" required>
            </div>
            @if ($errors->has('password'))
                <p class="login-form__error mt-1 text-danger f-12">{{ $errors->first('password') }}</p>
            @endif
        </div>

        @if (!empty(settings('recaptcha_preference')) && !empty(settings('recaptcha_key')))
            @if (str_contains(settings('recaptcha_preference'), 'admin_login'))
                <div class="recaptcha-wrapper mt-3">
                    <div class="g-recaptcha" data-sitekey="{{ settings('recaptcha_key') }}"></div>
                </div>
                <style>
                    .recaptcha-wrapper {
                        width: 100%;
                    }
                    .recaptcha-wrapper > div {
                        transform-origin: left top;
                        transform: scale(1.18);
                        margin-bottom: 14px;
                    }
                </style>
                @if ($errors->has('g-recaptcha-response'))
                    <p class="login-form__error mt-1 text-danger f-12">{{ $errors->first('g-recaptcha-response') }}</p>
                @endif
            @endif
        @endif

        <div class="login-form__options">
            <a href="{{ url('admin/forgot-password') }}" class="login-form__forgot">{{ __('Forgot password?') }}</a>
        </div>

        <div class="login-form__submit-wrap">
            <button type="submit" class="btn btn-login-primary login-form__submit login">
                <i class="spinner fa fa-spinner fa-spin d-none"></i> {{ __('Login') }}
            </button>
        </div>
    </form>
@endsection
