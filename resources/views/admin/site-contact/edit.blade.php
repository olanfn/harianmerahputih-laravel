@extends('admin.layout')
@section('title', 'Kontak & Media Sosial')
@section('section', 'Pengaturan Situs')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Identitas publik</span><h1>Kontak & Media Sosial</h1><p>Kelola tautan yang tampil di top bar dan footer situs.</p></div></header>
<form method="post" action="{{ route('admin.site-contact.update') }}" class="editor-form admin-form-card">
    @csrf @method('PUT')
    <div class="admin-card__head"><div><h2>Media sosial</h2><p>Kosongkan jika kanal belum digunakan. Masukkan URL lengkap dengan https://.</p></div></div>
    <div class="admin-form-grid">
        @foreach(['facebook'=>'Facebook','x'=>'X','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok','rss'=>'RSS'] as $key => $label)
            <label><span>{{ $label }}</span><input type="url" name="social[{{ $key }}]" value="{{ old('social.'.$key, $settings['social.'.$key] ?? '') }}" placeholder="https://..."></label>
        @endforeach
    </div>
    <div class="admin-card__head"><div><h2>Kontak redaksi</h2><p>Nomor digunakan sebagai tautan telepon dan WhatsApp.</p></div></div>
    <div class="admin-form-grid">
        <label><span>Telepon kantor</span><input type="text" name="contact[office_phone]" value="{{ old('contact.office_phone', $settings['contact.office_phone'] ?? '') }}" placeholder="021-1234567"></label>
        <label><span>WhatsApp</span><input type="text" name="contact[whatsapp]" value="{{ old('contact.whatsapp', $settings['contact.whatsapp'] ?? '') }}" placeholder="628123456789"></label>
    </div>
    <div class="admin-form-actions"><button class="admin-button" type="submit"><x-icon name="check" />Simpan perubahan</button></div>
</form>
@endsection
