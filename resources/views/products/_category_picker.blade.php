{{-- Pakai: @include('products._category_picker', ['selectedId' => old('product_cat', $product->product_cat ?? null)]) --}}
@php
    $selectedTitle = $selectedId ? optional($categories->firstWhere('cat_id', $selectedId))->cat_title : '';
@endphp

<div class="cat-picker" style="position:relative;">
    <input type="text" id="cat-search" autocomplete="off" placeholder="Ketik untuk mencari kategori..."
           value="{{ $selectedTitle }}" style="width:100%;padding:11px;border:1px solid var(--c-border);border-radius:7px;">
    <input type="hidden" name="product_cat" id="cat-value" value="{{ $selectedId }}">
    <ul id="cat-list" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:20;margin:2px 0 0;padding:4px;list-style:none;background:#fff;border:1px solid var(--c-border);border-radius:7px;max-height:220px;overflow:auto;"></ul>
</div>

<script>
(function () {
    var input = document.getElementById('cat-search'),
        value = document.getElementById('cat-value'),
        list  = document.getElementById('cat-list'),
        url   = @json(route('admin.categories.search')),
        timer;

    function validity() { input.setCustomValidity(value.value ? '' : 'Pilih kategori dari daftar.'); }

    function render(items) {
        list.innerHTML = '';
        if (!items.length) {
            var li = document.createElement('li'); li.textContent = 'Kategori tidak ditemukan';
            li.style.cssText = 'padding:8px;color:#888;'; list.appendChild(li);
        }
        items.forEach(function (c) {
            var li = document.createElement('li');
            li.textContent = c.cat_title; li.style.cssText = 'padding:8px;cursor:pointer;border-radius:5px;';
            li.onmouseenter = function () { li.style.background = 'var(--c-cream)'; };
            li.onmouseleave = function () { li.style.background = ''; };
            li.onmousedown = function (e) {
                e.preventDefault();
                value.value = c.cat_id; input.value = c.cat_title;
                list.style.display = 'none'; validity();
            };
            list.appendChild(li);
        });
        list.style.display = 'block';
    }

    function load() {
        fetch(url + '?q=' + encodeURIComponent(input.value), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); }).then(render);
    }

    input.addEventListener('focus', load);
    input.addEventListener('input', function () {
        value.value = ''; validity();           // ketik ulang = pilihan dibatalkan
        clearTimeout(timer); timer = setTimeout(load, 200);
    });
    input.addEventListener('blur', function () { setTimeout(function () { list.style.display = 'none'; }, 120); });
    validity();
})();
</script>