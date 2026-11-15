<style>
    #bsChat { position: fixed; right: 24px; bottom: 24px; z-index: 100; font-family: var(--font); }
    #bsChat .bs-fab { 
        width: 60px; 
        height: 60px; 
        border-radius: 50%; 
        border: 0; 
        background: linear-gradient(135deg, var(--c-pink) 0%, var(--c-pink-strong) 100%); 
        color: #fff; 
        font-size: 26px; 
        cursor: pointer; 
        box-shadow: 0 8px 24px rgba(233, 129, 158, 0.45); 
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #bsChat .bs-fab:hover { 
        transform: scale(1.08) translateY(-2px); 
        box-shadow: 0 12px 30px rgba(233, 129, 158, 0.55); 
    }
    #bsChat .bs-panel { 
        display: none; 
        position: absolute; 
        right: 0; 
        bottom: 76px; 
        width: 380px; 
        max-width: calc(100vw - 32px); 
        height: 560px; 
        max-height: calc(100vh - 120px); 
        background: rgba(255, 255, 255, 0.94); 
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.85); 
        border-radius: 22px; 
        box-shadow: 0 20px 50px rgba(61, 34, 48, 0.22), 0 0 0 1px rgba(243, 217, 214, 0.5); 
        overflow: hidden; 
        flex-direction: column; 
        animation: fadeInScale 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    #bsChat.open .bs-panel { display: flex; }
    #bsChat .bs-head { 
        background: linear-gradient(135deg, rgba(250, 172, 191, 0.85) 0%, rgba(233, 129, 158, 0.85) 100%); 
        backdrop-filter: blur(10px);
        color: #fff; 
        padding: 16px 20px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    }
    #bsChat .bs-head strong { font-size: 15.5px; font-weight: 800; display: block; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.1); }
    #bsChat .bs-head small { display: block; font-size: 12px; color: rgba(255, 255, 255, 0.9); margin-top: 2px; }
    #bsChat .bs-close { 
        background: rgba(255, 255, 255, 0.2); 
        border: 0; 
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-size: 16px; 
        cursor: pointer; 
        color: #fff; 
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    #bsChat .bs-close:hover { background: rgba(255, 255, 255, 0.35); }
    #bsChat .bs-list { 
        flex: 1; 
        overflow-y: auto; 
        padding: 16px; 
        background: #FCF8F6; 
        display: flex; 
        flex-direction: column; 
        gap: 12px; 
    }
    #bsChat .bs-msg { 
        max-width: 86%; 
        padding: 12px 16px; 
        border-radius: 18px; 
        font-size: 13.5px; 
        line-height: 1.55; 
        white-space: pre-line; 
        word-wrap: break-word; 
        box-shadow: 0 2px 8px rgba(61, 34, 48, 0.04);
    }
    #bsChat .bs-bot { 
        background: #FFFFFF; 
        border: 1px solid var(--c-border-subtle); 
        color: var(--c-ink);
        align-self: flex-start; 
        border-bottom-left-radius: 4px; 
    }
    #bsChat .bs-user { 
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong)); 
        color: #fff;
        align-self: flex-end; 
        border-bottom-right-radius: 4px; 
        box-shadow: 0 4px 12px rgba(233, 129, 158, 0.3);
    }
    #bsChat .bs-card { 
        align-self: stretch; 
        background: #FFFFFF; 
        border: 1px solid var(--c-border); 
        border-radius: 14px; 
        padding: 14px 16px; 
        font-size: 13px; 
        box-shadow: 0 4px 14px rgba(61, 34, 48, 0.05);
    }
    #bsChat .bs-card .bs-brand { color: var(--c-ink-light); font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    #bsChat .bs-card .bs-name { font-weight: 800; font-size: 14.5px; color: var(--c-ink-heading); margin-top: 2px; }
    #bsChat .bs-card .bs-price { font-weight: 800; color: var(--c-pink-strong); margin: 4px 0; font-size: 14px; }
    #bsChat .bs-card ul { padding-left: 18px; margin: 6px 0; color: var(--c-ink-soft); }
    #bsChat .bs-card .bs-note { color: #8A5A00; background: #FFF4D9; border-radius: 8px; padding: 6px 10px; margin-top: 6px; font-size: 12px; }
    #bsChat .bs-card a { color: var(--c-pink-strong); font-weight: 800; text-decoration: none; display: inline-block; margin-top: 6px; }
    #bsChat .bs-tablewrap { align-self: stretch; overflow-x: auto; background: #FFFFFF; border: 1px solid var(--c-border); border-radius: 14px; }
    #bsChat table { border-collapse: collapse; font-size: 12px; min-width: 100%; }
    #bsChat th, #bsChat td { border-bottom: 1px solid var(--c-border-subtle); padding: 8px 10px; text-align: left; vertical-align: top; min-width: 96px; }
    #bsChat th { background: #FAF2E8; color: var(--c-ink-heading); font-weight: 800; }
    #bsChat td:first-child { font-weight: 800; background: #FFFDF9; color: var(--c-ink); }
    #bsChat .bs-chips { display: flex; flex-wrap: wrap; gap: 7px; align-self: stretch; margin-top: 4px; }
    #bsChat .bs-chip { 
        border: 1px solid var(--c-pink); 
        background: rgba(255, 255, 255, 0.9); 
        color: var(--c-ink); 
        border-radius: 999px; 
        padding: 7px 13px; 
        font: 700 12px var(--font); 
        cursor: pointer; 
        text-align: left; 
        transition: all 0.2s;
        box-shadow: 0 1px 4px rgba(61, 34, 48, 0.04);
    }
    #bsChat .bs-chip:hover { 
        background: var(--c-cream); 
        border-color: var(--c-pink-strong);
        transform: translateY(-1px);
    }
    #bsChat .bs-link { align-self: flex-start; font-size: 13px; color: var(--c-pink-strong); font-weight: 800; text-decoration: none; }
    #bsChat .bs-form { 
        display: flex; 
        gap: 8px; 
        padding: 12px 14px; 
        border-top: 1px solid var(--c-border-subtle); 
        background: #FFFFFF; 
    }
    #bsChat .bs-form input { 
        flex: 1; 
        padding: 11px 14px; 
        border: 1.5px solid var(--c-border); 
        border-radius: 999px; 
        font: inherit; 
        font-size: 13.5px; 
        background: #FCF8F6;
        transition: border 0.2s;
    }
    #bsChat .bs-form input:focus { outline: none; border-color: var(--c-pink-strong); background: #fff; }
    #bsChat .bs-form button { 
        border: 0; 
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong)); 
        color: #fff; 
        border-radius: 999px; 
        padding: 0 18px; 
        font: 800 13.5px var(--font); 
        cursor: pointer; 
        transition: transform 0.2s;
        box-shadow: 0 2px 8px rgba(233, 129, 158, 0.35);
    }
    #bsChat .bs-form button:hover { transform: translateY(-1px); }
    #bsChat .bs-form button:disabled { opacity: .5; cursor: not-allowed; transform: none; }
    @media (max-width: 480px) {
        #bsChat { right: 14px; bottom: 14px; }
        #bsChat .bs-panel { width: calc(100vw - 28px); height: calc(100vh - 100px); }
    }
