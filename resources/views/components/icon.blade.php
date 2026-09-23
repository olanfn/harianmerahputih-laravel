@props(['name', 'size' => 18])
<svg {{ $attributes->merge(['class' => 'ui-icon', 'width' => $size, 'height' => $size, 'viewBox' => '0 0 24 24', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('facebook')<path fill="currentColor" d="M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.7 1.8-1.7H17V2.5c-.8-.1-1.7-.2-2.5-.2-2.6 0-4.4 1.6-4.4 4.6v2.6H7V13h3.1v9h3.4Z"/>@break
        @case('x')<path fill="currentColor" d="M3.5 3h4.2l5.2 6.9L18.8 3h1.7l-6.8 8.2L21 21h-4.2l-5.7-7.6L4.8 21H3l7.2-8.9L3.5 3Zm3.3 1.4H5.9l11.6 15.2h.9L6.8 4.4Z"/>@break
        @case('instagram')<path fill="currentColor" fill-rule="evenodd" d="M7.2 2h9.6A5.2 5.2 0 0 1 22 7.2v9.6a5.2 5.2 0 0 1-5.2 5.2H7.2A5.2 5.2 0 0 1 2 16.8V7.2A5.2 5.2 0 0 1 7.2 2Zm-.1 2A3.1 3.1 0 0 0 4 7.1v9.8A3.1 3.1 0 0 0 7.1 20h9.8a3.1 3.1 0 0 0 3.1-3.1V7.1A3.1 3.1 0 0 0 16.9 4H7.1Zm10.1 1.5a1.3 1.3 0 1 1 0 2.6 1.3 1.3 0 0 1 0-2.6ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>@break
        @case('youtube')<path fill="currentColor" d="M21.6 7.2a2.8 2.8 0 0 0-2-2C17.8 4.7 12 4.7 12 4.7s-5.8 0-7.6.5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2 12a29 29 0 0 0 .4 4.8 2.8 2.8 0 0 0 2 2c1.8.5 7.6.5 7.6.5s5.8 0 7.6-.5a2.8 2.8 0 0 0 2-2A29 29 0 0 0 22 12a29 29 0 0 0-.4-4.8ZM10 15.2V8.8l5.5 3.2-5.5 3.2Z"/>@break
        @case('tiktok')<path fill="currentColor" d="M15.8 2h-3.4v13.2a3 3 0 1 1-2.1-2.9V8.9a6.4 6.4 0 1 0 5.5 6.3V8.4A8.2 8.2 0 0 0 20.6 10V6.6A4.8 4.8 0 0 1 15.8 2Z"/>@break
        @case('rss')<path fill="currentColor" d="M5 17a2 2 0 1 1 0 4 2 2 0 0 1 0-4Zm-2-7v3a8 8 0 0 1 8 8h3C14 14.9 9.1 10 3 10Zm0-7v3c8.3 0 15 6.7 15 15h3C21 11.1 12.9 3 3 3Z"/>@break
        @case('mail')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h18v12H3V6Zm1 1 8 6 8-6"/>@break
        @case('lock')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 10h12v11H6V10Zm3 0V7a3 3 0 0 1 6 0v3m-3 4v3"/>@break
        @case('shield')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2 20 5v6c0 5-3.4 9-8 11-4.6-2-8-6-8-11V5l8-3Zm-3 10 2 2 4-5"/>@break
        @case('edit')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4 20 4.2-1 10.6-10.6a2.1 2.1 0 0 0-3-3L5.2 16 4 20Zm10.5-13.5 3 3"/>@break
        @case('image')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18v16H3V4Zm2 13 4.5-5 3.5 3 2-2 4 4M8 9h.01"/>@break
        @case('arrow')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-5-5 5 5-5 5"/>@break
        @case('chevron-down')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/>@break
        @case('dashboard')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4V4Zm10 0h6v9h-6V4ZM4 14h6v6H4v-6Zm10 3h6v3h-6v-3Z"/>@break
        @case('article')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h9l4 4v14H6V3Zm9 0v5h4M9 12h7M9 16h7M9 8h2"/>@break
        @case('camera')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h4l2-3h6l2 3h4v13H3V7Zm9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/>@break
        @case('video')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h13v12H3V6Zm13 4 5-3v10l-5-3v-4Z"/>@break
        @case('folder')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h7l2 2h9v11H3V6Z"/>@break
        @case('tag')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4 4 8 .5 8 8-7.5 7.5-8-8L4 4Zm5 5h.01"/>@break
        @case('audit')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 3h16v18H4V3Zm4 5h8M8 12h8M8 16h5"/>@break
        @case('users')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1a3 3 0 1 0 0-6m-14 15c0-4 2-6 6-6s6 2 6 6H2Zm13-6c4 0 6 2 6 6h-4"/>@break
        @case('logout')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 4H4v16h6m5-4 4-4-4-4m4 4H9"/>@break
        @case('menu')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>@break
        @case('close')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/>@break
        @case('external')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14 4h6v6m0-6-9 9M19 14v6H4V5h6"/>@break
        @case('check')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>@break
        @case('alert')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 2 21h20L12 3Zm0 6v5m0 3h.01"/>@break
        @case('plus')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M12 5v14M5 12h14"/>@break
        @case('trash')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M9 7V4h6v3m3 0-1 14H7L6 7m4 4v6m4-6v6"/>@break
        @case('history')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12a8 8 0 1 0 2-5.3L3 10m0-5v5h5m4-3v5l3 2"/>@break
        @case('upload')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m-5 5 5-5 5 5M4 15v5h16v-5"/>@break
        @case('search')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" d="m20 20-4.5-4.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>@break
        @case('clock')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm0 4v5l3 2"/>@break
        @case('home')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 11 9-8 9 8v9H3v-9Zm6 9v-6h6v6"/>@break
        @case('grid')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/>@break
        @case('star')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>@break
        @case('map')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15"/>@break
        @case('politics')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20h14M7 17V9m5 8V5m5 12v-6M4 20h16M5 9l7-4 7 4"/>@break
        @case('chart')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" d="M4 20V10m5 10V4m6 16v-7m5 7V7"/>@break
        @case('health')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21S4 16.5 4 10a4 4 0 0 1 8-1 4 4 0 0 1 8 1c0 6.5-8 11-8 11ZM8 12h3l1-3 2 6 1-3h2"/>@break
        @case('globe')<circle fill="none" stroke="currentColor" stroke-width="1.8" cx="12" cy="12" r="9"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" d="M3 12h18M12 3c2.2 2.5 3.3 5.5 3.3 9s-1.1 6.5-3.3 9c-2.2-2.5-3.3-5.5-3.3-9S9.8 5.5 12 3Z"/>@break
        @case('home')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 11 9-8 9 8v9H3v-9Zm6 9v-6h6v6"/>@break
        @case('grid')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"/>@break
        @case('star')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>@break
        @case('map')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Zm6-3v15m6-12v15"/>@break
        @case('politics')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20h14M7 17V9m5 8V5m5 12v-6M4 20h16M5 9l7-4 7 4"/>@break
        @case('chart')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V10m5 10V4m6 16v-7m5 7V7"/>@break
        @case('health')<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21S4 16.5 4 10a4 4 0 0 1 8-1 4 4 0 0 1 8 1c0 6.5-8 11-8 11ZM8 12h3l1-3 2 6 1-3h2"/>@break
        @case('globe')<circle fill="none" stroke="currentColor" stroke-width="1.8" cx="12" cy="12" r="9"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" d="M3 12h18M12 3c2.2 2.5 3.3 5.5 3.3 9s-1.1 6.5-3.3 9c-2.2-2.5-3.3-5.5-3.3-9S9.8 5.5 12 3Z"/>@break
    @endswitch
</svg>
