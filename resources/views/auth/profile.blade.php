@extends('layouts.app')

@section('content')
    <style>
        .profile-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: linear-gradient(135deg, rgba(37, 99, 235, .05), rgba(255, 255, 255, .98));
        }

        .profile-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #0f766e);
            font-size: 2rem;
            font-weight: 800;
        }

        .profile-info h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0 0 .35rem;
        }

        .profile-info p {
            margin: 0;
            color: #6b7280;
        }

        .profile-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: 2rem;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: .5rem;
            color: #111827;
        }

        .form-group input,
        .form-group textarea {
            padding: .75rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .form-actions .btn {
            padding: .75rem 1.5rem;
            font-weight: 600;
        }

        .text-muted-sm {
            font-size: .875rem;
            color: #9ca3af;
        }
    </style>

    <div class="profile-header">
        <div class="profile-avatar-large">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="profile-info">
            <h1>{{ auth()->user()->name }}</h1>
            <p>{{ auth()->user()->email }}</p>
            <p class="text-muted-sm">Member since {{ auth()->user()->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="profile-card">
            <div class="form-section">
                <h3>Personal Information</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" class="@error('name') is-invalid @enderror"
                            value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" class="@error('email') is-invalid @enderror"
                            value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="@error('phone') is-invalid @enderror"
                            value="{{ old('phone', auth()->user()->phone) }}" placeholder="+1 (555) 000-0000">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="@error('city') is-invalid @enderror"
                            value="{{ old('city', auth()->user()->city) }}" placeholder="e.g., New York">
                        @error('city')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <input type="text" id="address" name="address" class="@error('address') is-invalid @enderror"
                            value="{{ old('address', auth()->user()->address) }}" placeholder="123 Main St">
                        @error('address')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" class="@error('country') is-invalid @enderror"
                            value="{{ old('country', auth()->user()->country) }}" placeholder="e.g., USA">
                        @error('country')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </a>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf

        <div class="profile-card">
            <div class="form-section">
                <h3>Account Security</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password"
                            class="@error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New Password *</label>
                        <input type="password" id="password" name="password"
                            class="@error('password') is-invalid @enderror" autocomplete="new-password" required>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password" required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </button>
            </div>
        </div>
    </form>
@endsection
