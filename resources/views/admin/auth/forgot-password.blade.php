<x-admin-auth-shell title="Lupa Password">
    <span class="admin-auth-eyebrow">Pemulihan Akun</span>
    <h1>Lupa password?</h1>
    <p class="admin-auth-intro">Masukkan email akun editorial. Tautan pengaturan ulang akan dikirim bila akun ditemukan.</p>
    @if(session('status'))<p class="admin-auth-success" role="status">{{ session('status') }}</p>@endif
    @error('email')<p class="admin-auth-alert" role="alert">{{ $message }}</p>@enderror
    <form method="post" action="{{ route('admin.password.email') }}" class="admin-auth-form">
        @csrf
        <label for="email">Alamat email</label>
        <div class="admin-auth-field"><x-icon name="mail" /><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@harianmerahputih.id" autocomplete="email" required autofocus></div>
        <button type="submit" class="admin-auth-submit"><span>Kirim tautan reset</span><x-icon name="arrow" /></button>
    </form>
    <p class="admin-auth-back"><a href="{{ route('admin.login') }}">← Kembali ke halaman login</a></p>
</x-admin-auth-shell>