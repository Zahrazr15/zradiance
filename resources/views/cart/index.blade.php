@extends('layouts.user')

@section('title', 'Keranjang Belanja - Beauty Store')

@push('styles')
<style>
    .cart-container {
        max-width: 1240px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .cart-header {
        margin-bottom: 28px;
    }

    .cart-header h1 {
        font-size: 32px;
        color: var(--c-ink-heading);
        margin-bottom: 6px;
    }

    .cart-header p {
        color: var(--c-ink-soft);
        font-size: 15px;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
    }

    /* Cart Items Card */
    .cart-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .cart-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 24px;
        background: #FCF8F6;
        border-bottom: 1px solid var(--c-border-subtle);
        flex-wrap: wrap;
    }

    .select-all-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 14.5px;
        color: var(--c-ink-heading);
        cursor: pointer;
    }

    .select-product,
    .select-all {
        width: 20px;
        height: 20px;
        accent-color: var(--c-pink-strong);
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 4px;
    }

    .selection-info {
        color: var(--c-ink-soft);
        font-size: 13.5px;
        font-weight: 600;
    }

    .cart-items-list {
        display: flex;
        flex-direction: column;
    }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 22px 24px;
        border-bottom: 1px solid var(--c-border-subtle);
        transition: background 0.2s;
    }
    .cart-item:last-child { border-bottom: 0; }
    .cart-item:hover { background: rgba(255, 249, 238, 0.3); }

    .item-image {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        border: 1px solid var(--c-border-subtle);
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-info {
        flex: 1;
        min-width: 180px;
    }

    .item-brand {
        color: var(--c-ink-light);
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .item-name {
        font-size: 16px;
        font-weight: 700;
        color: var(--c-ink-heading);
        margin: 3px 0 6px;
    }
    .item-name a { color: inherit; text-decoration: none; }
    .item-name a:hover { color: var(--c-pink-strong); }

    .item-price {
        font-size: 16px;
        font-weight: 800;
        color: var(--c-ink-heading);
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #FCF8F6;
        padding: 4px;
        border-radius: var(--radius-pill);
        border: 1px solid var(--c-border-subtle);
    }
    .quantity-control form { margin: 0; }

    .quantity-button {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 50%;
        background: #fff;
        color: var(--c-ink);
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .quantity-button:hover {
        background: var(--c-pink);
        color: var(--c-ink);
    }

    .quantity-number {
        min-width: 30px;
        text-align: center;
        font-weight: 800;
        font-size: 14px;
    }

    .btn-remove-item {
        border: 0;
        background: rgba(192, 57, 43, 0.08);
        color: var(--c-danger);
        padding: 8px 14px;
        border-radius: var(--radius-pill);
        cursor: pointer;
        font: 700 12.5px var(--font);
        transition: var(--transition);
    }
    .btn-remove-item:hover {
        background: var(--c-danger);
        color: #fff;
    }

    /* Sticky Summary Card */
    .summary-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 100px;
    }

    .summary-card h2 {
        font-size: 20px;
        color: var(--c-ink-heading);
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--c-border-subtle);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        font-size: 14.5px;
        color: var(--c-ink-soft);
    }

    .summary-total-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding-top: 16px;
        margin-top: 16px;
        border-top: 2px dashed var(--c-border);
        margin-bottom: 24px;
    }

    .summary-total-row strong {
        font-size: 16px;
        color: var(--c-ink-heading);
    }

    .total-amount {
        font-size: 24px;
        font-weight: 800;
        color: var(--c-pink-strong);
    }

    .btn-checkout-action {
        display: block;
        width: 100%;
        padding: 15px;
        border: 0;
        border-radius: var(--radius-pill);
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 15px var(--font);
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        box-shadow: 0 4px 14px rgba(233, 129, 158, 0.4);
    }
    .btn-checkout-action:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(233, 129, 158, 0.5);
    }
    .btn-checkout-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .selection-status {
        margin-top: 12px;
        font-size: 12.5px;
        color: var(--c-ink-soft);
        text-align: center;
        min-height: 18px;
    }

    .empty-cart-box {
        background: var(--c-surface);
        padding: 70px 24px;
        border-radius: var(--radius-lg);
        text-align: center;
        border: 1px dashed var(--c-border);
    }
    .empty-cart-box h3 { font-size: 22px; margin-bottom: 8px; color: var(--c-ink-heading); }
    .empty-cart-box p { color: var(--c-ink-soft); margin-bottom: 24px; }

    @media (max-width: 960px) {
        .cart-layout { grid-template-columns: 1fr; }
        .summary-card { position: static; }
    }

    @media (max-width: 600px) {
        .cart-container { padding: 0 16px 60px; margin-top: 20px; }
        .cart-item { flex-direction: column; align-items: flex-start; gap: 14px; position: relative; padding: 18px 16px; }
        .item-info { width: 100%; }
        .cart-item-actions { width: 100%; display: flex; justify-content: space-between; align-items: center; }
    }
