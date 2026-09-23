<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SiteContactController extends Controller
{
    private const KEYS = ['social.facebook', 'social.x', 'social.instagram', 'social.youtube', 'social.tiktok', 'social.rss', 'contact.office_phone', 'contact.whatsapp'];

    private function allow(): void
    {
        abort_unless(in_array(request()->user()->role, ['super_admin', 'admin'], true), 403);
    }

    public function edit()
    {
        $this->allow();
        return view('admin.site-contact.edit', ['settings' => Setting::values(self::KEYS)]);
    }

    public function update(Request $request)
    {
        $this->allow();
        $data = $request->validate([
            'social.facebook' => ['nullable', 'url', 'max:500'],
            'social.x' => ['nullable', 'url', 'max:500'],
            'social.instagram' => ['nullable', 'url', 'max:500'],
            'social.youtube' => ['nullable', 'url', 'max:500'],
            'social.tiktok' => ['nullable', 'url', 'max:500'],
            'social.rss' => ['nullable', 'url', 'max:500'],
            'contact.office_phone' => ['nullable', 'string', 'max:50'],
            'contact.whatsapp' => ['nullable', 'string', 'max:50'],
        ]);
        foreach (self::KEYS as $key) {
            [$group, $name] = explode('.', $key, 2);
            $value = $request->input($group.'.'.$name);
            if ($value === null) {
                $value = $request->input($group)[$name] ?? null;
            }
            Setting::put($key, $value);
        }
        AuditLog::record('site_contact.updated', null, ['keys' => self::KEYS]);
        return back()->with('status', 'Kontak dan media sosial diperbarui.');
    }
}
