<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Media;
use App\Models\Tag;
use App\Support\ArticleContentRenderer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index() { $query = Article::with('category')->latest(); if (request()->user()->role === 'writer') $query->where('author_id', request()->user()->id); return view('admin.articles.index', ['articles' => $query->paginate(20)]); }
    public function create() { $this->authorize('create', Article::class); return view('admin.articles.form', ['article' => new Article, 'categories' => Category::active()->orderBy('sort_order')->get(), 'tags' => Tag::orderBy('name')->get()]); }
    public function store(StoreArticleRequest $request) { return $this->persist($request, new Article); }
    public function edit(Article $adminArticle) { $article = $adminArticle; $this->authorize('update', $article); $article->load('tags', 'mediaLinks.media'); return view('admin.articles.form', ['article' => $article, 'categories' => Category::active()->orderBy('sort_order')->get(), 'tags' => Tag::orderBy('name')->get()]); }
    public function update(UpdateArticleRequest $request, Article $adminArticle) { return $this->persist($request, $adminArticle); }
    public function preview(Article $adminArticle) { $this->authorize('update', $adminArticle); $adminArticle->load(['category', 'tags', 'author', 'mediaLinks.media', 'featuredMedia.media']); $related = Article::query()->where('category_id', $adminArticle->category_id)->whereKeyNot($adminArticle->getKey())->with(['category', 'featuredMedia.media'])->latest('published_at')->take(3)->get(); return view('news.show', ['article' => $adminArticle, 'contentSegments' => app(ArticleContentRenderer::class)->segments($adminArticle), 'related' => $related, 'categories' => Category::active()->orderBy('sort_order')->get(), 'robots' => 'noindex']); }
    public function revisions(Article $adminArticle) { $this->authorize('update', $adminArticle); return view('admin.articles.revisions', ['article' => $adminArticle, 'revisions' => $adminArticle->revisions()->with('editor')->paginate(20)]); }
    public function restore(Article $adminArticle, ArticleRevision $revision) { $this->authorize('update', $adminArticle); abort_unless($revision->article_id === $adminArticle->id, 404); $adminArticle->update(['title' => $revision->title, 'body' => $revision->body, 'excerpt' => $revision->excerpt ?: $adminArticle->excerpt ?: 'Dipulihkan dari revision.', 'status' => 'draft']); AuditLog::record('article.revision_restored', $adminArticle, ['revision_id' => $revision->id]); return redirect()->route('admin.articles.edit', $adminArticle)->with('status', 'Revisi dipulihkan sebagai draf.'); }
    public function destroy(Article $adminArticle) { $this->authorize('delete', $adminArticle); AuditLog::record('article.deleted', $adminArticle); $adminArticle->delete(); return redirect()->route('admin.articles.index')->with('status', 'Artikel dihapus.'); }
    private function persist($request, Article $article) { $data = $request->validated(); if (in_array($data['status'], ['published', 'scheduled'], true)) $this->authorize('publish', $article->exists ? $article : new Article); if (! in_array($request->user()->role, ['super_admin', 'admin', 'editor'], true)) unset($data['is_editor_pick']); else $data['is_editor_pick'] = $request->boolean('is_editor_pick'); if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6)); return DB::transaction(function () use ($article, $data, $request) { $wasNew = ! $article->exists; $previousStatus = $article->status; $article->fill(collect($data)->except(['tag_ids', 'media'])->all()); if ($wasNew) $article->author_id = $request->user()->id; $article->save(); ArticleRevision::create(['article_id' => $article->id, 'editor_id' => $request->user()->id, 'title' => $article->title, 'body' => $article->body, 'excerpt' => $article->excerpt, 'status' => $article->status, 'snapshot' => ['category_id' => $article->category_id, 'published_at' => optional($article->published_at)->toISOString()]]); AuditLog::record($wasNew ? 'article.created' : ($previousStatus !== $article->status ? 'article.status_changed' : 'article.updated'), $article, ['status' => $article->status, 'is_editor_pick' => $article->is_editor_pick]); $article->tags()->sync($data['tag_ids'] ?? []); $article->mediaLinks()->delete(); $featured = false; foreach ($data['media'] ?? [] as $position => $item) { $role = $item['role']; if ($role === 'featured' && $featured) $role = 'gallery'; if ($role === 'featured') $featured = true; $media = Media::findOrFail($item['id']); $media->update(['alt_text' => $item['alt_text'] ?? $media->alt_text, 'caption' => $item['caption'] ?? $media->caption, 'is_temporary' => false]); $article->mediaLinks()->create(['media_id' => $media->id, 'role' => $role, 'sort_order' => $item['sort_order'] ?? $position, 'caption_override' => $item['caption'] ?? null]); } return redirect()->route('admin.articles.edit', $article)->with('status', 'Artikel disimpan.'); }); }
}
