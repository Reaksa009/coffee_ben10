@extends('layouts.app')

@section('content')
    <style>
        .auth-wrap {
            min-height: calc(100vh - 7rem);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-panel {
            width: 100%;
            max-width: 920px;
            display: grid;
            grid-template-columns: minmax(0, .95fr) minmax(320px, .9fr);
            overflow: hidden;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .12);
        }

        .auth-photo {
            min-height: 500px;
            position: relative;
            background-image:
                linear-gradient(180deg, rgba(17, 24, 39, .18), rgba(17, 24, 39, .62)),
                url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }

        .auth-photo-content {
            position: absolute;
            left: 1.5rem;
            right: 1.5rem;
            bottom: 1.5rem;
            color: #fff;
        }

        .auth-photo-title {
            font-size: clamp(1.45rem, 2.4vw, 2rem);
            font-weight: 800;
            line-height: 1.05;
            margin: 0 0 .75rem;
        }

        .auth-photo-text {
            max-width: 440px;
            color: rgba(255, 255, 255, .86);
            margin: 0 0 1rem;
        }

        .auth-feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            max-width: 520px;
        }

        .auth-feature-group {
            padding: .75rem;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: .5rem;
            background: rgba(17, 24, 39, .34);
            backdrop-filter: blur(8px);
        }

        .auth-feature-title {
            display: flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: .5rem;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
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
            padding: .22rem .45rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .16);
            color: rgba(255, 255, 255, .9);
            font-size: .72rem;
            line-height: 1.2;
        }

        .auth-form-pane {
            display: flex;
            align-items: center;
            padding: clamp(1.25rem, 3vw, 2.25rem);
        }

        .auth-form-box {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1.35rem;
            font-weight: 800;
            color: #111827;
        }

        .auth-form-box .form-control {
            min-height: 42px;
        }

        @media (max-width: 991.98px) {
            .auth-panel {
                grid-template-columns: 1fr;
            }

            .auth-photo {
                min-height: 190px;
            }

            .auth-feature-grid {
                display: none;
            }
        }
    </style>

    <div class="auth-wrap">
        <section class="auth-panel">
            <div class="auth-photo">
                <div class="auth-photo-content">
                    <h2 class="auth-photo-title">Start the day with a clear POS.</h2>
                    <p class="auth-photo-text">Create an account to track sales, inventory, orders, and KHQR payments for your coffee shop.</p>
                    <div class="auth-feature-grid">
                        <div class="auth-feature-group">
                            <div class="auth-feature-title"><i class="bi bi-check2-circle"></i> Essential</div>
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
                            <div class="auth-feature-title"><i class="bi bi-stars"></i> Advanced</div>
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
                    <div class="auth-brand">
                        <span class="soft-icon text-primary bg-primary bg-opacity-10"><i class="bi bi-cup-hot fs-4"></i></span>
                        <span>{{ config('app.name', 'POS') }}</span>
                    </div>

                    <h1 class="h4 fw-bold mb-1">Create account</h1>
                    <p class="text-muted mb-3">Register and open the dashboard.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-speedometer2 me-1"></i> Register to Dashboard
                        </button>
                    </form>

                    @if(Route::has('login'))
                        <div class="text-center mt-4 small">
                            <span class="text-muted">Already have an account?</span>
                            <a href="{{ route('login') }}">Login</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
