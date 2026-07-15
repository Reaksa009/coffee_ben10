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
            text-decoration: none;
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

        .requirement-item {
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .requirement-item i {
            transition: transform 0.2s ease;
        }
        .requirement-item.text-success i {
            transform: scale(1.1);
        }
        .animate-bounce {
            animation: bounce 0.5s ease-out 1;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
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
                        <span class="brand-mark"><i class="bi bi-cup-hot"></i></span>
                        <span>
                            <span class="brand-name">Coffee Ben10</span>
                            <span class="brand-small">Coffee shop POS</span>
                        </span>
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
                            
                            <!-- Password Requirements Checklist -->
                            <div class="card mt-2 border-0 bg-body-tertiary shadow-sm p-3 password-checklist-card">
                                <h6 class="card-subtitle mb-2 text-muted fw-bold small"><i class="bi bi-shield-lock me-1"></i> Password Requirements</h6>
                                <ul class="list-unstyled mb-0 small text-secondary">
                                    <li id="req-lowercase" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">lowercase</strong> letter
                                    </li>
                                    <li id="req-uppercase" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">uppercase</strong> letter
                                    </li>
                                    <li id="req-symbol-at" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">@</strong> symbol
                                    </li>
                                    <li id="req-symbol-dollar" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">$</strong> symbol
                                    </li>
                                    <li id="req-symbol-hash" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">#</strong> symbol
                                    </li>
                                    <li id="req-digits" class="requirement-item text-danger py-1 transition-all">
                                        <i class="bi bi-x-circle-fill text-danger me-1"></i> Contains <strong class="text-dark">8 or more digits</strong>
                                    </li>
                                </ul>
                                
                                <!-- Sign indicating all conditions met -->
                                <div id="req-all-met" class="mt-3 p-2 bg-success-subtle text-success border border-success-subtle rounded d-none align-items-center justify-content-center transition-all">
                                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                    <span class="fw-bold small">All conditions have been met!</span>
                                </div>
                            </div>
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqAt = document.getElementById('req-symbol-at');
            const reqDollar = document.getElementById('req-symbol-dollar');
            const reqHash = document.getElementById('req-symbol-hash');
            const reqDigits = document.getElementById('req-digits');
            const reqAllMet = document.getElementById('req-all-met');

            function updateRequirement(element, isValid) {
                const icon = element.querySelector('i');
                if (isValid) {
                    element.classList.remove('text-danger');
                    element.classList.add('text-success');
                    if (icon) {
                        icon.className = 'bi bi-check-circle-fill text-success me-1';
                    }
                } else {
                    element.classList.remove('text-success');
                    element.classList.add('text-danger');
                    if (icon) {
                        icon.className = 'bi bi-x-circle-fill text-danger me-1';
                    }
                }
            }

            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                
                const hasLowercase = /[a-z]/.test(val);
                const hasUppercase = /[A-Z]/.test(val);
                const hasAt = val.includes('@');
                const hasDollar = val.includes('$');
                const hasHash = val.includes('#');
                
                // count digits
                const digitCount = (val.match(/\d/g) || []).length;
                const has8Digits = digitCount >= 8;

                updateRequirement(reqLowercase, hasLowercase);
                updateRequirement(reqUppercase, hasUppercase);
                updateRequirement(reqAt, hasAt);
                updateRequirement(reqDollar, hasDollar);
                updateRequirement(reqHash, hasHash);
                updateRequirement(reqDigits, has8Digits);

                if (hasLowercase && hasUppercase && hasAt && hasDollar && hasHash && has8Digits) {
                    if (reqAllMet.classList.contains('d-none')) {
                        reqAllMet.classList.remove('d-none');
                        reqAllMet.classList.add('d-flex');
                        reqAllMet.classList.add('animate-bounce');
                        setTimeout(() => {
                            reqAllMet.classList.remove('animate-bounce');
                        }, 500);
                    }
                } else {
                    reqAllMet.classList.remove('d-flex');
                    reqAllMet.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
