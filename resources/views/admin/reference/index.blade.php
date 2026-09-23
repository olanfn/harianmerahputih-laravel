@php($resource = $type === 'category' ? 'categories' : 'tags')
@php($label = $type === 'category' ? 'Kategori' : 'Tag')
@extends('admin.layout')
@section('title', $label)
@section('section', 'Manajemen Konten')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Taksonomi</span><h1>{{ $label }}</h1><p>Atur {{ strtolower($label) }} untuk memudahkan pembaca menemukan berita.</p></div><a class="admin-button" href="{{ route('admin.'.$resource.'.create') }}"><x-icon name="plus" />Tambah {{ strtolower($label) }}</a></header>
<section class="admin-card"><div class="admin-card__head"><div><h2>Daftar {{ $label }}</h2><p>{{ $items->count() }} item tersedia</p></div></div><div class="admin-table">@forelse($items as $item)<article><div class="admin-row-main"><span class="admin-row-icon"><x-icon :name="$type === 'category' ? 'folder' : 'tag'" /></span><div><h3>{{ $item->name }}</h3><span>/{{ $item->slug }} @if(isset($item->articles_count)) · {{ $item->articles_count }} artikel @endif</span></div></div><div class="admin-row-actions"><a class="admin-action-link" href="{{ route('admin.'.$resource.'.edit', $item) }}"><x-icon name="edit" />Edit</a><form method="post" action="{{ route('admin.'.$resource.'.destroy', $item) }}" onsubmit="return confirm('Hapus item ini?')">@csrf @method('DELETE')<button class="admin-icon-danger" aria-label="Hapus {{ $item->name }}"><x-icon name="trash" /></button></form></div></article>@empty<div class="admin-empty"><x-icon :name="$type === 'category' ? 'folder' : 'tag'" size="36" /><h3>Belum ada {{ strtolower($label) }}</h3><p>Tambahkan item pertama untuk mulai mengelompokkan konten.</p></div>@endforelse</div></section>
@endsection
