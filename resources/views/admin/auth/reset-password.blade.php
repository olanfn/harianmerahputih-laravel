<x-admin-auth-shell title="Password Baru">
    <span class="admin-auth-eyebrow">Pemulihan Akun</span>
    <h1>Buat password baru</h1>
    <p class="admin-auth-intro">Gunakan minimal 12 karakter dan hindari password yang pernah digunakan.</p>
    @if($errors->any())<p class="admin-auth-alert" role="alert">{{ $errors->first() }}</p>@endif
    <form method="post" action="{{ route('admin.password.update') }}" class="admin-auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="email">Alamat email</label>
        <div class="admin-auth-field"><x-icon name="mail" /><input id="email" name="email" type="email" value="{{ $email }}" autocomplete="email" required></div>
        <label for="password">Password baru</label>
        <div class="admin-auth-field"><x-icon name="lock" /><input id="password" name="password" type="password" minlength="12" autocomplete="new-password" required><button type="button" class="password-toggle" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="Tampilkan password" title="Tampilkan password"><x-icon name="eye" size="17" /><x-icon name="eye-off" size="17" /></button></div>
        <label for="password_confirmation">Konfirmasi password</label>
        <div class="admin-auth-field"><x-icon name="shield" /><input id="password_confirmation" name="password_confirmation" type="password" minlength="12" autocomplete="new-password" required><button type="button" class="password-toggle" data-password-toggle aria-controls="password_confirmation" aria-pressed="false" aria-label="Tampilkan password" title="Tampilkan password"><x-icon name="eye" size="17" /><x-icon name="eye-off" size="17" /></button></div>
        <button type="submit" class="admin-auth-submit"><span>Simpan password</span><x-icon name="arrow" /></button>
    </form>
</x-admin-auth-shell>
