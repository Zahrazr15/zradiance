@extends('layouts.user')

@section('title', 'Checkout Pesanan - Beauty Store')

@push('styles')
<style>
    .checkout-container {
        max-width: 1240px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .checkout-header {
        margin-bottom: 28px;
    }

    .checkout-header h1 {
        font-size: 32px;
        color: var(--c-ink-heading);
        margin-bottom: 6px;
    }

    .checkout-header p {
        color: var(--c-ink-soft);
        font-size: 15px;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 32px;
        align-items: start;
    }

    .checkout-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 32px;
        box-shadow: var(--shadow-sm);
    }

    .checkout-card h2 {
        font-size: 20px;
        color: var(--c-ink-heading);
        margin-bottom: 22px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--c-border-subtle);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .address-preview-box {
        background: #FCF8F6;
        border: 1.5px dashed var(--c-border);
        border-radius: var(--radius-md);
        padding: 18px 20px;
        margin-bottom: 20px;
        font-size: 14px;
        line-height: 1.6;
    }

    .address-preview-box strong {
        color: var(--c-ink-heading);
        font-size: 15px;
    }

    .shipping-button {
        width: 100%;
        padding: 13px 20px;
        border: 0;
        border-radius: var(--radius-pill);
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 14px var(--font);
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(233, 129, 158, 0.35);
    }
    .shipping-button:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-1px);
    }
    .shipping-button:disabled { opacity: 0.6; cursor: not-allowed; }

    .shipping-message {
        margin-top: 14px;
        padding: 14px 18px;
        border-radius: var(--radius-sm);
        background: #FCF8F6;
        font-size: 13.5px;
        line-height: 1.5;
        border: 1px solid var(--c-border-subtle);
    }
    .shipping-message.error-message {
        background: var(--c-danger-soft);
        color: var(--c-danger);
        border-color: rgba(192, 57, 43, 0.2);
    }
    .shipping-message.success-message {
        background: var(--c-success-soft);
        color: var(--c-success);
        border-color: rgba(30, 142, 90, 0.2);
    }

    .payment-option-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 8px;
    }

    .payment-option {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-md);
        background: #fff;
        cursor: pointer;
        transition: var(--transition);
    }
    .payment-option:hover {
        border-color: var(--c-blush);
        background: #FCF8F6;
    }
    .payment-option input {
        margin-top: 4px;
        width: 18px;
        height: 18px;
        accent-color: var(--c-pink-strong);
        flex-shrink: 0;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid var(--c-border-subtle);
    }
    .product-item:last-of-type { border-bottom: 0; }

    .product-name {
        font-size: 15px;
        font-weight: 700;
        color: var(--c-ink-heading);
    }
    .product-quantity {
        font-size: 13px;
        color: var(--c-ink-soft);
    }
    .product-price {
        font-size: 15px;
        font-weight: 800;
        color: var(--c-ink-heading);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 14px;
        font-size: 14.5px;
        color: var(--c-ink-soft);
    }

    .total-checkout-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 2px dashed var(--c-border);
    }
    .total-checkout-row strong {
        font-size: 16px;
        color: var(--c-ink-heading);
    }
    .grand-total-amount {
        font-size: 26px;
        font-weight: 800;
        color: var(--c-pink-strong);
    }

    .checkout-button {
        width: 100%;
        margin-top: 24px;
        padding: 15px;
        border: 0;
        border-radius: var(--radius-pill);
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 15px var(--font);
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 14px rgba(233, 129, 158, 0.4);
    }
    .checkout-button:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(233, 129, 158, 0.5);
    }
    .checkout-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    @media (max-width: 960px) {
        .checkout-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .checkout-container { padding: 0 16px 60px; margin-top: 20px; }
        .checkout-card { padding: 22px 18px; }
    }
</style>
@endpush

