<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\MidtransService;
use App\Services\ShippingService;
use App\Services\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    private array $couriers = [
        'jne', 'jnt', 'sicepat', 'ide', 'sap',
        'tiki', 'lion', 'anteraja', 'pos', 'ncs',
        'rex', 'rpx', 'sentral', 'star', 'wahana',
        'dse', 'ninja',
    ];

    public function __construct(
        private CartService $cart,
        private ShippingService $shipping,
        private MidtransService $midtrans,
        private AdminNotificationService $adminNotification
    ) {}

    /**
     * Memastikan produk yang dikirim sama dengan pilihan aktif di session.
     */
    private function matchesSelectedItems(
        Request $request,
        array $selectedIds
    ): bool {
        $sessionIds = $this->cart->selectedItems($request->user());

        $selectedIds = array_values(array_unique(
            array_map('intval', $selectedIds)
        ));

        $sessionIds = array_values(array_unique(
            array_map('intval', $sessionIds)
        ));

        sort($selectedIds);
        sort($sessionIds);

        return $selectedIds === $sessionIds && !empty($sessionIds);
    }

    /**
     * Menampilkan halaman checkout.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'selected' => 'required|array|min:1',
            'selected.*' => 'required|integer|distinct',
        ]);

        $user = $request->user();

        if ($warnings = $this->cart->syncWithStock($user)) {
            $request->session()->now(
                'warning',
                implode(' ', $warnings)
            );
        }

        $selectedIds = array_map(
            'intval',
            $validated['selected']
        );

        if (!$this->matchesSelectedItems($request, $selectedIds)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Pilihan produk berubah atau tidak valid. Silakan pilih ulang produk.');
        }

        $allItems = $this->cart->items($user);

        $items = array_intersect_key(
            $allItems,
            array_flip($selectedIds)
        );

        if (count($items) !== count($selectedIds)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sebagian produk yang dipilih sudah tidak tersedia.');
        }

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $defaultAddress = $addresses->first();

        return view('checkout.index', [
            'cart' => $items,
            'total' => $this->cart->total($items),
            'selectedIds' => $selectedIds,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    /**
     * Menghitung pilihan ongkir.
     */
    public function shippingCost(
        Request $request,
        ShippingService $shippingService
    ) {
        $validated = $request->validate([
            'address_id' => 'required|integer',
            'selected' => 'required|array|min:1',
            'selected.*' => 'required|integer|distinct',
            'courier' => [
                'required',
                Rule::in($this->couriers),
            ],
        ]);

        $user = $request->user();

        $selectedIds = array_map(
            'intval',
            $validated['selected']
        );

        if (!$this->matchesSelectedItems($request, $selectedIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilihan produk berubah atau tidak valid. Kembali ke keranjang dan pilih ulang produk.',
            ], 422);
        }

        $address = $user->addresses()
            ->where('id', $validated['address_id'])
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat pengiriman tidak valid.',
            ], 422);
        }

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->whereIn('product_id', $selectedIds)
            ->get();

        if (
            $cartItems->isEmpty() ||
            $cartItems->count() !== count($selectedIds)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Produk yang dipilih tidak valid.',
            ], 422);
        }

        $weight = 0;

        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->weight < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Berat salah satu produk belum diatur.',
                ], 422);
            }

            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok {$item->product->name} tidak mencukupi.",
                ], 422);
            }

            $weight += $item->product->weight * $item->quantity;
        }

        try {
            $destination = $this->findDestination(
                $shippingService,
                $address
            );

            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi alamat tidak ditemukan di RajaOngkir. Periksa kembali alamat tersimpan.',
                ], 422);
            }

            $costs = $shippingService->calculate(
                (string) $destination['id'],
                $weight,
                $validated['courier']
            );

            $options = collect($costs)->map(function ($item) {
                return [
                    'courier' => $item['code'] ?? '',
                    'courier_name' => $item['name'] ?? '',
                    'service' => $item['service'] ?? '',
                    'description' => $item['description'] ?? '',
                    'cost' => (int) ($item['cost'] ?? 0),
                    'etd' => $item['etd'] ?? '',
                ];
            })->values();

            return response()->json([
                'success' => true,
                'weight' => $weight,
                'destination' => $destination['label'] ?? '',
                'options' => $options,
            ]);

        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ongkos kirim. Periksa konfigurasi dan koneksi RajaOngkir.',
            ], 502);
        }
    }

    /**
     * Membuat pesanan dan memverifikasi ulang ongkir.
     */
    public function store(
        Request $request,
        ShippingService $shippingService
    ): RedirectResponse {
        $validated = $request->validate([
            'address_id' => 'required|integer',
            'delivery_note' => 'nullable|string|max:500',

            'selected' => 'required|array|min:1',
            'selected.*' => 'required|integer|distinct',

            'payment_method' => [
                'required',
                Rule::in(['online', 'cod']),
            ],

            'shipping_courier' => [
                'required',
                Rule::in($this->couriers),
            ],

            'shipping_service' => 'required|string|max:100',
        ]);

        $user = $request->user();

        $selectedIds = array_map(
            'intval',
            $validated['selected']
        );

        if (!$this->matchesSelectedItems($request, $selectedIds)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Pilihan produk berubah atau tidak valid. Silakan pilih ulang produk.');
        }

        $address = $user->addresses()
            ->where('id', $validated['address_id'])
            ->first();

        if (!$address) {
            return back()
                ->withInput()
                ->withErrors([
                    'address_id' => 'Alamat pengiriman tidak valid.',
                ]);
        }

        /*
         * Ambil produk yang dipilih dan hitung berat terbaru.
         */
        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->whereIn('product_id', $selectedIds)
            ->get();

        if (
            $cartItems->isEmpty() ||
            $cartItems->count() !== count($selectedIds)
        ) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Pilihan produk tidak valid atau sudah tidak tersedia.');
        }

        $weight = 0;

        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->weight < 1) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'shipping_service' => 'Berat salah satu produk belum diatur.',
                    ]);
            }

            if ($item->product->stock < $item->quantity) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', "Stok {$item->product->name} tidak mencukupi.");
            }

            $weight += $item->product->weight * $item->quantity;
        }

        /*
         * Verifikasi ulang ongkir dari API.
         * Tidak menggunakan shipping_cost dari browser.
         */
        try {
            $destination = $this->findDestination(
                $shippingService,
                $address
            );

            if (!$destination) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'shipping_service' => 'Lokasi pengiriman tidak ditemukan. Periksa alamat pelanggan.',
                    ]);
            }

            $costs = $shippingService->calculate(
                (string) $destination['id'],
                $weight,
                $validated['shipping_courier']
            );

            $selectedService = collect($costs)->first(function ($item) use ($validated) {
                return
                    strtolower($item['code'] ?? '') ===
                        strtolower($validated['shipping_courier']) &&
                    ($item['service'] ?? '') ===
                        $validated['shipping_service'];
            });

            if (!$selectedService) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'shipping_service' => 'Layanan pengiriman sudah tidak tersedia. Silakan hitung ulang ongkir dan pilih layanan lain.',
                    ]);
            }

            $shippingCost = (int) ($selectedService['cost'] ?? 0);
            $shippingCourier = $selectedService['code'];
            $shippingServiceName = $selectedService['service'];
            $shippingEtd = $selectedService['etd'] ?? null;

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'shipping_service' => 'Ongkos kirim gagal diverifikasi. Silakan coba hitung ulang.',
                ]);
        }

        try {
            $order = DB::transaction(function () use (
                $validated,
                $user,
                $selectedIds,
                $address,
                $weight,
                $shippingCost,
                $shippingCourier,
                $shippingServiceName,
                $shippingEtd
            ) {
                $cartItems = CartItem::where('user_id', $user->id)
                    ->whereIn('product_id', $selectedIds)
                    ->orderBy('product_id')
                    ->lockForUpdate()
                    ->get();

                if (
                    $cartItems->isEmpty() ||
                    $cartItems->count() !== count($selectedIds)
                ) {
                    throw new StockException(
                        'Pilihan produk tidak valid atau sudah tidak tersedia.'
                    );
                }

                $subtotal = 0;
                $currentWeight = 0;
                $rows = [];

                foreach ($cartItems as $cartItem) {
                    $product = Product::whereKey($cartItem->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new StockException(
                            'Salah satu produk sudah tidak tersedia.'
                        );
                    }

                    if ($product->stock < $cartItem->quantity) {
                        throw new StockException(
                            "Stok {$product->name} tidak mencukupi."
                        );
                    }

                    if ($product->weight < 1) {
                        throw new StockException(
                            "Berat produk {$product->name} belum diatur."
                        );
                    }

                    $currentWeight += $product->weight * $cartItem->quantity;

                    $price = (int) round((float) $product->price);

                    $subtotal += $price * $cartItem->quantity;

                    $rows[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $price,
                        'quantity' => $cartItem->quantity,
                    ];
                }

                if ($currentWeight !== $weight) {
                    throw new StockException(
                        'Keranjang atau berat produk berubah. Silakan hitung ulang ongkir.'
                    );
                }

                $fullAddress = implode(', ', array_filter([
                    $address->detail,
                    $address->village_name,
                    $address->district_name,
                    $address->city_name,
                    $address->province_name,
                    $address->postal_code,
                ]));

                $order = Order::create([
                    'user_id' => $user->id,

                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'address' => $fullAddress,

                    'province' => $address->province_name,
                    'city' => $address->city_name,
                    'district' => $address->district_name,
                    'postal_code' => $address->postal_code,
                    'address_detail' => $address->detail,

                    'delivery_note' => $validated['delivery_note'] ?? null,

                    'shipping_cost' => $shippingCost,
                    'shipping_weight' => $weight,
                    'shipping_courier' => $shippingCourier,
                    'shipping_service' => $shippingServiceName,
                    'shipping_etd' => $shippingEtd,

                    'total' => $subtotal + $shippingCost,

                    'status' => 'pending',
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'unpaid',
                ]);

                $order->items()->createMany($rows);

                CartItem::where('user_id', $user->id)
                    ->whereIn('product_id', $selectedIds)
                    ->delete();

                return $order;
            });

        } catch (StockException $e) {
            return redirect()
                ->route('cart.index')
                ->with('error', $e->getMessage());
        }

        /*
         * Transaksi database sudah berhasil commit.
         * Kirim notifikasi pesanan baru kepada seluruh admin.
         */
        try {
            $this->adminNotification->notifyOrderEvent(
                $order,
                'new_order'
            );
        } catch (Throwable $e) {
            // Kegagalan notifikasi tidak boleh membatalkan pesanan.
            report($e);
        }

        /*
         * Hapus produk yang sudah di-checkout dari pilihan session.
         * Produk lain yang belum di-checkout tetap berada di keranjang.
         */
        $this->cart->forgetSelected($user, $selectedIds);

        /*
         * Buat transaksi Midtrans untuk pembayaran online.
         */
        if ($order->payment_method !== 'cod') {
            try {
                $snapToken = $this->midtrans->createSnapToken($order);

                $order->update([
                    'snap_token' => $snapToken,
                ]);

            } catch (Throwable $e) {
                report($e);

                return redirect()
                    ->route('orders.show', $order)
                    ->with(
                        'error',
                        'Pesanan berhasil dibuat, tetapi pembayaran belum dapat disiapkan. Silakan coba lagi.'
                    );
            }
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Mencari ID tujuan RajaOngkir berdasarkan hierarki alamat.
     */
    private function findDestination(
        ShippingService $shippingService,
        Address $address
    ): ?array {
        $queries = array_filter(array_unique([
            $address->postal_code,
            $address->village_name,
        ]));

        foreach ($queries as $keyword) {
            $destinations = $shippingService->searchDestination($keyword);

            $match = collect($destinations)->first(function ($item) use ($address) {
                return
                    $this->normalizeLocation($item['province_name'] ?? '') ===
                        $this->normalizeLocation($address->province_name ?? '') &&

                    $this->normalizeLocation($item['city_name'] ?? '') ===
                        $this->normalizeLocation($address->city_name ?? '') &&

                    $this->normalizeLocation($item['district_name'] ?? '') ===
                        $this->normalizeLocation($address->district_name ?? '') &&

                    $this->normalizeLocation($item['subdistrict_name'] ?? '') ===
                        $this->normalizeLocation($address->village_name ?? '');
            });

            if ($match) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Menyamakan variasi penulisan nama wilayah.
     */
    private function normalizeLocation(?string $value): string
    {
        $value = mb_strtoupper(trim($value));

        $value = preg_replace(
            '/\b(KABUPATEN|KAB\.|KOTA ADMINISTRASI|KOTA)\b/u',
            '',
            $value
        );

        $value = preg_replace('/[^A-Z0-9]+/u', ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
    }
}
