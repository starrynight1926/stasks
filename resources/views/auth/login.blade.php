<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — ProjectFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F9FAFB !important;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #111827;
        }

        /* Header */
        .login-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid #E5E7EB;
            background: #fff;
        }
        .login-header h1 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
        }
        .login-header a {
            font-size: 0.8125rem;
            color: #6B7280;
            text-decoration: none;
        }
        .login-header a:hover { color: #111827; }

        /* Main */
        .login-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Card */
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }
        .login-card-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.375rem;
        }
        .login-card-subtitle {
            font-size: 0.875rem;
            color: #6B7280;
            text-align: center;
            margin-bottom: 1.75rem;
        }

        /* Form */
        .form-group { margin-bottom: 1.25rem; }
        .form-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.375rem;
        }
        .form-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
        }
        .form-link {
            font-size: 0.75rem;
            color: #3B82F6;
            text-decoration: none;
            font-weight: 500;
        }
        .form-link:hover { color: #2563EB; }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.125rem;
            height: 1.125rem;
            color: #9CA3AF;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            padding: 0.625rem 0.75rem 0.625rem 2.375rem;
            font-size: 0.875rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            color: #111827;
        }
        .form-input::placeholder { color: #9CA3AF; }
        .form-input:focus {
            border-color: #111827;
            box-shadow: 0 0 0 1px #111827;
        }
        .form-input-pw { padding-right: 2.75rem; }

        .toggle-pw-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.25rem;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 0.25rem;
            color: #9CA3AF;
            transition: color 0.15s;
        }
        .toggle-pw-btn:hover { color: #6B7280; }
        .toggle-pw-btn svg { width: 1.125rem; height: 1.125rem; }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .form-check input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #111827;
            cursor: pointer;
        }
        .form-check label {
            font-size: 0.8125rem;
            color: #374151;
            cursor: pointer;
        }

        /* Button */
        .btn-signin {
            width: 100%;
            padding: 0.625rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: #111827;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-signin:hover { background: #1F2937; }
        .btn-signin:active { background: #030712; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E5E7EB;
        }
        .divider span {
            font-size: 0.75rem;
            color: #9CA3AF;
            white-space: nowrap;
        }

        /* Social */
        .social-row {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .btn-social {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #374151;
            background: #fff;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            cursor: default;
            opacity: 0.5;
        }
        .btn-social svg { width: 1.125rem; height: 1.125rem; }

        /* Signup link */
        .signup-text {
            text-align: center;
            font-size: 0.8125rem;
            color: #6B7280;
        }
        .signup-text a {
            color: #3B82F6;
            font-weight: 600;
            text-decoration: none;
        }
        .signup-text a:hover { color: #2563EB; }

        /* Error */
        .login-error {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 0.75rem;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 0.5rem;
            margin-bottom: 1.25rem;
        }
        .login-error svg { flex-shrink: 0; width: 1rem; height: 1rem; color: #EF4444; }
        .login-error span { font-size: 0.8125rem; color: #DC2626; }

        /* Footer */
        .login-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            border-top: 1px solid #E5E7EB;
            background: #fff;
        }
        .login-footer span { font-size: 0.75rem; color: #9CA3AF; }
        .login-footer-links { display: flex; gap: 1.5rem; }
        .login-footer-links a {
            font-size: 0.75rem;
            color: #9CA3AF;
            text-decoration: none;
        }
        .login-footer-links a:hover { color: #6B7280; }
    </style>
</head>
<body>
    {{-- Header --}}
    <header class="login-header">
        <h1>ProjectFlow</h1>
        <a href="#">Help Center</a>
    </header>

    {{-- Main --}}
    <main class="login-main">
        <div class="login-card">
            <h2 class="login-card-title">Welcome back</h2>
            <p class="login-card-subtitle">Log in to manage your productivity</p>

            @if($errors->any())
                <div class="login-error">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <span>{{ $errors->first('login') }}</span>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                {{-- Username (Email address in design) --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="username" class="form-label">Email address</label>
                    </div>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="name@company.com" autocomplete="off" class="form-input">
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-group" x-data="{ show: false }">
                    <div class="form-label-row">
                        <label for="password" class="form-label">Password</label>
                        <span class="form-link">Forgot password?</span>
                    </div>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" placeholder="••••••••" autocomplete="current-password" class="form-input form-input-pw">
                        <button type="button" @click="show = !show" class="toggle-pw-btn" tabindex="-1">
                            <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="form-check">
                    <input type="checkbox" id="remember">
                    <label for="remember">Remember me for 30 days</label>
                </div>

                {{-- Sign In --}}
                <button type="submit" class="btn-signin">Sign In</button>
            </form>

            {{-- Divider --}}
            <div class="divider"><span>Or continue with</span></div>

            {{-- Social buttons (disabled/decorative) --}}
            <div class="social-row">
                <div class="btn-social">
                    <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Google
                </div>
                <div class="btn-social">
                    <svg viewBox="0 0 24 24" fill="#333"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                    GitHub
                </div>
            </div>

            {{-- Sign up link --}}
            <p class="signup-text">Don't have an account? <a href="#">Sign up</a></p>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="login-footer">
        <span>&copy; 2024 ProjectFlow Inc. All rights reserved.</span>
        <div class="login-footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Help Center</a>
        </div>
    </footer>
</body>
</html>
