@extends('layouts.user')

@section('title', 'Katalog & Rekomendasi Kecantikan - Beauty Store')

@push('styles')
<style>
    .home-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 24px 64px;
    }

    .catalog-intro { margin: 18px 0 20px; }
    .catalog-intro h1 {
        margin-bottom: 6px;
        color: var(--c-ink-heading);
        font-size: clamp(28px, 4vw, 38px);
    }
    .catalog-intro p { max-width: 620px; margin: 0; color: var(--c-ink-soft); }

    .section-block { margin-bottom: 32px; }
    .section-header-row {
        display: flex; justify-content: space-between; align-items: flex-end;
        margin-bottom: 16px; gap: 16px; flex-wrap: wrap;
    }
    .section-header-row h2 { font-size: 22px; color: var(--c-ink-heading); margin-bottom: 4px; }
    .section-header-row p { color: var(--c-ink-soft); font-size: 14px; margin: 0; }


    /* BRAND PILIHAN */
    .brand-section {
        padding: 22px; border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        background: linear-gradient(135deg, #fff 0%, #FFF8F8 100%);
        box-shadow: var(--shadow-xs);
    }

    .filter-panel {
        background: var(--glass-bg-elevated); backdrop-filter: blur(var(--glass-blur));
        -webkit-backdrop-filter: blur(var(--glass-blur)); border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg); padding: 16px 18px; margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }
    .filter-form {
        display: grid; grid-template-columns: 2fr 1.2fr 1.2fr 1fr 1fr 1fr auto;
        gap: 10px; align-items: center;
    }
    .filter-form input, .filter-form select {
        width: 100%; min-width: 0; padding: 10px 12px;
        border: 1.5px solid var(--c-border); border-radius: var(--radius-pill);
        background: #fff; color: var(--c-ink); font-size: 13.5px; transition: var(--transition);
    }
    .filter-form input:focus, .filter-form select:focus {
        outline: none; border-color: var(--c-pink-strong);
        box-shadow: 0 0 0 3px rgba(233,129,158,.15);
    }
    .filter-button {
        border: 0; border-radius: var(--radius-pill); padding: 12px 24px;
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff; font: 800 13.5px var(--font); cursor: pointer;
        transition: var(--transition); white-space: nowrap;
        box-shadow: 0 2px 8px rgba(233,129,158,.35);
    }
    .filter-button:hover {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-1px);
    }

    .empty-products {
        padding: 60px 20px; text-align: center; background: var(--c-surface);
        border-radius: var(--radius-lg); color: var(--c-ink-soft); border: 1px dashed var(--c-border);
    }
    .pagination { margin-top: 36px; display: flex; justify-content: center; }

    /* QUICK VIEW MODAL */
    .quick-view-backdrop { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; padding: 24px; background: rgba(28, 22, 25, .56); backdrop-filter: blur(5px); }
    .quick-view-backdrop.is-open { display: flex; }
    .quick-view-modal { position: relative; width: min(900px, 100%); max-height: 86vh; overflow: auto; background: var(--c-surface, #fff); border-radius: 22px; box-shadow: 0 25px 80px rgba(0,0,0,.25); animation: quickViewIn .22s ease-out; }
    @keyframes quickViewIn { from { opacity: 0; transform: translateY(12px) scale(.985); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .quick-view-close { position: sticky; top: 12px; margin: 12px 12px 0 auto; z-index: 2; display: flex; width: 38px; height: 38px; border: 0; border-radius: 50%; align-items: center; justify-content: center; background: #f4f1f1; color: #333; font-size: 22px; cursor: pointer; }
    .quick-view-content { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 28px; padding: 0 30px 30px; }
    .quick-view-image-wrap { min-height: 340px; overflow: hidden; border-radius: 16px; background: linear-gradient(135deg, #fff6ec, #fff0f4); display: flex; align-items: center; justify-content: center; }
    .quick-view-image { width: 100%; height: 100%; min-height: 340px; max-height: 480px; object-fit: contain; }
    .quick-view-copy { padding: 8px 0; }
    .quick-view-labels { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
    .quick-view-chip { display: inline-flex; border-radius: 999px; padding: 6px 10px; background: #f8e9ee; color: #9b4c68; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .45px; text-decoration: none; }
    .quick-view-chip.category { background: #f2eee8; color: #65594e; }
    .quick-view-title { color: var(--c-ink-heading); font-size: clamp(20px, 2.4vw, 28px); line-height: 1.35; margin: 0 0 12px; }
    .quick-view-price { font-size: 23px; font-weight: 900; color: var(--c-ink-heading); margin-bottom: 8px; }
    .quick-view-stock { color: var(--c-ink-soft); font-size: 13px; margin-bottom: 14px; }
    .quick-view-description { color: var(--c-ink-soft); font-size: 14px; line-height: 1.7; margin-bottom: 20px; }
    .quick-view-actions { display: grid; gap: 10px; }
    .quick-view-add { border: 0; border-radius: 999px; padding: 13px 18px; color: #fff; font-weight: 800; cursor: pointer; background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong)); }
    .quick-view-add:disabled { opacity: .55; cursor: not-allowed; }
    .quick-view-full-detail { text-align: center; text-decoration: none; color: var(--c-pink-strong); font-weight: 800; padding: 10px; }
    .quick-view-trigger { cursor: pointer; }
    .quick-view-trigger:focus-visible { outline: 3px solid var(--c-pink-strong); outline-offset: 3px; border-radius: 8px; }
    body.quick-view-open { overflow: hidden; }
    @media (max-width: 650px) {
        .quick-view-backdrop { padding: 12px; align-items: flex-end; }
        .quick-view-modal { max-height: 92vh; border-radius: 20px 20px 14px 14px; }
        .quick-view-content { grid-template-columns: 1fr; gap: 16px; padding: 0 18px 22px; }
        .quick-view-image-wrap, .quick-view-image { min-height: 230px; max-height: 290px; }
        .quick-view-copy { padding-top: 0; }
        .quick-view-close { position: absolute; top: 10px; right: 10px; margin: 0; }
    }
    @media (prefers-reduced-motion: reduce) { .quick-view-modal { animation: none; } }

    @media (max-width: 1040px) {
        .filter-form { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .home-wrapper { padding: 0 16px 60px; }
        .filter-form { grid-template-columns: 1fr 1fr; }
        .brand-section { padding: 15px; }
    }
    @media (max-width: 480px) {
        .filter-form { grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) { .product-image { transition: none !important; } }
</style>
@endpush

@section('content')
<div class="home-wrapper">
    <header class="catalog-intro">
        <h1>Koleksi Produk</h1>
        <p>Temukan produk yang sesuai kebutuhanmu.</p>
    </header>

    {{-- BRAND PILIHAN --}}
    @if(isset($brands) && $brands->isNotEmpty())
    <section class="section-block brand-section" aria-labelledby="brand-section-title">
        <div class="section-header-row">
            <div>
                <h2 id="brand-section-title">Brand Pilihan</h2>
                <p>Temukan produk favorit dari brand kecantikan pilihan.</p>
            </div>
            <a href="{{ route('products.index') }}#catalog-section" class="btn btn-outline btn-sm">Semua Produk →</a>
        </div>

        <div class="brand-marquee">
            <div class="brand-marquee-track">
                <div class="brand-marquee-group">
                    @foreach($brands as $brand)
                            <a href="{{ route('products.index', ['brand' => $brand->brand_id]) }}#catalog-section"
                               class="brand-marquee-item"
                               title="{{ $brand->brand_name }}">
                                @if($brand->brand_logo)
                                    <img src="{{ asset('storage/' . $brand->brand_logo) }}"
                                         alt="Logo {{ $brand->brand_name }}" loading="lazy">
                                @else
                                    <span class="brand-marquee-name">{{ $brand->brand_name }}</span>
                                @endif
                            </a>
                    @endforeach
                </div>
                {{-- Duplikat supaya animasi berjalan tanpa jeda --}}
                <div class="brand-marquee-group" aria-hidden="true">
                    @foreach($brands as $brand)
                            <a href="{{ route('products.index', ['brand' => $brand->brand_id]) }}#catalog-section"
                               class="brand-marquee-item"
                               title="{{ $brand->brand_name }}" tabindex="-1">
                                @if($brand->brand_logo)
                                    <img src="{{ asset('storage/' . $brand->brand_logo) }}"
                                         alt="" loading="lazy">
                                @else
                                    <span class="brand-marquee-name">{{ $brand->brand_name }}</span>
                                @endif
                            </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- KATALOG SEMUA PRODUK & FILTER --}}
    <div class="section-block" id="catalog-section">
        <div class="section-header-row">
            <div><h2>Semua Produk</h2></div>
            <span style="color:var(--c-ink-soft);font-size:13.5px;font-weight:700;">
                {{ $products->total() }} produk ditemukan
            </span>
        </div>

        <div class="filter-panel">
            <form action="{{ route('products.index') }}#catalog-section" method="GET" class="filter-form">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk atau brand...">

                <select name="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->cat_id }}" @selected((string) request('category') === (string) $category->cat_id)>
                            {{ $category->cat_title }}
                        </option>
                    @endforeach
                </select>

                <select name="brand">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->brand_id }}" @selected((string) request('brand') === (string) $brand->brand_id)>
                            {{ $brand->brand_name }}
                        </option>
                    @endforeach
                </select>

                <input type="number" name="min_price" min="0" value="{{ request('min_price') }}" placeholder="Harga min (Rp)">
                <input type="number" name="max_price" min="0" value="{{ request('max_price') }}" placeholder="Harga max (Rp)">

                <select name="sort">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Harga terendah</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Harga tertinggi</option>
                </select>

                <button type="submit" class="filter-button">Terapkan</button>
            </form>

            @if(request()->hasAny(['q', 'category', 'brand', 'min_price', 'max_price', 'sort']))
                <a href="{{ route('products.index') }}#catalog-section" style="display:inline-block;margin-top:12px;color:var(--c-pink-strong);font-size:13px;font-weight:700;text-decoration:none;">
                    ✕ Reset semua filter pencarian
                </a>
            @endif
        </div>

        @if($products->count())
            <div class="product-grid">
                @foreach($products as $product)
                    <article class="product-card"
                    data-quick-view-product
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-brand="{{ $product->brandRelation->brand_name ?? $product->brand }}"
                    data-product-brand-id="{{ $product->brand_id ?? '' }}"
                    data-product-category="{{ $product->category->cat_title ?? 'Kategori Umum' }}"
                    data-product-category-id="{{ $product->product_cat ?? '' }}"
                    data-product-price="{{ number_format($product->price, 0, ',', '.') }}"
                    data-product-stock="{{ $product->stock }}"
                    data-product-image="{{ $product->image ?? '' }}"
                    data-product-description="{{ $product->description ?? 'Lihat detail lengkap untuk informasi produk.' }}"
                    data-product-detail-url="{{ route('products.show', $product->id) }}"
                    data-product-cart-url="{{ route('cart.add', $product->id) }}"
                    data-product-brand-url="{{ !empty($product->brand_id) ? route('products.index', ['brand' => $product->brand_id]) : route('products.index') }}"
                    data-product-category-url="{{ !empty($product->product_cat) ? route('products.index', ['category' => $product->product_cat]) : route('products.index') }}">
                        <a href="{{ route('products.show', $product->id) }}" class="product-image-link quick-view-trigger" data-quick-view-trigger>
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                            @else
                                <div class="image-placeholder">✨</div>
                            @endif
                            <span class="product-view-hint">Quick View</span>
                        </a>
                        <div class="product-info">
                            <span class="product-brand">{{ $product->brandRelation->brand_name ?? $product->brand }}</span>
                            <h3 class="product-name">
                                <a href="{{ route('products.show', $product->id) }}" class="quick-view-trigger" data-quick-view-trigger>{{ $product->name }}</a>
                            </h3>
                            <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <div class="product-actions">
                                <a href="{{ route('products.show', $product->id) }}" class="detail-button quick-view-trigger" data-quick-view-trigger>Quick View</a>
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <button type="submit" class="cart-button">+ Keranjang</button>
                                    </form>
                                @else
                                    <button type="button" disabled>Habis</button>
                                @endif
                                <button type="button" class="btn-ask-bot"
                                    data-chat-ask="{{ $product->brandRelation->brand_name ?? $product->brand }} {{ $product->name }}"
                                    data-chat-product="{{ $product->id }}">💬 Tanya Chatbot</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="pagination">{{ $products->links() }}</div>
        @else
            <div class="empty-products">
                <h3 style="font-size:20px;color:var(--c-ink-heading);margin-bottom:8px;">Produk Tidak Ditemukan</h3>
                <p>Coba gunakan kata kunci pencarian yang lebih umum atau atur ulang filter Anda.</p>
            </div>
        @endif
    </div>

    {{-- QUICK VIEW MODAL --}}
    <div class="quick-view-backdrop" id="quickViewBackdrop" aria-hidden="true">
        <section class="quick-view-modal" id="quickViewModal" role="dialog" aria-modal="true" aria-labelledby="quickViewTitle">
            <button type="button" class="quick-view-close" id="quickViewClose" aria-label="Tutup detail produk">&times;</button>
            <div class="quick-view-content">
                <div class="quick-view-image-wrap" id="quickViewImageWrap"><img class="quick-view-image" id="quickViewImage" alt=""></div>
                <div class="quick-view-copy">
                    <div class="quick-view-labels">
                        <a class="quick-view-chip" id="quickViewBrand" href="#"></a>
                        <a class="quick-view-chip category" id="quickViewCategory" href="#"></a>
                    </div>
                    <h2 class="quick-view-title" id="quickViewTitle"></h2>
                    <div class="quick-view-price" id="quickViewPrice"></div>
                    <div class="quick-view-stock" id="quickViewStock"></div>
                    <p class="quick-view-description" id="quickViewDescription"></p>
                    <div class="quick-view-actions">
                        <form method="POST" id="quickViewCartForm">
                            @csrf
                            <button type="submit" class="quick-view-add" id="quickViewAddButton">+ Tambahkan ke Keranjang</button>
                        </form>
                        <a class="quick-view-full-detail" id="quickViewFullDetail" href="#">Lihat Detail Lengkap →</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function flyProductToCart(sourceImg, onComplete) {
        var cartTarget = document.getElementById('navCartButton') || document.querySelector('.nav-cart');
        if (!cartTarget) { if (onComplete) onComplete(); return; }

        var startRect = sourceImg ? sourceImg.getBoundingClientRect() : {
            left: window.innerWidth / 2, top: window.innerHeight / 2, width: 60, height: 60
        };
        var endRect = cartTarget.getBoundingClientRect();
        var ghost = document.createElement('img');
        ghost.src = (sourceImg && sourceImg.src) ? sourceImg.src :
            'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"><circle cx="30" cy="30" r="30" fill="%23FAACBF"/></svg>';
        ghost.className = 'flying-product-ghost';
        ghost.style.left = startRect.left + 'px';
        ghost.style.top = startRect.top + 'px';
        ghost.style.width = Math.min(startRect.width, 90) + 'px';
        ghost.style.height = Math.min(startRect.height, 90) + 'px';
        document.body.appendChild(ghost);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                var targetX = endRect.left + (endRect.width / 2) - 15;
                var targetY = endRect.top + (endRect.height / 2) - 15;
                ghost.style.transform = 'translate(' + (targetX - startRect.left) + 'px, ' +
                    (targetY - startRect.top) + 'px) scale(0.2)';
                ghost.style.opacity = '0.2';
            });
        });

        setTimeout(function () {
            ghost.remove();
            var badge = document.getElementById('navCartBadge') || document.querySelector('.cart-badge');
            if (badge) {
                badge.classList.remove('bounce'); void badge.offsetWidth; badge.classList.add('bounce');
                var count = parseInt(badge.textContent.trim()) || 0;
                badge.textContent = (count + 1) > 99 ? '99+' : (count + 1);
            } else {
                var newBadge = document.createElement('span');
                newBadge.className = 'cart-badge bounce'; newBadge.id = 'navCartBadge';
                newBadge.textContent = '1'; cartTarget.appendChild(newBadge);
            }
            if (onComplete) onComplete();
        }, 750);
    }


    // Quick view: gunakan data dari card katalog, tanpa request halaman baru.
    var quickBackdrop = document.getElementById('quickViewBackdrop');
    var quickModal = document.getElementById('quickViewModal');
    var quickClose = document.getElementById('quickViewClose');
    var quickImage = document.getElementById('quickViewImage');
    var quickImageWrap = document.getElementById('quickViewImageWrap');
    var quickBrand = document.getElementById('quickViewBrand');
    var quickCategory = document.getElementById('quickViewCategory');
    var quickTitle = document.getElementById('quickViewTitle');
    var quickPrice = document.getElementById('quickViewPrice');
    var quickStock = document.getElementById('quickViewStock');
    var quickDescription = document.getElementById('quickViewDescription');
    var quickCartForm = document.getElementById('quickViewCartForm');
    var quickAddButton = document.getElementById('quickViewAddButton');
    var quickDetailLink = document.getElementById('quickViewFullDetail');
    var lastQuickTrigger = null;

    function openQuickView(card, trigger) {
        if (!card || !quickBackdrop) return;
        lastQuickTrigger = trigger;
        var name = card.dataset.productName || 'Produk';
        var brand = card.dataset.productBrand || 'Brand';
        var category = card.dataset.productCategory || 'Kategori';
        var imageUrl = card.dataset.productImage || '';
        var stock = parseInt(card.dataset.productStock || '0', 10);
        quickTitle.textContent = name;
        quickBrand.textContent = brand;
        quickBrand.href = card.dataset.productBrandUrl || '#';
        quickCategory.textContent = category;
        quickCategory.href = card.dataset.productCategoryUrl || '#';
        quickPrice.textContent = 'Rp' + (card.dataset.productPrice || '0');
        quickStock.textContent = stock > 0 ? 'Stok tersedia: ' + stock + ' unit' : 'Stok habis';
        quickDescription.textContent = card.dataset.productDescription || 'Lihat detail lengkap untuk informasi produk.';
        quickDetailLink.href = card.dataset.productDetailUrl || '#';
        quickCartForm.action = card.dataset.productCartUrl || '#';
        quickAddButton.disabled = stock < 1;
        quickAddButton.textContent = stock > 0 ? '+ Tambahkan ke Keranjang' : 'Stok Habis';
        if (imageUrl) {
            quickImage.src = imageUrl;
            quickImage.alt = name;
            if (!quickImage.parentNode) quickImageWrap.innerHTML = '';
            quickImageWrap.appendChild(quickImage);
            quickImage.style.display = 'block';
            quickImageWrap.style.background = '';
        } else {
            quickImage.removeAttribute('src');
            quickImage.alt = '';
            quickImage.style.display = 'none';
            quickImageWrap.innerHTML = '<div class="image-placeholder" style="position:static;min-height:230px;width:100%">✨</div>';
        }
        quickBackdrop.classList.add('is-open');
        quickBackdrop.setAttribute('aria-hidden', 'false');
        document.body.classList.add('quick-view-open');
        quickClose.focus();
    }
    function closeQuickView() {
        if (!quickBackdrop) return;
        quickBackdrop.classList.remove('is-open');
        quickBackdrop.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('quick-view-open');
        if (lastQuickTrigger && typeof lastQuickTrigger.focus === 'function') lastQuickTrigger.focus();
    }
    // Event delegation: tetap menangkap trigger meski kartu diubah/dirender ulang.
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-quick-view-trigger]');
        if (!trigger) return;
        e.preventDefault();
        var card = trigger.closest('[data-quick-view-product]');
        if (card) openQuickView(card, trigger);
    });
    if (quickClose) quickClose.addEventListener('click', closeQuickView);
    if (quickBackdrop) quickBackdrop.addEventListener('click', function (e) { if (e.target === quickBackdrop) closeQuickView(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && quickBackdrop && quickBackdrop.classList.contains('is-open')) closeQuickView(); });

    if (quickCartForm) {
        quickCartForm.addEventListener('submit', function () {
            quickAddButton.disabled = true;
            quickAddButton.textContent = 'Menambahkan...';
        });
    }
    document.querySelectorAll('.add-to-cart-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            var card = form.closest('.product-card');
            var img = card ? card.querySelector('.product-image') : null;

            fetch(form.action, {
                method: 'POST', body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                }
            })
            .then(function (res) {
                if (res.ok || res.status === 302 || res.type === 'opaqueredirect') {
                    flyProductToCart(img, function () { if (btn) btn.disabled = false; });
                } else { throw new Error('Gagal'); }
            })
            .catch(function () {
                if (btn) btn.disabled = false;
                form.submit();
            });
        });
    });
});
</script>
@endpush
