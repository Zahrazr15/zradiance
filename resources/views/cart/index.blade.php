
@extends('layouts.user')

@section('title', 'Keranjang - Beauty Store')

@push('styles')
<style>
    .container {
        max-width: 1100px;
        margin: auto;
    }

    h1 {
        margin-bottom: 25px;
    }

    .cart {
        background: var(--c-surface);
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid var(--c-border);
    }

    .cart-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--c-border);
        flex-wrap: wrap;
    }

    .select-all-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .select-product,
    .select-all {
        width: 20px;
        height: 20px;
        accent-color: var(--c-pink-strong);
        cursor: pointer;
        flex-shrink: 0;
    }

    .selection-info {
        color: var(--c-ink-soft);
        font-size: 14px;
    }

    .item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border-bottom: 1px solid var(--c-border);
    }

    .image {
        width: 100px;
        height: 100px;
        background: var(--c-cream);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .info {
        flex: 1;
        min-width: 150px;
    }

    .brand {
        color: var(--c-ink-soft);
        font-size: 13px;
    }

    .name {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
    }

    .price {
        font-weight: bold;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-control form {
        margin: 0;
    }

    .quantity-button {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: var(--c-pink);
        color: var(--c-ink);
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-button:hover {
        background: var(--c-pink-strong);
    }

    .quantity-number {
        min-width: 25px;
        text-align: center;
        font-weight: bold;
    }

    .remove {
        border: none;
        background: var(--c-border);
        padding: 8px 14px;
        border-radius: 6px;
        cursor: pointer;
    }

    .summary {
        background: var(--c-surface);
        margin-top: 20px;
        padding: 25px;
        border-radius: 15px;
        border: 1px solid var(--c-border);
        text-align: right;
    }

    .total {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .selected-count {
        color: var(--c-ink-soft);
        font-size: 14px;
        margin-bottom: 15px;
    }

    .checkout {
        display: inline-block;
        text-decoration: none;
        background: var(--c-pink);
        color: var(--c-ink);
        padding: 12px 25px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: inherit;
        font-family: inherit;
    }

    .checkout:hover {
        background: var(--c-pink-strong);
    }

    .checkout:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .empty {
        background: var(--c-surface);
        padding: 60px;
        border-radius: 15px;
        text-align: center;
        color: var(--c-ink-soft);
    }

    .success,
    .error,
    .warning {
        padding: 12px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .success {
        background: #e9f7ef;
        color: #287a4b;
    }

    .error {
        background: #fdeaea;
        color: #a52a2a;
    }

    .warning {
        background: #fff4d6;
        color: #805b00;
    }

    .selection-status {
        color: var(--c-ink-soft);
        font-size: 13px;
        margin-top: 10px;
        min-height: 18px;
    }

    @media (max-width: 800px) {
        .item {
            flex-wrap: wrap;
        }

        .info {
            min-width: 200px;
        }
    }

    @media (max-width: 500px) {
        .item {
            flex-direction: column;
            align-items: flex-start;
        }

        .quantity-control {
            margin-top: 5px;
        }

        .remove {
            margin-top: 5px;
        }

        .summary {
            text-align: center;
        }

        .checkout {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container">

    <x-back-button :href="route('dashboard')" label="Kembali" />

    <h1>Keranjang Belanja</h1>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="warning">
            {{ session('warning') }}
        </div>
    @endif

    @if (count($cart) > 0)

        <div class="cart">

            {{-- PILIH SEMUA --}}
            <div class="cart-toolbar">

                <label class="select-all-wrapper">
                    <input
                        type="checkbox"
                        id="select-all"
                        class="select-all"
                    >

                    <span>Pilih Semua</span>
                </label>

                <div class="selection-info">
                    <span id="selected-count-toolbar">
                        {{ count($selectedIds) }} produk dipilih
                    </span>
                </div>

            </div>

            {{-- DAFTAR PRODUK --}}
            @foreach ($cart as $item)

                <div class="item">

                    <input
                        type="checkbox"
                        class="select-product"
                        name="selected[]"
                        value="{{ $item['id'] }}"
                        data-subtotal="{{ $item['price'] * $item['quantity'] }}"
                        form="checkout-selection"
                        @checked(in_array((int) $item['id'], $selectedIds, true))
                        aria-label="Pilih {{ $item['name'] }}"
                    >

                    {{-- GAMBAR --}}
                    <div class="image">
                        @if ($item['image'])
                            <img
                                src="{{ $item['image'] }}"
                                alt="{{ $item['name'] }}"
                            >
                        @else
                            No Image
                        @endif
                    </div>

                    {{-- INFORMASI PRODUK --}}
                    <div class="info">

                        <div class="brand">
                            {{ $item['brand'] }}
                        </div>

                        <div class="name">
                            {{ $item['name'] }}
                        </div>

                        <div class="price">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </div>

                    </div>

                    {{-- JUMLAH --}}
                    <div class="quantity-control">

                        <form
                            action="{{ route('cart.decrease', $item['id']) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="quantity-button"
                                aria-label="Kurangi jumlah"
                            >
                                −
                            </button>
                        </form>

                        <span class="quantity-number">
                            {{ $item['quantity'] }}
                        </span>

                        <form
                            action="{{ route('cart.increase', $item['id']) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="quantity-button"
                                aria-label="Tambah jumlah"
                            >
                                +
                            </button>
                        </form>

                    </div>

                    {{-- HAPUS --}}
                    <form
                        action="{{ route('cart.remove', $item['id']) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="remove">
                            Hapus
                        </button>
                    </form>

                </div>

            @endforeach

        </div>

        
    {{-- RINGKASAN --}}
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

    <div class="summary">

        <div class="total">
            Total:
            <span id="selected-total">
                Rp {{ number_format($selectedTotal, 0, ',', '.') }}
            </span>
        </div>

        <div class="selected-count">
            <span id="selected-count-summary">
                {{ count($selectedIds) }}
            </span>
            produk dipilih
        </div>

        <form
            action="{{ route('checkout.index') }}"
            method="GET"
            id="checkout-selection"
        >
            <button
                type="submit"
                class="checkout"
                id="checkout-button"
                @disabled(count($selectedIds) === 0)
            >
                Checkout Produk Terpilih
            </button>
        </form>

        <div
            class="selection-status"
            id="selection-status"
            aria-live="polite"
        ></div>

    </div>


    @else

        <div class="empty">
            Keranjang kamu masih kosong.
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

            // Jangan lanjut checkout dengan pilihan yang belum tersimpan.
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

            // Gunakan hasil server sebagai sumber status akhir.
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
