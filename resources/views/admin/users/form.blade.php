@extends('admin.layout')
@section('title', ($user->exists ? 'Edit' : 'Tambah').' Pengguna')
@section('section', 'Administrasi')
@section('content')
<header class="admin-page-head"><div><span class="admin-eyebrow">Akses & peran</span><h1>{{ $user->exists ? 'Edit' : 'Tambah' }} pengguna</h1><p>Tentukan identitas dan kewenangan anggota redaksi.</p></div><a class="admin-button admin-button--secondary" href="{{ route('admin.users.index') }}">Kembali</a></header>
<form class="editor-form admin-form-card" method="post" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">@csrf @if($user->exists)@method('PUT')@endif
    <div class="admin-form-heading"><span class="admin-row-icon"><x-icon name="users" /></span><div><h2>Informasi akun</h2><p>Password minimal 12 karakter.</p></div></div>
    <div class="admin-form-grid"><label><span>Nama lengkap <b>*</b></span><input name="name" value="{{ old('name', $user->name) }}" required></label><label><span>Email <b>*</b></span><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label></div>
    <label><span>Peran <b>*</b></span><select name="role">@foreach(['super_admin' => 'Super Admin', 'admin' => 'Admin', 'editor' => 'Editor', 'writer' => 'Writer'] as $value => $role)<option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $role }}</option>@endforeach</select></label>
    <div class="admin-form-grid"><label><span>Password {{ $user->exists ? '(opsional)' : '*' }}</span><input type="password" name="password" minlength="12" @required(!$user->exists) autocomplete="new-password"><small>{{ $user->exists ? 'Kosongkan jika tidak ingin mengubah password.' : 'Gunakan sedikitnya 12 karakter.' }}</small></label><label><span>Konfirmasi password {{ $user->exists ? '' : '*' }}</span><input type="password" name="password_confirmation" minlength="12" @required(!$user->exists) autocomplete="new-password"></label></div>
    <div class="admin-form-actions"><a href="{{ route('admin.users.index') }}">Batal</a><button class="admin-button" type="submit"><x-icon name="check" />Simpan pengguna</button></div>
</form>
@endsection
