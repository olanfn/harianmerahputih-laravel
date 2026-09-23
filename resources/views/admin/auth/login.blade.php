<x-admin-auth-shell title="Masuk Redaksi">
    <span class="admin-auth-eyebrow">Portal Redaksi</span>
    <h1>Selamat datang kembali</h1>
    <p class="admin-auth-intro">Masuk menggunakan akun editorial Anda untuk melanjutkan pekerjaan.</p>
    @error('email')<p class="admin-auth-alert" role="alert">{{ $message }}</p>@enderror
    <form method="post" action="{{ route('admin.login.store') }}" class="admin-auth-form">
        @csrf
        <label for="email">Alamat email</label>
        <div class="admin-auth-field"><x-icon name="mail" /><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@harianmerahputih.id" autocomplete="email" required autofocus></div>
        <label for="password">Password</label>
        <div class="admin-auth-field"><x-icon name="lock" /><input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required></div>
        <div class="admin-auth-options"><label class="admin-auth-check"><input type="checkbox" name="remember"><span>Ingat saya</span></label><a href="{{ route('admin.password.request') }}">Lupa password?</a></div>
        <button type="submit" class="admin-auth-submit"><span>Masuk ke Redaksi</span><x-icon name="arrow" /></button>
    </form>
    <p class="admin-auth-back"><a href="{{ route('home') }}">← Kembali ke portal publik</a></p>
</x-admin-auth-shell>