</style>

<div id="bsChat">
    <div class="bs-panel" role="dialog" aria-label="Chatbot Beauty Store">
        <div class="bs-head">
            <div>
                <strong>Asisten Beauty Store</strong>
                <small>Jawaban pintar seputar produk kecantikan</small>
            </div>
            <button type="button" class="bs-close" aria-label="Tutup chatbot">✕</button>
        </div>
        <div class="bs-list" aria-live="polite"></div>
        <form class="bs-form" autocomplete="off">
            <input type="text" maxlength="500" placeholder="Tulis pertanyaanmu..." aria-label="Pesan">
            <button type="submit">Kirim</button>
        </form>
    </div>
    <button type="button" class="bs-fab" aria-label="Buka chatbot">💬</button>
</div>

<script>
(function () {
    var root = document.getElementById('bsChat');
    var list = root.querySelector('.bs-list');
    var form = root.querySelector('.bs-form');
    var input = form.querySelector('input');
    var sendBtn = form.querySelector('button');
    var URL = @json(route('chatbot.send'));
    var TOKEN = @json(csrf_token());
    var KEY = 'bs-chat-v1';
    var busy = false;

    function el(tag, cls, text) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (text !== undefined && text !== null) n.textContent = text;
        return n;
    }

    function scrollDown() { list.scrollTop = list.scrollHeight; }

    function save() {
        try { sessionStorage.setItem(KEY, JSON.stringify({ html: list.innerHTML, open: root.classList.contains('open') })); } catch (e) {}
    }

    function setOpen(open) {
        root.classList.toggle('open', open);
        if (open) { input.focus(); scrollDown(); }
        save();
    }

    function addUser(text) { list.appendChild(el('div', 'bs-msg bs-user', text)); scrollDown(); }
    function addBot(text) { list.appendChild(el('div', 'bs-msg bs-bot', text)); scrollDown(); }

    function addCard(p) {
        var c = el('div', 'bs-card');
        c.appendChild(el('div', 'bs-brand', p.brand));
        c.appendChild(el('div', 'bs-name', p.name));
        c.appendChild(el('div', 'bs-price', p.price + (p.stock > 0 ? '  •  Stok ' + p.stock : '  •  Stok habis')));
        if (p.reasons && p.reasons.length) {
            var ul = el('ul');
            p.reasons.forEach(function (r) { ul.appendChild(el('li', null, r)); });
            c.appendChild(ul);
        }
        (p.notes || []).forEach(function (n) { c.appendChild(el('div', 'bs-note', '⚠ ' + n)); });
        if (p.url) {
            var a = el('a', null, 'Lihat produk →');
            a.href = p.url;
            c.appendChild(a);
        }
        list.appendChild(c);
    }

    function addTable(t) {
        var wrap = el('div', 'bs-tablewrap');
        var table = el('table');
        var thead = el('thead'), trh = el('tr');
        t.headers.forEach(function (h) { trh.appendChild(el('th', null, h)); });
        thead.appendChild(trh);
        table.appendChild(thead);
        var tbody = el('tbody');
        t.rows.forEach(function (r) {
            var tr = el('tr');
            r.forEach(function (cell) { tr.appendChild(el('td', null, cell)); });
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        wrap.appendChild(table);
        list.appendChild(wrap);
    }

    function render(data) {
        addBot(data.reply);
        if (data.table) addTable(data.table);
        if (!data.table) (data.products || []).forEach(addCard);
        (data.links || []).forEach(function (l) {
            var a = el('a', 'bs-link', '→ ' + l.label);
            a.href = l.url;
            list.appendChild(a);
        });
        if (data.suggestions && data.suggestions.length) {
            var chips = el('div', 'bs-chips');
            data.suggestions.forEach(function (s) {
                var b = el('button', 'bs-chip', s);
                b.type = 'button';
                b.setAttribute('data-chip', s);
                chips.appendChild(b);
            });
            list.appendChild(chips);
        }
        scrollDown();
        save();
    }

    function clearChips() {
        list.querySelectorAll('.bs-chips').forEach(function (c) { c.remove(); });
    }

    function send(text, productId) {
        text = (text || '').trim();
        if (!text || busy) return;
        busy = true;
        sendBtn.disabled = true;
        clearChips();
        addUser(text);
        var typing = el('div', 'bs-msg bs-bot', 'Sedang mengetik…');
        list.appendChild(typing);
        scrollDown();

        fetch(URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': TOKEN
            },
            body: JSON.stringify({ message: text, product_id: productId || null })
        })
        .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function (data) { typing.remove(); render(data); })
        .catch(function () {
            typing.remove();
            addBot('Maaf, terjadi gangguan saat menghubungi server. Coba lagi sebentar ya.');
            save();
        })
        .then(function () { busy = false; sendBtn.disabled = false; });
    }

    // Pemulihan percakapan antar halaman (satu tab)
    var restored = false;
    try {
        var saved = JSON.parse(sessionStorage.getItem(KEY) || 'null');
        if (saved && saved.html) {
            list.innerHTML = saved.html;
            restored = true;
            if (saved.open) root.classList.add('open');
            scrollDown();
        }
    } catch (e) {}

    if (!restored) {
        addBot('Halo! Aku asisten Beauty Store. Tanya aku soal kandungan dan cara pakai produk, minta rekomendasi atau perbandingan produk, atau tanya cara pemesanan.');
        render({
            reply: '', table: null, products: [], links: [],
            suggestions: [
                'Cari moisturizer untuk kulit berminyak di bawah Rp100.000',
                'Apa fungsi niacinamide?',
                'Bagaimana cara memesan produk?'
            ]
        });
        list.removeChild(list.querySelector('.bs-bot:nth-of-type(2)'));
    }

    root.querySelector('.bs-fab').addEventListener('click', function () { setOpen(!root.classList.contains('open')); });
    root.querySelector('.bs-close').addEventListener('click', function () { setOpen(false); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = input.value;
        input.value = '';
        send(v);
    });

    list.addEventListener('click', function (e) {
        var chip = e.target.closest('[data-chip]');
        if (chip) send(chip.getAttribute('data-chip'));
    });

    // Tombol "Tanya Chatbot" di kartu produk: <button data-chat-ask="Brand Nama" data-chat-product="ID">
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-chat-ask]');
        if (!btn) return;
        setOpen(true);
        send('Ceritakan tentang ' + btn.getAttribute('data-chat-ask'), btn.getAttribute('data-chat-product'));
    });
})();
</script>
