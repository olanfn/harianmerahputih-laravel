@extends('admin.layout')
@section('title', 'Halaman '.$page->title)
@section('section', 'Halaman Institusional')
@section('content')
<header class="admin-page-head">
    <div><span class="admin-eyebrow">Halaman publik</span><h1>{{ $page->title }}</h1><p>{{ $page->summary }}</p></div>
    <a class="admin-button admin-button--secondary" href="{{ route($page->publicRouteName()) }}" target="_blank" rel="noopener"><x-icon name="external" />Lihat halaman</a>
</header>
<nav class="institutional-admin-tabs" aria-label="Pilih halaman institusional">
    @foreach($pages as $item)
        <a href="{{ route('admin.pages.edit', $item) }}" @class(['is-active' => $item->is($page)])>{{ $item->title }}</a>
    @endforeach
</nav>
<form class="editor-form admin-form-card rich-editor-form" method="post" action="{{ route('admin.pages.update', $page) }}" data-rich-editor>
    @csrf @method('PUT')
    <div class="admin-form-heading"><span class="admin-row-icon"><x-icon name="edit" /></span><div><h2>Isi halaman {{ mb_strtolower($page->title) }}</h2><p>Gunakan toolbar seperti pengolah kata. Konten akan disaring sebelum ditampilkan.</p></div></div>
    <div class="rich-editor-toolbar" role="toolbar" aria-label="Format teks">
        <select data-rich-block aria-label="Format paragraf"><option value="p">Paragraf</option><option value="h2">Judul besar</option><option value="h3">Subjudul</option><option value="h4">Judul kecil</option></select>
        <select data-rich-size aria-label="Ukuran font"><option value="3">Normal</option><option value="2">Kecil</option><option value="4">Sedang</option><option value="5">Besar</option><option value="6">Sangat besar</option></select>
        <span class="rich-editor-toolbar__group"><button type="button" data-rich-command="bold" aria-label="Tebal"><b>B</b></button><button type="button" data-rich-command="italic" aria-label="Miring"><i>I</i></button><button type="button" data-rich-command="underline" aria-label="Garis bawah"><u>U</u></button><button type="button" data-rich-command="strikeThrough" aria-label="Coret"><s>S</s></button></span>
        <span class="rich-editor-toolbar__group"><button type="button" data-rich-command="insertUnorderedList" aria-label="Daftar bullet">• List</button><button type="button" data-rich-command="insertOrderedList" aria-label="Daftar nomor">1. List</button></span>
        <span class="rich-editor-toolbar__group"><button type="button" data-rich-align="justifyLeft" aria-label="Rata kiri">≡</button><button type="button" data-rich-align="justifyCenter" aria-label="Rata tengah">≣</button><button type="button" data-rich-align="justifyRight" aria-label="Rata kanan">≡</button></span>
        <button type="button" data-rich-link>🔗 Tautan</button><button type="button" data-rich-command="removeFormat">Hapus format</button>
    </div>
    <div class="rich-editor-surface" contenteditable="true" role="textbox" aria-multiline="true" data-rich-surface>{!! old('content', $page->content) !!}</div>
    <textarea class="sr-only" name="content" data-rich-input required>{{ old('content', $page->content) }}</textarea>
    <div class="rich-editor-note"><x-icon name="shield" /><span>Script, embed, dan atribut berbahaya otomatis dihapus saat disimpan.</span></div>
    <div class="admin-form-actions"><a href="{{ route($page->publicRouteName()) }}" target="_blank" rel="noopener">Buka halaman publik</a><button class="admin-button" type="submit"><x-icon name="check" />Simpan perubahan</button></div>
</form>
@endsection
