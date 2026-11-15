@extends('layouts.user')

@section('title', 'Pusat Bantuan & Panduan Skincare - Beauty Store')

@push('styles')
<style>
    .kb-page {
        max-width: 960px;
        margin: 40px auto;
        padding: 0 32px 80px;
    }

    .kb-header {
        text-align: center;
        margin-bottom: 44px;
    }

    .kb-header h1 {
        font-size: 34px;
        color: var(--c-ink-heading);
        margin-bottom: 12px;
    }

    .kb-header .lead {
        color: var(--c-ink-soft);
        font-size: 16px;
        line-height: 1.7;
        max-width: 680px;
        margin: 0 auto;
    }

    .kb-chat-cta {
        margin: 28px auto 40px;
        background: linear-gradient(135deg, rgba(250, 172, 191, 0.3) 0%, rgba(255, 234, 187, 0.4) 100%);
        border: 1px solid var(--c-border);
        border-radius: var(--radius-lg);
        padding: 24px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .kb-chat-cta strong { font-size: 16px; color: var(--c-ink-heading); }
    .kb-chat-cta p { font-size: 13.5px; color: var(--c-ink-soft); margin: 4px 0 0; }

    .kb-section-title {
        font-size: 22px;
        color: var(--c-ink-heading);
        margin: 40px 0 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .kb-faq-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .kb details {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        padding: 18px 22px;
        transition: var(--transition);
        box-shadow: var(--shadow-xs);
    }

    .kb details:hover {
        border-color: var(--c-blush);
        box-shadow: var(--shadow-sm);
    }

    .kb details[open] {
        border-color: var(--c-pink-strong);
        background: #fff;
        box-shadow: var(--shadow-md);
    }

    .kb summary {
        cursor: pointer;
        font-weight: 800;
        font-size: 15.5px;
        color: var(--c-ink-heading);
        list-style: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .kb summary::-webkit-details-marker { display: none; }
    .kb summary::after {
        content: '+';
        font-size: 20px;
        font-weight: 400;
        color: var(--c-pink-strong);
        transition: transform 0.2s;
    }
    .kb details[open] summary::after {
        content: '−';
        transform: rotate(180deg);
    }

    .kb .body {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--c-border-subtle);
        color: var(--c-ink-soft);
        line-height: 1.8;
        font-size: 14.5px;
    }

    .kb-disclaimer {
        margin-top: 40px;
        padding: 16px 20px;
        background: #FAF5F6;
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--c-pink-strong);
        color: var(--c-ink-soft);
        font-size: 13px;
        line-height: 1.6;
    }

    @media (max-width: 600px) {
        .kb-page { padding: 0 18px 50px; margin-top: 24px; }
        .kb-header h1 { font-size: 28px; }
        .kb-chat-cta { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
<div class="kb-page kb">

    <div class="kb-header">
        <h1>Pusat Bantuan & Panduan Kecantikan</h1>
        <p class="lead">
            Temukan jawaban atas pertanyaan umum seputar pemesanan, pengiriman, serta panduan bahan skincare aktif untuk merawat kulitmu dengan benar.
        </p>
    </div>

    {{-- Chatbot Prompt Banner --}}
    <div class="kb-chat-cta">
        <div>
            <strong>Butuh Bantuan Langsung?</strong>
            <p>Asisten AI Beauty Store siap membantu menjawab pertanyaan kapan pun.</p>
        </div>
        <button
            type="button"
            class="btn btn-primary"
            onclick="document.getElementById('bsChat').querySelector('.bs-fab').click()"
        >
            💬 Buka Chatbot
        </button>
    </div>

    <h2 class="kb-section-title">❓ Pertanyaan yang Sering Diajukan (FAQ)</h2>
    <div class="kb-faq-list">
        @forelse($faqs as $faq)
            <details id="faq-{{ $faq->id }}">
                <summary>{{ $faq->question }}</summary>
                <div class="body">{!! nl2br(e($faq->answer)) !!}</div>
            </details>
        @empty
            <p style="color:var(--c-ink-soft);padding:16px 0;">Belum ada pertanyaan umum yang terdaftar.</p>
        @endforelse
    </div>

    @foreach(['panduan' => '📖 Panduan Perawatan Skincare', 'bahan' => '🧪 Kamus Bahan Aktif (Active Ingredients)'] as $key => $label)
        @if(isset($articles[$key]) && count($articles[$key]))
            <h2 class="kb-section-title">{{ $label }}</h2>
            <div class="kb-faq-list">
                @foreach($articles[$key] as $article)
                    <details id="artikel-{{ $article->slug }}">
                        <summary>{{ $article->title }}</summary>
                        <div class="body">{!! nl2br(e($article->content)) !!}</div>
                    </details>
                @endforeach
            </div>
        @endif
    @endforeach

    <div class="kb-disclaimer">
        ℹ️ <strong>Catatan:</strong> Informasi artikel dan panduan pada halaman ini bersifat edukatif dan umum. Selalu lakukan patch test sebelum mencoba produk baru dan konsultasikan dengan dokter spesialis kulit untuk kondisi dermatologis medis.
    </div>

</div>

<script>
    function openFromHash() {
        var id = decodeURIComponent(location.hash.replace('#', ''));
        if (!id) return;
        var node = document.getElementById(id);
        if (node && node.tagName === 'DETAILS') {
            node.open = true;
            node.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    window.addEventListener('hashchange', openFromHash);
    openFromHash();
</script>
@endsection
