@extends('admin.layout')
@section('content')
<header class="admin-page-head"><div><span>Keamanan</span><h1>Audit Log</h1></div></header>
<div class="admin-table">@forelse($logs as $log)<article><div><small>{{ $log->created_at->format('d M Y H:i:s') }} · {{ $log->user?->email ?: 'system' }}</small><h2>{{ $log->action }}</h2><span>{{ $log->subject_type }} #{{ $log->subject_id }} · {{ $log->ip_address }}</span></div></article>@empty<p>Belum ada audit log.</p>@endforelse</div>{{ $logs->links() }}
@endsection
