
@extends('layouts.user')

@section('title', 'Checkout - Beauty Store')

@push('styles')
<style>
    .container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1 { margin-bottom: 30px; }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 25px;
    }

    .card {
        background: var(--c-surface);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .card h2 { margin-top: 0; margin-bottom: 20px; }

    .form-group { margin-bottom: 18px; }

    label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
    }

    input, textarea, select {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        font-size: 14px;
    }

    input[type="radio"] { width: auto; }

    textarea {
        min-height: 100px;
        resize: vertical;
    }

    .product-item {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 12px 0;
        border-bottom: 1px solid var(--c-border);
    }

    .product-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .product-name, .product-price { font-weight: 600; }

    .product-quantity {
        font-size: 13px;
        color: var(--c-ink-soft);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 14px;
    }

    .total {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--c-border);
        font-size: 18px;
        font-weight: bold;
    }

    .checkout-button, .shipping-button {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        background: var(--c-pink);
        color: var(--c-ink);
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
    }

    .checkout-button {
        margin-top: 20px;
        font-size: 16px;
    }

    .checkout-button:hover, .shipping-button:hover {
        background: var(--c-pink-strong);
    }

    .shipping-button:disabled, .checkout-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .error {
        background: #ffe5e5;
        color: #b00020;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .shipping-message {
        margin-top: 10px;
        padding: 12px;
        border-radius: 8px;
        background: var(--c-bg, #fff5f8);
        font-size: 13px;
    }

    .shipping-message.error-message {
        background: #ffe5e5;
        color: #b00020;
    }

    .shipping-message.success-message {
        background: #e8f7ec;
        color: #176b32;
    }

    .payment-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        cursor: pointer;
    }

    .payment-option input {
        margin-top: 4px;
    }

    .payment-option span { flex: 1; }

    @media (max-width: 768px) {
        .checkout-grid { grid-template-columns: 1fr; }
        .container { margin-top: 20px; }
    }
</style>
@endpush