@section('content')
<div class="checkout-container container">

    <div class="back-bar">
        <x-back-button :href="route('cart.index')" label="← Kembali ke Keranjang Belanja" />
    </div>

    <div class="checkout-header">
        <h1>Checkout & Pembayaran</h1>
        <p>Lengkapi tujuan pengiriman dan pilih ekspedisi kurir tepercaya.</p>
    </div>

    @if ($errors->any())
        <div class="alert-error error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-grid">

        {{-- INFORMASI PENGIRIMAN --}}
        <div class="checkout-card card">
            <h2>📦 Alamat & Pengiriman</h2>

            @if ($addresses->isNotEmpty())
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    @foreach ($selectedIds as $id)
                        <input type="hidden" name="selected[]" value="{{ $id }}">
                    @endforeach

                    <div class="form-group">
                        <label for="address_id">Pilih Alamat Tujuan</label>
                        <select id="address_id" name="address_id" required>
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @selected(old('address_id', $defaultAddress?->id) == $address->id)
                                >
                                    {{ $address->label }}
                                    @if ($address->is_default) (Alamat Utama) @endif
                                    - {{ $address->recipient_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="address-preview" class="address-preview-box">
                        <strong id="preview-name"></strong><br>
                        <span id="preview-phone" style="color:var(--c-ink-soft);"></span>
                        <p id="preview-detail" style="margin:8px 0 0;color:var(--c-ink);"></p>
                    </div>

                    <div style="margin-bottom:24px;">
                        <a href="{{ route('addresses.create') }}" class="btn btn-outline btn-sm">
                            + Tambah Alamat Baru
                        </a>
                    </div>

                    {{-- PILIH KURIR RAJAONGKIR --}}
                    <div class="form-group">
                        <label for="courier">Pilih Jasa Ekspedisi (Kurir)</label>
                        <select id="courier">
                            <option value="">-- Pilih Ekspedisi --</option>
                            <option value="jne">JNE Express</option>
                            <option value="jnt">J&T Express</option>
                            <option value="sicepat">SiCepat Ekspres</option>
                            <option value="ide">ID Express</option>
                            <option value="sap">SAP Express</option>
                            <option value="tiki">TIKI</option>
                            <option value="lion">Lion Parcel</option>
                            <option value="anteraja">AnterAja</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="ninja">Ninja Xpress</option>
                            <option value="wahana">Wahana Prestasi Logistik</option>
                        </select>
                    </div>

                    <button type="button" id="calculate-shipping" class="shipping-button">
                        Hitung Tarif Pengiriman
                    </button>

                    <div id="shipping-message" class="shipping-message" hidden></div>

                    {{-- PILIH LAYANAN LAYANAN TERSEDIA --}}
                    <div class="form-group" id="service-group" style="display:none; margin-top:20px;">
                        <label for="shipping-service-option">Pilih Paket Layanan Pengiriman</label>
                        <select id="shipping-service-option">
                            <option value="">-- Pilih Layanan --</option>
                        </select>
                    </div>

                    {{-- NILAI TERSEMBUNYI UNTUK CONTROLLER --}}
                    <input type="hidden" name="shipping_courier" id="shipping_courier">
                    <input type="hidden" name="shipping_service" id="shipping_service">
                    <input type="hidden" name="shipping_cost" id="shipping_cost">
                    <input type="hidden" name="shipping_etd" id="shipping_etd">

                    <div class="form-group" style="margin-top:24px;">
                        <label for="delivery_note">Catatan Khusus untuk Kurir (Opsional)</label>
                        <textarea
                            id="delivery_note"
                            name="delivery_note"
                            rows="3"
                            placeholder="Contoh: Rumah pagar cokelat, titipkan di pos satpam jika tidak ada orang..."
                        >{{ old('delivery_note') }}</textarea>
                        @error('delivery_note')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- METODE PEMBAYARAN --}}
                    <div class="form-group" style="margin-top:28px;">
                        <label><strong>Pilih Metode Pembayaran</strong></label>

                        <div class="payment-option-list">
                            <label class="payment-option">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="online"
                                    {{ old('payment_method', 'online') === 'online' ? 'checked' : '' }}
                                    required
                                >
                                <span>
                                    <strong>💳 Pembayaran Digital (Midtrans)</strong><br>
                                    <small style="color:var(--c-ink-soft);">
                                        Bebas biaya admin: QRIS (GoPay, OVO, ShopeePay), Virtual Account BCA/Mandiri/BRI/BNI, dan Kartu Kredit.
                                    </small>
                                </span>
                            </label>

                            <label class="payment-option">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    {{ old('payment_method') === 'cod' ? 'checked' : '' }}
                                    required
                                >
                                <span>
                                    <strong>💵 Bayar di Tempat (COD)</strong><br>
                                    <small style="color:var(--c-ink-soft);">
                                        Lakukan pembayaran tunai kepada kurir saat pesanan tiba di alamat Anda.
                                    </small>
                                </span>
                            </label>
                        </div>

                        @error('payment_method')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="checkout-button" id="submit-checkout" disabled>
                        Konfirmasi & Buat Pesanan
                    </button>
                </form>
            @else
                <div class="alert-error error" style="margin-bottom:20px;">
                    Anda belum memiliki alamat pengiriman yang tersimpan. Tambahkan alamat pengiriman terlebih dahulu untuk melanjutkan checkout.
                </div>
                <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                    + Tambah Alamat Sekarang
                </a>
            @endif
        </div>

        {{-- RINGKASAN PESANAN --}}
        <div class="checkout-card card" style="position:sticky;top:100px;">
            <h2>🛍️ Ringkasan Belanja</h2>

            <div style="margin-bottom:16px;">
                @foreach ($cart as $item)
                    <div class="product-item">
                        <div class="product-info">
                            <div class="product-name">{{ $item['name'] }}</div>
                            <div class="product-quantity">
                                {{ $item['quantity'] }} × Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="product-price">
                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="summary-row">
                <span>Subtotal Produk</span>
                <strong id="product-subtotal" data-value="{{ $total }}">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Biaya Pengiriman</span>
                <strong id="shipping-display">Belum dihitung</strong>
            </div>

            <div class="total-checkout-row total">
                <strong>Total Pembayaran</strong>
                <span class="grand-total-amount" id="grand-total">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>

            <small style="display:block; margin-top:14px; color:var(--c-ink-soft); line-height:1.5;">
                ℹ️ Total tagihan akhir akan diperbarui otomatis saat paket kurir telah Anda tentukan.
            </small>
        </div>

    </div>
