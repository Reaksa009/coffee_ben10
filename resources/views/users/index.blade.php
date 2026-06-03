@extends('layouts.app')

@section('content')
    @php
        $roleClass = function ($role) {
            return match ($role) {
                \App\Models\User::ROLE_ADMIN => 'danger',
                \App\Models\User::ROLE_MANAGER => 'primary',
                \App\Models\User::ROLE_CASHIER => 'success',
                default => 'secondary',
            };
        };
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Users</h1>
            <p class="page-subtitle">Set cashier permissions and reset staff passwords.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Staff Accounts</h2>
            <span class="badge text-bg-light">{{ $users->total() }} total</span>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">No users found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Current Role</th>
                            <th>Set Permission</th>
                            <th>Change Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $roleClass($user->role) }}">
                                        {{ $roles[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td style="min-width: 260px;">
                                    <form method="POST" action="{{ route('users.role.update', $user) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select form-select-sm" aria-label="Set permission for {{ $user->name }}">
                                            @foreach($roles as $value => $label)
                                                <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-shield-check me-1"></i> Save
                                        </button>
                                    </form>
                                </td>
                                <td style="min-width: 360px;">
                                    <form method="POST" action="{{ route('users.password.update', $user) }}" class="row g-2 align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <div class="col">
                                            <input type="password" name="password" class="form-control form-control-sm" placeholder="New password" autocomplete="new-password" required>
                                        </div>
                                        <div class="col">
                                            <input type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Confirm password" autocomplete="new-password" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-key me-1"></i> Change
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="d-flex justify-content-center mt-4">{{ $users->links() }}</div>
@endsection
