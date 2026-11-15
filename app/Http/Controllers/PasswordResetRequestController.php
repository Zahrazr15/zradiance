<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetRequestController extends Controller
{
    /* ------------------------- SISI USER (publik) ------------------------- */

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'note'     => 'nullable|string|max:255',
        ]);

        // Hanya akun user biasa. Admin direset lewat menu Pengguna.
        $user = User::where('email', $data['email'])->where('role', 'user')->first();

        if ($user) {
            // Satu permintaan pending per user; kirim ulang = menimpa yang lama.
            PasswordResetRequest::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'pending'],
                [
                    'new_password' => Hash::make($data['password']),
                    'note'         => $data['note'] ?? null,
                ]
            );
        }

        // Pesan sama baik email ada maupun tidak, supaya email tidak bisa ditebak-tebak.
        return redirect()
            ->route('password.request')
            ->with('success', 'Permintaan terkirim. Tunggu persetujuan admin, lalu login dengan password baru.');
    }

    /* ------------------------------ SISI ADMIN ----------------------------- */

    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $requests = PasswordResetRequest::with(['user', 'handler'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.password-resets.index', compact('requests', 'status'));
    }

    public function approve(Request $request, PasswordResetRequest $resetRequest): RedirectResponse
    {
        if ($resetRequest->status !== 'pending') {
            return back()->withErrors(['reset' => 'Permintaan ini sudah diproses.']);
        }

        DB::transaction(function () use ($request, $resetRequest) {
            // Query builder dipakai agar hash tidak di-hash dua kali oleh cast 'hashed'.
            DB::table('users')->where('id', $resetRequest->user_id)->update([
                'password'       => $resetRequest->new_password,
                'remember_token' => Str::random(60),
                'updated_at'     => now(),
            ]);

            // Paksa logout semua sesi lama user tsb (SESSION_DRIVER=database).
            DB::table('sessions')->where('user_id', $resetRequest->user_id)->delete();

            $resetRequest->update([
                'status'       => 'approved',
                'new_password' => null,
                'handled_by'   => $request->user()->id,
                'handled_at'   => now(),
            ]);
        });

        return back()->with('success', "Reset password untuk {$resetRequest->user->name} disetujui.");
    }

    public function reject(Request $request, PasswordResetRequest $resetRequest): RedirectResponse
    {
        if ($resetRequest->status !== 'pending') {
            return back()->withErrors(['reset' => 'Permintaan ini sudah diproses.']);
        }

        $resetRequest->update([
            'status'       => 'rejected',
            'new_password' => null,
            'handled_by'   => $request->user()->id,
            'handled_at'   => now(),
        ]);

        return back()->with('success', "Permintaan reset password {$resetRequest->user->name} ditolak.");
    }
}