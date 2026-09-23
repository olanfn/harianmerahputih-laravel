@extends('admin.layout')
@section('content')
<header class="admin-page-head"><div><span>Editorial</span><h1>Riwayat: {{ $article->title }}</h1></div><a href="{{ route('admin.articles.edit', $article) }}">Kembali ke editor</a></header>
<div class="admin-table">@forelse($revisions as $revision)<article><div><small>{{ $revision->created_at->format('d M Y H:i') }} · {{ $revision->editor?->name ?: 'system' }}</small><h2>{{ $revision->title }}</h2><span>Status: {{ $revision->status }}</span></div><form method="post" action="{{ route('admin.articles.revisions.restore', [$article, $revision]) }}" onsubmit="return confirm('Pulihkan snapshot ini sebagai draf?')">@csrf<button class="admin-button">Pulihkan</button></form></article>@empty<p>Belum ada revision.</p>@endforelse</div>{{ $revisions->links() }}
@endsection
