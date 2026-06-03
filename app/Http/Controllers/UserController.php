<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query();

        if (config('database.default') === 'mongodb') {
            $users->orderBy('role')->orderBy('name');
        } else {
            $users->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'manager' THEN 2 ELSE 3 END")
                ->orderBy('name');
        }

        return view('users.index', [
            'users' => $users->paginate(15),
            'roles' => $this->roles(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(array_keys($this->roles()))],
        ]);

        if ($this->wouldRemoveLastAdmin($user, $data['role'])) {
            return redirect()
                ->route('users.index')
                ->with('error', 'At least one admin account is required.');
        }

        $user->update(['role' => $data['role']]);

        return redirect()
            ->route('users.index')
            ->with('success', "{$user->name}'s permission was updated.");
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "{$user->name}'s password was changed.");
    }

    private function roles(): array
    {
        return [
            User::ROLE_CASHIER => 'Cashier',
            User::ROLE_MANAGER => 'Manager',
            User::ROLE_ADMIN => 'Admin',
        ];
    }

    private function wouldRemoveLastAdmin(User $user, string $newRole): bool
    {
        return $user->role === User::ROLE_ADMIN
            && $newRole !== User::ROLE_ADMIN
            && User::where('role', User::ROLE_ADMIN)->count() <= 1;
    }
}
