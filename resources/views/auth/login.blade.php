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
            --brand-start: #5b0fbd;
            --brand-end: #2575fc;
            --ink: #161a2e;
            --muted: #68708a;
            --bg: #edf0f7;
            --card-shadow: 0 24px 70px rgba(22, 26, 46, 0.12);
        }
        * { font-family: 'Figtree', system-ui, sans-serif; }
        html, body { height: 100%; }
        body {
            background:
                radial-gradient(900px 500px at 85% -10%, rgba(37, 117, 252, 0.10), transparent 60%),
                radial-gradient(700px 500px at -10% 110%, rgba(91, 15, 189, 0.10), transparent 60%),
                var(--bg);
        }
        .login-wrap { min-height: 100vh; }

        /* ============ Brand panel ============ */
        .brand-side {
            background: linear-gradient(150deg, #4a0ba3 0%, var(--brand-start) 38%, var(--brand-end) 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 3rem 3rem 2rem;
            min-height: 100vh;
        }
        .brand-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.16) 1.2px, transparent 1.2px);
            background-size: 26px 26px;
            mask-image: radial-gradient(560px 420px at 80% 10%, #000 30%, transparent 75%);
            pointer-events: none;
        }
        .orb { position: absolute; border-radius: 50%; pointer-events: none; }
        .orb-1 { width: 460px; height: 460px; top: -160px; right: -140px;
                 background: radial-gradient(circle, rgba(255,255,255,0.20), transparent 65%); }
        .orb-2 { width: 340px; height: 340px; bottom: -120px; left: -100px;
                 background: radial-gradient(circle, rgba(0,0,0,0.16), transparent 65%); }

        .brand-main { margin-top: auto; margin-bottom: auto; position: relative; z-index: 1; width: 100%; }
        .brand-logo {
            width: 62px; height: 62px;
            border-radius: 18px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.28);
            backdrop-filter: blur(6px);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.9rem;
            margin-bottom: 1.4rem;
            box-shadow: 0 10px 26px rgba(0,0,0,0.22);
        }
        .brand-side h1 { font-weight: 800; font-size: 2.5rem; letter-spacing: -0.02em; }
        .brand-side h1 .thin { font-weight: 400; opacity: 0.85; }
        .brand-side .tagline { color: rgba(255,255,255,0.82); font-size: 1.02rem; margin-bottom: 2.2rem; max-width: 420px; }

        .feature { display: flex; align-items: flex-start; gap: 0.95rem; margin-bottom: 1.15rem; }
        .feature:last-child { margin-bottom: 0; }
        .feature .icon {
            width: 44px; height: 44px; flex: 0 0 44px;
            border-radius: 13px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.24);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }
        .feature h6 { margin: 0 0 0.1rem; font-weight: 700; font-size: 0.95rem; }
        .feature p { margin: 0; color: rgba(255,255,255,0.72); font-size: 0.86rem; }

        .glass-stats {
            display: flex; gap: 0.7rem; margin-top: 2.2rem; flex-wrap: wrap;
        }
        .glass-chip {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .glass-chip strong { font-size: 0.95rem; }
        .glass-chip i { font-size: 1.05rem; opacity: 0.9; }

        .brand-foot {
            margin-top: auto; padding-top: 1.6rem;
            color: rgba(255,255,255,0.62); font-size: 0.82rem;
            position: relative; z-index: 1;
            display: flex; flex-wrap: wrap; gap: 0.25rem 0.6rem;
            align-items: center;
        }
        .brand-foot .dot { opacity: 0.5; }
        .brand-foot a { color: rgba(255,255,255,0.88); font-weight: 700; text-decoration: none; }
        .brand-foot a:hover { text-decoration: underline; color: #fff; }

        /* ============ Form panel ============ */
        .form-side {
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 1.5rem; min-height: 100vh;
        }
        .login-card {
            width: 100%; max-width: 440px;
            background: #fff;
            border: 1px solid rgba(22,26,46,0.06);
            border-radius: 22px;
            box-shadow: var(--card-shadow);
            padding: 2.6rem 2.4rem 2.2rem;
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 5px;
            background: linear-gradient(90deg, var(--brand-start), var(--brand-end));
        }
        .login-eyebrow {
            display: inline-flex; align-items: center; gap: 0.45rem;
            font-size: 0.72rem; font-weight: 800; letter-spacing: 0.12em;
            color: var(--brand-end);
            background: rgba(37,117,252,0.09);
            border-radius: 30px;
            padding: 0.35rem 0.85rem;
            margin-bottom: 1rem;
        }
        .login-card h2 { font-weight: 800; letter-spacing: -0.02em; color: var(--ink); margin-bottom: 0.3rem; }
        .login-card .sub { color: var(--muted); margin-bottom: 1.6rem; font-size: 0.95rem; }

        .form-label { font-weight: 700; color: var(--ink); font-size: 0.84rem; margin-bottom: 0.4rem; }
        .field .input-group-text {
            background: #f5f7fc; border: 1px solid #e1e7f2; color: #8a93ad;
        }
        .field .form-control {
            border: 1px solid #e1e7f2; padding: 0.72rem 0.9rem;
            font-size: 0.95rem; color: var(--ink); background: #fff;
        }
        .field .input-group .input-group-text { border-radius: 12px 0 0 12px; }
        .field .input-group .form-control { border-radius: 0 12px 12px 0; }
        .field .input-group .form-control.has-toggle { border-radius: 0; }
        .field .form-control:focus { border-color: var(--brand-end); box-shadow: 0 0 0 0.22rem rgba(37,117,252,0.14); }
        .field .form-control.is-invalid { border-color: #e5484d; }
        .field .form-control.is-invalid:focus { box-shadow: 0 0 0 0.22rem rgba(229,72,77,0.13); }
        .toggle-pass {
            cursor: pointer; border-radius: 0 12px 12px 0 !important;
            background: #f5f7fc; color: #8a93ad;
        }
        .toggle-pass:hover { color: var(--brand-end); }
        .invalid-feedback { font-size: 0.82rem; font-weight: 600; }
        .caps-hint { display: none; font-size: 0.78rem; font-weight: 600; color: #b7791f; margin-top: 0.35rem; }

        .form-check-input:checked { background-color: var(--brand-end); border-color: var(--brand-end); }
        .form-check-input:focus { box-shadow: 0 0 0 0.22rem rgba(37,117,252,0.14); border-color: var(--brand-end); }
        .form-check-label { font-size: 0.88rem; color: var(--muted); font-weight: 500; }
        .forgot-link { font-size: 0.88rem; text-decoration: none; font-weight: 700; color: var(--brand-end); }
        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-start) 0%, var(--brand-end) 100%);
            border: none; border-radius: 13px; padding: 0.8rem;
            font-weight: 800; font-size: 1rem; letter-spacing: 0.01em; width: 100%;
            box-shadow: 0 12px 26px rgba(64, 52, 200, 0.30);
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }
        .btn-login:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(1.06); color: #fff; }
        .btn-login:disabled { opacity: 0.85; cursor: wait; }

        .shake { animation: shake 0.4s ease; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-7px); }
            40%, 80% { transform: translateX(7px); }
        }

        @media (max-width: 991.98px) {
            .brand-side { min-height: auto; padding: 2.4rem 1.75rem; }
            .brand-side h1 { font-size: 1.9rem; }
            .brand-side .tagline { margin-bottom: 0; }
            .brand-features, .glass-stats, .brand-foot { display: none; }
            .brand-main { margin: 0; }
            .form-side { min-height: auto; padding: 2rem 1.25rem 3rem; }
            .login-card { padding: 2rem 1.5rem 1.75rem; }
        }
    </style>