</div>

@if ($addresses->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addresses = @json($addresses->keyBy('id'));
    const selectedIds = @json($selectedIds);

    const addressSelect = document.getElementById('address_id');
    const courierSelect = document.getElementById('courier');
    const calculateButton = document.getElementById('calculate-shipping');
    const serviceGroup = document.getElementById('service-group');
    const serviceSelect = document.getElementById('shipping-service-option');
    const message = document.getElementById('shipping-message');

    const shippingCourier = document.getElementById('shipping_courier');
    const shippingService = document.getElementById('shipping_service');
    const shippingCost = document.getElementById('shipping_cost');
    const shippingEtd = document.getElementById('shipping_etd');

    const shippingDisplay = document.getElementById('shipping-display');
    const grandTotal = document.getElementById('grand-total');
    const submitButton = document.getElementById('submit-checkout');

    const subtotal = Number(
        document.getElementById('product-subtotal').dataset.value
    );

    let shippingOptions = [];

    function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
    }

    function updatePreview() {
        const address = addresses[addressSelect.value];

        if (!address) return;

        document.getElementById('preview-name').textContent =
            address.recipient_name || '';

        document.getElementById('preview-phone').textContent =
            address.phone || '';

        document.getElementById('preview-detail').textContent = [
            address.detail,
            address.village_name,
            address.district_name,
            address.city_name,
            address.province_name,
            address.postal_code
        ].filter(Boolean).join(', ');
    }

    function clearShipping() {
        shippingOptions = [];

        serviceSelect.innerHTML =
            '<option value="">-- Pilih Layanan --</option>';

        serviceGroup.style.display = 'none';

        shippingCourier.value = '';
        shippingService.value = '';
        shippingCost.value = '';
        shippingEtd.value = '';

        shippingDisplay.textContent = 'Belum dihitung';
        grandTotal.textContent = formatRupiah(subtotal);

        submitButton.disabled = true;
    }

    function showMessage(text, type = '') {
        message.hidden = false;
        message.textContent = text;
        message.className = 'shipping-message';

        if (type) {
            message.classList.add(type);
        }
    }

    function updateSelectedService() {
        const index = serviceSelect.value;

        if (index === '') {
            shippingCourier.value = '';
            shippingService.value = '';
            shippingCost.value = '';
            shippingEtd.value = '';

            shippingDisplay.textContent = 'Belum dipilih';
            grandTotal.textContent = formatRupiah(subtotal);
            submitButton.disabled = true;

            return;
        }

        const option = shippingOptions[Number(index)];

        if (!option) {
            clearShipping();
            return;
        }

        shippingCourier.value = option.courier;
        shippingService.value = option.service;
        shippingCost.value = option.cost;
        shippingEtd.value = option.etd;

        shippingDisplay.textContent = formatRupiah(option.cost);

        grandTotal.textContent = formatRupiah(
            subtotal + Number(option.cost)
        );

        submitButton.disabled = false;
    }

    async function calculateShipping() {
        clearShipping();

        const addressId = addressSelect.value;
        const courier = courierSelect.value;

        if (!addressId) {
            showMessage('Pilih alamat pengiriman terlebih dahulu.', 'error-message');
            return;
        }

        if (!courier) {
            showMessage('Pilih kurir terlebih dahulu.', 'error-message');
            return;
        }

        calculateButton.disabled = true;
        calculateButton.textContent = 'Menghitung...';

        showMessage('Sedang menghitung tarif pengiriman...');

        try {
            const response = await fetch(
                @json(route('checkout.shipping-cost')),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({
                        address_id: addressId,
                        selected: selectedIds,
                        courier: courier
                    })
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Gagal menghitung tarif pengiriman.'
                );
            }

            if (!result.options || result.options.length === 0) {
                throw new Error('Tidak ada layanan pengiriman untuk tujuan ini.');
            }

            shippingOptions = result.options;

            shippingOptions.forEach((option, index) => {
                const element = document.createElement('option');

                element.value = index;
                element.textContent =
                    `${option.service} - ${option.description || option.courier_name} - ` +
                    `${formatRupiah(option.cost)} - Estimasi ${option.etd || '-'}`;

                serviceSelect.appendChild(element);
            });

            serviceGroup.style.display = 'block';

            showMessage(
                `Total berat: ${Number(result.weight).toLocaleString('id-ID')} gram. Silakan tentukan paket layanan pengiriman.`,
                'success-message'
            );

        } catch (error) {
            showMessage(error.message, 'error-message');
        } finally {
            calculateButton.disabled = false;
            calculateButton.textContent = 'Hitung Tarif Pengiriman';
        }
    }

    addressSelect.addEventListener('change', function () {
        updatePreview();
        clearShipping();
        message.hidden = true;
    });

    courierSelect.addEventListener('change', function () {
        clearShipping();
        message.hidden = true;
    });

    calculateButton.addEventListener('click', calculateShipping);

    serviceSelect.addEventListener('change', updateSelectedService);

    document.getElementById('checkout-form').addEventListener('submit', function (event) {
        if (
            !shippingCourier.value ||
            !shippingService.value ||
            !shippingCost.value
        ) {
            event.preventDefault();
            showMessage(
                'Harap hitung dan pilih layanan pengiriman terlebih dahulu.',
                'error-message'
            );
        }
    });

    updatePreview();
    clearShipping();
});
</script>
@endif
@endsection
