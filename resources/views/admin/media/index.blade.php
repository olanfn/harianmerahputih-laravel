@extends('admin.layout')
@section('title', 'Media Library')
@section('section', 'Asset Redaksi')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Asset redaksi</span><h1>Media Library</h1><p>Lihat penggunaan gambar dan hapus media yang tidak lagi terhubung.</p></div></header>
<form method="get" class="admin-search"><x-icon name="search" /><input name="q" value="{{ request('q') }}" placeholder="Cari berdasarkan nama file"><button class="admin-button">Cari media</button></form>
<div class="media-library-grid">
    @forelse($media as $item)
        <article class="media-library-card">
            <div class="media-library-card__image">
                <img src="{{ $item->url() }}" alt="{{ $item->alt_text ?: $item->original_name }}">
                <span>{{ strtoupper(pathinfo($item->original_name, PATHINFO_EXTENSION)) }}</span>
            </div>
            <div class="media-library-card__body">
                <h2 title="{{ $item->original_name }}">{{ $item->original_name }}</h2>
                <p><x-icon name="article" />{{ $item->article_links_count }} relasi artikel</p>
                @foreach($item->articleLinks as $link)
                    @if($link->article)
                        <a href="{{ route('admin.articles.edit', $link->article) }}">{{ $link->article->title }}</a>
                    @endif
                @endforeach
                @if($item->article_links_count === 0)
                    <form method="post" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Hapus media yang tidak dipakai?')">
                        @csrf
                        @method('DELETE')
                        <button class="admin-button admin-button--danger"><x-icon name="trash" />Hapus media</button>
                    </form>
                @endif
            </div>
        </article>
    @empty
        <div class="admin-empty admin-empty--wide">
            <x-icon name="image" size="40" />
            <h3>Media belum tersedia</h3>
            <p>Gambar yang diunggah melalui editor artikel akan muncul di sini.</p>
        </div>
    @endforelse
</div>
<div class="admin-pagination">{{ $media->links() }}</div>
@endsection