</head>
<body>
    <div class="container-fluid login-wrap px-0">
        <div class="row g-0">
            <!-- Brand panel -->
            <div class="col-lg-6 brand-side">
                <div class="orb orb-1"></div>
                <div class="orb orb-2"></div>

                <div class="brand-main">
                    <div class="brand-logo"><i class="ti ti-building-store"></i></div>
                    <h1>M.Traders <span class="thin">POS</span></h1>
                    <p class="tagline">Mehmood Traders — inventory, billing and profit analytics in one fast, reliable workspace.</p>

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

                    <div class="glass-stats">
                        <div class="glass-chip"><i class="ti ti-bolt"></i><span><strong>Seconds</strong>&nbsp;per invoice</span></div>
                        <div class="glass-chip"><i class="ti ti-shield-check"></i><span><strong>Daily</strong>&nbsp;auto backups</span></div>
                        <div class="glass-chip"><i class="ti ti-devices"></i><span><strong>Works</strong>&nbsp;on any device</span></div>
                    </div>
                </div>

                <div class="brand-foot">
                    <span>&copy; {{ date('Y') }} Mehmood Traders. All rights reserved.</span>
                    <span class="dot">•</span>
                    <span>Developed by <a href="https://genixstack.com" target="_blank" rel="noopener">Genixstack Solutions</a></span>
                </div>
            </div>

            <!-- Form panel -->
            <div class="col-lg-6 form-side">
                <div class="login-card{{ $errors->any() ? ' shake' : '' }}">
                    <span class="login-eyebrow"><i class="ti ti-lock"></i> SECURE SIGN IN</span>
                    <h2>Welcome back</h2>
                    <p class="sub">Sign in to your account to continue to the POS.</p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3 field">
                            <label for="email" class="form-label">{{ __('Email address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input id="email" type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required autofocus autocomplete="username"
                                       placeholder="you@example.com" aria-describedby="emailError">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block" id="emailError">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-2 field">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-key"></i></span>
                                <input id="password" type="password" name="password"
                                       class="form-control has-toggle @error('password') is-invalid @enderror"
                                       required autocomplete="current-password"
                                       placeholder="Enter your password" aria-describedby="passwordError">
                                <button type="button" class="input-group-text toggle-pass" id="togglePassword"
                                        title="Show / hide password" aria-label="Show or hide password">
                                    <i class="ti ti-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block" id="passwordError">{{ $message }}</div>
                            @enderror
                            <div class="caps-hint" id="capsHint"><i class="ti ti-alert-triangle"></i> Caps Lock is on</div>
                        </div>

                        <!-- Remember Me -->
                        <div class="d-flex align-items-center justify-content-between mt-3 mb-4">
                            <div class="form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary btn-login" id="loginBtn">
                            <span class="btn-label">{{ __('Log in') }} <i class="ti ti-arrow-right ms-1"></i></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            // Show / hide password (plain JS — no dependency).
            var toggle = document.getElementById('togglePassword');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    var input = document.getElementById('password');
                    var icon = document.getElementById('toggleIcon');
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    icon.classList.toggle('ti-eye', !show);
                    icon.classList.toggle('ti-eye-off', show);
                    input.focus();
                });
            }

            // Caps Lock warning on the password field.
            var pass = document.getElementById('password');
            var hint = document.getElementById('capsHint');
            if (pass && hint && typeof KeyboardEvent !== 'undefined') {
                ['keyup', 'keydown'].forEach(function (evt) {
                    pass.addEventListener(evt, function (e) {
                        try {
                            hint.style.display = e.getModifierState && e.getModifierState('CapsLock') ? 'block' : 'none';
                        } catch (err) { hint.style.display = 'none'; }
                    });
                });
                pass.addEventListener('blur', function () { hint.style.display = 'none'; });
            }

            // Loading state + double-submit guard.
            var form = document.getElementById('loginForm');
            if (form) {
                form.addEventListener('submit', function () {
                    var btn = document.getElementById('loginBtn');
                    if (btn && !btn.disabled) {
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in…';
                    }
                });
            }
        })();
    </script>
</body>
</html>
