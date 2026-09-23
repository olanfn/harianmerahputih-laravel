<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Redaksi') · Harian Merah Putih</title>
    <link rel="icon" href="{{ asset('branding/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
@php
    $adminUser = auth()->user();
    $roleNames = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'editor' => 'Editor', 'writer' => 'Writer'];
    $navItems = [
        ['route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.articles.index', 'active' => 'admin.articles.*', 'icon' => 'article', 'label' => 'Artikel'],
        ['route' => 'admin.showcase.index', 'params' => ['event-photos'], 'activePath' => 'admin/showcase/event-photos*', 'icon' => 'camera', 'label' => 'Foto Peristiwa'],
        ['route' => 'admin.showcase.index', 'params' => ['tv-videos'], 'activePath' => 'admin/showcase/tv-videos*', 'icon' => 'video', 'label' => 'Merah Putih TV'],
        ['route' => 'admin.media.index', 'active' => 'admin.media.*', 'icon' => 'image', 'label' => 'Media Library'],
    ];
@endphp
<div class="admin-shell" data-admin-shell>
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-sidebar__brand">
            <a href="{{ route('admin.dashboard') }}" aria-label="Dashboard redaksi"><img src="{{ asset('branding/logo-primary.png') }}" alt="Harian Merah Putih"></a>
            <button type="button" class="admin-sidebar__close" data-admin-menu aria-label="Tutup navigasi"><x-icon name="close" /></button>
        </div>
        <div class="admin-sidebar__workspace"><span>Workspace</span><strong>Portal Redaksi</strong></div>
        <nav class="admin-nav" aria-label="Navigasi redaksi">
            <span class="admin-nav__label">Konten</span>
            @foreach($navItems as $item)
                @php $isActive = isset($item['active']) ? request()->routeIs($item['active']) : request()->is($item['activePath']); @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}" @class(['is-active' => $isActive])><x-icon :name="$item['icon']" /><span>{{ $item['label'] }}</span></a>
            @endforeach
            @if(in_array($adminUser->role, ['super_admin', 'admin'], true))
                <span class="admin-nav__label">Manajemen</span>
                <a href="{{ route('admin.categories.index') }}" @class(['is-active' => request()->routeIs('admin.categories.*')])><x-icon name="folder" /><span>Kategori</span></a>
                <a href="{{ route('admin.tags.index') }}" @class(['is-active' => request()->routeIs('admin.tags.*')])><x-icon name="tag" /><span>Tag</span></a>
                <a href="{{ route('admin.redaction.edit') }}" @class(['is-active' => request()->routeIs('admin.redaction.*', 'admin.pages.*')])><x-icon name="edit" /><span>Halaman Publik</span></a>
                <a href="{{ route('admin.audit.index') }}" @class(['is-active' => request()->routeIs('admin.audit.*')])><x-icon name="audit" /><span>Audit Log</span></a>
            @endif
            @if($adminUser->role === 'super_admin')
                <a href="{{ route('admin.users.index') }}" @class(['is-active' => request()->routeIs('admin.users.*')])><x-icon name="users" /><span>Pengguna</span></a>
            @endif
        </nav>
        <div class="admin-sidebar__footer">
            <div class="admin-user-mini"><span class="admin-avatar">{{ strtoupper(mb_substr($adminUser->name, 0, 1)) }}</span><span><strong>{{ $adminUser->name }}</strong><small>{{ $roleNames[$adminUser->role] ?? $adminUser->role }}</small></span></div>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-logout"><x-icon name="logout" /><span>Keluar</span></button></form>
        </div>
    </aside>
    <div class="admin-workspace">
        <header class="admin-topbar">
            <button type="button" class="admin-menu-button" data-admin-menu aria-controls="admin-sidebar" aria-expanded="false"><x-icon name="menu" /><span class="sr-only">Buka navigasi</span></button>
            <div class="admin-topbar__context"><span>Harian Merah Putih</span><strong>@yield('section', 'Ruang Redaksi')</strong></div>
            <div class="admin-topbar__actions"><a href="{{ route('home') }}" target="_blank" rel="noopener"><x-icon name="external" /><span>Lihat situs</span></a><span class="admin-avatar">{{ strtoupper(mb_substr($adminUser->name, 0, 1)) }}</span></div>
        </header>
        <main class="admin-main">
            @if(session('status'))<div class="admin-flash admin-flash--success" role="status"><x-icon name="check" /><span>{{ session('status') }}</span></div>@endif
            @if($errors->any())<div class="admin-flash admin-flash--error" role="alert"><x-icon name="alert" /><div><strong>Data belum dapat diproses</strong><span>{{ $errors->first() }}</span></div></div>@endif
            @yield('content')
        </main>
    </div>
    <button type="button" class="admin-overlay" data-admin-menu aria-label="Tutup navigasi"></button>
</div>
</body>
</html>
