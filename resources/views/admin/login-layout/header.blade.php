
<body class="hold-transition login-page login-page--fancy">
    <div class="login-box">
        @if (Session::has('message'))
            <div class="alert {{ Session::get('alert-class') }} alert-dismissible fade show text-center login-alert" role="alert">
                {{ Session::get('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card login-card">
            <div class="card-body">
