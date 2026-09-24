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
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:draft,review,scheduled,published,archived'], 'category' => ['nullable', 'integer', 'exists:categories,id'], 'sort' => ['nullable', 'string', 'max:30']]);
        $query = Article::query()->select(['id', 'category_id', 'author_id', 'title', 'slug', 'status', 'published_at', 'updated_at', 'created_at'])->with('category:id,name');
        if ($request->user()->role === 'writer') $query->where('author_id', $request->user()->id);
        $q = trim($data['q'] ?? '');
        if ($q !== '') $query->where('title', 'like', '%'.$q.'%');
        if (! empty($data['status'])) $query->where('status', $data['status']);
        if (! empty($data['category'])) $query->where('category_id', $data['category']);
        $sorts = ['updated_desc' => ['updated_at', 'desc'], 'updated_asc' => ['updated_at', 'asc'], 'title_asc' => ['title', 'asc'], 'title_desc' => ['title', 'desc'], 'created_desc' => ['created_at', 'desc'], 'created_asc' => ['created_at', 'asc'], 'published_desc' => ['published_at', 'desc'], 'published_asc' => ['published_at', 'asc']];
        [$column, $direction] = $sorts[$data['sort'] ?? 'updated_desc'] ?? $sorts['updated_desc'];
        $articles = $query->orderBy($column, $direction)->paginate(20)->withQueryString();
        return view('admin.articles.index', ['articles' => $articles, 'categories' => Category::query()->select(['id', 'name'])->orderBy('name')->get(), 'filters' => ['q' => $q, 'status' => $data['status'] ?? '', 'category' => $data['category'] ?? '', 'sort' => $data['sort'] ?? 'updated_desc']]);
    }
    public function create() { $this->authorize('create', Article::class); return view('admin.articles.form', ['article' => new Article, 'categories' => Category::active()->orderBy('sort_order')->get(), 'tags' => Tag::orderBy('name')->get()]); }
    public function store(StoreArticleRequest $request) { return $this->persist($request, new Article); }
    public function edit(Article $adminArticle) { $article = $adminArticle; $this->authorize('update', $article); $article->load('tags', 'mediaLinks.media'); return view('admin.articles.form', ['article' => $article, 'categories' => Category::active()->orderBy('sort_order')->get(), 'tags' => Tag::orderBy('name')->get()]); }
    public function update(UpdateArticleRequest $request, Article $adminArticle) { return $this->persist($request, $adminArticle); }
    public function preview(Article $adminArticle) { $this->authorize('update', $adminArticle); $adminArticle->load(['category', 'tags', 'author', 'mediaLinks.media', 'featuredMedia.media']); $related = Article::query()->where('category_id', $adminArticle->category_id)->whereKeyNot($adminArticle->getKey())->with(['category', 'featuredMedia.media'])->latest('published_at')->take(3)->get(); return view('news.show', ['article' => $adminArticle, 'contentSegments' => app(ArticleContentRenderer::class)->segments($adminArticle), 'related' => $related, 'categories' => Category::active()->orderBy('sort_order')->get(), 'robots' => 'noindex']); }
    public function revisions(Article $adminArticle) { $this->authorize('update', $adminArticle); return view('admin.articles.revisions', ['article' => $adminArticle, 'revisions' => $adminArticle->revisions()->with('editor')->paginate(20)]); }
    public function restore(Article $adminArticle, ArticleRevision $revision) { $this->authorize('update', $adminArticle); abort_unless($revision->article_id === $adminArticle->id, 404); $adminArticle->update(['title' => $revision->title, 'body' => $revision->body, 'excerpt' => $revision->excerpt ?: $adminArticle->excerpt ?: 'Dipulihkan dari revision.', 'status' => 'draft']); AuditLog::record('article.revision_restored', $adminArticle, ['revision_id' => $revision->id]); return redirect()->route('admin.articles.edit', $adminArticle)->with('status', 'Revisi dipulihkan sebagai draf.'); }
    public function destroy(Article $adminArticle) { $this->authorize('delete', $adminArticle); AuditLog::record('article.deleted', $adminArticle); $adminArticle->delete(); return redirect()->route('admin.articles.index')->with('status', 'Artikel dihapus.'); }
    private function persist($request, Article $article) { $data = $request->validated(); if (in_array($data['status'], ['published', 'scheduled'], true)) $this->authorize('publish', $article->exists ? $article : new Article); if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now(); if (! in_array($request->user()->role, ['super_admin', 'admin', 'editor'], true)) unset($data['is_editor_pick']); else $data['is_editor_pick'] = $request->boolean('is_editor_pick'); if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6)); return DB::transaction(function () use ($article, $data, $request) { $wasNew = ! $article->exists; $previousStatus = $article->status; $article->fill(collect($data)->except(['tag_ids', 'media'])->all()); if ($wasNew) $article->author_id = $request->user()->id; $article->save(); ArticleRevision::create(['article_id' => $article->id, 'editor_id' => $request->user()->id, 'title' => $article->title, 'body' => $article->body, 'excerpt' => $article->excerpt, 'status' => $article->status, 'snapshot' => ['category_id' => $article->category_id, 'published_at' => optional($article->published_at)->toISOString()]]); AuditLog::record($wasNew ? 'article.created' : ($previousStatus !== $article->status ? 'article.status_changed' : 'article.updated'), $article, ['status' => $article->status, 'is_editor_pick' => $article->is_editor_pick]); $article->tags()->sync($data['tag_ids'] ?? []); $article->mediaLinks()->delete(); $featured = false; foreach ($data['media'] ?? [] as $position => $item) { $role = $item['role']; if ($role === 'featured' && $featured) $role = 'gallery'; if ($role === 'featured') $featured = true; $media = Media::findOrFail($item['id']); $media->update(['alt_text' => $item['alt_text'] ?? $media->alt_text, 'caption' => $item['caption'] ?? $media->caption, 'is_temporary' => false]); $article->mediaLinks()->create(['media_id' => $media->id, 'role' => $role, 'sort_order' => $item['sort_order'] ?? $position, 'caption_override' => $item['caption'] ?? null]); } return redirect()->route('admin.articles.edit', $article)->with('status', 'Artikel disimpan.'); }); }
}
