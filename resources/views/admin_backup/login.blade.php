@extends('admin.login-layout.template')

@section('main')
    <h1 class="login-card__title text-center">{{ __('Log In') }}</h1>
    <p class="login-card__subtitle text-center">{{ __('Sign in to start your session') }}</p>

    <form action="{{ url('admin/authenticate') }}" method="post" id="admin_login" class="login-form">
        @csrf
        <div class="login-form__group">
            <div class="input-group login-form__input-wrap">
                <span class="input-group-text login-form__icon"><i class="fa fa-envelope"></i></span>
                <input type="email" name="email" id="email" class="form-control login-form__input" placeholder="{{ __('Email') }}" value="{{ old('email') }}" required>
            </div>
            @if ($errors->has('email'))
                <p class="login-form__error">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="login-form__group">
            <div class="input-group login-form__input-wrap">
                <span class="input-group-text login-form__icon"><i class="fa fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control login-form__input" placeholder="{{ __('Password') }}" required>
            </div>
            @if ($errors->has('password'))
                <p class="login-form__error">{{ $errors->first('password') }}</p>
            @endif
        </div>

        @if (!empty(settings('recaptcha_preference')) && !empty(settings('recaptcha_key')))
            @if (str_contains(settings('recaptcha_preference'), 'admin_login'))
                <div class="g-recaptcha mt-3" data-sitekey="{{ settings('recaptcha_key') }}"></div>
                @if ($errors->has('g-recaptcha-response'))
                    <p class="login-form__error">{{ $errors->first('g-recaptcha-response') }}</p>
                @endif
            @endif
        @endif

        <div class="login-form__options">
            <div class="form-check login-form__remember">
                <input type="checkbox" name="remember_me" id="remember_me" value="1" class="form-check-input login-form__checkbox" {{ old('remember_me') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember_me">{{ __('Remember Me') }}</label>
            </div>
            <a href="{{ url('admin/forgot-password') }}" class="login-form__forgot">{{ __('Forgot Password') }}</a>
        </div>

        <div class="login-form__submit-wrap text-center">
            <button type="submit" class="btn btn-login-primary login-form__submit login"><i class="spinner fa fa-spinner fa-spin d-none"></i> {{ __('Log In') }}</button>
        </div>
    </form>
@endsection
