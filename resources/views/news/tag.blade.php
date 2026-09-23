@extends('layouts.app')

@php
    $robots = $articles->getCollection()->contains('is_demo', true) ? 'noindex' : null;
    $title = '#'.$tag->name;
    $metaDescription = 'Artikel Harian Merah Putih dengan tag '.$tag->name.'.';
@endphp

@section('content')
    <section class="site-container listing-page">
        <div class="listing-intro"><div><span class="section-kicker">Tag Artikel</span><h1>#{{ $tag->name }}</h1></div><p>Artikel yang terhubung dengan topik {{ $tag->name }}.</p></div>
        @if ($articles->isNotEmpty())
            <div class="listing-grid">@foreach ($articles as $article)<x-article-card :article="$article" />@endforeach</div>
            <div class="pagination-wrap">{{ $articles->links() }}</div>
        @else
            <div style="margin-top: 26px"><x-empty-state title="Belum ada artikel dengan tag ini" /></div>
        @endif
    </section>
@endsection