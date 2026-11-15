<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Menampilkan formulir tambah alamat.
     */
    public function create(): View
    {
        return view('addresses.create');
    }

    /**
     * Menyimpan alamat baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAddress($request);

        $user = $request->user();

        DB::transaction(function () use ($user, $data) {
            $lockedUser = $user->newQuery()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $isFirstAddress = !$lockedUser->addresses()->exists();

            $lockedUser->addresses()->create([
                ...$data,
                'is_default' => $isFirstAddress,
            ]);
        });

        return redirect()
            ->route('profile')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit alamat.
     */
    public function edit(Address $address): View
    {
        abort_unless($address->user_id === auth()->id(), 403);

        return view('addresses.edit', compact('address'));
    }

    /**
     * Memperbarui alamat.
     */
    public function update(
        Request $request,
        Address $address
    ): RedirectResponse {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        $data = $this->validateAddress($request);

        DB::transaction(function () use ($address, $data) {
            $lockedAddress = Address::where('user_id', $address->user_id)
                ->lockForUpdate()
                ->findOrFail($address->id);

            $lockedAddress->update($data);
        });

        return redirect()
            ->route('profile')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Menghapus alamat.
     * Jika alamat utama dihapus, alamat tertua menjadi alamat utama.
     */
    public function destroy(
        Request $request,
        Address $address
    ): RedirectResponse {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        DB::transaction(function () use ($request, $address) {
            $user = $request->user();

            $lockedUser = $user->newQuery()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $lockedAddress = $lockedUser->addresses()
                ->lockForUpdate()
                ->findOrFail($address->id);

            $wasDefault = $lockedAddress->is_default;

            $lockedAddress->delete();

            if ($wasDefault) {
                $newDefault = $lockedUser->addresses()
                    ->oldest()
                    ->lockForUpdate()
                    ->first();

                if ($newDefault) {
                    $newDefault->update([
                        'is_default' => true,
                    ]);
                }
            }
        });

        return redirect()
            ->route('profile')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Menjadikan alamat sebagai alamat utama.
     */
    public function setDefault(
        Request $request,
        Address $address
    ): RedirectResponse {
        abort_unless(
            $address->user_id === $request->user()->id,
            403
        );

        DB::transaction(function () use ($request, $address) {
            $user = $request->user();

            $lockedUser = $user->newQuery()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $lockedAddress = $lockedUser->addresses()
                ->lockForUpdate()
                ->findOrFail($address->id);

            $lockedUser->addresses()
                ->update(['is_default' => false]);

            $lockedAddress->update([
                'is_default' => true,
            ]);
        });

        return redirect()
            ->route('profile')
            ->with('success', 'Alamat utama berhasil diubah.');
    }

    /**
     * Validasi data alamat untuk store dan update.
     */
    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => [
                'required',
                'string',
                'max:50',
            ],

            'recipient_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]{8,20}$/',
            ],

            'province_code' => [
                'required',
                'string',
                'max:20',
            ],

            'province_name' => [
                'required',
                'string',
                'max:255',
            ],

            'city_code' => [
                'required',
                'string',
                'max:20',
            ],

            'city_name' => [
                'required',
                'string',
                'max:255',
            ],

            'district_code' => [
                'required',
                'string',
                'max:20',
            ],

            'district_name' => [
                'required',
                'string',
                'max:255',
            ],

            'village_code' => [
                'required',
                'string',
                'max:20',
            ],

            'village_name' => [
                'required',
                'string',
                'max:255',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'detail' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);
    }

    /**
     * Mengambil daftar provinsi.
     */
    public function provinces(): JsonResponse
    {
        return $this->fetchWilayah('provinces.json');
    }

    /**
     * Mengambil daftar kota/kabupaten berdasarkan provinsi.
     */
    public function regencies(string $code): JsonResponse
    {
        return $this->fetchWilayah(
            'regencies/' . rawurlencode($code) . '.json'
        );
    }

    /**
     * Mengambil daftar kecamatan berdasarkan kota/kabupaten.
     */
    public function districts(string $code): JsonResponse
    {
        return $this->fetchWilayah(
            'districts/' . rawurlencode($code) . '.json'
        );
    }

    /**
     * Mengambil daftar desa/kelurahan berdasarkan kecamatan.
     */
    public function villages(string $code): JsonResponse
    {
        return $this->fetchWilayah(
            'villages/' . rawurlencode($code) . '.json'
        );
    }

    /**
     * Menghubungkan Laravel dengan API Wilayah.id.
     */
    private function fetchWilayah(string $endpoint): JsonResponse
    {
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->get("https://wilayah.id/api/{$endpoint}");

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Gagal mengambil data wilayah dari server.',
                ], 502);
            }

            $data = $response->json();

            if (!is_array($data)) {
                return response()->json([
                    'message' => 'Format data wilayah tidak valid.',
                ], 502);
            }

            return response()->json($data);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Server tidak dapat terhubung ke API wilayah.',
            ], 502);
        }
    }
}
