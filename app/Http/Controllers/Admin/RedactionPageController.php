<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RedactionPage;
use App\Support\SafeRichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedactionPageController extends Controller
{
    public function edit(): View
    {
        return $this->editPage($this->page());
    }

    public function update(Request $request, SafeRichText $richText): RedirectResponse
    {
        return $this->updatePage($request, $this->page(), $richText);
    }

    public function editPage(RedactionPage $page): View
    {
        $this->allow();

        return view('admin.redaction.edit', [
            'page' => $page,
            'pages' => RedactionPage::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function updatePage(Request $request, RedactionPage $page, SafeRichText $richText): RedirectResponse
    {
        $this->allow();
        $data = $request->validate(['content' => ['required', 'string', 'max:100000']]);
        $content = $richText->clean($data['content']);
        abort_if(trim(strip_tags($content)) === '', 422, 'Konten halaman tidak boleh kosong.');

        $page->update(['content' => $content, 'updated_by' => $request->user()->id]);
        AuditLog::record('institutional_page.updated', $page, ['slug' => $page->slug]);

        return back()->with('status', 'Halaman '.$page->title.' berhasil diperbarui.');
    }

    private function allow(): void
    {
        abort_unless(in_array(request()->user()->role, ['super_admin', 'admin'], true), 403);
    }

    private function page(): RedactionPage
    {
        return RedactionPage::query()->where('slug', 'redaksi')->firstOrFail();
    }
}
