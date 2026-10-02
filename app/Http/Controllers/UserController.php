<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with('roles')
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::query()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $user->syncRoles([
            $validated['role'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = Role::query()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Cegah user mengubah role/status aktif dirinya sendiri dari halaman ini
        if ($user->is($request->user())) {
            unset($validated['role'], $validated['is_active']);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nip' => $validated['nip'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (array_key_exists('is_active', $validated)) {
            $user->is_active = (bool) $validated['is_active'];
        }

        $user->save();

        if (isset($validated['role'])) {
            $user->syncRoles([
                $validated['role'],
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->pengajuanSurat()->exists() || $user->penandatangananSurat()->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena memiliki riwayat pengajuan.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}