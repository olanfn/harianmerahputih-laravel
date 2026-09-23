@php($resource = $type === 'category' ? 'categories' : 'tags')
@php($label = $type === 'category' ? 'Kategori' : 'Tag')
@extends('admin.layout')
@section('title', ($item->exists ? 'Edit ' : 'Tambah ').$label)
@section('section', 'Manajemen Konten')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Taksonomi</span><h1>{{ $item->exists ? 'Edit' : 'Tambah' }} {{ strtolower($label) }}</h1><p>Isi informasi berikut agar struktur konten tetap rapi dan konsisten.</p></div><a class="admin-button admin-button--secondary" href="{{ route('admin.'.$resource.'.index') }}">Kembali</a></header>
<form class="editor-form admin-form-card" method="post" action="{{ $item->exists ? route('admin.'.$resource.'.update', $item) : route('admin.'.$resource.'.store') }}">@csrf @if($item->exists)@method('PUT')@endif
    <div class="admin-form-heading"><span class="admin-row-icon"><x-icon :name="$type === 'category' ? 'folder' : 'tag'" /></span><div><h2>Informasi {{ strtolower($label) }}</h2><p>Kolom bertanda wajib harus diisi.</p></div></div>
    <div class="admin-form-grid"><label><span>Nama <b>*</b></span><input name="name" value="{{ old('name', $item->name) }}" required></label><label><span>Slug URL</span><input name="slug" value="{{ old('slug', $item->slug) }}" placeholder="Dibuat otomatis bila kosong"></label></div>
    @if($type === 'category')<label><span>Deskripsi</span><textarea name="description" rows="5" placeholder="Deskripsi singkat kategori">{{ old('description', $item->description) }}</textarea></label><div class="admin-form-grid"><label><span>Urutan</span><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}"></label><label class="admin-switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))><span><b>Kategori aktif</b><small>Tampilkan pada navigasi publik</small></span></label></div>@endif
    <div class="admin-form-actions"><a href="{{ route('admin.'.$resource.'.index') }}">Batal</a><button class="admin-button" type="submit"><x-icon name="check" />Simpan {{ strtolower($label) }}</button></div>
</form>
@endsection
