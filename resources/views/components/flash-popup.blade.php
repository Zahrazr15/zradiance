@php
    $flash = collect(['success', 'error', 'warning'])
        ->mapWithKeys(fn ($k) => [$k => session($k)])
        ->filter()
        ->all();
@endphp

<style>
    .bs-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;z-index:9999;padding:20px}
    .bs-box{background:#fff;border-radius:14px;padding:28px 26px;max-width:380px;width:100%;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,.2)}
    .bs-icon{width:52px;height:52px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#fff}
    .bs-success .bs-icon{background:#1f9d62}.bs-error .bs-icon{background:#d64535}.bs-warning .bs-icon{background:#e0a020}
    .bs-box h3{margin:0 0 8px;font-size:18px}.bs-box p{margin:0 0 20px;font-size:14px;color:#555;line-height:1.5}
    .bs-actions{display:flex;gap:10px;justify-content:center}
    .bs-actions button{padding:10px 18px;border-radius:8px;border:0;font:700 14px inherit;cursor:pointer}
    .bs-ok{background:#f4a6b8;color:#222}.bs-cancel{background:#eee;color:#222}
</style>

<script>
(function () {
    var icons  = { success: '✓', error: '✕', warning: '!' };
    var titles = { success: 'Berhasil', error: 'Gagal', warning: 'Perhatian' };

    function show(o) {
        var ov = document.createElement('div');  ov.className = 'bs-overlay';
        var box = document.createElement('div'); box.className = 'bs-box bs-' + o.type;
        var ic = document.createElement('div');  ic.className = 'bs-icon'; ic.textContent = icons[o.type];
        var h = document.createElement('h3');    h.textContent = o.title;
        var p = document.createElement('p');     p.textContent = o.text;
        var row = document.createElement('div'); row.className = 'bs-actions';

        function close() { ov.remove(); document.removeEventListener('keydown', onKey); }
        function onKey(e) { if (e.key === 'Escape') close(); }

        if (o.cancel) {
            var c = document.createElement('button'); c.className = 'bs-cancel'; c.textContent = 'Batal';
            c.onclick = close; row.appendChild(c);
        }
        var ok = document.createElement('button'); ok.className = 'bs-ok'; ok.textContent = o.okText || 'OK';
        ok.onclick = function () { close(); if (o.onOk) o.onOk(); };
        row.appendChild(ok);

        box.append(ic, h, p, row); ov.appendChild(box);
        ov.addEventListener('click', function (e) { if (e.target === ov) close(); });
        document.addEventListener('keydown', onKey);
        document.body.appendChild(ov);
        ok.focus();
    }

    // Popup dari flash session
    var flash = @json($flash);
    var key = ['error', 'warning', 'success'].find(function (k) { return flash[k]; });
    if (key) show({ type: key, title: titles[key], text: flash[key] });

    // Konfirmasi untuk form ber-atribut data-confirm
    document.addEventListener('submit', function (e) {
        var f = e.target, msg = f.getAttribute && f.getAttribute('data-confirm');
        if (!msg) return;
        e.preventDefault();
        show({ type: 'warning', title: 'Konfirmasi', text: msg, okText: 'Ya, lanjut', cancel: true,
               onOk: function () { f.submit(); } });
    });
})();
</script>