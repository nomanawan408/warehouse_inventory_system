<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log in — {{ config('app.name', 'M.Traders') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        :root {
            --brand-start: #6a11cb;
            --brand-end: #2575fc;
            --ink: #1a1d2e;
            --muted: #6b7280;
            --bg: #eef1f7;
        }
        * { font-family: 'Figtree', system-ui, sans-serif; }
        html, body { height: 100%; }
        body { background: var(--bg); }

        .login-wrap { min-height: 100vh; }

        /* ---------- Brand side ---------- */
        .brand-side {
            background: linear-gradient(135deg, var(--brand-start) 0%, var(--brand-end) 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem 3rem;
            min-height: 100vh;
        }
        .brand-side::before,
        .brand-side::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
        }
        .brand-side::before { width: 420px; height: 420px; top: -140px; right: -140px; }
        .brand-side::after { width: 300px; height: 300px; bottom: -100px; left: -80px; background: rgba(255, 255, 255, 0.07); }

        .brand-logo {
            width: 64px; height: 64px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative; z-index: 1;
        }
        .brand-side h1 { font-weight: 800; font-size: 2.4rem; position: relative; z-index: 1; }
        .brand-side .tagline { color: rgba(255, 255, 255, 0.82); font-size: 1.05rem; margin-bottom: 2.5rem; position: relative; z-index: 1; }

        .feature { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.4rem; position: relative; z-index: 1; }
        .feature .icon {
            width: 46px; height: 46px; flex: 0 0 46px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.22);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .feature h6 { margin: 0 0 0.15rem; font-weight: 700; }
        .feature p { margin: 0; color: rgba(255, 255, 255, 0.75); font-size: 0.9rem; }
        .brand-foot { margin-top: auto; padding-top: 2rem; color: rgba(255,255,255,0.6); font-size: 0.85rem; position: relative; z-index: 1; }
        .brand-foot a { color: rgba(255,255,255,0.85); font-weight: 600; text-decoration: none; }
        .brand-foot a:hover { text-decoration: underline; color: #fff; }

        /* Vertically centered, left-aligned content block */
        .brand-main { margin-top: auto; margin-bottom: auto; position: relative; z-index: 1; width: 100%; }

        /* ---------- Form side ---------- */
        .form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem;
            min-height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(26, 29, 46, 0.10);
            padding: 2.5rem 2.25rem;
        }
        .login-card h2 { font-weight: 800; color: var(--ink); margin-bottom: 0.25rem; }
        .login-card .sub { color: var(--muted); margin-bottom: 1.75rem; }

        .form-label { font-weight: 600; color: var(--ink); font-size: 0.9rem; }
        .input-group-text {
            background: #f4f6fb;
            border: 1px solid #e2e8f0;
            color: var(--muted);
        }
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 0.7rem 0.9rem;
            border-radius: 0 12px 12px 0;
            font-size: 0.95rem;
        }
        .input-group .input-group-text { border-radius: 12px 0 0 12px; }
        .form-control:focus { border-color: var(--brand-end); box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.15); }
        .toggle-pass { cursor: pointer; border-radius: 0 12px 12px 0 !important; }
        .toggle-pass + .form-control, .form-control.has-toggle { border-radius: 0; }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-start) 0%, var(--brand-end) 100%);
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 700;
            width: 100%;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(37, 117, 252, 0.35); color: #fff; }

        .forgot-link { font-size: 0.9rem; text-decoration: none; font-weight: 600; color: var(--brand-end); }
        .forgot-link:hover { text-decoration: underline; }

        @media (max-width: 991.98px) {
            .brand-side { min-height: auto; padding: 2.5rem 1.75rem; }
            .brand-side h1 { font-size: 1.8rem; }
            .brand-side .tagline { margin-bottom: 0; }
            .brand-features, .brand-foot { display: none; }
            .form-side { min-height: auto; padding: 2rem 1.25rem 3rem; }
        }
    </style>
</head>
<body>
    <div class="container-fluid login-wrap px-0">
        <div class="row g-0">
            <!-- Brand panel -->
            <div class="col-lg-6 brand-side">
                <div class="brand-main">
                <div class="brand-logo"><i class="ti ti-building-store"></i></div>
                <h1>M.Traders</h1>
                <p class="tagline">Mehmood Traders — inventory, sales &amp; profit in one place.</p>

                <div class="brand-features">
                    <div class="feature">
                        <div class="icon"><i class="ti ti-box"></i></div>
                        <div>
                            <h6>Live stock control</h6>
                            <p>Products, quantities and low-stock alerts in real time.</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="icon"><i class="ti ti-receipt"></i></div>
                        <div>
                            <h6>Fast billing &amp; invoices</h6>
                            <p>Create sales, apply discounts and print invoices in seconds.</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="icon"><i class="ti ti-chart-bar"></i></div>
                        <div>
                            <h6>Profit &amp; reports</h6>
                            <p>Revenue, margins and customer insights at a glance.</p>
                        </div>
                    </div>
                </div>
                </div>

                <div class="brand-foot">
                    <div>&copy; {{ date('Y') }} Mehmood Traders. All rights reserved.</div>
                    <div>Developed by <a href="https://genixstack.com" target="_blank" rel="noopener">Genixstack Solutions</a></div>
                </div>
            </div>

            <!-- Form panel -->
            <div class="col-lg-6 form-side">
                <div class="login-card">
                    <h2>Welcome back</h2>
                    <p class="sub">Sign in to your account to continue.</p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="ti ti-alert-circle me-2"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input id="email" class="form-control" type="email" name="email"
                                       value="{{ old('email') }}" required autofocus autocomplete="username"
                                       placeholder="you@example.com">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                <input id="password" class="form-control has-toggle" type="password"
                                       name="password" required autocomplete="current-password"
                                       placeholder="••••••••">
                                <span class="input-group-text toggle-pass" id="togglePassword" title="Show / hide password">
                                    <i class="ti ti-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary btn-login">
                            {{ __('Log in') }} <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            var input = document.getElementById('password');
            var icon = document.getElementById('toggleIcon');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('ti-eye', !show);
            icon.classList.toggle('ti-eye-off', show);
        });
    </script>
</body>
</html>
