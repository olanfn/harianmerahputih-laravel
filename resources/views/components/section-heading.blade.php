<div class="section-heading">
    <div><span class="section-kicker">{{ $eyebrow ?? 'Harian Merah Putih' }}</span><h2>{{ $title }}</h2></div>
    @isset($link)<a href="{{ $link }}" class="section-heading__link focus-ring">{{ $linkText ?? 'Lihat Semua' }} <span aria-hidden="true">→</span></a>@endisset
</div>