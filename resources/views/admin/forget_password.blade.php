@extends('admin.login-layout.template')

@section('main')
    <h1 class="login-card__title text-center">{{ __('Reset Password') }}</h1>

    <div class="mb-4 text-center">
        <p class="text-secondary f-14">{{ __('We will send you a email to reset your password') }}</p>
    </div>

    <form id="forgot_password_form" method="post" action="{{ url('admin/forgot-password') }}" class="login-form">
        @csrf
        <div class="login-form__group">
            <div class="login-form__input-wrapper">
                <i class="fa fa-envelope login-form__input-icon"></i>
                <input type="email" name="email" id="email" class="form-control login-form__input"
                    placeholder="{{ __('Email') }}" required>
            </div>
            @if ($errors->has('email'))
                <p class="login-form__error mt-1 text-danger f-12">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <div class="mt-4">
            <button id="reset_btn" class="btn btn-login-primary" type="submit">
                <i class="spinner fa fa-spinner fa-spin d-none"></i>
                <span>{{ __('Continue') }}</span>
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="{{ url('admin/login') }}" class="login-form__forgot d-inline-flex align-items-center gap-1">
            <i class="fa fa-angle-left" aria-hidden="true"></i> {{ __('Back to sign-in') }}
        </a>
    </div>
@endsection
