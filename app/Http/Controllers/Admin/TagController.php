<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(): View
    {
        $this->allow();

        return view('admin.reference.index', ['type' => 'tag', 'items' => Tag::orderBy('name')->get()]);
    }

    public function create(): View
    {
        $this->allow();

        return view('admin.reference.form', ['type' => 'tag', 'item' => new Tag]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->allow();
        $data = $request->validate(['name' => 'required|max:100', 'slug' => 'nullable|max:100|unique:tags,slug']);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $tag = Tag::create($data);
        AuditLog::record('tag.created', $tag);

        return redirect()->route('admin.tags.index')->with('status', 'Tag dibuat.');
    }

    public function edit(int $tag): View
    {
        $this->allow();

        return view('admin.reference.form', ['type' => 'tag', 'item' => Tag::findOrFail($tag)]);
    }

    public function update(Request $request, int $tag): RedirectResponse
    {
        $this->allow();
        $item = Tag::findOrFail($tag);
        $data = $request->validate(['name' => 'required|max:100', 'slug' => 'required|max:100|unique:tags,slug,'.$item->id]);
        $item->update($data);
        AuditLog::record('tag.updated', $item);

        return redirect()->route('admin.tags.index')->with('status', 'Tag diperbarui.');
    }

    public function destroy(int $tag): RedirectResponse
    {
        $this->allow();
        $item = Tag::findOrFail($tag);
        $item->delete();
        AuditLog::record('tag.deleted', $item);

        return back()->with('status', 'Tag dihapus.');
    }

    private function allow(): void
    {
        abort_unless(in_array(request()->user()->role, ['super_admin', 'admin'], true), 403);
    }
}
