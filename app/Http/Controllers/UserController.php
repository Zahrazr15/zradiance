<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Daftar pengguna + pencarian + filter peran.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->withCount('orders')
            ->when($request->q, function ($query, $search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, fn ($query, $role) => $query->where('role', $role))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Form edit pengguna.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Perbarui pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'user'])],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Admin tidak boleh menurunkan perannya sendiri (supaya tidak terkunci).
        if ($user->id === $request->user()->id && $validated['role'] !== 'admin') {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Kamu tidak bisa mengubah peranmu sendiri menjadi user.']);
        }

        // Password kosong = tidak diganti.
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Kamu tidak bisa menghapus akunmu sendiri.']);
        }

        // Tabel orders memakai onDelete('cascade'): menghapus user akan ikut
        // menghapus semua pesanannya. Diblok supaya riwayat pesanan aman.
        if ($user->orders()->exists()) {
            return back()->withErrors([
                'user' => "Pengguna \"{$user->name}\" punya {$user->orders()->count()} pesanan, jadi tidak bisa dihapus.",
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}