<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('roles')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $term = $request->string('cari')->trim()->toString();
                $query->where(function ($search) use ($term) {
                    $search->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->status === 'active'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::where('code', $data['role'])->firstOrFail();

        DB::transaction(function () use ($data, $role) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'],
            ]);

            $this->syncRole($user, $role);
        });

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $newRole = Role::where('code', $data['role'])->firstOrFail();

        if ($user->is(auth()->user()) && ($data['role'] !== 'admin' || ! $data['is_active'])) {
            return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan atau mengubah peran akun sendiri.');
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $data['role'], $data['is_active'])) {
            return back()->withInput()->with('error', 'Setidaknya satu akun administrator aktif harus tetap tersedia.');
        }

        DB::transaction(function () use ($data, $newRole, $user) {
            $attributes = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'],
            ];

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            $user->update($attributes);
            $this->syncRole($user, $newRole);
        });

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $newStatus = ! $user->is_active;
        $role = $user->roles()->first();

        if (! $newStatus && $role?->code === 'admin' && $this->isLastActiveAdmin($user)) {
            return back()->with('error', 'Setidaknya satu akun administrator aktif harus tetap tersedia.');
        }

        $user->update(['is_active' => $newStatus]);

        return back()->with('success', $newStatus ? 'Akun pengguna telah diaktifkan.' : 'Akun pengguna telah dinonaktifkan.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($this->isLastActiveAdmin($user)) {
            return back()->with('error', 'Akun administrator aktif terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }

    private function syncRole(User $user, Role $role): void
    {
        $user->roles()->sync([
            $role->id => [
                'is_active' => true,
                'assigned_at' => now(),
                'ended_at' => null,
            ],
        ]);
    }

    private function wouldRemoveLastActiveAdmin(User $user, string $newRole, bool $isActive): bool
    {
        return $user->is_active
            && $user->hasRole('admin')
            && ($newRole !== 'admin' || ! $isActive)
            && $this->isLastActiveAdmin($user);
    }

    private function isLastActiveAdmin(User $user): bool
    {
        return $user->is_active
            && $user->hasRole('admin')
            && User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($query) => $query->where('code', 'admin'))
                ->count() <= 1;
    }
}
