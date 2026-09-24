<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EventPhoto;
use App\Models\Media;
use App\Models\TvVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShowcaseController extends Controller
{
    public function index(string $type) { $this->allow($type); $model = $this->model($type); $items = $model::with('media')->latest('sort_order')->paginate(20); return view('admin.showcase.index', compact('items', 'type')); }
    public function create(string $type) { $this->allow($type); $model = $this->model($type); return view('admin.showcase.form', ['type' => $type, 'item' => new $model]); }
    public function store(Request $request, string $type) { $this->allow($type); $data = $this->prepareData($request); $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['title']).'-'.Str::lower(Str::random(5)); $data['author_id'] = $request->user()->id; $item = $this->model($type)::create($data); $this->markMediaPermanent($data['media_id'] ?? null); AuditLog::record($type.'.created', $item); return redirect()->route('admin.showcase.index', $type)->with('status', 'Konten berhasil disimpan.'); }
    public function edit(string $type, int $id) { $this->allow($type); return view('admin.showcase.form', ['type' => $type, 'item' => $this->model($type)::with('media')->findOrFail($id)]); }
    public function update(Request $request, string $type, int $id) { $this->allow($type); $item = $this->model($type)::findOrFail($id); $data = $this->prepareData($request); $item->update($data); $this->markMediaPermanent($data['media_id'] ?? null); AuditLog::record($type.'.updated', $item); return redirect()->route('admin.showcase.index', $type)->with('status', 'Konten berhasil diperbarui.'); }
    public function destroy(string $type, int $id) { $this->allow($type); $item = $this->model($type)::findOrFail($id); AuditLog::record($type.'.deleted', $item); $item->delete(); return back(); }
    private function allow(string $type): void { abort_unless(in_array(request()->user()->role, ['super_admin', 'admin', 'editor'], true) && in_array($type, ['event-photos', 'tv-videos'], true), 403); }
    private function model(string $type): string { return $type === 'event-photos' ? EventPhoto::class : TvVideo::class; }
    private function prepareData(Request $request): array { $data = $request->validate(['title' => 'required|max:255', 'slug' => 'nullable|max:255', 'excerpt' => 'nullable|max:1000', 'body' => 'nullable', 'status' => 'required|in:draft,published', 'published_at' => 'nullable|date', 'media_id' => 'nullable|exists:media,id', 'sort_order' => 'nullable|integer|min:0']); if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now(); return $data; }
    private function markMediaPermanent(?int $mediaId): void { if ($mediaId !== null) Media::whereKey($mediaId)->update(['is_temporary' => false]); }
}
