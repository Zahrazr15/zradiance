<style>
    #bsChat { position: fixed; right: 22px; bottom: 22px; z-index: 100; font-family: var(--font); }
    #bsChat .bs-fab { width: 58px; height: 58px; border-radius: 50%; border: 0; background: var(--c-pink-strong); color: #fff; font-size: 26px; cursor: pointer; box-shadow: 0 6px 20px rgba(61,34,48,.25); }
    #bsChat .bs-fab:hover { background: var(--c-ink); }
    #bsChat .bs-panel { display: none; position: absolute; right: 0; bottom: 72px; width: 370px; max-width: calc(100vw - 30px); height: 540px; max-height: calc(100vh - 110px); background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 16px; box-shadow: 0 12px 40px rgba(61,34,48,.22); overflow: hidden; flex-direction: column; }
    #bsChat.open .bs-panel { display: flex; }
    #bsChat .bs-head { background: var(--c-pink); color: var(--c-ink); padding: 14px 16px; display: flex; justify-content: space-between; align-items: center; }
    #bsChat .bs-head strong { font-size: 15px; }
    #bsChat .bs-head small { display: block; font-size: 12px; color: var(--c-ink-soft); }
    #bsChat .bs-close { background: none; border: 0; font-size: 20px; cursor: pointer; color: var(--c-ink); }
    #bsChat .bs-list { flex: 1; overflow-y: auto; padding: 14px; background: var(--c-bg); display: flex; flex-direction: column; gap: 10px; }
    #bsChat .bs-msg { max-width: 88%; padding: 10px 13px; border-radius: 14px; font-size: 14px; line-height: 1.5; white-space: pre-line; word-wrap: break-word; }
    #bsChat .bs-bot { background: var(--c-surface); border: 1px solid var(--c-border); align-self: flex-start; border-bottom-left-radius: 4px; }
    #bsChat .bs-user { background: var(--c-pink); align-self: flex-end; border-bottom-right-radius: 4px; }
    #bsChat .bs-card { align-self: stretch; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 12px; padding: 11px 13px; font-size: 13px; }
    #bsChat .bs-card .bs-brand { color: var(--c-ink-soft); font-size: 12px; }
    #bsChat .bs-card .bs-name { font-weight: 800; font-size: 14px; }
    #bsChat .bs-card .bs-price { font-weight: 800; margin: 3px 0; }
    #bsChat .bs-card ul { padding-left: 17px; margin: 5px 0; }
    #bsChat .bs-card .bs-note { color: #8a5a00; background: #FFF3D6; border-radius: 6px; padding: 4px 8px; margin-top: 5px; font-size: 12px; }
    #bsChat .bs-card a { color: var(--c-pink-strong); font-weight: 700; text-decoration: none; }
    #bsChat .bs-tablewrap { align-self: stretch; overflow-x: auto; background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 12px; }
    #bsChat table { border-collapse: collapse; font-size: 12px; min-width: 100%; }
    #bsChat th, #bsChat td { border-bottom: 1px solid var(--c-border); padding: 7px 9px; text-align: left; vertical-align: top; min-width: 96px; }
    #bsChat th { background: var(--c-cream); }
    #bsChat td:first-child { font-weight: 800; background: var(--c-bg); }
    #bsChat .bs-chips { display: flex; flex-wrap: wrap; gap: 6px; align-self: stretch; }
    #bsChat .bs-chip { border: 1px solid var(--c-pink-strong); background: var(--c-surface); color: var(--c-ink); border-radius: 999px; padding: 6px 11px; font: 600 12px var(--font); cursor: pointer; text-align: left; }
    #bsChat .bs-chip:hover { background: var(--c-cream); }
    #bsChat .bs-link { align-self: flex-start; font-size: 13px; color: var(--c-pink-strong); font-weight: 700; text-decoration: none; }
    #bsChat .bs-form { display: flex; gap: 8px; padding: 10px; border-top: 1px solid var(--c-border); background: var(--c-surface); }
    #bsChat .bs-form input { flex: 1; padding: 10px 12px; border: 1px solid var(--c-border); border-radius: 10px; font: inherit; font-size: 14px; }
    #bsChat .bs-form input:focus { outline: none; border-color: var(--c-pink-strong); }
    #bsChat .bs-form button { border: 0; background: var(--c-pink-strong); color: #fff; border-radius: 10px; padding: 0 16px; font: 800 14px var(--font); cursor: pointer; }
    #bsChat .bs-form button:disabled { opacity: .5; cursor: not-allowed; }
    @media (max-width: 480px) {
        #bsChat { right: 12px; bottom: 12px; }
        #bsChat .bs-panel { width: calc(100vw - 24px); height: calc(100vh - 100px); }
    }
</style>

<div id="bsChat">
    <div class="bs-panel" role="dialog" aria-label="Chatbot Beauty Store">
        <div class="bs-head">
            <div>
                <strong>Asisten Beauty Store</strong>
                <small>Jawaban berdasarkan data toko</small>
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
        // Saat tabel perbandingan tampil, kartu produk tidak perlu diulang
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
        list.removeChild(list.querySelector('.bs-bot:nth-of-type(2)')); // buang bubble kosong dari render()
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