</style>
@endpush

@section('content')
<div class="cart-container">

    <div class="cart-header">
        <h1>Keranjang Belanja</h1>
        <p>Pilih produk yang ingin Anda beli sekarang atau ubah jumlah pesanan.</p>
    </div>

    @if (count($cart) > 0)
        <div class="cart-layout">

            {{-- DAFTAR ITEM KERANJANG --}}
            <div class="cart-card">
                <div class="cart-toolbar">
                    <label class="select-all-wrapper" for="select-all">
                        <input
                            type="checkbox"
                            id="select-all"
                            class="select-all"
                        >
                        <span>Pilih Semua Produk</span>
                    </label>

                    <div class="selection-info">
                        <span id="selected-count-toolbar">{{ count($selectedIds ?? []) }} produk dipilih</span>
                    </div>
                </div>

                <div class="cart-items-list">
                    @foreach ($cart as $item)
                        <div class="cart-item">
                            <input
                                type="checkbox"
                                class="select-product"
                                value="{{ $item['id'] }}"
                                data-id="{{ $item['id'] }}"
                                data-subtotal="{{ (float) $item['price'] * (int) $item['quantity'] }}"
                                @checked(in_array((int) $item['id'], $selectedIds ?? [], true))
                            >

                            <div class="item-image">
                                @if(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                @else
                                    <span style="font-size:32px;">✨</span>
                                @endif
                            </div>

                            <div class="item-info">
                                <div class="item-brand">{{ $item['brand'] ?? 'Beauty Brand' }}</div>
                                <h3 class="item-name">
                                    <a href="{{ route('products.show', $item['id']) }}">{{ $item['name'] }}</a>
                                </h3>
                                <div class="item-price">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="cart-item-actions" style="display:flex;align-items:center;gap:14px;">
                                {{-- JUMLAH --}}
                                <div class="quantity-control">
                                    <form action="{{ route('cart.decrease', $item['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="quantity-button" aria-label="Kurangi jumlah">−</button>
                                    </form>

                                    <span class="quantity-number">{{ $item['quantity'] }}</span>

                                    <form action="{{ route('cart.increase', $item['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="quantity-button" aria-label="Tambah jumlah">+</button>
                                    </form>
                                </div>

                                {{-- HAPUS --}}
                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove-item remove">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RINGKASAN BELANJA & CHECKOUT --}}
            @php
                $selectedIds = array_map('intval', $selectedIds ?? []);
                $selectedTotal = collect($cart)
                    ->filter(function ($item) use ($selectedIds) {
                        return in_array((int) $item['id'], $selectedIds, true);
                    })
                    ->sum(function ($item) {
                        return (float) $item['price'] * (int) $item['quantity'];
                    });
            @endphp

            <div class="summary-card summary">
                <h2>Ringkasan Belanja</h2>

                <div class="summary-row">
                    <span>Produk Terpilih</span>
                    <span id="selected-count-summary" style="font-weight:700;">{{ count($selectedIds) }}</span>
                </div>

                <div class="summary-total-row">
                    <strong>Total Harga</strong>
                    <div class="total-amount" id="selected-total">
                        Rp {{ number_format($selectedTotal, 0, ',', '.') }}
                    </div>
                </div>

                <form action="{{ route('checkout.index') }}" method="GET" id="checkout-selection">
                    <button
                        type="submit"
                        class="btn-checkout-action checkout"
                        id="checkout-button"
                        @disabled(count($selectedIds) === 0)
                    >
                        Lanjut ke Checkout →
                    </button>
                </form>

                <div class="selection-status" id="selection-status" aria-live="polite"></div>
            </div>

        </div>
    @else
        <div class="empty-cart-box empty">
            <h3>Keranjang Belanja Kosong</h3>
            <p>Sepertinya Anda belum memilih produk kecantikan favorit Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Mulai Belanja Produk Sekarang 🛍️
            </a>
        </div>
    @endif

</div>
@endsection

@push('scripts')
@if (count($cart) > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = Array.from(
        document.querySelectorAll('.select-product')
    );

    const selectAll = document.getElementById('select-all');
    const totalElement = document.getElementById('selected-total');
    const checkoutButton = document.getElementById('checkout-button');

    const toolbarCount = document.getElementById('selected-count-toolbar');
    const summaryCount = document.getElementById('selected-count-summary');
    const statusElement = document.getElementById('selection-status');

    const selectionUrl = @json(route('cart.selection'));
    const selectAllUrl = @json(route('cart.select-all'));
    const checkoutUrl = @json(route('checkout.index'));

    const csrfToken = @json(csrf_token());

    let saving = false;
    let requestVersion = 0;

    function getSelectedIds() {
        return checkboxes
            .filter(checkbox => checkbox.checked)
            .map(checkbox => Number(checkbox.value));
    }

    function formatRupiah(value) {
        return 'Rp ' + value.toLocaleString('id-ID');
    }

    function updateDisplay() {
        let total = 0;
        let selectedCount = 0;

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                total += Number(checkbox.dataset.subtotal);
                selectedCount++;
            }
        });

        totalElement.textContent = formatRupiah(total);

        toolbarCount.textContent =
            selectedCount + ' produk dipilih';

        summaryCount.textContent = selectedCount;

        checkoutButton.disabled =
            selectedCount === 0 || saving;

        selectAll.checked =
            checkboxes.length > 0 &&
            selectedCount === checkboxes.length;

        selectAll.indeterminate =
            selectedCount > 0 &&
            selectedCount < checkboxes.length;
    }

    async function saveSelection() {
        const currentVersion = ++requestVersion;
        const selectedIds = getSelectedIds();

        saving = true;
        updateDisplay();

        statusElement.textContent = 'Menyimpan pilihan...';

        try {
            const response = await fetch(selectionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    selected: selectedIds
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Gagal menyimpan pilihan produk.'
                );
            }

            if (currentVersion !== requestVersion) {
                return;
            }

            statusElement.textContent = 'Pilihan berhasil disimpan.';

        } catch (error) {
            if (currentVersion !== requestVersion) {
                return;
            }

            statusElement.textContent =
                error.message || 'Gagal menyimpan pilihan produk.';

            checkboxes.forEach(function (checkbox) {
                checkbox.disabled = true;
            });

            checkoutButton.disabled = true;

        } finally {
            if (currentVersion === requestVersion) {
                saving = false;
                updateDisplay();
            }
        }
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            checkboxes.forEach(item => item.disabled = saving);
            saveSelection();
        });
    });

    selectAll.addEventListener('change', async function () {
        const shouldSelectAll = selectAll.checked;

        checkboxes.forEach(function (checkbox) {
            checkbox.checked = shouldSelectAll;
            checkbox.disabled = true;
        });

        const currentVersion = ++requestVersion;

        saving = true;
        updateDisplay();

        statusElement.textContent = 'Menyimpan pilihan...';

        try {
            const response = await fetch(
                shouldSelectAll ? selectAllUrl : selectionUrl,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(
                        shouldSelectAll
                            ? {}
                            : { selected: [] }
                    )
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Gagal memperbarui pilihan produk.'
                );
            }

            if (currentVersion !== requestVersion) {
                return;
            }

            const selectedSet = new Set(
                result.selected.map(Number)
            );

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = selectedSet.has(
                    Number(checkbox.value)
                );
            });

            statusElement.textContent = 'Pilihan berhasil disimpan.';

        } catch (error) {
            if (currentVersion !== requestVersion) {
                return;
            }

            statusElement.textContent =
                error.message || 'Gagal memperbarui pilihan produk.';

            checkboxes.forEach(function (checkbox) {
                checkbox.disabled = true;
            });

            checkoutButton.disabled = true;

        } finally {
            if (currentVersion === requestVersion) {
                saving = false;

                checkboxes.forEach(function (checkbox) {
                    checkbox.disabled = false;
                });

                updateDisplay();
            }
        }
    });

    // Pastikan pilihan tersimpan sebelum checkout.
    document.getElementById('checkout-selection')
        .addEventListener('submit', function (event) {
            if (saving || getSelectedIds().length === 0) {
                event.preventDefault();
            }
        });

    updateDisplay();

});
</script>
@endif
@endpush
