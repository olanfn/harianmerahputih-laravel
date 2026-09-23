<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request) { abort_unless(in_array($request->user()->role, ['super_admin', 'admin'], true), 403); $logs = AuditLog::with('user')->latest()->paginate(40); return view('admin.audit.index', compact('logs')); }
}
