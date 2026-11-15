@extends('layouts.user')

@section('title', 'Pusat Bantuan - Beauty Store')

@push('styles')
<style>
    .kb { max-width: 900px; margin: 50px auto; padding: 0 30px 40px; }
    .kb h1 { font-size: 30px; margin-bottom: 6px; }
    .kb .lead { color: var(--c-ink-soft); margin-bottom: 30px; }
    .kb h2 { font-size: 20px; margin: 34px 0 14px; }
    .kb details { background: var(--c-surface); border: 1px solid var(--c-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 10px; }
    .kb summary { cursor: pointer; font-weight: 800; }
    .kb details[open] { border-color: var(--c-pink-strong); }
    .kb .body { margin-top: 10px; color: var(--c-ink-soft); line-height: 1.7; }
</style>
@endpush

@section('content')
<div class="kb">
    <h1>Pusat Bantuan</h1>
    <p class="lead">Pertanyaan umum dan panduan skincare. Kamu juga bisa bertanya langsung ke chatbot di pojok kanan bawah.</p>

    <h2>Pertanyaan yang sering diajukan</h2>
    @forelse($faqs as $faq)
        <details id="faq-{{ $faq->id }}">
            <summary>{{ $faq->question }}</summary>
            <div class="body">{!! nl2br(e($faq->answer)) !!}</div>
        </details>
    @empty
        <p>Belum ada FAQ.</p>
    @endforelse

    @foreach(['panduan' => 'Panduan skincare', 'bahan' => 'Kamus bahan skincare'] as $key => $label)
        @if(isset($articles[$key]))
            <h2>{{ $label }}</h2>
            @foreach($articles[$key] as $article)
                <details id="artikel-{{ $article->slug }}">
                    <summary>{{ $article->title }}</summary>
                    <div class="body">{!! nl2br(e($article->content)) !!}</div>
                </details>
            @endforeach
        @endif
    @endforeach

    <p style="margin-top:30px;color:var(--c-ink-soft);font-size:13px;">
        Informasi pada halaman ini bersifat umum dan bukan pengganti saran dokter.
    </p>
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