<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staffs = User::query()
            ->where('school_id', auth()->user()->school_id)
            ->whereIn('role', UserRole::schoolRoles())
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(15);

        $roles = UserRole::schoolRoles();

        return view('admin.staff.index', compact('staffs', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(UserRole::schoolRoles())],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'school_id' => auth()->user()->school_id,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staf berhasil ditambahkan.');
    }

    public function edit(User $staff)
    {
        if ($staff->school_id !== auth()->user()->school_id || !in_array($staff->role, UserRole::schoolRoles())) {
            abort(403);
        }

        $roles = UserRole::schoolRoles();
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->school_id !== auth()->user()->school_id || !in_array($staff->role, UserRole::schoolRoles())) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(UserRole::schoolRoles())],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staf berhasil diperbarui.');
    }

    public function destroy(User $staff)
    {
        if ($staff->school_id !== auth()->user()->school_id || !in_array($staff->role, UserRole::schoolRoles())) {
            abort(403);
        }
        
        if ($staff->id === auth()->id()) {
            return redirect()->route('admin.staff.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staf berhasil dihapus.');
    }
}
