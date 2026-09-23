<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{
    private function allow(): void { abort_unless(in_array(request()->user()->role, ['super_admin', 'admin', 'editor'], true), 403); }
    public function index(Request $request) { $this->allow(); $media = Media::query()->where('is_temporary', false)->with('articleLinks.article')->withCount('articleLinks')->when($request->filled('q'), fn ($query) => $query->where('original_name', 'like', '%'.$request->string('q').'%'))->latest()->paginate(24)->withQueryString(); return view('admin.media.index', compact('media')); }
    public function destroy(Media $media) { $this->allow(); if ($media->articleLinks()->exists()) return back()->withErrors(['media' => 'Media masih dipakai artikel dan tidak dapat dihapus.']); Storage::disk($media->disk)->delete($media->path); AuditLog::record('media.deleted', $media); $media->delete(); return back()->with('status', 'Media dihapus.'); }
}
