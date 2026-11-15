@php
    $flash = collect(['success', 'error', 'warning'])
        ->mapWithKeys(fn ($k) => [$k => session($k)])
        ->filter()
        ->all();
@endphp

<style>
    .bs-overlay{
        position:fixed;
        inset:0;
        background:rgba(36, 20, 29, 0.45);
        backdrop-filter:blur(10px);
        -webkit-backdrop-filter:blur(10px);
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:9999;
        padding:20px;
        animation:fadeInScale 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .bs-box{
        background:rgba(255, 255, 255, 0.94);
        backdrop-filter:blur(20px);
        -webkit-backdrop-filter:blur(20px);
        border:1px solid rgba(255, 255, 255, 0.8);
        border-radius:22px;
        padding:34px 30px;
        max-width:400px;
        width:100%;
        text-align:center;
        box-shadow:0 24px 60px -10px rgba(61, 34, 48, 0.28), 0 0 0 1px rgba(243, 217, 214, 0.5);
    }
    .bs-icon{
        width:58px;
        height:58px;
        border-radius:50%;
        margin:0 auto 16px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:26px;
        font-weight:800;
        color:#fff;
        box-shadow:0 8px 20px rgba(0, 0, 0, 0.12);
    }
    .bs-success .bs-icon{background:linear-gradient(135deg, #27AE60, #1E8E5A)}
    .bs-error .bs-icon{background:linear-gradient(135deg, #E74C3C, #C0392B)}
    .bs-warning .bs-icon{background:linear-gradient(135deg, #F39C12, #D68910)}
    .bs-box h3{margin:0 0 8px;font-size:20px;color:var(--c-ink-heading);font-weight:800}
    .bs-box p{margin:0 0 24px;font-size:14.5px;color:var(--c-ink-soft);line-height:1.6}
    .bs-actions{display:flex;gap:12px;justify-content:center}
    .bs-actions button{
        padding:12px 24px;
        border-radius:999px;
        border:0;
        font:700 14px var(--font, sans-serif);
        cursor:pointer;
        transition:all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .bs-actions button:hover{transform:translateY(-1.5px)}
    .bs-actions button:active{transform:translateY(0)}
    .bs-ok{
        background:linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color:#fff;
        box-shadow:0 4px 14px rgba(233, 129, 158, 0.35);
    }
    .bs-ok:hover{
        background:linear-gradient(135deg, var(--c-pink-strong), #D46684);
        box-shadow:0 6px 18px rgba(233, 129, 158, 0.45);
    }
    .bs-cancel{
        background:rgba(0, 0, 0, 0.06);
        color:var(--c-ink);
    }
    .bs-cancel:hover{background:rgba(0, 0, 0, 0.1)}
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
