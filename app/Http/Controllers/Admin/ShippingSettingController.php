<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingSetting;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingSettingController extends Controller
{
    public function searchLocation(
        Request $request,
        ShippingService $shippingService
    ): JsonResponse {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        try {
            $results = $shippingService->searchDestination(
                $validated['keyword']
            );

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Pencarian lokasi gagal. Periksa konfigurasi API RajaOngkir.',
            ], 502);
        }
    }
    public function edit(): View
    {
        $shippingSetting = ShippingSetting::first();

        return view('admin.shipping-settings.edit', compact('shippingSetting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'province_name' => ['required', 'string', 'max:255'],
            'province_code' => ['nullable', 'string', 'max:50'],
            'city_name' => ['required', 'string', 'max:255'],
            'city_code' => ['nullable', 'string', 'max:50'],
            'district_name' => ['required', 'string', 'max:255'],
            'district_code' => ['nullable', 'string', 'max:50'],
            'rajaongkir_origin_id' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);

        $shippingSetting = ShippingSetting::first();

        if (!$shippingSetting) {
            ShippingSetting::create($validated);
        } else {
            $shippingSetting->update($validated);
        }

        return redirect()
            ->route('admin.shipping-settings.edit')
            ->with('success', 'Pengaturan lokasi pengiriman berhasil disimpan.');
    }
}
