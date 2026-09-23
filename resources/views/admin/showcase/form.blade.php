@php($label = $type === 'event-photos' ? 'Foto Peristiwa' : 'Merah Putih TV')
@extends('admin.layout')
@section('title', ($item->exists ? 'Edit ' : 'Tambah ').$label)
@section('section', 'Konten Visual')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Konten visual</span><h1>{{ $item->exists ? 'Edit' : 'Tambah' }} {{ $label }}</h1><p>Lengkapi informasi dan pengaturan publikasi konten.</p></div><a class="admin-button admin-button--secondary" href="{{ route('admin.showcase.index', $type) }}">Kembali</a></header>
<form class="editor-form admin-form-card" method="post" action="{{ $item->exists ? route('admin.showcase.update', [$type, $item->id]) : route('admin.showcase.store', $type) }}">@csrf @if($item->exists) @method('PUT') @endif
    <div class="admin-form-heading"><span class="admin-row-icon"><x-icon :name="$type === 'event-photos' ? 'camera' : 'video'" /></span><div><h2>Informasi konten</h2><p>Judul dan status wajib diisi.</p></div></div>
    <label><span>Judul <b>*</b></span><input name="title" value="{{ old('title', $item->title) }}" required></label><label><span>Slug URL</span><input name="slug" value="{{ old('slug', $item->slug) }}"></label><label><span>Ringkasan</span><textarea name="excerpt" rows="4">{{ old('excerpt', $item->excerpt) }}</textarea></label><label><span>Isi</span><textarea name="body" rows="10">{{ old('body', $item->body) }}</textarea></label>
    <div class="admin-form-grid"><label><span>Status</span><select name="status"><option value="draft" @selected(old('status', $item->status ?: 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $item->status) === 'published')>Published</option></select></label><label><span>Waktu terbit</span><input type="datetime-local" name="published_at" value="{{ old('published_at', optional($item->published_at)->format('Y-m-d\TH:i')) }}"></label><label><span>ID media utama</span><input type="number" name="media_id" value="{{ old('media_id', $item->media_id) }}"></label><label><span>Urutan</span><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $item->sort_order ?: 0) }}"></label></div>
    <div class="admin-form-actions"><a href="{{ route('admin.showcase.index', $type) }}">Batal</a><button class="admin-button" type="submit"><x-icon name="check" />Simpan konten</button></div>
</form>
@endsection
