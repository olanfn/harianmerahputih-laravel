@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#b3132b]">Internal readiness</p>
        <h1 class="display-font mt-3 text-5xl font-bold">Foundation siap diperiksa.</h1>
        <p class="mt-5 max-w-xl text-base leading-7 text-black/60">Halaman ini hanya menampilkan status teknis minimum dan tidak mengekspos nilai environment atau kredensial.</p>
        <dl class="mt-10 divide-y divide-black/10 border-y border-black/15">
            @foreach ($checks as $label => $passed)
                <div class="flex items-center justify-between gap-5 py-5"><dt class="font-semibold">{{ $label }}</dt><dd class="text-sm font-bold {{ $passed ? 'text-emerald-700' : 'text-[#b3132b]' }}">{{ $passed ? 'OK' : 'Perlu perhatian' }}</dd></div>
            @endforeach
        </dl>
        <p class="mt-7 text-xs text-black/45">Laravel {{ $application::VERSION }} · {{ config('app.env') }}</p>
    </section>
@endsection