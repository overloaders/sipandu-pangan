<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\Role;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Web\StoreUserRequest;
use App\Http\Requests\Web\UpdateUserRequest;

class UserController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = User::with(['unit', 'roles']);

        // Non-super-admin only sees users from their unit
        if (!auth()->user()->hasRole('super-admin')) {
            $query->where('unit_id', auth()->user()->unit_id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Non-super-admin only assigns users to their own unit
        if (auth()->user()->hasRole('super-admin')) {
            $units = Unit::orderBy('nama_unit')->get();
        } else {
            $units = Unit::where('id', auth()->user()->unit_id)->get();
        }

        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('units', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        // Non-super-admin can only create users in their own unit
        if (!auth()->user()->hasRole('super-admin')) {
            $data['unit_id'] = auth()->user()->unit_id;
        }

        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids']);

        $user = User::create($data);
        $user->roles()->sync($roleIds);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat');
    }

    public function show(User $user)
    {
        // Check unit access
        if (!$this->canAccessUnit($user->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke user ini');
        }

        $user->load('unit', 'roles');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Check unit access
        if (!$this->canAccessUnit($user->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke user ini');
        }

        $user->load('roles');

        if (auth()->user()->hasRole('super-admin')) {
            $units = Unit::orderBy('nama_unit')->get();
        } else {
            $units = Unit::where('id', auth()->user()->unit_id)->get();
        }

        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'units', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Check unit access
        if (!$this->canAccessUnit($user->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke user ini');
        }

        $data = $request->validated();

        // Non-super-admin cannot change unit
        if (!auth()->user()->hasRole('super-admin')) {
            unset($data['unit_id']);
        }

        $roleIds = $data['role_ids'] ?? [];
        unset($data['role_ids']);

        $user->update($data);
        $user->roles()->sync($roleIds);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Cannot delete yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        // Cannot delete super-admin
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Tidak dapat menghapus akun Super Admin');
        }

        // Check unit access
        if (!$this->canAccessUnit($user->unit_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke user ini');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }
}