@section('content')
<div class="container">

    <x-back-button :href="route('cart.index')" label="Kembali ke Keranjang" />

    <h1>Checkout</h1>

    @if ($errors->any())
        <div class="error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-grid">

        {{-- INFORMASI PENGIRIMAN --}}
        <div class="card">
            <h2>Informasi Pengiriman</h2>

            @if ($addresses->isNotEmpty())
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    @foreach ($selectedIds as $id)
                        <input type="hidden" name="selected[]" value="{{ $id }}">
                    @endforeach

                    <div class="form-group">
                        <label for="address_id">Pilih Alamat Pengiriman</label>

                        <select id="address_id" name="address_id" required>
                            @foreach ($addresses as $address)
                                <option
                                    value="{{ $address->id }}"
                                    @selected(old('address_id', $defaultAddress?->id) == $address->id)
                                >
                                    {{ $address->label }}
                                    @if ($address->is_default)
                                        (Alamat Utama)
                                    @endif
                                    - {{ $address->recipient_name }}
                                </option>
                            @endforeach
                        </select>

                        <small>Alamat utama otomatis dipilih. Kamu bisa menggantinya.</small>
                    </div>

                    <div
                        id="address-preview"
                        class="form-group"
                        style="background:var(--c-bg, #fff5f8); padding:15px; border-radius:10px;"
                    >
                        <strong id="preview-name"></strong><br>
                        <span id="preview-phone"></span>
                        <p id="preview-detail" style="margin:8px 0 0;"></p>
                    </div>

                    <a
                        href="{{ route('addresses.create') }}"
                        style="display:inline-block; margin-bottom:18px;"
                    >
                        + Tambah Alamat Baru
                    </a>

                    {{-- PILIH KURIR --}}
                    <div class="form-group">
                        <label for="courier">Pilih Kurir</label>

                        <select id="courier">
                            <option value="">-- Pilih Kurir --</option>
                            <option value="jne">JNE</option>
                            <option value="jnt">J&T Express</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="ide">ID Express</option>
                            <option value="sap">SAP Express</option>
                            <option value="tiki">TIKI</option>
                            <option value="lion">Lion Parcel</option>
                            <option value="anteraja">AnterAja</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="ninja">Ninja Xpress</option>
                            <option value="wahana">Wahana</option>
                        </select>
                    </div>

                    <button
                        type="button"
                        id="calculate-shipping"
                        class="shipping-button"
                    >
                        Hitung Ongkos Kirim
                    </button>

                    <div id="shipping-message" class="shipping-message" hidden></div>

                    {{-- PILIH LAYANAN --}}
                    <div class="form-group" id="service-group" style="display:none; margin-top:18px;">
                        <label for="shipping-service-option">Pilih Layanan Pengiriman</label>

                        <select id="shipping-service-option">
                            <option value="">-- Pilih Layanan --</option>
                        </select>
                    </div>

                    {{-- NILAI YANG DIKIRIM KE SERVER --}}
                    <input type="hidden" name="shipping_courier" id="shipping_courier">
                    <input type="hidden" name="shipping_service" id="shipping_service">
                    <input type="hidden" name="shipping_cost" id="shipping_cost">
                    <input type="hidden" name="shipping_etd" id="shipping_etd">

                    <div class="form-group">
                        <label for="delivery_note">Catatan untuk Kurir (Opsional)</label>
                        <textarea
                            id="delivery_note"
                            name="delivery_note"
                            placeholder="Contoh: Rumah pagar putih, dekat minimarket..."
                        >{{ old('delivery_note') }}</textarea>

                        @error('delivery_note')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- METODE PEMBAYARAN --}}
                    <div class="form-group" style="margin-top:20px;">
                        <label><strong>Metode Pembayaran</strong></label>

                        <div style="display:flex; flex-direction:column; gap:12px;">

                            <label class="payment-option">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="online"
                                    {{ old('payment_method', 'online') === 'online' ? 'checked' : '' }}
                                    required
                                >

                                <span>
                                    <strong>Pembayaran Online</strong><br>
                                    <small style="color:var(--c-ink-soft);">
                                        QRIS, Transfer Bank, dan E-Wallet melalui Midtrans.
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
                                    <strong>Cash on Delivery (COD)</strong><br>
                                    <small style="color:var(--c-ink-soft);">
                                        Bayar ketika pesanan diterima.
                                    </small>
                                </span>
                            </label>

                        </div>

                        @error('payment_method')
                            <small style="color:red;">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="checkout-button" id="submit-checkout" disabled>
                        Buat Pesanan
                    </button>
                </form>
            @else
                <div class="error">
                    Kamu belum memiliki alamat tersimpan.
                    Tambahkan alamat terlebih dahulu sebelum checkout.
                </div>

                <a href="{{ route('addresses.create') }}">+ Tambah Alamat</a>
            @endif
        </div>

        {{-- RINGKASAN PESANAN --}}
        <div class="card">
            <h2>Ringkasan Pesanan</h2>

            @foreach ($cart as $item)
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-name">{{ $item['name'] }}</div>

                        <div class="product-quantity">
                            {{ $item['quantity'] }} ×
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="product-price">
                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                    </div>
                </div>
            @endforeach

            <div class="summary-row">
                <span>Subtotal Produk</span>
                <strong id="product-subtotal" data-value="{{ $total }}">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <strong id="shipping-display">Belum dihitung</strong>
            </div>

            <div class="total">
                <span>Total Pembayaran</span>
                <span id="grand-total">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>

            <small style="display:block; margin-top:12px; color:var(--c-ink-soft);">
                Total akhir akan diperbarui setelah layanan pengiriman dipilih.
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

        showMessage('Sedang menghitung ongkos kirim...');

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
                    result.message || 'Gagal menghitung ongkos kirim.'
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
                `Berat pengiriman: ${Number(result.weight).toLocaleString('id-ID')} gram. Pilih layanan yang tersedia.`,
                'success-message'
            );

        } catch (error) {
            showMessage(error.message, 'error-message');
        } finally {
            calculateButton.disabled = false;
            calculateButton.textContent = 'Hitung Ongkos Kirim';
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
                'Hitung ongkir dan pilih layanan pengiriman terlebih dahulu.',
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