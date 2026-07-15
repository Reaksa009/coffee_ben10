@extends('layouts.app')

@section('content')
    <style>
        .auth-wrap {
            min-height: calc(100vh - 7rem);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: radial-gradient(circle at 10% 20%, rgba(15, 118, 110, 0.08) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.08) 0%, transparent 40%), var(--soft);
            --auth-bg: rgba(255, 255, 255, 0.85);
            --auth-border: rgba(15, 118, 110, 0.15);
            --auth-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
            --auth-form-bg: rgba(255, 255, 255, 0.45);
            --auth-glow-color-1: rgba(20, 184, 166, 0.12);
            --auth-glow-color-2: rgba(37, 99, 235, 0.08);
        }

        [data-bs-theme="dark"] .auth-wrap {
            --auth-bg: rgba(24, 30, 43, 0.7);
            --auth-border: rgba(255, 255, 255, 0.08);
            --auth-shadow: 0 30px 60px rgba(0, 0, 0, 0.35);
            --auth-form-bg: rgba(15, 23, 42, 0.35);
            --auth-glow-color-1: rgba(20, 184, 166, 0.1);
            --auth-glow-color-2: rgba(37, 99, 235, 0.08);
        }

        .auth-glow-1 {
            position: absolute;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--auth-glow-color-1) 0%, transparent 70%);
            top: 15%;
            left: 20%;
            z-index: 1;
            filter: blur(40px);
            pointer-events: none;
        }

        .auth-glow-2 {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--auth-glow-color-2) 0%, transparent 70%);
            bottom: 15%;
            right: 20%;
            z-index: 1;
            filter: blur(50px);
            pointer-events: none;
        }

        .auth-panel {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 940px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.95fr);
            overflow: hidden;
            background: var(--auth-bg);
            border: 1px solid var(--auth-border);
            border-radius: 1.25rem;
            box-shadow: var(--auth-shadow);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: all 0.3s ease;
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-photo {
            min-height: 480px;
            position: relative;
            background-image:
                linear-gradient(180deg, rgba(15, 118, 110, 0.15), rgba(11, 15, 25, 0.75)),
                url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }

        .auth-photo-content {
            position: absolute;
            left: 2rem;
            right: 2rem;
            bottom: 2rem;
            color: #fff;
        }

        .auth-photo-title {
            font-size: clamp(1.45rem, 2.4vw, 2rem);
            font-weight: 800;
            line-height: 1.1;
            margin: 0 0 .75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .auth-photo-text {
            max-width: 440px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 1.5rem;
            font-size: 0.95rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .auth-feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            max-width: 520px;
        }

        .auth-feature-group {
            padding: .85rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: .75rem;
            background: rgba(17, 24, 39, 0.45);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .auth-feature-title {
            display: flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .6rem;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .auth-feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .auth-feature-list li {
            padding: .25rem .55rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, .95);
            font-size: .72rem;
            line-height: 1.2;
            transition: all 0.2s ease;
        }

        .auth-feature-list li:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-1px);
        }

        .auth-form-pane {
            display: flex;
            align-items: center;
            padding: clamp(1.5rem, 4vw, 2.75rem);
        }

        .auth-form-box {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.75rem;
            font-weight: 800;
            color: var(--ink);
            text-decoration: none;
        }

        .auth-form-box .form-control {
            min-height: 44px;
            border-radius: 0.5rem;
            border: 1px solid var(--line);
            background: var(--auth-form-bg);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-form-box .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.18);
        }

        .btn-primary-glow {
            min-height: 44px;
            background: linear-gradient(135deg, var(--brand), #2563eb);
            border: none;
            color: white;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(15, 118, 110, 0.2);
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .btn-primary-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(15, 118, 110, 0.32);
            filter: brightness(1.08);
            color: white;
        }

        @media (max-width: 991.98px) {
            .auth-panel {
                grid-template-columns: 1fr;
                max-width: 480px;
            }

            .auth-photo {
                min-height: 220px;
            }

            .auth-feature-grid {
                display: none;
            }
        }
    </style>

    <div class="auth-wrap">
        <div class="auth-glow-1"></div>
        <div class="auth-glow-2"></div>
        
        <section class="auth-panel">
            <div class="auth-photo">
                <div class="auth-photo-content">
                    <h2 class="auth-photo-title">Fresh coffee, faster checkout.</h2>
                    <p class="auth-photo-text">Manage daily sales, product stock, orders, and KHQR payments from one dashboard.</p>
                    <div class="auth-feature-grid">
                        <div class="auth-feature-group">
                            <div class="auth-feature-title"><i class="bi bi-check2-circle text-teal"></i> Essential</div>
                            <ul class="auth-feature-list">
                                <li>Touchscreen cashier</li>
                                <li>Kitchen/barista display</li>
                                <li>QR ordering</li>
                                <li>Mobile payment</li>
                                <li>Inventory alerts</li>
                                <li>Loyalty system</li>
                                <li>Offline mode</li>
                                <li>Sales dashboard</li>
                            </ul>
                        </div>
                        <div class="auth-feature-group">
                            <div class="auth-feature-title"><i class="bi bi-stars text-warning"></i> Advanced</div>
                            <ul class="auth-feature-list">
                                <li>AI sales forecasting</li>
                                <li>Multi-branch support</li>
                                <li>Self-order kiosk</li>
                                <li>Online delivery integration</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-form-pane">
                <div class="auth-form-box">
                    <a href="/" class="auth-brand">
                        <span class="brand-mark"><i class="bi bi-cup-hot"></i></span>
                        <span>
                            <span class="brand-name">Coffee Ben10</span>
                            <span class="brand-small">Coffee shop POS</span>
                        </span>
                    </a>

                    <h1 class="h4 fw-bold mb-1">Welcome back</h1>
                    <p class="text-muted mb-4">Sign in to open the dashboard.</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required autofocus placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input id="password" type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required placeholder="Enter password">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input">
                            <label for="remember" class="form-check-label text-muted">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary-glow w-100">
                            <i class="bi bi-speedometer2 me-1"></i> Login to Dashboard
                        </button>
                    </form>

                    @if(Route::has('register'))
                        <div class="text-center mt-4 small">
                            <span class="text-muted">No account?</span>
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-teal">Register</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle logic
            document.querySelectorAll('.toggle-password-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });
        });
    </script>
@endsection
