<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', Rule::in(['admin', 'player'])],
        ]);

        $user = User::create([
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => 'active',
        ]);

        ActivityLogger::log($request->user()->id, "Admin added user: {$user->username} (role: {$user->role})");

        return back()->with('success', "User '{$user->username}' created successfully.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)],
            'role' => ['required', Rule::in(['admin', 'player'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'two_factor_enabled' => ['sometimes', 'boolean'],
        ]);

        $data = collect($validated)->except('password')->toArray();

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        if ($request->has('two_factor_enabled')) {
            $data['two_factor_enabled'] = $request->boolean('two_factor_enabled');
            if ($data['two_factor_enabled'] && ! $user->two_factor_secret) {
                $data['two_factor_secret'] = \App\Services\TwoFactorService::generateSecret();
            }
        }

        $user->update($data);

        ActivityLogger::log($request->user()->id, "Admin updated user ID: {$user->id} ({$user->username})");

        return back()->with('success', "User '{$user->username}' updated.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $username = $user->username;
        $user->delete();

        ActivityLogger::log($request->user()->id, "Admin deleted user: {$username}");

        return back()->with('success', 'User deleted.');
    }
}
