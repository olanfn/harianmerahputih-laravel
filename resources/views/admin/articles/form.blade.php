@extends('admin.layout')
@section('title', $article->exists ? 'Edit Artikel' : 'Artikel Baru')
@section('section', 'Editor Artikel')
@section('content')
<header class="admin-page-head admin-page-head--editor">
    <div><span class="admin-eyebrow">Konten editorial</span><h1>{{ $article->exists ? 'Edit artikel' : 'Artikel baru' }}</h1><p>{{ $article->exists ? 'Perubahan akan tercatat dalam riwayat revisi.' : 'Lengkapi informasi utama, status, dan gambar artikel.' }}</p></div>
    <div class="admin-head-actions">@if($article->exists)<a class="admin-button admin-button--secondary" href="{{ route('admin.articles.revisions', $article) }}"><x-icon name="history" />Riwayat</a><a class="admin-button admin-button--secondary" href="{{ route('admin.articles.preview', $article) }}" target="_blank"><x-icon name="external" />Pratinjau</a>@endif<a class="admin-button admin-button--secondary" href="{{ route('admin.articles.index') }}">Kembali</a></div>
</header>
<form class="editor-form" method="post" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
    @csrf @if($article->exists) @method('PUT') @endif
    <div class="editor-grid">
        <section class="admin-form-card">
            <div class="admin-form-heading"><span class="admin-row-icon"><x-icon name="article" /></span><div><h2>Isi berita</h2><p>Tulis judul yang jelas dan isi yang mudah dibaca.</p></div></div>
            <label><span>Judul <b>*</b></span><input class="admin-title-input" name="title" value="{{ old('title', $article->title) }}" placeholder="Tulis judul berita" required></label>
            <label><span>Slug URL</span><input name="slug" value="{{ old('slug', $article->slug) }}" placeholder="Dibuat otomatis bila kosong"></label>
            <label><span>Ringkasan <small>(opsional)</small></span><textarea name="excerpt" rows="4" placeholder="Kosongkan untuk membuat ringkasan otomatis dari isi artikel">{{ old('excerpt', $article->excerpt) }}</textarea><span class="admin-field-help">Jika dikosongkan, sistem mengambil ringkasan dari paragraf awal isi artikel.</span></label>
            <label><span>Isi artikel <b>*</b></span><span class="admin-field-help">Gunakan tombol sisipkan setelah memilih media berperan Inline.</span><textarea id="article-body" name="body" rows="18" placeholder="Mulai tulis berita di sini..." required>{{ old('body', $article->body) }}</textarea></label>
        </section>
        <aside class="admin-form-card admin-editor-sidebar">
            <div class="admin-form-heading"><span class="admin-row-icon"><x-icon name="check" /></span><div><h2>Publikasi</h2><p>Atur klasifikasi dan status.</p></div></div>
            <label><span>Kategori <b>*</b></span><select name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((int) old('category_id', $article->category_id) === $category->id)>{{ $category->name }}</option>@endforeach</select></label>
            <label><span>Status <b>*</b></span><select name="status">@foreach(['draft' => 'Simpan draf', 'review' => 'Kirim review', 'scheduled' => 'Jadwalkan', 'published' => 'Terbitkan', 'archived' => 'Arsipkan'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $article->status ?: 'draft') === $value)>{{ $label }}</option>@endforeach</select></label>
            <label><span>Waktu terbit</span><input type="datetime-local" name="published_at" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"><small class="admin-field-help">Kosongkan jika langsung menerbitkan sekarang. Wajib diisi jika status dijadwalkan.</small></label>
            @if(in_array(auth()->user()->role, ['super_admin', 'admin', 'editor'], true))<label class="admin-switch"><input type="checkbox" name="is_editor_pick" value="1" @checked(old('is_editor_pick', $article->is_editor_pick))><span><b>Pilihan Editor</b><small>Tampilkan artikel pada daftar kurasi redaksi.</small></span></label>@endif
            <fieldset class="admin-tag-field"><legend>Tag</legend><div>@forelse($tags as $tag)<label><input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tag_ids', $article->tags->pluck('id')->all())))><span>{{ $tag->name }}</span></label>@empty<small>Belum ada tag.</small>@endforelse</div></fieldset>
        </aside>
    </div>
    <section class="media-editor admin-form-card" data-upload-url="{{ route('admin.media.store') }}">
        <div class="admin-form-heading"><span class="admin-row-icon"><x-icon name="image" /></span><div><h2>Gambar artikel</h2><p>Unggah maksimal 20 gambar, masing-masing hingga 10 MB.</p></div></div>
        <label class="media-drop" id="media-drop" for="media-input"><x-icon name="upload" size="30" /><strong>Tarik gambar ke sini atau pilih berkas</strong><span>JPG, PNG, atau WebP</span><input id="media-input" type="file" accept="image/jpeg,image/png,image/webp" multiple></label>
        <div id="media-upload-status" class="media-upload-status" aria-live="polite"></div>
        <div class="media-toolbar"><span>Seret kartu untuk mengubah urutan.</span><button type="button" class="admin-button admin-button--secondary" id="insert-media"><x-icon name="image" />Sisipkan media inline</button></div>
        <div id="media-list" class="media-list">@foreach($article->mediaLinks ?? [] as $link)<article class="media-item" draggable="true" data-id="{{ $link->media_id }}" data-url="{{ $link->media->url() }}"><div class="media-item__preview"><img src="{{ $link->media->url() }}" alt=""><span class="media-item__handle">⋮⋮</span></div><div class="media-item__fields"><label><span>Peran</span><select data-field="role"><option value="featured" @selected($link->role === 'featured')>Gambar utama</option><option value="gallery" @selected($link->role === 'gallery')>Galeri</option><option value="inline" @selected($link->role === 'inline')>Inline</option></select></label><label><span>Alt text</span><input data-field="alt_text" value="{{ $link->media->alt_text }}"></label><label><span>Caption</span><input data-field="caption" value="{{ $link->caption_override ?: $link->media->caption }}"></label><button class="media-remove" type="button" data-remove><x-icon name="trash" />Hapus relasi</button></div></article>@endforeach</div>
    </section>
    <div class="admin-editor-actions"><span>Pastikan judul, kategori, dan status sudah sesuai.</span><button class="admin-button admin-button--large" type="submit"><x-icon name="check" />Simpan artikel</button></div>
</form>
@endsection
