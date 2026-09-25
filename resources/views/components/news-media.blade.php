@props(['article' => null, 'size' => 'card'])
@php($featured = $article?->featuredMedia?->media)
<div class="news-media news-media--{{ $size }}" role="img" aria-label="{{ $featured?->alt_text ?: 'Visual artikel Harian Merah Putih' }}">
    @if ($featured)<img src="{{ $featured->url() }}" alt="{{ $featured->alt_text }}" class="news-media__image">@else<img src="{{ asset('branding/logo-mark.svg') }}" alt="" aria-hidden="true">@endif
</div>
