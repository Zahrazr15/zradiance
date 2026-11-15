<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil dan daftar alamat pengguna.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('profile', compact('addresses'));
    }

    /**
     * Menampilkan halaman edit profil.
     */
    public function edit(): View
    {
        return view('profile-edit');
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]{8,20}$/',
            ],
        ]);

        $user->update($data);

        return redirect()
            ->route('profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Mengunggah atau mengganti foto profil.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user = $request->user();
        $oldPhoto = $user->avatar;

        $newPhoto = $request->file('photo')
            ->store('avatars', 'public');

        if (!$newPhoto) {
            return back()->withErrors([
                'photo' => 'Foto profil gagal disimpan.',
            ]);
        }

        try {
            $user->update([
                'avatar' => $newPhoto,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPhoto);

            throw $e;
        }

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Menghapus foto profil.
     */
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $oldPhoto = $user->avatar;

        if ($oldPhoto) {
            $user->update([
                'avatar' => null,
            ]);

            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()
            ->route('profile')
            ->with('success', 'Foto profil dihapus.');
    }
}
